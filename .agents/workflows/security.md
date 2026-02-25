---
description: Master security workflow. Run this to apply ALL security hardening layers to any web app. Automatically detects tech stack and applies the appropriate sub-workflows.
---

# 🛡️ Security Hardening — Master Workflow

Run individual sub-workflows or apply all at once.

## Step 1: Detect Tech Stack
Scan the project for `package.json`, `composer.json`, `requirements.txt`, `Gemfile`, `go.mod`, etc.
Identify: Laravel, Next.js, React, Express.js, Django, FastAPI, Rails, Go, etc.

## Step 2: Apply All Security Layers
Run each sub-workflow in order:

1. `/security-injection` — SQL/Command/XSS injection protection
2. `/security-auth` — Brute force, 2FA, password policy, CSRF
3. `/security-session` — Session hijacking, RBAC, IDOR, cookie flags
4. `/security-upload` — File upload validation & scanning
5. `/security-headers` — HTTP security headers & CSP
6. `/security-infra` — HTTPS, CORS, trusted hosts, proxy config
7. `/security-crypto` — Field encryption, HashID, secrets management
8. `/security-monitoring` — Activity logging, security alerts, webhook
9. `/security-checklist` — Final production readiness checklist

## Step 3: Verify
- Run the app and test each security layer
- Check headers at https://securityheaders.com
- Verify no sensitive data leaks in error pages
