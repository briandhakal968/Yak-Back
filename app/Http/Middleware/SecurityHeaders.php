<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds baseline security headers to every API response.
 *
 *   - X-Content-Type-Options: nosniff  → prevents MIME-sniffing attacks
 *   - X-Frame-Options: DENY           → prevents clickjacking
 *   - Referrer-Policy: no-referrer    → does not leak the API URL to third parties
 *   - Strict-Transport-Security       → force HTTPS in production
 *   - Permissions-Policy              → deny powerful features by default
 *   - Cache-Control: no-store         → responses may contain private user data
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'no-referrer');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Auth and user data should never be cached by intermediaries.
        if ($request->is('api/*')) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
        }

        return $response;
    }
}
