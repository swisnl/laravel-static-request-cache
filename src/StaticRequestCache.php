<?php

namespace Swis\Laravel\StaticRequestCache;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class StaticRequestCache
{
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @param \Illuminate\Filesystem\Filesystem $files
     */
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
     * @param \Illuminate\Http\Request                   $request
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return bool
     */
    public function shouldStoreResponse(Request $request, Response $response): bool
    {
        $isGETRequest = $request->getMethod() === 'GET';
        $hasNoParams = count($request->input()) === 0;
        $contentTypeData = $this->getContentTypeFromResponse($response);
        $hasIndexPhpInRequestUri = Str::contains($request->getRequestUri(), 'index.php');

        $isCachableMimeType = false;
        foreach ($contentTypeData as $contentType) {
            $isCachableMimeType = in_array($contentType, config('static-html-cache.cachable_mimetypes'), true) !== false;

            if ($isCachableMimeType === true) {
                break;
            }
        }

        $nonCacheableCacheControlHeaders = config('static-html-cache.non_cacheable_cache_control_values', []);

        $hasDisablingCacheHeaders = false;
        foreach ($nonCacheableCacheControlHeaders as $nonCacheableCacheControlHeader) {
            if ($response->headers->hasCacheControlDirective($nonCacheableCacheControlHeader)) {
                $hasDisablingCacheHeaders = true;
                break;
            }
        }

        return $this->enabled
            && $response->isOk()
            && !$hasDisablingCacheHeaders
            && !$hasIndexPhpInRequestUri
            && $isGETRequest
            && $hasNoParams
            && $isCachableMimeType;
    }

    /**
     * @param \Illuminate\Http\Request                   $request
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return string
     */
    private function getFilename(Request $request, Response $response): string
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
     * @param string $filename
     */
    private function ensureStorageDirectory(string $filename): void
    {
        $path = $this->files->dirname($filename);

        $this->files->makeDirectory($path, 0777, true, true);

        if (!$this->files->isDirectory($path)) {
            throw new RuntimeException(sprintf('Directory "%s" could not be created', $path));
        }
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return array
     */
    private function getContentTypeFromResponse(Response $response): array
    {
        return explode(';', $response->headers->get('content-type'));
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function disable(): void
    {
        $this->enabled = false;
    }

    /**
     * @param \Illuminate\Http\Request                   $request
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function store(Request $request, Response $response): void
    {
        $filename = $this->getFilename($request, $response);

        $this->ensureStorageDirectory($filename);

        $file = $response->getContent();

        $this->files->put($filename, $file);
    }
}
