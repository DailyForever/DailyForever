<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $isProd = app()->environment('production');
        $scriptSrc = "script-src 'self' http://127.0.0.1:5173 http://localhost:5173 https://cdnjs.cloudflare.com https://cdn.jsdelivr.net";
        if (!$isProd) {
            // Dev: allow inline/eval for HMR tooling only outside production
            $scriptSrc .= " 'unsafe-inline' 'unsafe-eval'";
        }

        $csp = "default-src 'self'; "
            . $scriptSrc . "; "
            . ( $isProd
                ? "style-src 'self' https://fonts.googleapis.com https://cdnjs.cloudflare.com; "
                : "style-src 'self' 'unsafe-inline' http://127.0.0.1:5173 http://localhost:5173 https://fonts.googleapis.com https://cdnjs.cloudflare.com; "
              )
            . "font-src 'self' https://fonts.gstatic.com; "
            . "img-src 'self' data: https://chart.googleapis.com; "
            . "connect-src 'self' blob: https: http://127.0.0.1:5173 http://localhost:5173 ws://127.0.0.1:5173 ws://localhost:5173; "
            . "worker-src 'self' blob:; object-src 'none'; "
            . "base-uri 'none'; form-action 'self'; frame-ancestors 'none'";

        $response->headers->set('Referrer-Policy', 'no-referrer');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        $response->headers->set('Content-Security-Policy', $csp);

        // Enforce HSTS in production only
        if (app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        return $response;
    }
}


