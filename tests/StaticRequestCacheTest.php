<?php

class StaticRequestCacheTest extends Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return ['Swis\LaravelStaticRequestCache\Provider\CacheProvider'];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:

        // $app['config']->set('database.default', 'testbench');
    }

    public function testEnabledConfig()
    {
        $staticRequestCache = app(\Swis\LaravelStaticRequestCache\StaticRequestCache::class);
        $this->app['config']->set('static-html-cache.enabled', true);
        $this->assertTrue($staticRequestCache->isEnabled());
    }

    public function testDisabledConfig()
    {
        $this->app['config']->set('static-html-cache.enabled', false);

        $staticRequestCache = app(\Swis\LaravelStaticRequestCache\StaticRequestCache::class);
        $this->assertFalse($staticRequestCache->isEnabled());
    }

    public function testDebugConfigEnabled()
    {
        $this->app['config']->set('static-html-cache.enabled', 'debug');
        $this->app['config']->set('app.debug', true);

        $staticRequestCache = app(\Swis\LaravelStaticRequestCache\StaticRequestCache::class);
        $this->assertFalse($staticRequestCache->isEnabled());
    }

    public function testDebugConfigDisabled()
    {
        $this->app['config']->set('static-html-cache.enabled', 'debug');
        $this->app['config']->set('app.debug', false);

        $staticRequestCache = app(\Swis\LaravelStaticRequestCache\StaticRequestCache::class);
        $this->assertTrue($staticRequestCache->isEnabled());
    }

    public function testValidRequestIsCached()
    {
        $this->app['config']->set('static-html-cache.cachable_mimetypes', ['text/html']);

        $request = \Illuminate\Http\Request::create('', 'GET');
        $response = $this->getCacheablesResponse();

        $staticRequestCache = app(\Swis\LaravelStaticRequestCache\StaticRequestCache::class);
        $this->assertTrue($staticRequestCache->shouldStoreResponse($request, $response));
    }

    public function testResponseIsNotCachedWhenDisabled()
    {
        $request = \Illuminate\Http\Request::create('', 'GET');
        $response = $this->getCacheablesResponse();

        $staticRequestCache = app(\Swis\LaravelStaticRequestCache\StaticRequestCache::class);
        $staticRequestCache->disable();

        $this->assertFalse($staticRequestCache->shouldStoreResponse($request, $response));
    }

    public function testPostRequestIsNotCached()
    {
        $request = \Illuminate\Http\Request::create('', 'POST');
        $response = $this->getCacheablesResponse();

        $request->setMethod('POST');

        $staticRequestCache = app(\Swis\LaravelStaticRequestCache\StaticRequestCache::class);
        $this->assertFalse($staticRequestCache->shouldStoreResponse($request, $response));
    }

    public function testGetRequestWithQueryIsNotCached()
    {
        $request = \Illuminate\Http\Request::create('', 'GET', ['foo' => 'bar']);
        $request->setMethod('GET');

        $response = $this->getCacheablesResponse();

        $staticRequestCache = app(\Swis\LaravelStaticRequestCache\StaticRequestCache::class);
        $this->assertFalse($staticRequestCache->shouldStoreResponse($request, $response));
    }

    public function testNotOkResponseIsNotCached()
    {
        $request = \Illuminate\Http\Request::create('', 'GET');
        $response = $this->getCacheablesResponse();

        $response->setStatusCode(404);

        $staticRequestCache = app(\Swis\LaravelStaticRequestCache\StaticRequestCache::class);
        $this->assertFalse($staticRequestCache->shouldStoreResponse($request, $response));
    }

    public function testNotCachableContentTypesIsNotCached()
    {
        $request = \Illuminate\Http\Request::create('', 'GET');
        $response = $this->getCacheablesResponse();

        $response->header('content-type', 'foo/bar');

        $staticRequestCache = app(\Swis\LaravelStaticRequestCache\StaticRequestCache::class);
        $this->assertFalse($staticRequestCache->shouldStoreResponse($request, $response));
    }

    public function testItStoresResponse()
    {
        $request = \Illuminate\Http\Request::create('foo/bar', 'GET');
        $response = $this->getCacheablesResponse();
        $response->setContent('Lorem ipsum');

        $filesystemMock = Mockery::mock(\Illuminate\Filesystem\Filesystem::class);
        $filesystemMock->shouldReceive('isDirectory')->once();
        $filesystemMock->shouldReceive('makeDirectory')->once();
        $filesystemMock->shouldReceive('put')->once()->withArgs(
            function ($path, $content) {
                return ends_with($path, 'static/html/foo/bar/index.html') && $content === 'Lorem ipsum';
            }
        );

        app()->instance(\Illuminate\Filesystem\Filesystem::class, $filesystemMock);

        $staticRequestCache = app(\Swis\LaravelStaticRequestCache\StaticRequestCache::class);
        $staticRequestCache->store($request, $response);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    protected function getCacheablesResponse(): \Illuminate\Http\Response
    {
        $response = new \Illuminate\Http\Response();
        $response->setStatusCode(200);
        $response->header('content-type', 'text/html');

        return $response;
    }
}
