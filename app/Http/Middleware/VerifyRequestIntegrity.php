<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyRequestIntegrity
{
    public function handle(Request $request, Closure $next)
    {
        // Skip integrity checks in development
        if (app()->environment('local', 'testing')) {
            return $next($request);
        }

        // Verify Content-Type for POST/PUT/PATCH requests
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            $contentType = $request->header('Content-Type');

            if ($request->expectsJson() && $contentType !== 'application/json') {
                abort(415, 'Unsupported Media Type');
            }
        }

        // Verify Origin header for state-changing requests
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $origin = $request->header('Origin');
            $host = $request->header('Host');

            if ($origin && $host) {
                $originHost = parse_url($origin, PHP_URL_HOST);
                if ($originHost !== $host) {
                    abort(403, 'Invalid Origin');
                }
            }
        }

        return $next($request);
    }
}
