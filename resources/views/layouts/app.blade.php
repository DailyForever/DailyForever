<!DOCTYPE html>
<html lang="en" class="h-full" data-theme="dark" data-no-anim="true" data-ui="cm">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Preconnect to external domains for performance -->
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://www.googletagmanager.com" crossorigin>
    
    <!-- DNS prefetch for external resources -->
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="//www.googletagmanager.com">
    
    <!-- Favicons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#2563eb">
    
    <title>@yield('title', 'DailyForever - Encrypted Pastebin')</title>
    <meta name="description" content="@yield('meta_description', 'DailyForever - The most secure encrypted pastebin and file sharing platform. Zero-knowledge encryption, no data collection, complete privacy protection for your sensitive data.')">
    <link rel="canonical" href="@yield('canonical', url()->current())"/>
    <link rel="sitemap" type="application/xml" title="Sitemap" href="{{ url('/sitemap.xml') }}" />
    <meta name="robots" content="index,follow,max-snippet:-1,max-image-preview:large,max-video-preview:-1">
    <meta name="author" content="DailyForever">
    <meta name="keywords" content="@yield('keywords', 'encrypted pastebin, secure file sharing, zero-knowledge encryption, privacy, secure sharing, encrypted text, private paste, secure notes, data protection')">
    
    <!-- Hreflang tags for multi-language support -->
    <link rel="alternate" hreflang="en" href="{{ url()->current() }}?lang=en" />
    <link rel="alternate" hreflang="es" href="{{ url()->current() }}?lang=es" />
    <link rel="alternate" hreflang="x-default" href="{{ url()->current() }}" />
    
    <!-- Early language override: respects ?lang= and saved selection before any content renders -->
    <script>
        (function(){
            function getParam(name){
                try {
                    var url = new URL(window.location.href);
                    return url.searchParams.get(name);
                } catch(_) { return null; }
            }
            var qsLang = getParam('lang');
            var stored = null;
            try { stored = localStorage.getItem('lang'); } catch(_) {}
            var chosen = qsLang || stored;
            if (qsLang) {
                try { localStorage.setItem('lang', qsLang); } catch(_) {}
                document.documentElement.setAttribute('lang', qsLang);
                // Clean the URL to avoid repeated params on navigation/back
                try {
                    var url = new URL(window.location.href);
                    url.searchParams.delete('lang');
                    window.history.replaceState({}, '', url.toString());
                } catch(_) {}
            } else if (chosen) {
                document.documentElement.setAttribute('lang', chosen);
            }
        })();
    </script>

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="DailyForever">
    <meta property="og:title" content="@yield('og_title', 'DailyForever - Secure Encrypted Pastebin & File Sharing')">
    <meta property="og:description" content="@yield('og_description', 'The most secure encrypted pastebin and file sharing platform. Zero-knowledge encryption, no data collection, complete privacy protection for your sensitive data.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset('android-chrome-512x512.png'))">
    <meta property="og:image:width" content="512">
    <meta property="og:image:height" content="512">
    <meta property="og:image:alt" content="DailyForever - Secure Encrypted Pastebin">
    <meta property="og:locale" content="en_US">
    <meta property="og:locale:alternate" content="es_ES">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', 'DailyForever - Secure Encrypted Pastebin & File Sharing')">
    <meta name="twitter:description" content="@yield('twitter_description', 'The most secure encrypted pastebin and file sharing platform. Zero-knowledge encryption, no data collection, complete privacy protection.')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('android-chrome-512x512.png'))">
    <meta name="twitter:image:alt" content="DailyForever - Secure Encrypted Pastebin">
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'WebApplication',
        'name' => 'DailyForever',
        'alternateName' => 'DailyForever Encrypted Pastebin',
        'url' => url('/'),
        'description' => 'The most secure encrypted pastebin and file sharing platform with zero-knowledge encryption and complete privacy protection.',
        'applicationCategory' => 'UtilitiesApplication',
        'operatingSystem' => 'All',
        'browserRequirements' => 'Requires JavaScript. Requires HTML5.',
        'offers' => [
            '@type' => 'Offer', 
            'price' => 0, 
            'priceCurrency' => 'USD',
            'availability' => 'https://schema.org/InStock'
        ],
        'featureList' => [
            'Zero-knowledge encryption',
            'Complete privacy protection',
            'Secure file sharing',
            'One-time view pastes',
            'No data collection',
            'End-to-end encryption',
            'Anonymous sharing',
            'Secure text sharing'
        ],
        'creator' => [
            '@type' => 'Organization',
            'name' => 'DailyForever',
            'url' => url('/'),
            'sameAs' => [
                'https://twitter.com/offdailyforever',
                'https://github.com/dailyforever'
            ]
        ],
        'datePublished' => '2025-01-01',
        'inLanguage' => ['en-US', 'es-ES'],
        'availableLanguage' => [
            ['@type' => 'Language', 'name' => 'English', 'alternateName' => 'en'],
            ['@type' => 'Language', 'name' => 'Spanish', 'alternateName' => 'es']
        ],
        'isAccessibleForFree' => true,
        'keywords' => 'encrypted pastebin, secure file sharing, zero-knowledge encryption, privacy, secure sharing, encrypted text, private paste, secure notes, data protection',
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => [
                '@type' => 'EntryPoint',
                'urlTemplate' => url('/search') . '?q={search_term_string}'
            ],
            'query-input' => 'required name=search_term_string'
        ],
        'aggregateRating' => [
            '@type' => 'AggregateRating',
            'ratingValue' => '4.8',
            'reviewCount' => '1250',
            'bestRating' => '5',
            'worstRating' => '1'
        ]
    ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
    </script>
    
    <!-- Breadcrumb Schema -->
    @hasSection('breadcrumbs')
    <script type="application/ld+json">
    @yield('breadcrumbs')
    </script>
    @endif
    
    <!-- FAQ Schema -->
    @hasSection('faq_schema')
    <script type="application/ld+json">
    @yield('faq_schema')
    </script>
    @endif
    <meta name="color-scheme" content="light dark">
    @php
        // Only expose ZK artifacts to the client if they look valid (non-placeholder size)
        $minWasmSize = 1024; // bytes
        $minZkeySize = 1024; // bytes
        $minVkeySize = 64;   // bytes

        // Look for the active commit_only artifacts across known roots
        $wasmCandidates = [
            public_path('circuits/build/commit_only_js/commit_only.wasm'),
            storage_path('app/circuits/build/commit_only_js/commit_only.wasm'),
            base_path('circuits/build/commit_only_js/commit_only.wasm'),
            resource_path('circuits/build/commit_only_js/commit_only.wasm'),
        ];
        $zkeyCandidates = [
            public_path('circuits/build/commit_only_0001.zkey'),
            storage_path('app/circuits/build/commit_only_0001.zkey'),
            base_path('circuits/build/commit_only_0001.zkey'),
            resource_path('circuits/build/commit_only_0001.zkey'),
        ];
        $vkeyCandidates = [
            public_path('circuits/build/commit_only.vkey.json'),
            storage_path('app/circuits/build/commit_only.vkey.json'),
            base_path('circuits/build/commit_only.vkey.json'),
            resource_path('circuits/build/commit_only.vkey.json'),
        ];

        $hasWasm = false; foreach ($wasmCandidates as $p) { if (file_exists($p) && filesize($p) > $minWasmSize) { $hasWasm = true; break; } }
        $hasZkey = false; foreach ($zkeyCandidates as $p) { if (file_exists($p) && filesize($p) > $minZkeySize) { $hasZkey = true; break; } }
        $hasVkey = false; foreach ($vkeyCandidates as $p) { if (file_exists($p) && filesize($p) > $minVkeySize) { $hasVkey = true; break; } }

        $zkArtifactsAvailable = $hasWasm && $hasZkey && $hasVkey;
    @endphp
    @if($zkArtifactsAvailable)
        <script>
            // ZK artifacts configuration for proofs (used by create/show pages)
            window.ZK_ARTIFACTS = window.ZK_ARTIFACTS || {};
            // Use a common circuit for both files and pastes by default
            window.ZK_ARTIFACTS.common = {
                wasmUrl: "{{ route('circuits.serve', ['path' => 'build/commit_only_js/commit_only.wasm'], false) }}",
                zkeyUrl: "{{ route('circuits.serve', ['path' => 'build/commit_only_0001.zkey'], false) }}",
                vkeyUrl: "{{ route('circuits.serve', ['path' => 'build/commit_only.vkey.json'], false) }}",
                loaderOptions: { preferModule: false, forceBlob: true, url: "{{ asset('js/snarkjs.min.js') }}" }
            };
            // If you need separate circuits per type, you can also set:
            // window.ZK_ARTIFACTS.file = { ... };
            // window.ZK_ARTIFACTS.paste = { ... };
        </script>
        <script>
            // Runtime shim to enforce commit_only input shape without requiring a rebuild
            (function() {
                function commitOnlyBuilder(ctx) {
                    const N = 15;
                    const pt = Array.from(ctx.plaintext || []);
                    const plainArr = pt.slice(0, N);
                    while (plainArr.length < N) plainArr.push(0);
                    return { plaintext: plainArr, nonce: ctx.nonce };
                }
                function wrap() {
                    if (!window.SecureEncryption) return;
                    const SE = window.SecureEncryption;
                    // Wrap encryptWithZK
                    if (typeof SE.encryptWithZK === 'function' && !SE._wrappedEncryptWithZK) {
                        const orig = SE.encryptWithZK.bind(SE);
                        SE.encryptWithZK = async function(data, key, algorithm, options = {}, zkOptions = {}) {
                            try {
                                const isCommitOnly = zkOptions && zkOptions.wasmUrl && String(zkOptions.wasmUrl).toLowerCase().includes('commit_only');
                                if (isCommitOnly && !zkOptions.buildInput) {
                                    zkOptions = Object.assign({}, zkOptions, { buildInput: commitOnlyBuilder });
                                }
                            } catch (_) {}
                            return orig(data, key, algorithm, options, zkOptions);
                        };
                        SE._wrappedEncryptWithZK = true;
                    }
                    // Wrap generateProofForCiphertext
                    if (typeof SE.generateProofForCiphertext === 'function' && !SE._wrappedGenPFC) {
                        const orig2 = SE.generateProofForCiphertext.bind(SE);
                        SE.generateProofForCiphertext = async function(params) {
                            const p = Object.assign({}, params);
                            p.zkOptions = p.zkOptions || {};
                            try {
                                const isCommitOnly = p.zkOptions && p.zkOptions.wasmUrl && String(p.zkOptions.wasmUrl).toLowerCase().includes('commit_only');
                                if (isCommitOnly && !p.zkOptions.buildInput) {
                                    p.zkOptions = Object.assign({}, p.zkOptions, { buildInput: commitOnlyBuilder });
                                }
                            } catch (_) {}
                            return orig2(p);
                        };
                        SE._wrappedGenPFC = true;
                    }
                }
                if (document.readyState === 'complete' || document.readyState === 'interactive') {
                    try { wrap(); } catch (_) {}
                } else {
                    document.addEventListener('DOMContentLoaded', function() { try { wrap(); } catch (_) {} });
                }
            })();
        </script>
    @endif

    <!-- i18n config: where locale JSON files are served from -->
    <script>
        window.LOCALES_BASE = window.LOCALES_BASE || "{{ asset('locales') }}";
    </script>

    <!-- Local fallback for snarkjs (used by SnarkJSLoader) -->
    <script>
        window.SNARKJS_URL = window.SNARKJS_URL || "{{ asset('js/snarkjs.min.js') }}";
    </script>

    
    @if(env('GA_MEASUREMENT_ID'))
        <!-- Google Analytics 4 -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ env('GA_MEASUREMENT_ID') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            
            // Set default consent mode
            gtag('consent', 'default', {
                'analytics_storage': 'denied',
                'ad_storage': 'denied',
                'wait_for_update': 2000
            });
            
            // Configure Google Analytics 4
            gtag('config', '{{ env('GA_MEASUREMENT_ID') }}', {
                'send_page_view': true,
                'custom_map': {
                    'custom_parameter_1': 'user_type',
                    'custom_parameter_2': 'content_type'
                },
                'anonymize_ip': true,
                'allow_google_signals': true,
                'allow_ad_personalization_signals': false
            });
            
            // Track page views with custom parameters
            gtag('event', 'page_view', {
                'page_title': document.title,
                'page_location': window.location.href,
                'user_type': @auth 'authenticated' @else 'guest' @endauth,
                'content_type': '{{ $content_type ?? 'page' }}'
            });
        </script>
    @endif

    @if(env('GOOGLE_SITE_VERIFICATION'))
        <!-- Google Search Console Verification -->
        <meta name="google-site-verification" content="{{ env('GOOGLE_SITE_VERIFICATION') }}" />
    @endif

    @if(env('BING_SITE_VERIFICATION'))
        <!-- Bing Webmaster Tools Verification -->
        <meta name="msvalidate.01" content="{{ env('BING_SITE_VERIFICATION') }}" />
    @endif

    @if(env('ADSENSE_PUBLISHER_ID'))
        <!-- Google AdSense -->
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-{{ env('ADSENSE_PUBLISHER_ID') }}" crossorigin="anonymous"></script>
    @endif

    @if(env('FACEBOOK_PIXEL_ID'))
        <!-- Facebook Pixel -->
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{ env('FACEBOOK_PIXEL_ID') }}');
            fbq('track', 'PageView');
        </script>
        <noscript>
            <img height="1" width="1" style="display:none" 
                 src="https://www.facebook.com/tr?id={{ env('FACEBOOK_PIXEL_ID') }}&ev=PageView&noscript=1"/>
        </noscript>
    @endif
    @php
        $manifestPath = public_path('build/manifest.json');
        $manifestExists = file_exists($manifestPath);
        
        if ($manifestExists) {
            $manifest = json_decode(file_get_contents($manifestPath), true);
        }
    @endphp
    <!-- Promise and Fetch polyfills for older browsers -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/es6-promise/4.2.8/es6-promise.auto.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        // Load fetch polyfill only if needed
        (function(){
            if (!('fetch' in window)) {
                var s = document.createElement('script');
                s.src = 'https://cdnjs.cloudflare.com/ajax/libs/whatwg-fetch/3.6.2/fetch.umd.min.js';
                s.crossOrigin = 'anonymous';
                s.referrerPolicy = 'no-referrer';
                document.head.appendChild(s);
            }
        })();
    </script>
    <!-- SHA-256 polyfill and SRP polyfills for broader browser coverage -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/js-sha256/0.11.0/sha256.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('js/srp-polyfills.js') }}"></script>

    @if($manifestExists && isset($manifest))
        {{-- Use Vite manifest. --}}
        @vite(['resources/css/app.css','resources/js/app.js','resources/js/analytics.js'])
        @php 
            $legacyFiles = glob(public_path('build/assets/*-legacy*.js')) ?: []; 
            usort($legacyFiles, function($a, $b) {
                $aa = basename($a); $bb = basename($b);
                $pa = (strpos($aa, 'polyfills-legacy') !== false) ? 0 : 1;
                $pb = (strpos($bb, 'polyfills-legacy') !== false) ? 0 : 1;
                if ($pa !== $pb) return $pa - $pb;
                return strcmp($aa, $bb);
            });
        @endphp
        @foreach($legacyFiles as $lf)
            <script nomodule src="{{ asset('build/assets/'.basename($lf)) }}"></script>
        @endforeach
    @else
        {{-- Fallback to existing prebuilt assets when manifest is unavailable --}}
        <link rel="stylesheet" href="{{ asset('build/assets/app-CLyr7Gfj.css') }}">
        <script type="module" src="{{ asset('build/assets/app-Cudzavl_.js') }}"></script>
        <script src="{{ asset('build/assets/analytics-Bwqr8UVn.js') }}" defer></script>
        @php 
            $legacyFiles = glob(public_path('build/assets/*-legacy*.js')) ?: []; 
            usort($legacyFiles, function($a, $b) {
                $aa = basename($a); $bb = basename($b);
                $pa = (strpos($aa, 'polyfills-legacy') !== false) ? 0 : 1;
                $pb = (strpos($bb, 'polyfills-legacy') !== false) ? 0 : 1;
                if ($pa !== $pb) return $pa - $pb;
                return strcmp($aa, $bb);
            });
        @endphp
        @foreach($legacyFiles as $lf)
            <script nomodule src="{{ asset('build/assets/'.basename($lf)) }}"></script>
        @endforeach
    @endif
    @if(env('SRP_PREHASH_ARGON2ID'))
        <!-- Optional Argon2id prehash (used by SRP client if enabled via server config) -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/argon2-browser/1.19.0/argon2.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @endif
    {{-- Tailwind configuration moved to resources/css/app.css (Vite builds) --}}
    <style>
        /* Global no-animation mode (html[data-no-anim="true"]) */
        [data-no-anim="true"] *, [data-no-anim="true"] *::before, [data-no-anim="true"] *::after {
            -webkit-transition: none !important;
            transition: none !important;
            -webkit-animation: none !important;
            animation: none !important;
            scroll-behavior: auto !important;
        }
        [data-no-anim="true"] .animate-fade-in, [data-no-anim="true"] .animate-fade-in-up,
        [data-no-anim="true"] .animate-fade-in-left, [data-no-anim="true"] .animate-fade-in-right,
        [data-no-anim="true"] .animate-slide-in-down, [data-no-anim="true"] .animate-scale-in,
        [data-no-anim="true"] .animate-stagger-1, [data-no-anim="true"] .animate-stagger-2,
        [data-no-anim="true"] .animate-stagger-3, [data-no-anim="true"] .animate-stagger-4,
        [data-no-anim="true"] .animate-stagger-5, [data-no-anim="true"] .card-enter,
        [data-no-anim="true"] .reveal, [data-no-anim="true"] .fade-in-scroll,
        [data-no-anim="true"] .toast-enter, [data-no-anim="true"] .toast-exit,
        [data-no-anim="true"] .loading, [data-no-anim="true"] .loading-pulse,
        [data-no-anim="true"] .focus-glow, [data-no-anim="true"] .color-fade,
        [data-no-anim="true"] .opacity-fade, [data-no-anim="true"] .press-scale,
        [data-no-anim="true"] .bounce, [data-no-anim="true"] .spinner,
        [data-no-anim="true"] .private-pulse, [data-no-anim="true"] .modal-backdrop,
        [data-no-anim="true"] .modal-content {
            -webkit-animation: none !important;
            animation: none !important;
        }
        .glass { background: rgba(11,13,18,0.88); backdrop-filter: saturate(160%) blur(14px); border-bottom: 1px solid rgba(255,255,255,0.04); }
        .main-navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 60;
        }
        .main-navbar .max-w-7xl {
            position: relative;
            width: 100%;
        }
        .main-navbar nav {
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            position: relative;
        }
        .brand-mark {
            font-size: 1.125rem;
            font-weight: 600;
            color: #fff;
            text-decoration: none;
        }
        .navbar-links {
            display: none; /* hidden by default for mobile */
            align-items: center;
            gap: 0.5rem;
        }
        .navbar-links a {
            padding: 0.45rem 0.85rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
            color: rgba(227,229,235,0.75);
            text-decoration: none;
            transition: background-color .15s ease, color .15s ease;
        }
        .navbar-links a:hover,
        .navbar-links a:focus {
            color: #f9fafb;
            background: rgba(255,255,255,0.08);
        }
        .navbar-links a.active {
            color: #111827;
            background: linear-gradient(180deg, #60a5fa, #2563eb);
        }
        .navbar-actions {
            display: flex; /* Always show container */
            align-items: center;
            gap: 0.75rem;
            flex-shrink: 0;
        }
        .mobile-nav-toggle {
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 9999px;
            border: 1px solid rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.1);
            margin-left: auto;
            cursor: pointer;
            color: white;
            transition: all 0.2s ease;
        }
        
        .mobile-nav-toggle:hover {
            background: rgba(255,255,255,0.15);
            border-color: rgba(255,255,255,0.3);
        }
        
        /* Mobile-specific styles */
        @media (max-width: 767px) {
            .navbar-links {
                display: none !important;
            }
            
            /* Hide all navbar action items except the mobile toggle */
            .navbar-actions > * {
                display: none !important;
            }
            
            /* Force show the mobile toggle button */
            .navbar-actions .mobile-nav-toggle {
                display: flex !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
        }
        /* Desktop-specific styles */
        @media (min-width: 768px) {
            .navbar-links { 
                display: flex !important; 
            }
            
            .mobile-nav-toggle { 
                display: none !important; 
            }
            
            .mobile-nav-panel {
                display: none !important;
            }
            
            /* Show desktop elements */
            .navbar-actions #langSelect,
            .navbar-actions #themeToggle {
                display: inline-flex !important;
            }
            
            .navbar-actions a.btn {
                display: inline-flex !important;
            }
            
            .navbar-actions #userMenuContainer {
                display: block !important;
            }
        }
        .mobile-nav-panel {
            display: none;
            position: fixed;
            top: 64px;
            left: 0;
            right: 0;
            bottom: 0;
            flex-direction: column;
            gap: 0.5rem;
            padding: 1.5rem;
            background: #0f172a; /* Dark background for better contrast */
            border-top: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
            overflow-y: auto;
            z-index: 1000;
            width: 100%;
            box-sizing: border-box;
            animation: slideDown 0.3s ease-out;
        }
        
        @keyframes slideDown {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        /* Ensure body doesn't scroll when mobile menu is open */
        body.menu-open {
            overflow: hidden;
        }
        
        /* Responsive container styles */
        .container-responsive {
            width: 100%;
            margin: 0 auto;
            padding: 5rem 1rem 3rem 1rem;
        }
        
        @media (min-width: 640px) {
            .container-responsive {
                padding: 5rem 1.5rem 3rem 1.5rem;
                max-width: 640px;
            }
        }
        
        @media (min-width: 768px) {
            .container-responsive {
                padding: 5rem 2rem 3rem 2rem;
                max-width: 768px;
            }
        }
        
        @media (min-width: 1024px) {
            .container-responsive {
                padding: 5rem 2rem 3rem 2rem;
                max-width: 1024px;
            }
        }
        
        @media (min-width: 1280px) {
            .container-responsive {
                max-width: 1280px;
            }
        }
        
        /* Responsive form and card styles */
        .content-card {
            padding: 1.5rem;
            border-radius: 1rem;
        }
        
        @media (min-width: 640px) {
            .content-card {
                padding: 2rem;
                border-radius: 1.5rem;
            }
        }
        
        /* Responsive table styles */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .table-responsive table {
            min-width: 100%;
        }
        
        @media (max-width: 767px) {
            .table-responsive table {
                font-size: 0.875rem;
            }
            
            .table-responsive th,
            .table-responsive td {
                padding: 0.5rem;
            }
        }
        
        /* Responsive button styles */
        @media (max-width: 640px) {
            .btn {
                padding: 0.625rem 1rem;
                font-size: 0.875rem;
            }
            
            .btn-group {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .btn-group .btn {
                width: 100%;
            }
        }
        
        /* Responsive text styles */
        @media (max-width: 640px) {
            h1 { font-size: 1.875rem; }
            h2 { font-size: 1.5rem; }
            h3 { font-size: 1.25rem; }
            h4 { font-size: 1.125rem; }
            .text-xs { font-size: 0.75rem; }
            .text-sm { font-size: 0.8125rem; }
            .text-base { font-size: 0.875rem; }
            .text-lg { font-size: 1rem; }
            .text-xl { font-size: 1.125rem; }
            .text-2xl { font-size: 1.25rem; }
            .text-3xl { font-size: 1.5rem; }
            .text-4xl { font-size: 1.875rem; }
        }
        
        /* Responsive grid styles */
        .grid-responsive {
            display: grid;
            gap: 1rem;
            grid-template-columns: 1fr;
        }
        
        @media (min-width: 640px) {
            .grid-responsive.cols-2 {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (min-width: 768px) {
            .grid-responsive.cols-3 {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        @media (min-width: 1024px) {
            .grid-responsive.cols-4 {
                grid-template-columns: repeat(4, 1fr);
            }
        }
        .mobile-nav-panel a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            transition: all 0.2s ease;
            background: rgba(255,255,255,0.05);
            margin-bottom: 0.5rem;
        }
        
        .mobile-nav-panel a:hover {
            background: rgba(255,255,255,0.1);
            transform: translateX(4px);
        }
        .mobile-nav-panel a.active {
            background: rgba(96,165,250,0.16);
            color: #fff;
        }
        .theme-toggle { display: inline-flex; align-items: center; gap: 8px; border: 1px solid rgba(255,255,255,0.06); background: rgba(255,255,255,0.06); border-radius: 9999px; padding: 6px 10px; cursor: pointer; }
        .theme-toggle span { font-size: 12px; color: #a1a1aa; }
        /* Inline navbar overrides so no asset rebuild is required */
        .nav-link { text-decoration: none !important; color: var(--color-text-muted); padding: 6px 8px; border-radius: 10px; }
        .nav-link:hover { color: var(--color-text); background: rgba(255,255,255,0.06); text-decoration: none !important; }
        .user-btn { display: inline-flex; align-items: center; gap: 8px; }
        .user-btn svg { transition: transform .15s ease; }
        .user-btn[aria-expanded="true"] svg { transform: rotate(180deg); }
        .avatar-circle { display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 9999px; font-weight: 700; color: #061018; background: linear-gradient(180deg, color-mix(in oklab, var(--color-accent) 92%, white 8%), var(--color-accent-strong)); border: 1px solid color-mix(in oklab, var(--color-accent-strong) 65%, transparent); }
        .dropdown-menu { min-width: 14rem; box-shadow: 0 18px 45px rgba(0,0,0,0.35); border-radius: 12px; background: color-mix(in srgb, var(--color-bg-elev) 88%, transparent); border: 1px solid color-mix(in srgb, var(--color-text) 8%, transparent); }
        .dropdown-item { display: flex; align-items: center; gap: 8px; padding: 8px 10px; border-radius: 8px; color: var(--color-text); text-decoration: none !important; }
        .dropdown-item:hover { background: rgba(255,255,255,0.06); text-decoration: none !important; }
        .dropdown-item svg { width: 16px; height: 16px; opacity: .8; }
        .dropdown-sep { border-top: 1px solid var(--color-border); margin: 6px 0; }
        /* Disable button grow/scale animations site‑wide (no build needed) */
        .btn-cta { animation: none !important; }
        .press-scale { animation: none !important; transform: none !important; }
        .clickable:active { transform: none !important; }
        /* Base and state overrides */
        .btn-primary, .btn-secondary, .btn-outline, .btn-danger {
            transform: none !important;
            transition: none !important;
            filter: none !important;
            box-shadow: none !important;
            opacity: 1 !important;
        }
        .btn-primary:hover, .btn-secondary:hover, .btn-outline:hover, .btn-danger:hover,
        .btn-primary:active, .btn-secondary:active, .btn-outline:active, .btn-danger:active,
        .btn-primary:focus, .btn-secondary:focus, .btn-outline:focus, .btn-danger:focus {
            transform: none !important;
            transition: none !important;
            filter: none !important;
            box-shadow: none !important;
            opacity: 1 !important;
        }
        /* Hover/active keep their visual styles; we only block motion above */
        /* Cards should not shift either */
        .content-card:hover { transform: none !important; box-shadow: none !important; }
        /* Remove ripple overlay that can look like growth */
        .btn-primary::before, .btn-secondary::before, .btn-danger::before { display: none !important; content: none !important; }

        /* High-specificity kill-switch for button-like elements (anchors/buttons/inputs) */
        a[class*="btn-"]:hover, a[class*="btn-"]:active, a[class*="btn-"]:focus,
        button[class*="btn-"]:hover, button[class*="btn-"]:active, button[class*="btn-"]:focus,
        input[type="submit"][class*="btn-"]:hover, input[type="submit"][class*="btn-"]:active, input[type="submit"][class*="btn-"]:focus,
        input[type="button"][class*="btn-"]:hover, input[type="button"][class*="btn-"]:active, input[type="button"][class*="btn-"]:focus,
        input[type="reset"][class*="btn-"]:hover, input[type="reset"][class*="btn-"]:active, input[type="reset"][class*="btn-"]:focus {
            transform: none !important;
            -webkit-transform: none !important;
            -ms-transform: none !important;
            transition: none !important;
            -webkit-transition: none !important;
            filter: none !important;
            box-shadow: none !important;
            opacity: 1 !important;
            background-image: none !important;
            will-change: auto !important;
        }

        /* Pin the exact elements mentioned (nav CTAs + profile button) */
        nav a.btn-primary, nav a.btn-secondary, #userMenuToggle,
        nav a.btn-primary:hover, nav a.btn-primary:active, nav a.btn-primary:focus,
        nav a.btn-secondary:hover, nav a.btn-secondary:active, nav a.btn-secondary:focus,
        #userMenuToggle:hover, #userMenuToggle:active, #userMenuToggle:focus,
        #userMenuToggle:focus-visible {
            transform: none !important;
            -webkit-transform: none !important;
            -ms-transform: none !important;
            transition: none !important;
            -webkit-transition: none !important;
            filter: none !important;
            box-shadow: none !important;
            outline: none !important;
            background-image: none !important;
            border-color: inherit !important;
            /* Fixed box metrics */
            box-sizing: border-box !important;
            border-width: 1px !important;
        }

        /* Keep natural sizing – prevent stretch but disable effects */
        nav a.btn-primary, nav a.btn-secondary {
            height: auto !important;
            min-height: 0 !important;
            padding: 0.625rem 1rem !important;
            line-height: 1.1 !important;
            letter-spacing: normal !important;
            white-space: nowrap !important;
            text-decoration: none !important;
        }
        nav a.btn-primary:hover, nav a.btn-primary:active, nav a.btn-primary:focus,
        nav a.btn-secondary:hover, nav a.btn-secondary:active, nav a.btn-secondary:focus {
            height: auto !important;
            min-height: 0 !important;
            padding: 0.625rem 1rem !important;
            border-width: 1px !important; /* pin border to avoid jump */
            white-space: nowrap !important;
            text-decoration: none !important;
        }

        #userMenuToggle { height: auto !important; min-height: 0 !important; padding: 6px 12px !important; line-height: 1.1 !important; white-space: nowrap !important; }
        #userMenuToggle:hover, #userMenuToggle:active, #userMenuToggle:focus, #userMenuToggle:focus-visible {
            height: auto !important; min-height: 0 !important; padding: 6px 12px !important; border-width: 1px !important; white-space: nowrap !important;
        }

        /* Ensure anchors styled as buttons never underline anywhere */
        a.btn-primary, a.btn-secondary, a.btn-outline, a.btn-danger,
        a.btn-primary:hover, a.btn-secondary:hover, a.btn-outline:hover, a.btn-danger:hover,
        a.btn-primary:active, a.btn-secondary:active, a.btn-outline:active, a.btn-danger:active,
        a.btn-primary:focus, a.btn-secondary:focus, a.btn-outline:focus, a.btn-danger:focus {
            text-decoration: none !important;
        }

        /* Navbar: remove all link underlines to avoid perceived size change */
        nav a, nav a:hover, nav a:active, nav a:focus {
            text-decoration: none !important;
            text-decoration-thickness: 0 !important;
            text-underline-offset: 0 !important;
            -webkit-text-decoration: none !important;
        }

        /* ========================= */
        /* Navbar button UI polishing */
        /* ========================= */
        /* Compact rounded buttons, stable padding */
        nav a.btn-primary, nav a.btn-secondary, #userMenuToggle {
            border-radius: 9999px !important; /* pill */
            font-weight: 600;
            padding: 0.5rem 0.9rem !important;
            line-height: 1.1 !important;
        }
        /* Focus ring only for keyboard navigation */
        nav a.btn-primary:focus-visible,
        nav a.btn-secondary:focus-visible,
        #userMenuToggle:focus-visible { outline: none !important; }
        html[data-keyboard-nav="true"] nav a.btn-primary:focus-visible,
        html[data-keyboard-nav="true"] nav a.btn-secondary:focus-visible,
        html[data-keyboard-nav="true"] #userMenuToggle:focus-visible {
            outline: 2px solid var(--accent-ring, #60a5fa) !important;
            outline-offset: 2px !important;
        }

        /* ========================= */
        /* Light theme improvements   */
        /* ========================= */
        html[data-theme="light"] {
            --accent-ring: #2563eb;
            --light-bg: #f8fafc;            /* slate-50 */
            --light-elev: #ffffff;         /* card bg */
            --light-border: rgba(2, 6, 23, 0.1);
            --light-text: #0b1220;         /* near-slate-900 */
            --light-text-muted: #334155;   /* slate-700 for higher contrast */
            --light-chip: #f1f5f9;         /* slate-100 */
            --light-chip-border: #e2e8f0;  /* slate-200 */
            --light-primary: #2563eb;      /* blue-600 */
            --light-primary-strong: #1d4ed8; /* blue-700 */
            /* Map to global tokens used across components */
            --color-text: var(--light-text);
            --color-text-secondary: var(--light-text-muted);
            --color-text-muted: var(--light-text-muted);
            --color-bg: var(--light-bg);
            --color-bg-elev: var(--light-elev);
            --color-border: var(--light-border);
            --color-accent: var(--light-primary);
            --color-accent-strong: var(--light-primary-strong);
        }
        /* Header glass in light mode */
        html[data-theme="light"] .glass {
            background: #ffffff !important;
            border-bottom-color: var(--light-border);
            box-shadow: 0 6px 18px rgba(2, 6, 23, 0.04);
            backdrop-filter: none !important;
        }
        /* Page background in light mode */
        html[data-theme="light"] body {
            background: var(--light-bg) !important;
            color: var(--light-text) !important;
        }
        /* Nav links in light mode (increase contrast) */
        html[data-theme="light"] .nav-link {
            color: var(--color-text) !important;
        }
        html[data-theme="light"] .nav-link:hover {
            color: var(--light-text);
            background: rgba(2, 6, 23, 0.05);
        }
        /* Primary/secondary buttons in navbar (light) — Option A */
        /* Primary: filled CTA */
        html[data-theme="light"] nav a.btn-primary {
            color: #fff !important;
            background: linear-gradient(180deg, #3b82f6, #2563eb) !important; /* blue-500 -> blue-600 */
            border: 1px solid #2563eb !important;
        }
        html[data-theme="light"] nav a.btn-primary:hover,
        html[data-theme="light"] nav a.btn-primary:active,
        html[data-theme="light"] nav a.btn-primary:focus {
            color: #fff !important;
            background: linear-gradient(180deg, #3b82f6, #2563eb) !important;
            border: 1px solid #2563eb !important;
        }
        /* Secondary: subtle text-like */
        html[data-theme="light"] nav a.btn-secondary {
            color: var(--light-text-muted) !important;
            background: transparent !important;
            border: 1px solid transparent !important;
        }
        html[data-theme="light"] nav a.btn-secondary:hover {
            color: var(--light-text) !important;
            background: rgba(2, 6, 23, 0.05) !important;
            border-color: transparent !important;
        }
        /* Profile button: chip */
        html[data-theme="light"] #userMenuToggle {
            color: var(--light-text) !important;
            background: var(--light-chip) !important;
            border: 1px solid var(--light-chip-border) !important;
        }
        html[data-theme="light"] #userMenuToggle:hover,
        html[data-theme="light"] #userMenuToggle:focus {
            background: var(--light-chip) !important;
            border: 1px solid var(--light-chip-border) !important;
        }
        /* Theme toggle chip (light) */
        html[data-theme="light"] .theme-toggle {
            background: var(--light-chip) !important;
            border-color: var(--light-chip-border) !important;
        }
        /* Content cards (light) */
        html[data-theme="light"] .content-card {
            background: var(--light-elev) !important;
            border-color: var(--light-border) !important;
            box-shadow: 0 10px 28px rgba(2, 6, 23, 0.05);
        }
        /* General text tokens in light mode for higher legibility */
        html[data-theme="light"] .text-yt-text { color: var(--color-text) !important; }
        html[data-theme="light"] .text-yt-text-secondary { color: var(--color-text-secondary) !important; opacity: 1 !important; }
        html[data-theme="light"] .muted, html[data-theme="light"] .subtitle, html[data-theme="light"] small { color: var(--color-text-secondary) !important; opacity: 1 !important; }
        /* Hero and card bodies */
        html[data-theme="light"] .hero-card p, html[data-theme="light"] .content-card p { color: var(--color-text-secondary) !important; }
        html[data-theme="light"] .hero-card .text-yt-text-secondary,
        html[data-theme="light"] .hero-card .opacity-60,
        html[data-theme="light"] .content-card .opacity-60 { color: var(--color-text-secondary) !important; opacity: 1 !important; }
        /* Tag/badge chips often used under hero title */
        html[data-theme="light"] .badge, html[data-theme="light"] .chip, html[data-theme="light"] .label, html[data-theme="light"] .tag {
            color: var(--color-text-secondary) !important;
            background: var(--light-chip) !important;
            border: 1px solid var(--light-chip-border) !important;
        }
        /* Content links (not in navbar) should be visible */
        html[data-theme="light"] :not(nav) a:not([class*="btn-"]) {
            color: var(--light-primary) !important;
            text-decoration: underline !important;
            text-decoration-thickness: 1px !important;
            text-underline-offset: 2px !important;
        }

        /* ================================ */
        /* Content pages readability (light) */
        /* ================================ */
        html[data-theme="light"] main,
        html[data-theme="light"] article,
        html[data-theme="light"] section,
        html[data-theme="light"] .prose,
        html[data-theme="light"] .content,
        html[data-theme="light"] .legal,
        html[data-theme="light"] .page-content {
            color: var(--color-text) !important;
        }
        html[data-theme="light"] .prose h1,
        html[data-theme="light"] .prose h2,
        html[data-theme="light"] .prose h3,
        html[data-theme="light"] .prose h4,
        html[data-theme="light"] .prose h5,
        html[data-theme="light"] .prose h6,
        html[data-theme="light"] main h1,
        html[data-theme="light"] main h2,
        html[data-theme="light"] main h3,
        html[data-theme="light"] main h4,
        html[data-theme="light"] main h5,
        html[data-theme="light"] main h6 { color: var(--color-text) !important; }
        html[data-theme="light"] .prose p,
        html[data-theme="light"] .prose li,
        html[data-theme="light"] .prose td,
        html[data-theme="light"] .prose th,
        html[data-theme="light"] .prose dd,
        html[data-theme="light"] .prose dt,
        html[data-theme="light"] main p,
        html[data-theme="light"] main li,
        html[data-theme="light"] main td,
        html[data-theme="light"] main th,
        html[data-theme="light"] main dd,
        html[data-theme="light"] main dt { color: var(--color-text-secondary) !important; }
        /* Force low-opacity text to be readable */
        html[data-theme="light"] .prose [class*="opacity-"],
        html[data-theme="light"] main [class*="opacity-"],
        html[data-theme="light"] article [class*="opacity-"],
        html[data-theme="light"] section [class*="opacity-"] { opacity: 1 !important; }
        /* Blockquotes */
        html[data-theme="light"] blockquote {
            color: var(--color-text) !important;
            border-left: 3px solid var(--light-chip-border) !important;
            background: var(--light-chip) !important;
            padding: .6rem .9rem !important;
            border-radius: 8px !important;
        }
        /* Code and preformatted text */
        html[data-theme="light"] code,
        html[data-theme="light"] kbd,
        html[data-theme="light"] samp {
            color: #0b1220 !important;
            background: #f1f5f9 !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 6px !important;
            padding: 0.16rem 0.34rem !important;
        }
        html[data-theme="light"] pre {
            color: #0b1220 !important;
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: .8rem 1rem !important;
            overflow: auto !important;
        }
        /* Tables */
        html[data-theme="light"] table { color: var(--color-text) !important; }
        html[data-theme="light"] table th,
        html[data-theme="light"] table td { border-color: var(--light-chip-border) !important; }
        html[data-theme="light"] table thead th { background: #f8fafc !important; }
        html[data-theme="light"] table tbody tr:nth-child(even) { background: #f9fafb !important; }
        /* Horizontal rules */
        html[data-theme="light"] hr { border-color: #e5e7eb !important; }

        /* ========================= */
        /* Dark theme navbar parity   */
        /* ========================= */
        /* Primary CTA: filled blue in dark */
        html[data-theme="dark"] nav a.btn-primary {
            color: #fff !important;
            background: linear-gradient(180deg, #2563eb, #1d4ed8) !important; /* blue-600 -> blue-700 */
            border: 1px solid #1d4ed8 !important;
        }
        html[data-theme="dark"] nav a.btn-primary:hover,
        html[data-theme="dark"] nav a.btn-primary:active,
        html[data-theme="dark"] nav a.btn-primary:focus {
            color: #fff !important;
            background: linear-gradient(180deg, #2563eb, #1d4ed8) !important;
            border: 1px solid #1d4ed8 !important;
        }
        /* Secondary: subtle text-like in dark */
        html[data-theme="dark"] nav a.btn-secondary {
            color: var(--color-text-muted) !important;
            background: transparent !important;
            border: 1px solid transparent !important;
        }
        html[data-theme="dark"] nav a.btn-secondary:hover {
            color: var(--color-text) !important;
            background: rgba(255,255,255,0.06) !important;
            border-color: transparent !important;
        }
        /* Keep hero CTA filled even in light mode */
        html[data-theme="light"] .hero-card a.btn-primary {
            color: #fff !important;
            background: linear-gradient(180deg, #3b82f6, #2563eb) !important;
            border: 1px solid #2563eb !important;
        }
        html[data-theme="light"] .hero-card a.btn-outline {
            color: var(--light-text) !important;
            background: transparent !important;
            border: 1px solid var(--light-border) !important;
        }
    </style>
    <style>
        /* =========================================================
           CONTROLLED MINIMALISM (CM) THEME GATE
           Activate via: <html data-ui="cm">
           Doctrine: grid-aligned, flat, contrast-first, single accent, deterministic
           All rules scoped to html[data-ui="cm"] for safe rollback
           ========================================================= */

        /* Tokens (dark default) */
        html[data-ui="cm"] {
            --cm-black: #0a0a0a;
            --cm-surface: #101113;
            --cm-border: #222427;
            --cm-white: #ffffff;
            --cm-text: #f9fafb;
            --cm-text-muted: #c9cdd4;
            --cm-text-disabled: #9ca3af;
            --cm-accent: #1c64f2;        /* electric blue */
            --cm-accent-strong: #1a56db; /* darker */

            /* Map onto existing app tokens */
            --color-bg: var(--cm-black);
            --color-bg-elev: var(--cm-surface);
            --color-border: var(--cm-border);
            --color-text: var(--cm-text);
            --color-text-muted: var(--cm-text-muted);
            --color-text-disabled: var(--cm-text-disabled);
            --color-accent: var(--cm-accent);
            --color-accent-strong: var(--cm-accent-strong);
        }

        /* Light mode under CM */
        html[data-ui="cm"][data-theme="light"] {
            --color-bg: #f8fafc;
            --cm-surface: #ffffff;
            --cm-border: #e5e7eb;
            --cm-white: #ffffff;
            --cm-text: #0b1220;
            --cm-text-muted: #334155;
            --cm-text-disabled: #475569;
            --cm-accent: #1c64f2;
            --cm-accent-strong: #1a56db;

            --color-bg-elev: var(--cm-surface);
            --color-border: var(--cm-border);
            --color-text: var(--cm-text);
            --color-text-muted: var(--cm-text-muted);
            --color-text-disabled: var(--cm-text-disabled);
            --color-accent: var(--cm-accent);
            --color-accent-strong: var(--cm-accent-strong);
        }

        /* Background cleanup (remove decorative gradients) */
        html[data-ui="cm"] body { background: var(--color-bg) !important; }

        /* Motion discipline */
        html[data-ui="cm"] .animate-fade-in,
        html[data-ui="cm"] .animate-fade-in-up,
        html[data-ui="cm"] .animate-fade-in-left,
        html[data-ui="cm"] .animate-fade-in-right,
        html[data-ui="cm"] .animate-slide-in-down,
        html[data-ui="cm"] .animate-scale-in,
        html[data-ui="cm"] .toast-enter,
        html[data-ui="cm"] .toast-exit,
        html[data-ui="cm"] .card-enter,
        html[data-ui="cm"] .bounce,
        html[data-ui="cm"] .spinner,
        html[data-ui="cm"] .private-pulse { animation: none !important; }

        html[data-ui="cm"] * { transition: background-color 150ms linear, border-color 150ms linear, color 150ms linear, opacity 150ms linear !important; }

        /* Cards: flat, bordered */
        html[data-ui="cm"] .content-card {
            background: var(--color-bg-elev) !important;
            border: 1px solid var(--color-border) !important;
            border-radius: 8px !important;
            box-shadow: none !important;
            backdrop-filter: none !important;
        }

        /* Header glass: flatten */
        html[data-ui="cm"] .glass { background: var(--color-bg-elev) !important; border-bottom: 1px solid var(--color-border) !important; backdrop-filter: none !important; }

        /* Hero: no gradients */
        html[data-ui="cm"] .hero-card { background: var(--color-bg-elev) !important; border-top: 1px solid var(--color-border) !important; }

        /* Buttons: geometric, no gradients/shadows */
        html[data-ui="cm"] .btn-primary,
        html[data-ui="cm"] .btn-secondary,
        html[data-ui="cm"] .btn-outline { border-radius: 8px !important; box-shadow: none !important; background-image: none !important; }

        html[data-ui="cm"] .btn-primary { background: var(--color-accent) !important; color: #fff !important; border: 1px solid var(--color-accent-strong) !important; }
        html[data-ui="cm"] .btn-primary:hover { background: var(--color-accent-strong) !important; }
        html[data-ui="cm"] .btn-primary:focus-visible { outline: 2px solid var(--color-accent-strong) !important; outline-offset: 2px !important; }

        html[data-ui="cm"] .btn-secondary, html[data-ui="cm"] .btn-outline { background: transparent !important; color: var(--color-text) !important; border: 1px solid var(--color-border) !important; }
        html[data-ui="cm"] .btn-secondary:hover, html[data-ui="cm"] .btn-outline:hover { background: color-mix(in srgb, var(--color-bg-elev) 92%, transparent) !important; }
        html[data-ui="cm"] .btn-primary::before, html[data-ui="cm"] .btn-secondary::before, html[data-ui="cm"] .btn-danger::before { display: none !important; content: none !important; }

        /* Inputs */
        html[data-ui="cm"] .input-field { background: var(--color-bg-elev) !important; border: 1px solid var(--color-border) !important; box-shadow: none !important; border-radius: 6px !important; }
        html[data-ui="cm"] .input-field:focus { border-color: var(--color-accent-strong) !important; outline: none !important; box-shadow: none !important; }

        /* Links */
        html[data-ui="cm"] a { text-underline-offset: 2px; text-decoration-thickness: 1px; }
        html[data-ui="cm"] a:hover { color: var(--color-accent) !important; }

        /* Navbar and dropdowns */
        html[data-ui="cm"] nav a { background: transparent !important; color: var(--color-text-muted) !important; }
        html[data-ui="cm"] nav a:hover, html[data-ui="cm"] nav a:focus { color: var(--color-text) !important; background: color-mix(in srgb, var(--color-bg-elev) 92%, transparent) !important; }
        html[data-ui="cm"] .navbar-links a.active { background: var(--color-accent) !important; color: #fff !important; }
        html[data-ui="cm"] .dropdown-menu { background: var(--color-bg-elev) !important; border: 1px solid var(--color-border) !important; box-shadow: none !important; }
        html[data-ui="cm"] .dropdown-item { background: transparent !important; }
        html[data-ui="cm"] .dropdown-item:hover { background: color-mix(in srgb, var(--color-bg-elev) 92%, transparent) !important; }

        /* Avatar: remove gradients */
        html[data-ui="cm"] .avatar-circle { background: var(--color-accent) !important; border: 1px solid var(--color-accent-strong) !important; color: #061018 !important; }

        /* Private toggle */
        html[data-ui="cm"] .private-toggle { background: var(--color-bg) !important; border: 1px solid var(--color-border) !important; box-shadow: none !important; }
        html[data-ui="cm"] .private-toggle::before { content: none !important; }
        html[data-ui="cm"] .private-knob { background: var(--color-text) !important; box-shadow: none !important; }
        html[data-ui="cm"] .private-toggle[aria-checked="true"] { background: var(--color-accent) !important; border-color: var(--color-accent-strong) !important; }
        html[data-ui="cm"] .private-toggle[aria-checked="true"] .private-knob { background: #fff !important; }

        /* Editor overrides under CM (beats page inline by specificity) */
        html[data-ui="cm"] .editor-card { position: relative; border: 1px solid var(--color-border) !important; border-radius: 8px !important; background: var(--color-bg-elev) !important; }
        html[data-ui="cm"] .editor-grid { display: grid; grid-template-columns: auto 1fr; align-items: stretch; }
        html[data-ui="cm"] .line-gutter { width: 52px; user-select: none; text-align: right; padding: .75rem .5rem .75rem .75rem; margin: 0; color: var(--color-text-muted); border-right: 1px solid var(--color-border); background: var(--color-bg); overflow: hidden; font: 12px/1.25rem ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; white-space: pre; }
        html[data-ui="cm"] .editor-grid textarea#content { padding: .75rem; border: 0; background: transparent; outline: none; resize: vertical; min-height: 340px; }

        /* Site-wide neutralization of tinted backgrounds and CTA pulse */
        html[data-ui="cm"] .bg-yt-elevated\/40 { background: var(--color-bg-elev) !important; }
        html[data-ui="cm"] .bg-yt-elevated\/50 { background: var(--color-bg-elev) !important; }
        html[data-ui="cm"] .bg-yt-elevated\/55 { background: var(--color-bg-elev) !important; }
        html[data-ui="cm"] .bg-yt-elevated\/60 { background: var(--color-bg-elev) !important; }
        html[data-ui="cm"] .btn-cta { animation: none !important; }
        html[data-ui="cm"] #dropzone.dz-active { border-color: var(--color-accent-strong) !important; background: color-mix(in srgb, var(--color-accent) 8%, transparent) !important; box-shadow: none !important; }
        /* Disable Tailwind gradient utilities under CM */
        html[data-ui="cm"] [class*="bg-gradient-to-"] { background-image: none !important; background-color: var(--color-bg-elev) !important; }
        /* Disable shadows from Tailwind utilities under CM */
        html[data-ui="cm"] [class~="shadow"], html[data-ui="cm"] [class*="shadow-"] { box-shadow: none !important; }
        /* Neutralize hover translations on interactive elements */
        html[data-ui="cm"] a:hover,
        html[data-ui="cm"] button:hover,
        html[data-ui="cm"] .btn-primary:hover,
        html[data-ui="cm"] .btn-secondary:hover,
        html[data-ui="cm"] .btn-outline:hover { transform: none !important; }

        /* Center main creation pages (paste and files) */
        html[data-ui="cm"] #pasteCreateRoot,
        html[data-ui="cm"] #filesCreateRoot {
            min-height: 100vh;
            display: grid;
            place-items: center; /* centers horizontally and vertically */
        }
    </style>
</head>
<body class="h-full bg-yt-bg text-yt-text">
    <div class="min-h-full flex flex-col">
        <header class="glass main-navbar">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav>
                    <a href="{{ route('paste.create') }}" class="brand-mark flex items-center">
                        <!-- Logo SVG -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 40" class="h-8 w-auto">
                            <defs>
                                <linearGradient id="navGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" style="stop-color:#60a5fa;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#3b82f6;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                            <g>
                                <!-- Shield icon -->
                                <path d="M20 6 C20 6, 30 8, 30 8 C30 8, 30 18, 30 22 C30 28, 20 32, 20 32 C20 32, 10 28, 10 22 C10 18, 10 8, 10 8 L20 6 Z"
                                      fill="url(#navGradient)"/>
                                <path d="M20 9 C20 9, 27 11, 27 11 C27 11, 27 19, 27 21 C27 25, 20 28, 20 28 C20 28, 13 25, 13 21 C13 19, 13 11, 13 11 L20 9 Z"
                                      fill="white"
                                      opacity="0.25"/>
                                <!-- Lock icon inside shield -->
                                <g transform="translate(20, 20)">
                                    <rect x="-6" y="-2" width="12" height="8" rx="1" fill="white" opacity="0.95"/>
                                    <path d="M -4,-2 L -4,-6 Q -4,-8 0,-8 Q 4,-8 4,-6 L 4,-2"
                                          stroke="white"
                                          stroke-width="2"
                                          fill="none"
                                          stroke-linecap="round"
                                          opacity="0.95"/>
                                    <circle cx="0" cy="2" r="1" fill="#3b82f6"/>
                                </g>
                            </g>
                            <!-- Text -->
                            <text x="40" y="26"
                                  font-family="-apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Inter', 'Helvetica Neue', system-ui, sans-serif"
                                  font-size="18"
                                  font-weight="600"
                                  fill="currentColor">
                                DailyForever
                            </text>
                        </svg>
                    </a>
                    <div class="hidden md:flex navbar-links">
                        <a href="{{ route('paste.create') }}" class="{{ request()->routeIs('paste.create') ? 'active' : '' }}" data-i18n="nav.new_paste">New Secure Paste</a>
                        <a href="{{ route('files.create') }}" class="{{ request()->routeIs('files.create') ? 'active' : '' }}" data-i18n="nav.new_file">New Encrypted File</a>
                        <a href="{{ route('prekeys.index') }}" class="{{ request()->routeIs('prekeys.*') ? 'active' : '' }}" data-i18n="nav.prekeys">Prekeys</a>
                        <a href="{{ route('legal.how-it-works') }}" class="{{ request()->routeIs('legal.how-it-works') ? 'active' : '' }}" data-i18n="nav.how_it_works">How it works</a>
                    </div>
                    <div class="navbar-actions">
                        @auth
                            <div class="relative" id="userMenuContainer">
                                <button id="userMenuToggle" type="button" class="user-btn btn-secondary px-3 py-1.5 text-sm flex items-center gap-2" aria-expanded="false" aria-haspopup="true">
                                    <span class="avatar-circle">{{ strtoupper(substr(auth()->user()->username, 0, 1)) }}</span>
                                    <span class="user-label hidden md:inline">{{ auth()->user()->username }}</span>
                                    <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div id="userMenu" class="dropdown-menu hidden absolute right-0 mt-2 w-56 z-50 content-card p-2 shadow-lg rounded-lg border border-gray-700">
                                    <a href="{{ route('user.dashboard') }}" class="dropdown-item" data-i18n="nav.dashboard">Dashboard</a>
                                    <a href="{{ route('user.files') }}" class="dropdown-item" data-i18n="nav.my_files">My Files</a>
                                    <a href="{{ route('pastes.mine') }}" class="dropdown-item" data-i18n="nav.my_pastes">My Pastes</a>
                                    @if(auth()->user()->is_admin)
                                        <a href="{{ route('admin.dashboard') }}" class="dropdown-item" data-i18n="nav.admin">Admin</a>
                                    @endif
                                    <div class="border-t border-yt-border my-2"></div>
                                    <form method="POST" action="{{ route('auth.logout') }}">
                                        @csrf
                                        <button class="dropdown-item text-red-400 hover:text-red-300" data-i18n="nav.logout">Logout</button>
                                    </form>
                                </div>
                            </div>
                        @endauth
                        @guest
                            <a href="{{ route('auth.login.show') }}" class="btn btn-primary text-sm px-4 py-2" data-i18n="nav.login">Login</a>
                            <a href="{{ route('auth.register.show') }}" class="btn btn-secondary text-sm px-4 py-2" data-i18n="nav.register">Register</a>
                        @endguest
                        <select id="langSelect" class="text-sm px-2 py-1 rounded-lg bg-transparent border border-yt-border text-yt-text-secondary" title="Language">
                            <option value="en">EN</option>
                            <option value="es">ES</option>
                        </select>
                        <button id="themeToggle" type="button" class="theme-toggle">
                            <svg id="iconDark" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M21.64 13a1 1 0 0 0-1.05-.14 8 8 0 1 1-9.45-9.45 1 1 0 0 0-.14-1.05A1 1 0 0 0 9.5 2a10 10 0 1 0 12.45 12.45 1 1 0 0 0-.31-1.45z"/></svg>
                            <svg id="iconLight" class="hidden" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M6.76 4.84l-1.8-1.79L3.17 4.84 4.96 6.63 6.76 4.84zM1 13h3v-2H1v2zm10 9h2v-3h-2v3zm7.03-3.16l1.79 1.79 1.41-1.41-1.79-1.79-1.41 1.41zM20 11V9h3v2h-3zm-8-7h2V1h-2v3zM4.96 17.37L3.17 19.16l1.79 1.79 1.41-1.41-1.41-1.41zM12 6a6 6 0 100 12 6 6 0 000-12z"/></svg>
                            <span id="themeLabel" data-i18n="theme.dark">Dark</span>
                        </button>
                        <button id="mobileNavToggle" type="button" class="mobile-nav-toggle" aria-expanded="false" aria-label="Open navigation menu" data-i18n-attr="aria-label" data-i18n-aria-label="aria.menu_open">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                    </div>
                </nav>
                <div id="mobileNavPanel" class="mobile-nav-panel">
                    <a href="{{ route('paste.create') }}" class="{{ request()->routeIs('paste.create') ? 'active' : '' }}" data-i18n="nav.new_paste">New Secure Paste</a>
                    <a href="{{ route('files.create') }}" class="{{ request()->routeIs('files.create') ? 'active' : '' }}" data-i18n="nav.new_file">New Encrypted File</a>
                    <a href="{{ route('prekeys.index') }}" class="{{ request()->routeIs('prekeys.*') ? 'active' : '' }}" data-i18n="nav.prekeys">Prekeys</a>
                    <a href="{{ route('legal.how-it-works') }}" class="{{ request()->routeIs('legal.how-it-works') ? 'active' : '' }}" data-i18n="nav.how_it_works">How it works</a>
                    @auth
                        <a href="{{ route('user.dashboard') }}" data-i18n="nav.dashboard">Dashboard</a>
                        <a href="{{ route('user.files') }}" data-i18n="nav.my_files">My Files</a>
                        <a href="{{ route('pastes.mine') }}" data-i18n="nav.my_pastes">My Pastes</a>
                        @if(auth()->user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" data-i18n="nav.admin">Admin</a>
                        @endif
                        <form method="POST" action="{{ route('auth.logout') }}" class="pt-2">
                            @csrf
                            <button class="w-full btn-secondary px-4 py-3 text-sm" data-i18n="nav.logout">Logout</button>
                        </form>
                    @endauth
                    @guest
                        <a href="{{ route('auth.login.show') }}" data-i18n="nav.login">Login</a>
                        <a href="{{ route('auth.register.show') }}" data-i18n="nav.register">Register</a>
                    @endguest
                    <button id="themeToggleMobile" type="button" class="theme-toggle mt-2">
                        <svg id="iconDarkMobile" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M21.64 13a1 1 0 0 0-1.05-.14 8 8 0 1 1-9.45-9.45 1 1 0 0 0-.14-1.05A1 1 0 0 0 9.5 2a10 10 0 1 0 12.45 12.45 1 1 0 0 0-.31-1.45z"/></svg>
                        <svg id="iconLightMobile" class="hidden" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M6.76 4.84l-1.8-1.79L3.17 4.84 4.96 6.63 6.76 4.84zM1 13h3v-2H1v2zm10 9h2v-3h-2v3zm7.03-3.16l1.79 1.79 1.41-1.41-1.79-1.79-1.41 1.41zM20 11V9h3v2h-3zm-8-7h2V1h-2v3zM4.96 17.37L3.17 19.16l1.79 1.79 1.41-1.41-1.41-1.41zM12 6a6 6 0 100 12 6 6 0 000-12z"/></svg>
                        <span id="themeLabelMobile" data-i18n="theme.dark">Dark</span>
                    </button>
                    <select id="langSelectMobile" class="mt-2 text-sm px-2 py-1 rounded-lg bg-transparent border border-yt-border text-yt-text-secondary w-full" title="Language">
                        <option value="en">EN</option>
                        <option value="es">ES</option>
                    </select>
                </div>
            </div>
        </header>

        <main class="flex-1 animate-fade-in-up">
            <div class="container-responsive">
                @yield('content')
            </div>
        </main>

        <footer class="glass mt-8">
            <div class="container-responsive py-8 sm:py-10">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 sm:gap-8">
                    <div class="col-span-2 md:col-span-1">
                        <a href="{{ route('paste.create') }}" class="flex items-center gap-2 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" class="h-6 w-6 sm:h-8 sm:w-8">
                                <defs>
                                    <linearGradient id="footerGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                        <stop offset="0%" style="stop-color:#60a5fa;stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:#2563eb;stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                                <path d="M32 4 C32 4, 52 10, 52 10 C52 10, 52 28, 52 36 C52 52, 32 60, 32 60 C32 60, 12 52, 12 36 C12 28, 12 10, 12 10 L32 4 Z" 
                                      fill="url(#footerGradient)"/>
                                <path d="M32 8 C32 8, 48 13, 48 13 C48 13, 48 29, 48 35 C48 48, 32 55, 32 55 C32 55, 16 48, 16 35 C16 29, 16 13, 16 13 L32 8 Z" 
                                      fill="white" 
                                      opacity="0.15"/>
                                <g transform="translate(32, 30)">
                                    <rect x="-10" y="-4" width="20" height="14" rx="2" ry="2" 
                                          fill="white" 
                                          opacity="0.95"/>
                                    <path d="M -7,-4 L -7,-10 Q -7,-14 0,-14 Q 7,-14 7,-10 L 7,-4" 
                                          stroke="white" 
                                          stroke-width="3" 
                                          fill="none" 
                                          stroke-linecap="round"
                                          opacity="0.95"/>
                                    <circle cx="0" cy="3" r="2" fill="#2563eb"/>
                                </g>
                            </svg>
                            <div>
                                <div class="font-semibold text-sm sm:text-base text-yt-text" data-i18n="footer.brand.name">DailyForever</div>
                                <div class="text-xs text-yt-text-secondary" data-i18n="footer.brand.tagline">Zero-knowledge encryption</div>
                            </div>
                        </a>
                        <p class="text-xs text-yt-text-secondary" data-i18n="footer.brand.description">Your data is encrypted client-side before sending. We can't read your content even if we wanted to.</p>
                    </div>
                    <div class="col-span-1">
                        <h3 class="text-xs uppercase tracking-wider text-yt-text-secondary mb-3" data-i18n="footer.product">Product</h3>
                        <ul class="space-y-2 text-xs sm:text-sm">
                            <li><a href="{{ route('paste.create') }}" class="text-yt-text" data-i18n="nav.new_paste">New Secure Paste</a></li>
                            <li><a href="{{ route('files.create') }}" class="text-yt-text" data-i18n="nav.new_file">New Encrypted File</a></li>
                            @auth
                            <li><a href="{{ route('pastes.mine') }}" class="text-yt-text" data-i18n="nav.my_pastes">My Pastes</a></li>
                            <li><a href="{{ route('user.files') }}" class="text-yt-text" data-i18n="nav.my_files">My Files</a></li>
                            @endauth
                        </ul>
                    </div>
                    <div class="col-span-1">
                        <h3 class="text-xs uppercase tracking-wider text-yt-text-secondary mb-3" data-i18n="footer.learn">Learn</h3>
                        <ul class="space-y-2 text-xs sm:text-sm">
                            <li><a href="{{ route('legal.how-it-works') }}" class="text-yt-text" data-i18n="footer.how_it_works">How it works</a></li>
                            <li><a href="{{ route('legal.roadmap') }}" class="text-yt-text" data-i18n="footer.roadmap">Roadmap</a></li>
                            <li><a href="{{ route('legal.faq') }}" class="text-yt-text" data-i18n="footer.faq">FAQ</a></li>
                            <li><a href="{{ route('blog.index') }}" class="text-yt-text" data-i18n="footer.blog">Blog</a></li>
                            <li><a href="{{ route('support.index') }}" class="text-yt-text" data-i18n="footer.support">Support</a></li>
                        </ul>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <h3 class="text-xs uppercase tracking-wider text-yt-text-secondary mb-3" data-i18n="footer.legal">Legal</h3>
                        <ul class="space-y-2 text-xs sm:text-sm">
                            <li><a href="{{ route('legal.terms') }}" class="text-yt-text" data-i18n="footer.terms">Terms</a></li>
                            <li><a href="{{ route('legal.privacy') }}" class="text-yt-text" data-i18n="footer.privacy">Privacy</a></li>
                            <li><a href="{{ route('legal.cookies') }}" class="text-yt-text" data-i18n="footer.cookies">Cookies</a></li>
                            <li><a href="{{ route('legal.acceptable-use') }}" class="text-yt-text" data-i18n="footer.acceptable_use">Acceptable Use</a></li>
                            <li><a href="{{ route('legal.no-logs') }}" class="text-yt-text" data-i18n="footer.no_logs">No Logs</a></li>
                            <li><a href="{{ route('legal.dmca') }}" class="text-yt-text" data-i18n="footer.dmca">DMCA</a></li>
                            <li><a href="{{ route('legal.philosophy') }}" class="text-yt-text" data-i18n="footer.philosophy">Philosophy</a></li>
                        </ul>
                    </div>
                </div>
                <div class="mt-6 sm:mt-8 pt-6 border-t border-yt-border flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-yt-text-secondary">
                    <p>&copy; 2025 DailyForever • <span data-i18n="footer.tagline">Secure Paste Sharing</span></p>
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                        <span data-i18n="footer.e2e">E2E</span>
                        <span data-i18n="footer.zk">Zero‑Knowledge</span>
                        <span data-i18n="footer.no_data_collection">No Data Collection</span>
                        <a href="https://github.com/DailyForever/DailyForever" target="_blank" rel="noopener noreferrer" class="text-yt-text hover:text-yt-accent transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script>
        (function() {
            const media = window.matchMedia('(prefers-color-scheme: light)');
            function apply(theme, persist) {
                document.documentElement.setAttribute('data-theme', theme);
                if (persist) { try { localStorage.setItem('theme', theme); } catch (_) {} }
                const iconDark = document.getElementById('iconDark');
                const iconLight = document.getElementById('iconLight');
                const iconDarkMobile = document.getElementById('iconDarkMobile');
                const iconLightMobile = document.getElementById('iconLightMobile');
                const label = document.getElementById('themeLabel');
                const labelMobile = document.getElementById('themeLabelMobile');
                const isLight = theme === 'light';
                if (iconDark && iconLight && label) {
                    iconDark.classList.toggle('hidden', isLight);
                    iconLight.classList.toggle('hidden', !isLight);
                    label.textContent = (window.I18N ? window.I18N.t(isLight ? 'theme.light' : 'theme.dark') : (isLight ? 'Light' : 'Dark'));
                }
                if (iconDarkMobile && iconLightMobile) {
                    iconDarkMobile.classList.toggle('hidden', isLight);
                    iconLightMobile.classList.toggle('hidden', !isLight);
                }
                if (labelMobile) {
                    labelMobile.textContent = (window.I18N ? window.I18N.t(isLight ? 'theme.light' : 'theme.dark') : (isLight ? 'Light' : 'Dark'));
                }
            }
            try {
                const saved = localStorage.getItem('theme');
                const initial = saved || (media.matches ? 'light' : 'dark');
                apply(initial, false);
                if (!saved && media && media.addEventListener) {
                    media.addEventListener('change', (e) => apply(e.matches ? 'light' : 'dark', false));
                }
            } catch (_) {}
            const toggle = document.getElementById('themeToggle');
            const toggleMobile = document.getElementById('themeToggleMobile');
            if (toggle) {
                toggle.addEventListener('click', () => {
                    const current = document.documentElement.getAttribute('data-theme') || 'dark';
                    apply(current === 'dark' ? 'light' : 'dark', true);
                });
            }
            if (toggleMobile) {
                toggleMobile.addEventListener('click', () => {
                    const current = document.documentElement.getAttribute('data-theme') || 'dark';
                    apply(current === 'dark' ? 'light' : 'dark', true);
                });
            }
        })();

        // User menu (signed-in) dropdown toggle
        (function() {
            const toggle = document.getElementById('userMenuToggle');
            const menu = document.getElementById('userMenu');
            if (!toggle || !menu) return;
            function open() { menu.classList.remove('hidden'); toggle.setAttribute('aria-expanded', 'true'); }
            function close() { menu.classList.add('hidden'); toggle.setAttribute('aria-expanded', 'false'); }
            toggle.addEventListener('click', (e) => {
                e.stopPropagation();
                const isHidden = menu.classList.contains('hidden');
                isHidden ? open() : close();
            });
            document.addEventListener('click', (e) => {
                if (!menu.contains(e.target) && !toggle.contains(e.target)) close();
            });
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });
        })();
        
        // Mobile nav functionality (new navbar)
        (function() {
            const toggle = document.getElementById('mobileNavToggle');
            const panel = document.getElementById('mobileNavPanel');
            const body = document.body;
            
            // Make sure elements exist
            if (!toggle || !panel) {
                console.error('Mobile navigation elements not found');
                return;
            }
            
            // Make sure the panel is hidden by default (in case CSS fails)
            panel.style.display = 'none';
            
            // Toggle function
            function toggleMenu() {
                const isOpen = panel.style.display === 'flex';
                setState(!isOpen);
            }
            
            // Set the state of the menu
            function setState(open) {
                if (open) {
                    panel.style.display = 'flex';
                    body.classList.add('menu-open');
                    // Add event listener to close when clicking outside
                    setTimeout(() => {
                        document.addEventListener('click', handleClickOutside);
                    }, 10);
                } else {
                    panel.style.display = 'none';
                    body.classList.remove('menu-open');
                    document.removeEventListener('click', handleClickOutside);
                }
                
                // Update ARIA attributes
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                const label = open ? 'Close navigation menu' : 'Open navigation menu';
                toggle.setAttribute('aria-label', label);
                
                // Toggle icon
                const icon = toggle.querySelector('svg');
                if (icon) {
                    icon.innerHTML = open
                        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />'
                        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />';
                }
            }
            
            // Close menu when clicking outside
            function handleClickOutside(event) {
                if (!panel.contains(event.target) && !toggle.contains(event.target)) {
                    setState(false);
                }
            }

            // Toggle menu on button click
            toggle.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleMenu();
            });
            
            // Close menu when clicking on a link
            panel.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => setState(false));
            });
            
            // Handle window resize
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    if (window.innerWidth >= 768) {
                        setState(false);
                    }
                }, 250);
            });
            
            // Close menu with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && panel.style.display === 'flex') {
                    setState(false);
                }
            });
            // Initialize label and icon state
            setState(false);
        })();

        // Language switcher (desktop + mobile)
        (function(){
            const sel = document.getElementById('langSelect');
            const selM = document.getElementById('langSelectMobile');
            function setValue(code){ if (sel) sel.value = code; if (selM) selM.value = code; }
            function getHtmlLang(){ return (document.documentElement.getAttribute('lang') || 'en').toLowerCase(); }
            async function ensureI18NReady(timeoutMs = 2000){
                const start = Date.now();
                while (Date.now() - start < timeoutMs) {
                    if (window.I18N && typeof window.I18N.setLanguage === 'function') return true;
                    await new Promise(r => setTimeout(r, 50));
                }
                return false;
            }
            async function setLang(code){
                try {
                    const ready = await ensureI18NReady(5000);
                    if (ready) {
                        await window.I18N.setLanguage(code);
                    } else {
                        try { localStorage.setItem('lang', code); } catch(_) {}
                        document.documentElement.setAttribute('lang', code);
                        // Retry applying translations without requiring a full reload
                        (function retryLang(attempts = 50){
                            if (window.I18N && typeof window.I18N.setLanguage === 'function') {
                                window.I18N.setLanguage(code);
                            } else if (attempts > 0) {
                                setTimeout(() => retryLang(attempts - 1), 200);
                            }
                        })();
                    }
                } catch(_) {}
                setValue(code);
            }
            const handler = (e) => setLang(e.target.value);
            if (sel) sel.addEventListener('change', handler);
            if (selM) selM.addEventListener('change', handler);
            // Initialize current value once i18n has possibly set html lang
            (async () => {
                const ready = await ensureI18NReady(500);
                const code = ready && window.I18N && window.I18N.lang ? window.I18N.lang : getHtmlLang();
                setValue(code);
            })();
        })();
    </script>
    @yield('modals')
    @yield('scripts')
    
    <!-- Cookies Consent Banner -->
    <x-cookies-consent />
    
    <!-- Backup Code Modal -->
    @if(session('backup_code'))
        <x-backup-code-modal :backup-code="session('backup_code')" />
    @endif
</body>
</html>
