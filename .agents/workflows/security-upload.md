---
description: Apply file upload security — extension/MIME validation, content scanning, size limits, and image verification.
---

# 📁 Security — File Upload Protection

## What to Implement

### 1. Validation Chain (in order)
```
1. File exists and is valid (not corrupted)
2. File size ≤ max limit (default 5MB)
3. Extension NOT in blocked list
4. Extension IS in allowed list
5. MIME type matches allowed list
6. No double extension (image.php.jpg)
7. No script code inside file content
8. If image: verify with image library
```

### 2. Blocked Extensions
```
.php, .phtml, .php3, .php4, .php5, .phps,
.exe, .bat, .cmd, .com, .pif, .msi,
.sh, .bash, .zsh, .csh,
.js, .jsx, .ts, .tsx, .py, .pl, .rb, .java, .jar, .war,
.asp, .aspx, .jsp, .cgi,
.htaccess, .htpasswd, .env, .config, .ini, .yml, .yaml
```

### 3. Allowed Extensions (whitelist)
```
Images: .jpg, .jpeg, .png, .gif, .webp, .svg, .bmp
Docs: .pdf, .doc, .docx, .xls, .xlsx, .ppt, .pptx
Media: .mp4, .mp3, .wav, .ogg, .webm
```

### 4. Content Scanning
Scan raw file bytes for embedded code:
```
<?php, <?=, <?, <%           (PHP/ASP tags)
<script                       (JavaScript)
#!/bin/                       (Shell scripts)
import os; import subprocess  (Python)
```

### 5. Image Verification
For image uploads, verify the file IS a real image:
- Use `getimagesize()` (PHP), `sharp` (Node), `Pillow` (Python)
- Invalid image data = reject file

### 6. Storage Security
- NEVER store uploads in web-accessible directories
- Use random filenames: `uuid.ext` instead of original name
- Store outside `public/` and serve via controller with auth check
- Set `X-Content-Type-Options: nosniff` on served files

## Implementation by Tech

| Tech | Middleware | Image Check | Storage |
|------|-----------|-------------|---------|
| **Laravel** | Custom middleware + `UploadedFile` | `getimagesize()` | `Storage::disk('local')` |
| **Express.js** | `multer` + custom validator | `sharp` / `image-size` | S3 or private dir |
| **Next.js** | API route validation | `sharp` | Vercel Blob / S3 |
| **Django** | Custom validator on `FileField` | `Pillow` | `MEDIA_ROOT` (private) |
| **FastAPI** | `UploadFile` + `Depends()` | `Pillow` | Local dir / S3 |
