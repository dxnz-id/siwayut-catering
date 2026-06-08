# Security Policy

## Supported Versions

| Version | Supported |
|---------|-----------|
| latest  | ✅ |

## Reporting a Vulnerability

If you discover a security vulnerability, please report it privately by emailing **dxnzid@icloud.com**.

Do **not** disclose the vulnerability publicly until it has been addressed.

## Security Measures

This project implements:

- **CSRF protection** — token verification on all non-GET requests
- **SQL injection prevention** — PDO prepared statements only
- **XSS prevention** — `htmlspecialchars()` output escaping on all views
- **Brute-force protection** — progressive delay on login + rate limiting middleware
- **Session security** — regeneration on login, HTTP-only cookies, idle timeout
- **SSRF protection** — private IP validation on URL-based file uploads
- **CAPTCHA** — Cloudflare Turnstile on public forms

See the full [Security Guide](docs/security/SECURITY.md) for details.
