<?php

namespace Swis\LaravelStaticRequestCache\Tests\Commands;

use Illuminate\Foundation\Application;

class ClearStaticCacheTest extends \Orchestra\Testbench\TestCase
{
    /**
     * @var string
     */
    protected $publicDir;

    public function setUp()
    {
        parent::setUp();
        $this->publicDir = \dirname(__DIR__).'/_public';
    }

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
            return $this->publicDir;
        };

        $app['config']->set('static-html-cache.cachable_mimetypes', ['text/html', 'application/json']);
    }

    public function testEnabledConfig()
    {
        $dir = $this->publicDir.'/'.$this->app['config']->get('static-html-cache.cache_path_prefix');
        $file = $dir.'/index.html';
        @mkdir($dir, 0777, true);
        file_put_contents($file, 'Foo bar');

        $this->assertTrue(is_file($file));

        if (version_compare(Application::VERSION, '5.7.0', '>=')) {
            $this->artisan('static-html-cache:clear')
                ->expectsOutput("Clearing `{$dir}`…")
                ->expectsOutput('Static caches cleared!')
                ->assertExitCode(0);
        } else {
            $this->assertSame(0, $this->artisan('static-html-cache:clear'));
        }

        $this->assertFalse(is_file($file));
    }
}
