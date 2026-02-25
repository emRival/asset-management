<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (method_exists($response, 'header')) {
            $response->header('X-Frame-Options', 'SAMEORIGIN');
            $response->header('X-Content-Type-Options', 'nosniff');
            $response->header('X-XSS-Protection', '1; mode=block');
            $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');

            // Note: HSTS requires HTTPS
            $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');

            // A permissive but solid CSP that works with Filament/AlpineJS/Livewire
            $csp = "default-src 'self'; " .
                "script-src 'self' 'unsafe-eval' 'unsafe-inline' https://cdn.jsdelivr.net https://static.cloudflareinsights.com; " .
                "worker-src 'self' blob:; " .
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
                "img-src 'self' data: https: blob:; " .
                "font-src 'self' https://fonts.gstatic.com data:; " .
                "connect-src 'self' ws: wss:; " .
                "object-src 'none'; " .
                "base-uri 'self'; " .
                "upgrade-insecure-requests;";

            $response->header('Content-Security-Policy', $csp);

            $response->header('Permissions-Policy', 'geolocation=(), microphone=(), camera=(), payment=(), usb=()');

            // Optional: Hide server signature
            // $response->header('X-Powered-By', '');
            // $response->header('Server', '');
        }

        return $response;
    }
}
