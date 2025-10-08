# DailyForever - Open Source Zero-Knowledge Encrypted Paste & File Sharing Platform

<div align="center">

![DailyForever Logo](https://pouch.jumpshare.com/preview/v9sHHiVwhzbxzr_e8LRINcJAQWYyIg-_KyoIKSkHlXH55VDBObiowgAUPWQ7GHio5Z6EezX-eonjvadRSmnkhXAn4LEqBhLNx5pPdcwW-Mw)

**The Secure Way to Share Sensitive Information Online**

[![PHP Version](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=flat-square&logo=php)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![Security](https://img.shields.io/badge/Security-Zero--Knowledge-green?style=flat-square&logo=shield)](https://dailyforever.com)
[![License](https://img.shields.io/badge/license-AGPLv3-green.svg?style=flat)](LICENSE)
[![Status](https://img.shields.io/badge/Status-Production-success?style=flat-square)](https://dailyforever.com)

[WEBSITE](https://dailyforever.com) • [Documentation](https://dailyforever.com/how-it-works) • [Security](https://dailyforever.com/security) • [Support](https://dailyforever.com/support)

</div>

---

## Overview

DailyForever is a encrypted pastebin alternative and file sharing platform that implements true zero-knowledge encryption. Unlike conventional services, we **never** have access to your data - not even encrypted. Every piece of content is encrypted client-side before transmission, ensuring absolute privacy and security.

### Why DailyForever?

- **Zero-Knowledge with Proof**: Your data is encrypted in your browser before it ever leaves your device
- **Post-Quantum Ready**: Implementing ML-KEM (Kyber) for future-proof security
- **No Data Collection**: Zero tracking, zero analytics, zero logs
- **Open Source**: Full transparency - audit our code anytime
- **Self-Hostable**: Run your own instance with complete control

## Key Features

### Advanced Security
- **AES-256-GCM Encryption**: Encryption for all content
- **Zero-Knowledge Proofs**: Mathematical proof of authenticity without content exposure
- **SRP-6a Authentication**: Zero-knowledge password authentication protocol
- **Argon2id Hashing**: State-of-the-art password hashing when needed
- **Post-Quantum Cryptography**: ML-KEM prekey system for quantum-resistant sharing (Experimental)

### Enterprise Features
- **File Sharing**: Upload and share encrypted files up to 160MB
- **Burn After Reading**: Single-view pastes that self-destruct
- **Expiration Control**: Set automatic deletion (1 hour to never)
- **Password Protection**: Additional layer with server-side verification
- **Private Pastes**: Visible only to authenticated users
- **Recipient Addressing**: Send directly to specific users using prekeys

### User Experience
- **Mobile-First Design**: Fully responsive on all devices
- **Dark/Light Themes**: Automatic or manual theme switching
- **Multi-Language**: English and Spanish support (more coming)
- **Syntax Highlighting**: 20+ programming languages supported
- **QR Code Generation**: Share links instantly via QR
- **Rich Text Editor**: Advanced code editor with line numbers

## Technical Architecture

```
┌─────────────────┐     ┌──────────────────┐     ┌─────────────────┐
│                 │     │                  │     │                 │
│   Browser       │────▶│   CloudFlare     │────▶│   Application   │
│   (Encryption)  │     │   (DDoS/CDN)     │     │   (Laravel)     │
│                 │     │                  │     │                 │
└─────────────────┘     └──────────────────┘     └─────────────────┘
        │                                                │
        │                                                │
        ▼                                                ▼
┌─────────────────┐                          ┌─────────────────┐
│                 │                          │                 │
│   Web Crypto    │                          │   MySQL/        │
│   API           │                          │   PostgreSQL    │
│                 │                          │                 │
└─────────────────┘                          └─────────────────┘
```

### Technology Stack

| Component | Technology | Purpose |
|-----------|------------|---------|
| **Backend** | Laravel 11.x | Core application framework |
| **Frontend** | Blade + Alpine.js | Templating and interactivity |
| **Styling** | Tailwind CSS 3.x | Responsive, modern UI |
| **Database** | MySQL 8.0+ / PostgreSQL 15+ | Data persistence |
| **Caching** | Redis / Memcached | Performance optimization |
| **Queue** | Redis / Database | Background job processing |
| **Encryption** | Web Crypto API | Client-side encryption |
| **Post-Quantum** | ML-KEM (Kyber-1024) | Future-proof key exchange |
| **Authentication** | SRP-6a Protocol | Zero-knowledge auth |
| **File Storage** | Local / S3 Compatible | Encrypted file storage |

## Installation

### Prerequisites

- **PHP** 8.3 or higher with extensions: `openssl`, `pdo`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `gmp`
- **Composer** 2.5+
- **MySQL** 8.0+ or **PostgreSQL** 15+
- **Node.js** 18+ and npm 9+
- **Redis** (optional, for caching/queues)
- **HTTPS Certificate** (required for production)

### Quick Start

```bash
# 1. Clone the repository
git clone https://github.com/dailyforever/dailyforever.git
cd dailyforever

# 2. Install PHP dependencies
composer install --optimize-autoloader

# 3. Install Node dependencies and build assets
npm install
npm run build

# 4. Configure environment
cp .env.example .env
php artisan key:generate

# 5. Configure database (edit .env with your credentials)
nano .env

# 6. Run database migrations
php artisan migrate

# 7. Set up storage links
php artisan storage:link

# 8. Configure web server (see below)
# For development:
php artisan serve --host=0.0.0.0 --port=8000

# For production: Configure Apache/Nginx (see documentation)
```

### Environment Configuration

```env
# Application Settings
APP_NAME="DailyForever"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
APP_LOCALE=en
APP_TIMEZONE=UTC

# Security
FORCE_HTTPS=true
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dailyforever
DB_USERNAME=your_username
DB_PASSWORD=your_secure_password

# File Upload
FILE_MAX_SIZE=167772160  # 160MB in bytes
ALLOWED_FILE_TYPES=txt,pdf,doc,docx,xls,xlsx,png,jpg,jpeg,gif,zip,tar,gz,7z,rar

# Cache & Queue (optional)
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail (optional, for notifications)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=tls

# AdSense (optional)
ADSENSE_ENABLED=false
ADSENSE_CLIENT_ID=ca-pub-xxxxx
ADSENSE_BANNER_SLOT=xxxxx
```

## Deployment

### Production Deployment with Apache

```apache
<VirtualHost *:443>
    ServerName dailyforever.com
    DocumentRoot /var/www/dailyforever/public
    
    SSLEngine on
    SSLCertificateFile /path/to/certificate.crt
    SSLCertificateKeyFile /path/to/private.key
    SSLCertificateChainFile /path/to/chain.pem
    
    # Security Headers
    Header always set Strict-Transport-Security "max-age=63072000; includeSubDomains; preload"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
    Header always set Permissions-Policy "geolocation=(), microphone=(), camera=()"
    
    <Directory /var/www/dailyforever/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/dailyforever-error.log
    CustomLog ${APACHE_LOG_DIR}/dailyforever-access.log combined
</VirtualHost>
```

### Production Deployment with Nginx

```nginx
server {
    listen 443 ssl http2;
    server_name dailyforever.com;
    root /var/www/dailyforever/public;
    
    # SSL Configuration
    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    
    # Security Headers
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
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Performance Optimization

```bash
# Enable Laravel optimizations
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Set up scheduled tasks (cron)
* * * * * cd /var/www/dailyforever && php artisan schedule:run >> /dev/null 2>&1

# Queue worker (if using queues)
php artisan queue:work --sleep=3 --tries=3 --timeout=90
```

## Security Features in Detail

### Client-Side Encryption Flow

```javascript
// 1. Generate random encryption key
const key = crypto.getRandomValues(new Uint8Array(32))

// 2. Encrypt content with AES-GCM
const encrypted = await crypto.subtle.encrypt(
    { name: 'AES-GCM', iv: nonce },
    cryptoKey,
    encoder.encode(plaintext)
)

// 3. Send encrypted data to server
// Server NEVER sees the key or plaintext
```

### Zero-Knowledge Proof System

Our ZKP implementation ensures:
- Server can verify paste ownership without seeing content
- Cryptographic proof of authenticity
- No metadata leakage
- Quantum-resistant commitment schemes

### Post-Quantum Prekey System (Experimental)

Using ML-KEM (Kyber-1024) for:
- Future-proof key exchange
- Recipient-addressed encryption
- One-time use keys
- Forward secrecy

## Performance Metrics

| Metric | Value | Notes |
|--------|-------|-------|
| **Encryption Speed** | < 50ms | For 1MB of data |
| **Page Load Time** | < 500ms | Cached, CDN-enabled |
| **Max File Size** | 160MB | Configurable |
| **Concurrent Users** | 10,000+ | Per server instance |
| **Uptime SLA** | 99.9% | With proper infrastructure |

## Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details.

### Development Workflow

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/amazing-feature`
3. Make your changes and test thoroughly
4. Run tests: `php artisan test`
5. Check code style: `./vendor/bin/phpcs`
6. Commit: `git commit -m 'Add amazing feature'`
7. Push: `git push origin feature/amazing-feature`
8. Create a Pull Request

### Code Standards

- **PHP**: PSR-12 coding standard
- **JavaScript**: ESLint with Airbnb config
- **CSS**: Tailwind CSS best practices
- **Git**: Conventional Commits specification

## Bug Reports & Security

### Reporting Bugs

Please use [GitHub Issues](https://github.com/dailyforever/dailyforever/issues) for bug reports. Include:
- Steps to reproduce
- Expected vs actual behavior
- Browser/OS information
- Screenshots if applicable

### Security Vulnerabilities

**DO NOT** report security issues publicly. Email us at: **general@dailyforever.com**

We follow responsible disclosure:
1. Acknowledgment within 24 hours
2. Investigation within 72 hours
3. Fix deployed ASAP
4. Credit given (if desired)

## License

DailyForever is open source software licensed under the [GNU Affero General Public License v3.0](https://www.gnu.org/licenses/agpl-3.0.html).

This license requires that if you run a modified version of DailyForever as a network service, you must provide users with access to the source code of your modified version.

See [LICENSE](LICENSE) for the full license text.

## Acknowledgments

- [Laravel Framework](https://laravel.com) - The PHP framework for web artisans
- [Web Crypto API](https://developer.mozilla.org/en-US/docs/Web/API/Web_Crypto_API) - Browser cryptography
- [Tailwind CSS](https://tailwindcss.com) - Utility-first CSS framework
- [Alpine.js](https://alpinejs.dev) - Lightweight JavaScript framework
- [CIRCL](https://github.com/cloudflare/circl) - Post-quantum cryptography libraries
- The privacy and security community for inspiration and feedback

## Contact & Support

- **Website**: [https://dailyforever.com](https://dailyforever.com)
- **Email**: dailyforever@proton.me
- **GitHub**: [https://github.com/dailyforever](https://github.com/dailyforever)
- **Documentation**: [https://dailyforever.com/how-it-works](https://dailyforever.com/how-it-works)

---

<div align="center">

**Built for Privacy**

*Your data belongs to you, and only you.*

</div>
