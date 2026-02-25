---
description: Apply authentication hardening — brute force protection, 2FA, password policy, CSRF, JWT security.
---

# 🔐 Security — Authentication Hardening

## What to Implement

### 1. Brute Force / Rate Limiting
- Throttle key = hash of `IP + email/username`
- Max 5 attempts per 15 minutes
- After limit: return 429 with `retry_after` countdown
- After 15+ total failures: ban IP for 60 minutes
- Successful login: clear counter immediately
- Notify admin via email/webhook on suspected brute force

### 2. Two-Factor Authentication (TOTP)
- Generate TOTP secret (Google Authenticator compatible)
- Generate QR code URL for scanning
- 8 one-time recovery codes, stored **encrypted**
- Verify TOTP code on login (window tolerance ±1)
- Recovery code: one-time use, auto-deleted after use
- Session cache: verified for 12 hours

### 3. Password Policy
Enforce on registration AND password change:
```
Minimum 8 characters
At least 1 uppercase letter (A-Z)
At least 1 lowercase letter (a-z)
At least 1 number (0-9)
At least 1 special character (!@#$%^&*)
Block username "admin" from registration
```

### 4. CSRF Protection
- Generate unique token per session/form
- Validate token on every POST/PUT/PATCH/DELETE
- Reject request with 419/403 on invalid token
- SPA with API: use `SameSite=Strict` cookie + CSRF header

### 5. JWT Security (for API-based apps)
- Short expiry: access token 15 min, refresh token 7 days
- Store refresh token in HttpOnly cookie, NOT localStorage
- Rotate refresh tokens on each use
- Blacklist tokens on logout
- Include `iat`, `exp`, `sub` claims minimum

### 6. OAuth/Social Login Security
- Validate `state` parameter to prevent CSRF
- Verify `id_token` signature
- Never trust client-side tokens without server verification

## Implementation by Tech

| Tech | Brute Force | 2FA | CSRF | JWT |
|------|-------------|-----|------|-----|
| **Laravel** | `RateLimiter` + middleware | `PragmaRX/Google2FA` | Built-in `@csrf` | `laravel/sanctum` |
| **Express.js** | `express-rate-limit` | `speakeasy` + `qrcode` | `csurf` | `jsonwebtoken` |
| **Next.js** | `next-rate-limit` / Upstash | `otpauth` | Built-in (Server Actions) | `jose` |
| **Django** | `django-axes` | `django-otp` | Built-in `{% csrf_token %}` | `djangorestframework-simplejwt` |
| **React SPA** | Server-side only | Server-side only | Double-submit cookie | Store in HttpOnly cookie |
| **FastAPI** | `slowapi` | `pyotp` | Custom header check | `python-jose` |
