# DailyForever Installation Guide

A complete guide to self-host and develop DailyForever, a zero-knowledge encrypted paste and file sharing app.

This document is tailored to this repository's codebase:
- Backend: Laravel 12.x (PHP 8.2+)
- Frontend build: Vite 7, Tailwind CSS 4
- Optional components: Redis (cache/queues), ZK-SNARK circuits (circom/snarkjs), SRP-6a auth, ML-KEM prekeys


## 1) Prerequisites

- PHP 8.2+ with extensions:
  - openssl, pdo (pdo_mysql/pdo_pgsql/sqlite3 as needed), mbstring, tokenizer, xml, ctype, json, bcmath, gmp
  - sodium (recommended for best crypto performance; fallback via `paragonie/sodium_compat` is installed)
- Composer 2.5+
- Node.js 18+ and npm 9+
- Database: MySQL 8+/MariaDB 10.6+, PostgreSQL 13+, or SQLite (dev-friendly)
- Redis (optional, for cache/queues)
- Git
- Web server for production: Nginx or Apache with PHP-FPM
- HTTPS certificate for production
- Optional (for ZK circuits): `circom` and `snarkjs`

Recommended OS packages (Ubuntu/Debian example):
```bash
sudo apt update && sudo apt install -y \
  php php-fpm php-cli php-mbstring php-xml php-ctype php-json php-bcmath php-gmp \
  php-mysql php-pgsql php-sqlite3 php-curl php-zip \
  nginx git unzip redis-server
```


## 2) Clone and install dependencies

```bash
# 1. Clone
git clone <your-fork-or-repo-url> dailyforever
cd dailyforever

# 2. PHP deps
composer install --no-interaction --prefer-dist --optimize-autoloader

# 3. Node deps
npm install
```


## 3) Environment setup

Copy `.env` and generate app key:
```bash
cp .env.example .env
php artisan key:generate
```

Choose your database:
- Quick dev (SQLite file):
  ```bash
  # Use SQLite in .env (already default in .env.example)
  # DB_CONNECTION=sqlite
  # Create the database file if you prefer file-based sqlite
  mkdir -p database && touch database/database.sqlite
  ```
- MySQL example (`.env`):
  ```env
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=dailyforever
  DB_USERNAME=your_user
  DB_PASSWORD=your_password
  ```
- PostgreSQL example (`.env`):
  ```env
  DB_CONNECTION=pgsql
  DB_HOST=127.0.0.1
  DB_PORT=5432
  DB_DATABASE=dailyforever
  DB_USERNAME=your_user
  DB_PASSWORD=your_password
  ```

Cache/queue/filesystem defaults (from `.env.example`):
```env
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local

# Redis (optional)
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null

# Mail (dev default is log)
MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_FROM_ADDRESS="hello@example.com"

# SRP (Secure Remote Password) settings
SRP_PRIME_BITS=2048
SRP_PREHASH_ARGON2ID=false
```

Apply database migrations and link storage:
```bash
php artisan migrate
php artisan storage:link
```

Note: If you use database sessions (`SESSION_DRIVER=database`), create the sessions table first:
```bash
php artisan session:table
php artisan migrate
```

Ensure writable permissions for storage and cache (Linux example):
```bash
# Replace www-data with your web server user if different
sudo chown -R $USER:www-data storage bootstrap/cache
sudo chmod -R ug+rwX storage bootstrap/cache
```


## 4) Run in development

Option A: separate processes
```bash
# PHP server
php artisan serve --host=0.0.0.0 --port=8000

# In another terminal: Vite dev server
npm run dev
```

Option B: one command via Composer script (runs server, queue listener, logs, and Vite):
```bash
composer run dev
```

Open http://localhost:8000


## 5) Build assets

For production (or to test a production build locally):
```bash
npm run build
```
Vite inputs are defined in `vite.config.js` and include:
- `resources/css/app.css`
- `resources/js/app.js`
- `resources/js/analytics.js`
- `resources/js/webcrypto-wrapper.js`


## 6) Production deployment

