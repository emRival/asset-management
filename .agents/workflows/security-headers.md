---
description: Apply HTTP security headers — CSP, HSTS, X-Frame-Options, Permissions-Policy, and cache controls.
---

# 🪖 Security — HTTP Headers

## What to Implement

### 1. Anti-Attack Headers
```
X-Frame-Options: SAMEORIGIN              # Prevent clickjacking
X-Content-Type-Options: nosniff          # Prevent MIME sniffing
X-XSS-Protection: 1; mode=block         # Legacy XSS filter
Referrer-Policy: strict-origin-when-cross-origin
```

### 2. HTTPS Enforcement (HSTS)
```
Strict-Transport-Security: max-age=31536000; includeSubDomains; preload
```

### 3. Content Security Policy (CSP)
```
default-src 'self';
script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com;
style-src 'self' 'unsafe-inline' https://fonts.googleapis.com;
img-src 'self' data: https: blob:;
font-src 'self' https://fonts.gstatic.com data:;
connect-src 'self' https://your-api.com;
object-src 'none';
frame-ancestors 'self';
base-uri 'self';
upgrade-insecure-requests;
```

### 4. Permissions Policy
```
geolocation=(), microphone=(), camera=(), payment=(),
usb=(), magnetometer=(), gyroscope=(), accelerometer=()
```

### 5. Cross-Origin Policies
```
Cross-Origin-Embedder-Policy: unsafe-none
Cross-Origin-Opener-Policy: same-origin-allow-popups
Cross-Origin-Resource-Policy: cross-origin
```

### 6. Cache Control (sensitive pages)
For login, admin, profile, settings:
```
Cache-Control: no-store, no-cache, must-revalidate, max-age=0
Pragma: no-cache
Expires: 0
```

### 7. Hide Server Info
Remove: `X-Powered-By`, `Server` headers.

## Implementation by Tech

| Tech | How |
|------|-----|
| **Laravel** | Custom middleware setting `$response->headers->set()` |
| **Express.js** | `helmet` npm package (covers most headers automatically) |
| **Next.js** | `next.config.js` → `headers()` function |
| **Django** | `SecurityMiddleware` + `django-csp` package |
| **Nginx** | `add_header` directives in server block |
| **Cloudflare** | Dashboard → SSL/TLS → Edge Certificates (HSTS) |
| **React (CRA)** | Configure in hosting platform (Vercel/Netlify headers config) |
