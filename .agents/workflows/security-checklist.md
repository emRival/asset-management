---
description: Final production security checklist. Run this after implementing all other security workflows to verify everything is in place.
---

# ✅ Security — Production Checklist

Run through this checklist before deploying to production.

## Environment
```
[ ] APP_DEBUG=false / DEBUG=False (NEVER true in production)
[ ] APP_ENV=production
[ ] SESSION_DRIVER=database or file (NEVER cookie for apps with big forms)
[ ] Secret keys are strong and random (not defaults)
[ ] .env file is NOT in version control (.gitignore)
[ ] .env file is NOT web-accessible (404 on /.env)
```

## HTTPS & Network
```
[ ] HTTPS enforced (HTTP → HTTPS redirect)
[ ] HSTS header active (max-age=31536000)
[ ] SSL certificate valid and auto-renewing
[ ] CORS configured (NOT wildcard * with credentials)
[ ] Trusted proxies configured (Cloudflare/Nginx)
```

## Security Headers
```
[ ] X-Frame-Options: SAMEORIGIN
[ ] X-Content-Type-Options: nosniff
[ ] Content-Security-Policy configured
[ ] Permissions-Policy configured
[ ] X-Powered-By / Server headers removed
[ ] Test at: https://securityheaders.com
```

## Authentication
```
[ ] Brute force rate limiting active on login
[ ] Password policy enforced (8+ chars, mixed case, symbols)
[ ] CSRF protection on all forms
[ ] 2FA available (optional or mandatory per config)
[ ] Session timeout configured (idle + absolute)
[ ] Session ID regenerated periodically
```

## Input Protection
```
[ ] SQL injection scanning middleware active
[ ] XSS sanitization middleware active
[ ] Command injection scanning middleware active
[ ] Auto-ban IP after 5 strikes active
[ ] Skip fields configured for WYSIWYG content
```

## File Uploads
```
[ ] Extension whitelist configured
[ ] MIME type validation active
[ ] File size limits set (app + server)
[ ] Content scanning active (no PHP in images)
[ ] Uploads stored outside web root
[ ] Random filenames used (not original names)
```

## Access Control
```
[ ] RBAC middleware on admin/privileged routes
[ ] IDOR checks on all resource endpoints
[ ] Authorization policies defined per model
[ ] Admin panel IP-restricted (if applicable)
[ ] HashIDs in URLs (no numeric ID exposure)
```

## Database & Secrets
```
[ ] PII fields encrypted (phone, SSN, etc.)
[ ] Database user has minimum required privileges
[ ] Database not accessible from public internet
[ ] Backups encrypted and tested
[ ] API keys and secrets in .env, not in code
```

## Monitoring
```
[ ] Activity logging active for authenticated users
[ ] Security log separate from application log
[ ] Webhook alerts configured (optional but recommended)
[ ] Error pages don't leak stack traces or paths
[ ] Log rotation configured (prevent disk full)
```

## Server
```
[ ] Firewall configured (only 80/443 open)
[ ] SSH key-based auth (no password SSH)
[ ] Server timezone correct (timedatectl)
[ ] PHP/Node/Python version up to date
[ ] OS security updates applied
[ ] Unnecessary services disabled
```

## Dependencies
```
[ ] npm audit / composer audit / pip-audit clean
[ ] No known vulnerable dependencies
[ ] Lock files committed (package-lock.json, composer.lock)
[ ] Automated vulnerability scanning (Dependabot/Snyk)
```