Basic steps:
1. Set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://your-domain`
2. Configure your DB/Redis/mail in `.env`
3. Build assets: `npm run build`
4. Point your web server document root to `public/`
5. Ensure writable `storage/` and `bootstrap/cache/`
6. Run Laravel optimizations:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan optimize
   ```
7. Configure queue worker and scheduler:
   - Queue (choose your driver accordingly):
     ```bash
     php artisan queue:work --sleep=3 --tries=3 --timeout=90
     ```
   - Scheduler (cron):
     ```bash
     * * * * * cd /var/www/dailyforever && php artisan schedule:run >> /dev/null 2>&1
     ```

### Nginx example
```nginx
server {
    listen 80;
    server_name your-domain.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name your-domain.com;
    root /var/www/dailyforever/public;

    ssl_certificate     /etc/ssl/certs/your.crt;
    ssl_certificate_key /etc/ssl/private/your.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    add_header Strict-Transport-Security "max-age=63072000; includeSubDomains; preload" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock; # adjust to your PHP-FPM socket
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_index index.php;
    }

    location ~ /\.(?!well-known).* { deny all; }
}
```

### Apache (vhost) example
```apache
<VirtualHost *:443>
    ServerName your-domain.com
    DocumentRoot /var/www/dailyforever/public

    SSLEngine on
    SSLCertificateFile /etc/ssl/certs/your.crt
    SSLCertificateKeyFile /etc/ssl/private/your.key

    Header always set Strict-Transport-Security "max-age=63072000; includeSubDomains; preload"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"

    <Directory /var/www/dailyforever/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/dailyforever-error.log
    CustomLog ${APACHE_LOG_DIR}/dailyforever-access.log combined
</VirtualHost>
```


## 7) Security configuration

- Set `APP_DEBUG=false` in production.
- Enforce HTTPS at the web server and set secure cookies (Laravel uses secure cookie settings based on `APP_URL` and your server).
- Keep PHP, Node.js, and dependencies up-to-date.
- Limit upload sizes via PHP ini (e.g., `upload_max_filesize`, `post_max_size`).
- Use Redis for cache/queues where possible for better performance.

Advanced (from `docs/SECURITY_FEATURES.md`):
```env
# ZK Proof Settings
ZK_REQUIRE_PROOF=false  # Set true to enforce proofs for submissions

# Key Rotation Settings
KEY_MAX_AGE_DAYS=90
KEY_MAX_USES=10000
KEY_ROTATION_GRACE_DAYS=7

# Randomness Validation
RANDOM_MIN_ENTROPY_BITS=128
RANDOM_VALIDATION_ENABLED=true
```


## 8) Zero-Knowledge circuits (optional)

Prebuilt artifacts are included under `public/zkp/`:
- `encryption_commitment.wasm`
- `encryption_commitment.zkey`
- `verification_key.json`

You can rebuild from source using `circuits/build.sh` (requires `circom` and `snarkjs`):
```bash
cd circuits
chmod +x build.sh
./build.sh
```
This will output web-compatible artifacts into `public/zkp/`.

Tip: In development and production, the app serves circuit files via the route `GET /circuits/{path}` from multiple candidate locations, including `public/zkp` and `base_path('circuits')`.


## 9) Analytics (privacy-first)

- The file `resources/js/analytics.js` implements a strict no-PII, consent-first analytics helper.
- Analytics are effectively disabled unless GA4 `gtag` is present and the user has granted consent.
- Consent is stored in `localStorage` under `analytics_consent` and defaults to denied.

To enable GA4, add the GA snippet to your main layout (e.g., `resources/views/layouts/app.blade.php`) and provide a consent UI that calls `window.Analytics.updateConsent(true|false)`.


## 10) Testing

Run the full test suite:
```bash
php artisan test
```

Useful targeted tests (from repository):
```bash
php artisan test --filter=KeyRotationTest
```

Testing environment uses in-memory SQLite (see `phpunit.xml`).


## 11) Common issues & troubleshooting

- 500 error after deploy: ensure `.env` is correct, `php artisan key:generate` was run, and storage/cache are writable.
- Assets not loading in production: run `npm run build` and clear caches (`php artisan optimize:clear` then re-run optimization commands).
- Database locked (SQLite): for heavy concurrent usage, switch to MySQL/PostgreSQL.
- Queues not processing: start a worker (`php artisan queue:work`) and verify your `QUEUE_CONNECTION`.
- ZK proofs failing: ensure `/public/zkp/*` files exist or rebuild circuits; verify your browser supports WebAssembly.


## 12) Optional: Docker with Laravel Sail

Sail is available as a dev dependency. If you prefer Docker-based dev:
```bash
# If Sail isn't set up yet
php artisan sail:install
./vendor/bin/sail up -d

# In another terminal
./vendor/bin/sail artisan migrate
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```
Visit http://localhost (ports depend on your Sail config).


## 13) Production checklist

- [ ] `APP_ENV=production`, `APP_DEBUG=false`, correct `APP_URL`
- [ ] `npm run build` completed; Vite build artifacts deployed
- [ ] `php artisan config:cache route:cache view:cache optimize`
- [ ] Webserver docroot is `public/`
- [ ] `storage/` and `bootstrap/cache/` writable by web user
- [ ] Queue worker and cron configured
- [ ] HTTPS enabled with strong TLS settings
- [ ] PHP `upload_max_filesize` and `post_max_size` reflect your policy


---

If you need a distro-specific setup (Ubuntu, AlmaLinux, Docker Compose) or managed DB/Redis examples, let us know and we’ll add tailored steps.
