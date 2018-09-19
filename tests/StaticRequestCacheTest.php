<?php

namespace Swis\LaravelStaticRequestCache\Tests;

class StaticRequestCacheTest extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return ['Swis\LaravelStaticRequestCache\Provider\CacheProvider'];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['path.public'] = function () {
            return __DIR__;
        };

        $app['config']->set('static-html-cache.cachable_mimetypes', ['text/html', 'application/json']);
    }

    public function testEnabledConfig()
    {
        $staticRequestCache = new \Swis\LaravelStaticRequestCache\StaticRequestCache($this->getFilesystemMock());
        $this->app['config']->set('static-html-cache.enabled', true);
        $this->assertTrue($staticRequestCache->isEnabled());
    }

    public function testDisabledConfig()
    {
        $this->app['config']->set('static-html-cache.enabled', false);

        $staticRequestCache = new \Swis\LaravelStaticRequestCache\StaticRequestCache($this->getFilesystemMock());
        $this->assertFalse($staticRequestCache->isEnabled());
    }

    public function testDebugConfigEnabled()
    {
        $this->app['config']->set('static-html-cache.enabled', 'debug');
        $this->app['config']->set('app.debug', true);

        $staticRequestCache = new \Swis\LaravelStaticRequestCache\StaticRequestCache($this->getFilesystemMock());
        $this->assertFalse($staticRequestCache->isEnabled());
    }

    public function testDebugConfigDisabled()
    {
        $this->app['config']->set('static-html-cache.enabled', 'debug');
        $this->app['config']->set('app.debug', false);

        $staticRequestCache = new \Swis\LaravelStaticRequestCache\StaticRequestCache($this->getFilesystemMock());
        $this->assertTrue($staticRequestCache->isEnabled());
    }

    public function testValidRequestIsCached()
    {
        $request = \Illuminate\Http\Request::create('', 'GET');
        $response = $this->getCacheablesResponse();

        $staticRequestCache = new \Swis\LaravelStaticRequestCache\StaticRequestCache($this->getFilesystemMock());
        $this->assertTrue($staticRequestCache->shouldStoreResponse($request, $response));
    }

    public function testResponseIsNotCachedWhenDisabled()
    {
        $request = \Illuminate\Http\Request::create('', 'GET');
        $response = $this->getCacheablesResponse();

        $staticRequestCache = new \Swis\LaravelStaticRequestCache\StaticRequestCache($this->getFilesystemMock());
        $staticRequestCache->disable();

        $this->assertFalse($staticRequestCache->shouldStoreResponse($request, $response));
    }

    public function testPostRequestIsNotCached()
    {
        $request = \Illuminate\Http\Request::create('', 'POST');
        $response = $this->getCacheablesResponse();

        $request->setMethod('POST');

        $staticRequestCache = new \Swis\LaravelStaticRequestCache\StaticRequestCache($this->getFilesystemMock());
        $this->assertFalse($staticRequestCache->shouldStoreResponse($request, $response));
    }

    public function testGetRequestWithQueryIsNotCached()
    {
        $request = \Illuminate\Http\Request::create('', 'GET', ['foo' => 'bar']);
        $request->setMethod('GET');

        $response = $this->getCacheablesResponse();

        $staticRequestCache = new \Swis\LaravelStaticRequestCache\StaticRequestCache($this->getFilesystemMock());
        $this->assertFalse($staticRequestCache->shouldStoreResponse($request, $response));
    }

    public function testNotOkResponseIsNotCached()
    {
        $request = \Illuminate\Http\Request::create('', 'GET');
        $response = $this->getCacheablesResponse();

        $response->setStatusCode(404);

        $staticRequestCache = new \Swis\LaravelStaticRequestCache\StaticRequestCache($this->getFilesystemMock());
        $this->assertFalse($staticRequestCache->shouldStoreResponse($request, $response));
    }

    public function testNonCacheableContentTypesIsNotCached()
    {
        $request = \Illuminate\Http\Request::create('', 'GET');
        $response = $this->getCacheablesResponse();

        $response->header('content-type', 'foo/bar');

        $staticRequestCache = new \Swis\LaravelStaticRequestCache\StaticRequestCache($this->getFilesystemMock());
        $this->assertFalse($staticRequestCache->shouldStoreResponse($request, $response));
    }

    public function testNonCacheableResponseHeadersAreNotCached()
    {
        $request = \Illuminate\Http\Request::create('', 'GET');
        $response = $this->getCacheablesResponse();

        $this->app['config']->set('static-html-cache.non_cacheable_cache_control_values', ['no-store', 'no-cache', 'private']);

        $response->headers->set('Cache-Control', 'no-store');

        $staticRequestCache = new \Swis\LaravelStaticRequestCache\StaticRequestCache($this->getFilesystemMock());
        $this->assertFalse($staticRequestCache->shouldStoreResponse($request, $response));
    }

    public function testCacheableResponseHeadersAreCached()
    {
        $request = \Illuminate\Http\Request::create('', 'GET');
        $response = $this->getCacheablesResponse();

        $this->app['config']->set('static-html-cache.non_cacheable_cache_control_values', ['no-store, private']);

        $response->headers->set('Cache-Control', 'public');

        $staticRequestCache = new \Swis\LaravelStaticRequestCache\StaticRequestCache($this->getFilesystemMock());
        $this->assertTrue($staticRequestCache->shouldStoreResponse($request, $response));
    }

    public function testItStoresHtmlResponseAndSavesAsIndexDotHtmlFile()
    {
        $request = \Illuminate\Http\Request::create('foo/bar', 'GET');
        $response = $this->getCacheablesResponse();
        $response->setContent('Lorem ipsum');

        $filesystemMock = $this->getFilesystemMock();
        $filesystemMock
            ->expects($this->once())
            ->method('put')
            ->with(__DIR__.'/static/html/foo/bar/index.html', 'Lorem ipsum');

        $staticRequestCache = new \Swis\LaravelStaticRequestCache\StaticRequestCache($filesystemMock);
        $staticRequestCache->store($request, $response);
    }

    public function testItStoresJsonResponseAndSavesAsJsonFile()
    {
        $request = \Illuminate\Http\Request::create('foo/bar.json', 'GET');
        $response = $this->getCacheablesResponse();
        $response->header('Content-type', 'application/json');
        $response->setContent('{foo: "bar"}');

        $filesystemMock = $this->getFilesystemMock();
        $filesystemMock
            ->expects($this->once())
            ->method('put')
            ->with(__DIR__.'/static/html/foo/bar.json', '{foo: "bar"}');

        $staticRequestCache = new \Swis\LaravelStaticRequestCache\StaticRequestCache($filesystemMock);
        $staticRequestCache->store($request, $response);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    protected function getCacheablesResponse(): \Illuminate\Http\Response
    {
        $response = new \Illuminate\Http\Response();
        $response->setStatusCode(200);
        $response->header('Content-Type', 'text/html');
        $response->header('Cache-Control', 'public');

        return $response;
    }

    /**
     * @return \Illuminate\Filesystem\Filesystem|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getFilesystemMock()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject|\Illuminate\Filesystem\Filesystem $filesystemMock */
        return $this->getMockBuilder(\Illuminate\Filesystem\Filesystem::class)
            ->setMethods(['isDirectory', 'makeDirectory', 'put'])
            ->getMock();
    }
}
