# AlbaTech Solutions

Kenya-focused human digital assistance platform.

AlbaTech helps individuals and businesses get practical digital tasks done, including KRA and eCitizen assistance, SHA registration support, online applications, business registration assistance, CVs and documents. Websites, software and other technology services are available as a second layer for businesses that need more.

AlbaTech is an independent business and is not a government agency.

## Stack

- PHP 8.2+
- Custom PHP framework
- MySQL/MariaDB
- Composer
- Public document root: `public_html/`

## Local setup

```bash
composer install
php bin/preflight.php
```

Configure the required environment values in a local `.env`. Never commit `.env`, credentials, API keys or deployment secrets.

## Production notes

Production fail-closed checks require `APP_DEBUG=false`, a sufficiently long `APP_KEY`, and an HTTPS `APP_URL`.

Optional GitHub deployment automation is server-side only. Configure `GIT_WEBHOOK_SECRET`, `GIT_GITHUB_REPO`, `GIT_DEPLOY_BRANCH`, and set `GIT_AUTO_PULL=true` only on a clean deployment checkout. The web application never accepts a PAT and never performs `git push`.

## Tests

Run the preflight checks before deployment and exercise authentication, 2FA, assistance requests, private quote links, payment verification, and webhook signature handling in a staging environment.
