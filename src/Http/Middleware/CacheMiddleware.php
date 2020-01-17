<?php

namespace Swis\Laravel\StaticRequestCache\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Swis\Laravel\StaticRequestCache\StaticRequestCache;
use Symfony\Component\HttpFoundation\Response;

class CacheMiddleware
{
    /**
     * @var \Swis\Laravel\StaticRequestCache\StaticRequestCache
     */
    protected $staticRequestCache;

    /**
     * @param \Swis\Laravel\StaticRequestCache\StaticRequestCache $staticRequestCache
     */
    public function __construct(StaticRequestCache $staticRequestCache)
    {
        $this->staticRequestCache = $staticRequestCache;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    /**
     * @param \Illuminate\Http\Request                   $request
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function terminate(Request $request, Response $response): void
    {
        $store = function () use ($request, $response) {
            if ($this->staticRequestCache->shouldStoreResponse($request, $response)) {
                $this->staticRequestCache->store($request, $response);
            }
        };

        if (config('static-html-cache.graceful')) {
            rescue($store);
        } else {
            $store();
        }
    }
}
