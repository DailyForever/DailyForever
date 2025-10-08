<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CrawlerTestController extends Controller
{
    public function test(Request $request): Response
    {
        $userAgent = $request->header('User-Agent', 'Unknown');
        $isCrawler = $this->isCrawler($userAgent);
        
        $data = [
            'timestamp' => now()->toISOString(),
            'user_agent' => $userAgent,
            'is_crawler' => $isCrawler,
            'ip' => $request->ip(),
            'headers' => $request->headers->all(),
            'site_info' => [
                'name' => config('app.name'),
                'url' => url('/'),
                'environment' => app()->environment(),
                'version' => config('app.version', '1.0.0')
            ],
            'crawler_tests' => [
                'robots_txt' => url('/robots.txt'),
                'sitemap_index' => url('/sitemap.xml'),
                'sitemap_blog' => url('/sitemap-blog.xml'),
                'sitemap_pastes' => url('/sitemap-pastes.xml'),
                'sitemap_files' => url('/sitemap-files.xml'),
                'ads_txt' => url('/ads.txt'),
                'crawl_endpoint' => url('/crawl')
            ],
            'public_pages' => [
                'home' => url('/'),
                'blog' => url('/blog'),
                'terms' => url('/terms'),
                'privacy' => url('/privacy'),
                'support' => url('/support'),
                'how_it_works' => url('/how-it-works'),
                'faq' => url('/faq')
            ]
        ];
        
        return response()->json($data, 200, [
            'Content-Type' => 'application/json',
            'Cache-Control' => 'public, max-age=300', // 5 minutes
            'X-Crawler-Test' => 'true',
            'X-Site-Status' => 'crawlable'
        ]);
    }
    
    private function isCrawler(string $userAgent): bool
    {
        $crawlers = [
            'googlebot', 'bingbot', 'slurp', 'duckduckbot', 'baiduspider', 
            'yandexbot', 'facebookexternalhit', 'twitterbot', 'linkedinbot',
            'whatsapp', 'telegrambot', 'applebot', 'crawler', 'spider', 'bot'
        ];
        
        $userAgentLower = strtolower($userAgent);
        
        foreach ($crawlers as $crawler) {
            if (strpos($userAgentLower, $crawler) !== false) {
                return true;
            }
        }
        
        return false;
    }
}
