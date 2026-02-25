---
description: Apply monitoring, activity logging, security event tracking, and webhook alerts for real-time attack notifications.
---

# 📊 Security — Monitoring & Alerting

## What to Implement

### 1. Activity Logging
Log every authenticated user action to database:
```json
{
  "user_id": 104,
  "action": "POST /articles",
  "ip_address": "103.x.x.x",
  "user_agent": "Mozilla/5.0...",
  "created_at": "2026-02-22 22:00:00"
}
```
Store in `activity_logs` table. Show in admin dashboard.

### 2. Security Event Logging
Log ALL violations to a **dedicated** log file/channel (NOT the general app log):
- SQL injection attempts
- XSS/Command injection attempts
- Brute force login attempts
- File upload violations
- IDOR access attempts
- Session anomalies (hijacking detected)
- IP bans (auto-ban triggered)

**Log format:**
```json
{
  "type": "SQL Injection",
  "ip": "103.x.x.x",
  "user_agent": "...",
  "url": "/search?q=1' OR 1=1",
  "field": "q",
  "pattern": "matched_regex",
  "user_id": null,
  "timestamp": "2026-02-22T22:00:00"
}
```

### 3. Webhook Alerts
Send real-time alerts when attacks are detected:

**Payload to send:**
```json
{
  "type": "SQL Injection",
  "ip": "103.x.x.x",
  "url": "attacked URL",
  "banned": true,
  "ban_duration": "24 hours",
  "timestamp": "ISO 8601"
}
```

**Supported endpoints:**
- Discord: `POST https://discord.com/api/webhooks/{id}/{token}`
- Telegram: `POST https://api.telegram.org/bot{token}/sendMessage`
- Slack: `POST https://hooks.slack.com/services/...`
- n8n: `POST https://n8n.yourdomain.com/webhook/...`
- Custom: any URL that accepts JSON POST

**Config via env:** `SECURITY_WEBHOOK_URL=https://...`
If empty → skip sending (graceful degradation).

### 4. Login Tracking
On each successful login, record:
```
last_login_at: timestamp
last_login_ip: IP address
login_count: increment
```

### 5. Dependency Vulnerability Scanning
Run periodically:
```bash
# Node.js
npm audit

# Python
pip-audit / safety check

# PHP
composer audit

# Ruby
bundle audit
```

## Implementation by Tech

| Tech | Activity Log | Security Log | Webhook |
|------|-------------|-------------|---------|
| **Laravel** | Custom middleware + `ActivityLog` model | `Log::channel('security')` | Queue job + `Http::post()` |
| **Express.js** | `morgan` + custom DB logger | `winston` dedicated transport | `axios.post()` in middleware |
| **Next.js** | Server Action logging | `pino` or file-based logger | `fetch()` in API middleware |
| **Django** | `django-auditlog` | `logging` module, separate handler | `requests.post()` in signal |
| **FastAPI** | Custom middleware + SQLAlchemy | `logging` module | `httpx.post()` background task |
