<?php

class CacheMiddlewareTest extends Orchestra\Testbench\TestCase {

    protected function getPackageProviders($app)
    {
        return ['Swis\LaravelStaticRequestCache\Provider\CacheProvider'];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:

        // $app['config']->set('database.default', 'testbench');
    }

    public function testEnabledConfig(){
        $middleware = app(\Swis\LaravelStaticRequestCache\Http\Middleware\CacheMiddleware::class);
        $this->app['config']->set('static-html-cache.enabled', true);
        $this->assertTrue($middleware->isEnabled());
    }

    public function testDisabledConfig(){
        $this->app['config']->set('static-html-cache.enabled', false);

        $middleware = app(\Swis\LaravelStaticRequestCache\Http\Middleware\CacheMiddleware::class);
        $this->assertFalse($middleware->isEnabled());
    }

    public function testDebugConfigEnabled(){
        $this->app['config']->set('static-html-cache.enabled', 'debug');
        $this->app['config']->set('app.debug', true);

        $middleware = app(\Swis\LaravelStaticRequestCache\Http\Middleware\CacheMiddleware::class);
        $this->assertFalse($middleware->isEnabled());
    }

    public function testDebugConfigDisabled(){
        $this->app['config']->set('static-html-cache.enabled', 'debug');
        $this->app['config']->set('app.debug', false);

        $middleware = app(\Swis\LaravelStaticRequestCache\Http\Middleware\CacheMiddleware::class);
        $this->assertTrue($middleware->isEnabled());
    }

}