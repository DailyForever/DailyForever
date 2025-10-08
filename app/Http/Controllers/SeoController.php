<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function robots(): Response
    {
        $isProd = config('app.env') === 'production';
        $lines = [];
        $lines[] = 'User-agent: *';
        if ($isProd) {
            $lines[] = 'Allow: /';
            $lines[] = 'Sitemap: ' . url('/sitemap.xml');
        } else {
            $lines[] = 'Disallow: /';
        }
        $body = implode("\n", $lines) . "\n";
        return response($body, 200)->header('Content-Type', 'text/plain');
    }

    public function ads(): Response
    {
        $publisher = env('ADSENSE_PUBLISHER');
        if (!$publisher) {
            return response("", 200)->header('Content-Type', 'text/plain');
        }
        $body = 'google.com, pub-' . $publisher . ', DIRECT, f08c47fec0942fa0' . "\n";
        return response($body, 200)->header('Content-Type', 'text/plain');
    }
}


