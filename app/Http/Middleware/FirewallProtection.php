<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Http;

class FirewallProtection
{
    /**
     * Fields to skip scanning for false positives (like rich text or passwords).
     */
    protected array $skipFields = [
        'password',
        'password_confirmation',
        'content',
        'body',
        'description',
        'reason',
        'bio',
        'excerpt',
        'message',
        'comment'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $banKey = "security_ban:{$ip}";
        $strikeKey = "security_strikes:{$ip}";

        // If IP is banned, reject entirely
        if (RateLimiter::tooManyAttempts($banKey, 1)) {
            abort(403, 'Access Denied: Your IP has been temporarily banned due to suspicious activity.');
        }

        // Scan inputs
        if ($this->detectMaliciousInput($request)) {
            $strikesHit = RateLimiter::hit($strikeKey, 60); // 1 minute decay per strike

            Log::channel('security')->warning('Malicious payload detected', [
                'ip' => $ip,
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'user_agent' => $request->userAgent(),
                'strikes' => $strikesHit
            ]);

            // 5 strikes = Ban for 24 hours
            if ($strikesHit >= 5) {
                RateLimiter::hit($banKey, 24 * 60 * 60); // Ban 24h
                RateLimiter::clear($strikeKey);

                Log::channel('security')->alert('IP BANNED', ['ip' => $ip]);
                $this->sendWebhookAlert($ip, $request->fullUrl());

                abort(403, 'Access Denied: Your IP has been banned due to repeated suspicious activity.');
            }

            abort(400, 'Bad Request: Suspicious input detected.');
        }

        return $next($request);
    }

    protected function detectMaliciousInput(Request $request): bool
    {
        $inputs = $request->except($this->skipFields);

        // Basic pattern matching for SQLi, XSS, Command Injection, SSRF
        $patterns = [
            '/(?:UNION\s+(?:ALL\s+)?SELECT|OR\s+\'1\'\=\'1|AND\s+1\=1)/i', // SQLi basics
            '/(?:DROP|DELETE|TRUNCATE|INSERT|UPDATE|ALTER)\s+(?:TABLE|DATABASE|FROM|INTO)/i', // Destructive SQL
            '/(?:SLEEP\(\d+\)|BENCHMARK\(|WAITFOR\s+DELAY)/i', // Time-based blind SQLi
            '/(?:information_schema|sys\.tables)/i', // Info gathering
            '/<script\b[^>]*>(.*?)<\/script>/is', // Basic XSS
            '/on(?:click|load|error|mouseover|submit)\s*=/i', // XSS Event handlers
            '/(?:javascript|vbscript|data):/i', // Dangerous URI schemes
            '/(?:\$\(|`|\|\s*[\w]+|;\s*[\w]+|&&\s*[\w]+)/i', // Command injection
            '/(?:eval|exec|system|passthru|shell_exec|popen)\s*\(/i', // PHP execution functions
            '/O:\d+:"[^"]+":/i', // PHP Deserialization
            '/file:\/\/|gopher:\/\/|dict:\/\/|ldap:\/\//i', // SSRF protocols
            '/(?:\.\.\/|\.\.%2f)/i', // Path traversal
        ];

        // Recursive flatten to check all nested array data
        $flattened = \Illuminate\Support\Arr::dot($inputs);

        foreach ($flattened as $key => $value) {
            if (!is_string($value)) {
                continue;
            }

            foreach ($patterns as $pattern) {
                if (preg_match($pattern, (string) $value)) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function sendWebhookAlert(string $ip, string $url): void
    {
        $webhookUrl = env('SECURITY_WEBHOOK_URL');
        if (!$webhookUrl) {
            return;
        }

        try {
            Http::post($webhookUrl, [
                'content' => "🚨 **SECURITY ALERT: IP BANNED** 🚨\n\n" .
                    "**IP Address:** {$ip}\n" .
                    "**Target URL:** {$url}\n" .
                    "**Reason:** 5 consecutive malicious payloads detected.\n" .
                    "**Action:** IP banned for 24 hours."
            ]);
        } catch (\Exception $e) {
            Log::channel('security')->error('Failed to send webhook', ['error' => $e->getMessage()]);
        }
    }
}
