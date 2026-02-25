---
description: Apply injection protection (SQLi, XSS, Command Injection, SSRF). Detect tech stack and implement appropriate middleware/guards.
---

# 🧱 Security — Injection Protection

## What to Implement

### 1. SQL Injection Protection
Create a middleware/guard that scans ALL user input against these patterns:
```
UNION (ALL) SELECT, ' OR '1'='1, ' AND 1=1
; DROP/DELETE/INSERT/UPDATE/ALTER/CREATE/TRUNCATE
SLEEP(), BENCHMARK(), WAITFOR DELAY
information_schema, sys.tables
-- (comment), # (MySQL comment)
```

**Skip fields** (avoid false positives): `password, content, body, bio, excerpt, title, name, slug, description, message, comment`

### 2. XSS Protection
**Plain text fields:** Strip all HTML → encode special chars.
**Rich text fields (WYSIWYG):** Strip only dangerous elements:
- Tags: `<script>`, `<style>`, `<iframe>`, `<object>`, `<embed>`, `<form>`, `<input>`, `<link>`, `<meta>`, `<base>`
- Attributes: all `on*` event handlers (`onclick`, `onerror`, `onload`, etc.)
- Protocols: `javascript:`, `vbscript:`, `data:` in src/href

### 3. Command Injection Protection
Detect: `$()`, backticks, `| command`, `; command`, `&& command`, `||`
Detect functions: `eval()`, `exec()`, `system()`, `passthru()`, `shell_exec()`, `popen()`
Detect: path traversal `../`, `..%2f`, null bytes `\x00`

### 4. SSRF Protection
Block URLs targeting: `localhost`, `127.0.0.1`, `10.x.x.x`, `172.16-31.x.x`, `192.168.x.x`, `169.254.169.254`
Block protocols: `file://`, `gopher://`, `dict://`, `ftp://`, `ldap://`

### 5. Deserialization Protection
Detect: PHP objects `O:`, Java `rO0AB`, Fastjson `{"@type"`

## Auto-Ban Integration
Every violation = 1 strike. 5 strikes in 60s = IP banned 24h.
Log every violation to security log. Send webhook alert.

## Implementation by Tech

| Tech | How |
|------|-----|
| **Laravel** | Middleware class, `$request->all()` scanning, `RateLimiter` for strikes |
| **Express.js** | Middleware function on `req.body/query/params`, `express-rate-limit` |
| **Next.js** | `middleware.ts` for API routes, or custom API wrapper |
| **Django** | Custom middleware class in `MIDDLEWARE` setting |
| **React (SPA)** | Server-side only! Frontend sanitization is a bonus, never a replacement |
| **FastAPI** | Dependency injection middleware, `Depends()` |
