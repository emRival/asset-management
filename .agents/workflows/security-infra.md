---
description: Apply infrastructure security — HTTPS, CORS, trusted hosts, proxy config, error handling, and rate limiting.
---

# 🌐 Security — Infrastructure

## What to Implement

### 1. Force HTTPS
- Redirect HTTP → HTTPS (301) in production
- Skip for: local development, health checks
- Configure at app level AND server level (Nginx/Apache)

### 2. CORS (Cross-Origin Resource Sharing)
Only allow your own domains:
```
Access-Control-Allow-Origin: https://yourdomain.com
Access-Control-Allow-Methods: GET, POST, PUT, DELETE
Access-Control-Allow-Headers: Content-Type, Authorization, X-CSRF-TOKEN
Access-Control-Allow-Credentials: true
Access-Control-Max-Age: 86400
```
**NEVER** use `*` for `Allow-Origin` when `Allow-Credentials: true`.

### 3. Trusted Hosts Validation
- Validate `Host` header against your domain whitelist
- Block requests with spoofed Host headers
- Support: exact match, subdomain, wildcard

### 4. Trusted Proxies
If behind Cloudflare/Nginx/Load Balancer:
- Configure proxy IP ranges so `request.ip` returns the real client IP
- Cloudflare IP ranges: https://www.cloudflare.com/ips/

### 5. Error Handling (No Info Leak)
- **Production:** Show generic error page, NEVER show stack traces
- Set `APP_DEBUG=false` / `DEBUG=False` in production
- Custom error pages: 400, 401, 403, 404, 419, 429, 500, 503
- Log full errors server-side, show safe messages client-side

### 6. API Rate Limiting
```
General API: 60 requests/minute per IP
Auth endpoints: 5 requests/minute per IP
Upload endpoints: 10 requests/minute per IP
Search: 30 requests/minute per IP
```

### 7. Request Size Limiting
```
Nginx: client_max_body_size 10M;
PHP: post_max_size=10M, upload_max_filesize=10M
Express: express.json({ limit: '10mb' })
Next.js: api route config { bodyParser: { sizeLimit: '10mb' } }
```

### 8. DNS Rebinding Protection
- Validate Host header matches expected domains
- Reject requests where Host doesn't match configured domains

## Implementation by Tech

| Tech | HTTPS | CORS | Proxy | Rate Limit |
|------|-------|------|-------|------------|
| **Laravel** | `ForceHttps` middleware | `config/cors.php` | `TrustProxies` middleware | `RateLimiter` |
| **Express.js** | `express-enforce-https` | `cors` npm | `trust proxy` setting | `express-rate-limit` |
| **Next.js** | Vercel auto / middleware | `next.config.js` headers | Vercel auto | `next-rate-limit` |
| **Django** | `SECURE_SSL_REDIRECT` | `django-cors-headers` | `SECURE_PROXY_SSL_HEADER` | `django-ratelimit` |
| **FastAPI** | Uvicorn `--ssl-*` flags | `CORSMiddleware` | `--proxy-headers` | `slowapi` |
