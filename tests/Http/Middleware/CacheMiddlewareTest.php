<?php

namespace Swis\LaravelStaticRequestCache\Tests\Http\Middleware;

use Illuminate\Http\Request;
use RuntimeException;
use Swis\LaravelStaticRequestCache\Http\Middleware\CacheMiddleware;
use Swis\LaravelStaticRequestCache\StaticRequestCache;
use Symfony\Component\HttpFoundation\Response;

class CacheMiddlewareTest extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return ['Swis\LaravelStaticRequestCache\Provider\CacheProvider'];
    }

    public function testHandlesRequest()
    {
        $request = new Request();
        $response = new Response();

        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\LaravelStaticRequestCache\StaticRequestCache $staticRequestCache */
        $staticRequestCache = $this->getMockBuilder(StaticRequestCache::class)
            ->disableOriginalConstructor()
            ->getMock();

        $middleware = new CacheMiddleware($staticRequestCache);
        $result = $middleware->handle($request, function ($req) use ($request, $response) {
            $this->assertEquals($request, $req);

            return $response;
        });

        $this->assertEquals($response, $result);
    }

    public function testResponseIsCachedWhenItShould()
    {
        $request = new Request();
        $response = new Response();

        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\LaravelStaticRequestCache\StaticRequestCache $staticRequestCache */
        $staticRequestCache = $this->getMockBuilder(StaticRequestCache::class)
            ->disableOriginalConstructor()
            ->setMethods(['shouldStoreResponse', 'store'])
            ->getMock();

        $staticRequestCache->expects($this->once())
            ->method('shouldStoreResponse')
            ->with($request, $response)
            ->willReturn(true);

        $staticRequestCache->expects($this->once())
            ->method('store')
            ->with($request, $response);

        $middleware = new CacheMiddleware($staticRequestCache);
        $middleware->terminate($request, $response);
    }

    public function testResponseIsNotCachedWhenItShouldNot()
    {
        $request = new Request();
        $response = new Response();

        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\LaravelStaticRequestCache\StaticRequestCache $staticRequestCache */
        $staticRequestCache = $this->getMockBuilder(StaticRequestCache::class)
            ->disableOriginalConstructor()
            ->setMethods(['shouldStoreResponse', 'store'])
            ->getMock();

        $staticRequestCache->expects($this->once())
            ->method('shouldStoreResponse')
            ->with($request, $response)
            ->willReturn(false);

        $staticRequestCache->expects($this->never())
            ->method('store');

        $middleware = new CacheMiddleware($staticRequestCache);
        $middleware->terminate($request, $response);
    }

    public function testItDoesNotThrowInGracefulMode()
    {
        $this->app['config']->set('static-html-cache.graceful', true);

        $request = new Request();
        $response = new Response();

        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\LaravelStaticRequestCache\StaticRequestCache $staticRequestCache */
        $staticRequestCache = $this->getMockBuilder(StaticRequestCache::class)
            ->disableOriginalConstructor()
            ->setMethods(['shouldStoreResponse', 'store'])
            ->getMock();

        $staticRequestCache->expects($this->once())
            ->method('shouldStoreResponse')
            ->with($request, $response)
            ->willReturn(true);

        $staticRequestCache->expects($this->once())
            ->method('store')
            ->with($request, $response)
            ->willThrowException(new RuntimeException('Directory "/test/" could not be created'));

        $middleware = new CacheMiddleware($staticRequestCache);
        $middleware->terminate($request, $response);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testItThrowsWhenNotInGracefulMode()
    {
        $this->app['config']->set('static-html-cache.graceful', false);

        $request = new Request();
        $response = new Response();

        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\LaravelStaticRequestCache\StaticRequestCache $staticRequestCache */
        $staticRequestCache = $this->getMockBuilder(StaticRequestCache::class)
            ->disableOriginalConstructor()
            ->setMethods(['shouldStoreResponse', 'store'])
            ->getMock();

        $staticRequestCache->expects($this->once())
            ->method('shouldStoreResponse')
            ->with($request, $response)
            ->willReturn(true);

        $staticRequestCache->expects($this->once())
            ->method('store')
            ->with($request, $response)
            ->willThrowException(new RuntimeException('Directory "/test/" could not be created'));

        $middleware = new CacheMiddleware($staticRequestCache);
        $middleware->terminate($request, $response);
    }
}
