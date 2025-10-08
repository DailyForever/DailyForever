# Robots.txt for DailyForever
# Optimized for maximum search engine crawling

# Allow all crawlers to access public content
User-agent: *
Allow: /

# Block private/admin areas
Disallow: /admin/
Disallow: /api/
Disallow: /storage/
Disallow: /my/
Disallow: /user/
Disallow: /settings/
Disallow: /dashboard/
Disallow: /prekeys/
Disallow: /login
Disallow: /register
Disallow: /recover/

# Block API endpoints that don't need indexing
Disallow: /api/paste/*/raw
Disallow: /api/files/*/download
Disallow: /api/users/*/prekey
Disallow: /api/prekeys/
Disallow: /api/files/upload
Disallow: /api/files/chunk/

# Block authentication and user-specific pages
Disallow: /auth/
Disallow: /logout

# Allow important public pages
Allow: /blog
Allow: /blog/*
Allow: /paste/*
Allow: /files/*
Allow: /terms
Allow: /privacy
Allow: /dmca
Allow: /acceptable-use
Allow: /no-logs
Allow: /philosophy
Allow: /faq
Allow: /cookies
Allow: /how-it-works
Allow: /support

# Allow sitemaps and SEO files
Allow: /sitemap*.xml
Allow: /robots.txt
Allow: /ads.txt
Allow: /google*.html
Allow: /crawl

# Specific crawler optimizations
User-agent: Googlebot
Allow: /
Crawl-delay: 0

User-agent: Bingbot
Allow: /
Crawl-delay: 1

User-agent: Slurp
Allow: /
Crawl-delay: 1

User-agent: DuckDuckBot
Allow: /
Crawl-delay: 1

User-agent: Baiduspider
Allow: /
Crawl-delay: 2

User-agent: YandexBot
Allow: /
Crawl-delay: 1

# Block bad bots
User-agent: AhrefsBot
Disallow: /

User-agent: MJ12bot
Disallow: /

User-agent: DotBot
Disallow: /

# Sitemaps - All available sitemaps
Sitemap: {{ url('/sitemap.xml') }}
Sitemap: {{ url('/sitemap-blog.xml') }}
Sitemap: {{ url('/sitemap-pastes.xml') }}
Sitemap: {{ url('/sitemap-files.xml') }}


