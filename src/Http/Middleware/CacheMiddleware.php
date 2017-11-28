<?php

namespace Swis\LaravelStaticRequestCache\Http\Middleware;

use Closure;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheMiddleware
{

    /**
     * @var Filesystem
     */
    private $files;

    /**
     * @var bool
     */
    private $enabled;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;

        if (config('static-html-cache.enabled') === 'debug') {
            $this->enabled = !config('app.debug');
        } else {
            $this->enabled = config('static-html-cache.enabled');
        }
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

    public function terminate(Request $request, Response $response)
    {
        if ($this->shouldStoreResponse($request, $response)) {
            $filename = $this->getFilename($request, $response);

            $this->ensureStorageDirectory($filename);

            $file = $response->getContent();

            $this->files->put($filename, $file);
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return bool
     */
    private function shouldStoreResponse(Request $request, Response $response)
    {
        $isGETRequest = $request->getMethod() === 'GET';
        $hasNoParams = count($request->input()) === 0;
        $contentTypeData = $this->getContentTypeFromResponse($response);

        $isCachableMimeType = false;
        foreach ($contentTypeData as $contentType) {
            $isCachableMimeType = in_array($contentType, config('static-html-cache.cachable_mimetypes')) !== false;

            if ($isCachableMimeType === true) {
                break;
            }
        }

        return $this->enabled && $response->isOk() && $isGETRequest && $hasNoParams && $isCachableMimeType;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    private function getFilename(Request $request, Response $response)
    {
        $request_uri = trim($request->getRequestUri(), '/');
        $request_uri = empty($request_uri) ? '' : '/'.$request_uri;

        $contentType = $this->getContentTypeFromResponse($response);

        $filename = public_path(config('static-html-cache.cache_path_prefix').$request_uri);
        if (in_array('text/html', $contentType, true)) {
            $filename .= '/index.html';
        }

        return $filename;
    }

    /**
     * @param $filename
     */
    private function ensureStorageDirectory($filename)
    {
        $path = dirname($filename);

        if (!$this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true);
        }
    }

    /**
     * @param Response $response
     *
     * @return array
     */
    private function getContentTypeFromResponse(Response $response): array
    {
        return explode(';', $response->headers->get('content-type'));
    }

    public function isEnabled(){
        return $this->enabled;
    }
}
