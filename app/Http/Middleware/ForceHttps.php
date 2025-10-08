<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('production')) {
            $proto = $request->headers->get('X-Forwarded-Proto');
            $isHttps = $request->secure() || ($proto && strtolower($proto) === 'https');
            if (!$isHttps) {
                // Redirect only for idempotent methods to avoid breaking POST/PUT bodies
                if (in_array($request->getMethod(), ['GET', 'HEAD'], true)) {
                    return redirect()->secure($request->getRequestUri());
                }
                // For non-idempotent requests, allow through (expect TLS termination at proxy)
            }
        }

        return $next($request);
    }
}
