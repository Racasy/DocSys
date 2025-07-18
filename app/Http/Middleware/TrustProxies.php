<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrustProxies
{
    public function handle(Request $request, Closure $next): Response
    {
        // Trust all Cloudflare proxies
        $request->setTrustedProxies(
            $request->getClientIps(), 
            Request::HEADER_X_FORWARDED_FOR |
            Request::HEADER_X_FORWARDED_HOST |
            Request::HEADER_X_FORWARDED_PORT |
            Request::HEADER_X_FORWARDED_PROTO |
            Request::HEADER_X_FORWARDED_PREFIX
        );

        return $next($request);
    }
}