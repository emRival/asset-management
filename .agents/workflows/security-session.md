---
description: Apply session security, RBAC, IDOR protection, and cookie hardening.
---

# 🔒 Security — Session & Access Control

## What to Implement

### 1. Session Hijacking Prevention
- Fingerprint: store `IP` + `User-Agent hash` in session
- User-Agent changed → **force logout** (session stolen)
- IP changed → **log warning** only (mobile networks change IPs)
- Auto-regenerate session ID every 30 minutes
- Idle timeout: force logout after 2 hours of inactivity

### 2. Cookie Security Flags
Set these flags on ALL session/auth cookies:
```
HttpOnly: true    (JavaScript cannot read the cookie)
Secure: true      (only sent over HTTPS)
SameSite: Lax     (prevents CSRF from external sites)
Path: /           (available on all paths)
Max-Age: 7200     (2 hours, match your session timeout)
```

### 3. Role-Based Access Control (RBAC)
- Define roles: `admin`, `moderator`, `user`, etc.
- Create middleware that checks `user.role` against route's allowed roles
- Unauthorized → 403 Forbidden
- Never rely on frontend-only role checks

### 4. IDOR Protection (Insecure Direct Object Reference)
- Before returning ANY resource, verify: `resource.owner_id === currentUser.id`
- Admin roles bypass ownership check
- Public resources (published articles) readable by all via GET
- All IDOR violations → log to security channel

### 5. HashID URL Obfuscation
- Encode numeric IDs in URLs: `/users/5` → `/users/Kx9mP`
- Prevents enumeration: attacker can't guess `/users/1, /2, /3...`
- Decode in middleware before reaching controller
- Invalid hash → 404 Not Found

### 6. Admin Panel Restriction
- IP whitelist for admin routes (configurable via env/config)
- Support: exact IP, CIDR (`10.0.0.0/8`), wildcard (`192.168.1.*`)
- Empty whitelist = allow all (safe default for development)

## Implementation by Tech

| Tech | Session | RBAC | IDOR | HashID |
|------|---------|------|------|--------|
| **Laravel** | Middleware + `session()` | `CheckRole` middleware | `Policy` classes | `hashids/hashids` |
| **Express.js** | `express-session` + custom | Custom middleware | Custom middleware | `hashids` npm |
| **Next.js** | NextAuth.js callbacks | Middleware + `getServerSession` | Server Action checks | `hashids` npm |
| **Django** | `SessionMiddleware` + custom | `@permission_required` | `get_object_or_404` + check | `django-hashid-field` |
| **React SPA** | Server-side only | Server-side only | Server-side only | Server-side only |
| **FastAPI** | `Depends()` + Redis | `Depends()` guard | `Depends()` owner check | `hashids` pip |
