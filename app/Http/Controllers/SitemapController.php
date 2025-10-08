<?php

namespace App\Http\Controllers;

use App\Models\Paste;
use App\Models\File;
use Carbon\Carbon;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        try {
            $urls = [
                [
                    'loc' => url('/'),
                    'lastmod' => Carbon::now()->toAtomString(),
                    'changefreq' => 'daily',
                    'priority' => '1.0'
                ],
                [
                    'loc' => url('/blog'),
                    'lastmod' => Carbon::now()->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8'
                ],
                [
                    'loc' => url('/terms'),
                    'lastmod' => Carbon::now()->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.3'
                ],
                [
                    'loc' => url('/privacy'),
                    'lastmod' => Carbon::now()->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.3'
                ],
                [
                    'loc' => url('/dmca'),
                    'lastmod' => Carbon::now()->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.2'
                ],
                [
                    'loc' => url('/acceptable-use'),
                    'lastmod' => Carbon::now()->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.2'
                ],
                [
                    'loc' => url('/no-logs'),
                    'lastmod' => Carbon::now()->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.4'
                ],
                [
                    'loc' => url('/philosophy'),
                    'lastmod' => Carbon::now()->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.4'
                ],
                [
                    'loc' => url('/how-it-works'),
                    'lastmod' => Carbon::now()->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.5'
                ],
                [
                    'loc' => url('/faq'),
                    'lastmod' => Carbon::now()->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.5'
                ],
                [
                    'loc' => url('/support'),
                    'lastmod' => Carbon::now()->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.6'
                ]
            ];

            $sitemaps = [
                url('/sitemap-pastes.xml'),
                url('/sitemap-files.xml'),
                url('/sitemap-blog.xml'),
            ];

            $xml = view('sitemaps.index', [
                'urls' => $urls,
                'sitemaps' => $sitemaps,
                'lastmod' => Carbon::now()->toAtomString(),
            ])->render();

            return response($xml, 200)
                ->header('Content-Type', 'application/xml')
                ->header('Cache-Control', 'public, max-age=3600'); // Cache for 1 hour
        } catch (\Exception $e) {
            // Return minimal sitemap on error
            $xml = '<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"><sitemap><loc>' . url('/') . '</loc><lastmod>' . Carbon::now()->toAtomString() . '</lastmod></sitemap></sitemapindex>';
            return response($xml, 200)->header('Content-Type', 'application/xml');
        }
    }

    public function pastes(): Response
    {
        try {
            $pastes = Paste::query()
                ->where(function ($q) {
                    $q->whereNull('is_removed')->orWhere('is_removed', false);
                })
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', Carbon::now());
                })
                ->where(function ($q) {
                    $q->whereNull('is_private')->orWhere('is_private', false);
                })
                ->orderByDesc('id')
                ->limit(5000)
                ->get(['identifier', 'updated_at']);

            $xml = view('sitemaps.pastes', [
                'pastes' => $pastes,
            ])->render();

            return response($xml, 200)
                ->header('Content-Type', 'application/xml')
                ->header('Cache-Control', 'public, max-age=3600'); // Cache for 1 hour
        } catch (\Exception $e) {
            // Return empty sitemap on error to prevent crawling issues
            $xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>';
            return response($xml, 200)->header('Content-Type', 'application/xml');
        }
    }

    public function blog(): Response
    {
        try {
            $posts = \App\Models\Post::where('is_published', true)
                ->orderByDesc('published_at')
                ->get(['slug', 'updated_at']);

            $xml = view('sitemaps.blog', [
                'posts' => $posts,
            ])->render();

            return response($xml, 200)
                ->header('Content-Type', 'application/xml')
                ->header('Cache-Control', 'public, max-age=3600'); // Cache for 1 hour
        } catch (\Exception $e) {
            // Return empty sitemap on error to prevent crawling issues
            $xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>';
            return response($xml, 200)->header('Content-Type', 'application/xml');
        }
    }

    public function files(): Response
    {
        try {
            $files = File::query()
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', Carbon::now());
                })
                ->where(function ($q) {
                    $q->whereNull('is_private')->orWhere('is_private', false);
                })
                ->orderByDesc('id')
                ->limit(5000)
                ->get(['identifier', 'updated_at']);

            $xml = view('sitemaps.files', [
                'files' => $files,
            ])->render();

            return response($xml, 200)
                ->header('Content-Type', 'application/xml')
                ->header('Cache-Control', 'public, max-age=3600'); // Cache for 1 hour
        } catch (\Exception $e) {
            // Return empty sitemap on error to prevent crawling issues
            $xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>';
            return response($xml, 200)->header('Content-Type', 'application/xml');
        }
    }
}


