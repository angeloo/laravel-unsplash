<?php

namespace Xchimx\UnsplashApi\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Xchimx\UnsplashApi\Facades\Unsplash;

class UnsplashRateLimitMiddleware
{
    public function handle($request, Closure $next)
    {
        if (! config('unsplash.rate_limiting.enabled', true)) {
            return $next($request);
        }

        $rateRemaining = Cache::get('unsplash_rate_remaining');

        if ($rateRemaining !== null && $rateRemaining <= config('unsplash.rate_limiting.threshold', 10)) {
            return response()->json([
                'error' => 'Unsplash API rate limit reached. Please try again later.',
            ], 429);
        }

        $response = $next($request);

        $this->updateRateLimitInfo();

        return $response;
    }

    protected function updateRateLimitInfo()
    {
        $headers = Unsplash::getLastResponseHeaders();

        if ($headers) {
            $rateLimit = $headers['X-Ratelimit-Limit'][0] ?? null;
            $rateRemaining = $headers['X-Ratelimit-Remaining'][0] ?? null;

            if ($rateLimit !== null) {
                Cache::put('unsplash_rate_limit', $rateLimit, now()->addHour());
            }

            if ($rateRemaining !== null) {
                Cache::put('unsplash_rate_remaining', $rateRemaining, now()->addHour());
            }
        }
    }
}
