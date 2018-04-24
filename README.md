# laravel-static-request-cache

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)

## Setup

Add the service provider to the `config/app.php` provider array
```php
    Swis\LaravelStaticRequestCache\Provider\CacheProvider::class,,
```

Then add the middleware to the end of your `Http/Kernel.php` middleware array.
 ```php
protected $middleware = [
    \Swis\LaravelStaticRequestCache\Http\Middleware\CacheMiddleware::class,
];
```

Add the following snippet into your `.htaccess`
```apacheconfig
# Rewrite to html cache if it exists and the request is off a static page
# (no url query params and only get requests)
RewriteCond %{REQUEST_METHOD} GET
RewriteCond %{QUERY_STRING} !.*=.*
RewriteCond %{DOCUMENT_ROOT}/static/html%{REQUEST_URI} -f
RewriteRule ^(.*)$  /static/html%{REQUEST_URI} [L]

RewriteCond %{REQUEST_METHOD} GET
RewriteCond %{QUERY_STRING} !.*=.*
RewriteCond %{DOCUMENT_ROOT}/static/html%{REQUEST_URI}/index.html -f
RewriteRule ^(.*)$ /static/html%{REQUEST_URI}/index.html [L]
```

## Disabling 

If you want to disable the cache for some reason (the content might be dynamic), you can use the StaticRequestCache singleton in the IoC:

```php
public function __construct(StaticRequestCache $staticRequestCache)
    {
        $this->staticRequestCache = $staticRequestCache;
        $this->staticRequestCache->disable();
    }
```

Please note that this package also checks for Cache-control headers and caches accordingly. You can change this behaviour in the config by editing `non_cacheable_cache_control_values`.

## Clear the files
To clear all the files manually you can use an artisan task.
```bash
php artisan static-html-cache:clear
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

Based on [mscharl/laravel-static-html-cache](https://github.com/mscharl/laravel-static-html-cache). Added configuration for setting cachable content-type.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email security@swis.nl instead of using the issue tracker.

## Credits

- [Björn Brala][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/swisnl/laravel-static-request-cache.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/swisnl/laravel-static-request-cache/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/swisnl/laravel-static-request-cache.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/swisnl/laravel-static-request-cache.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/swisnl/laravel-static-request-cache.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/swisnl/laravel-static-request-cache
[link-travis]: https://travis-ci.org/swisnl/laravel-static-request-cache
[link-scrutinizer]: https://scrutinizer-ci.com/g/swisnl/laravel-static-request-cache/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/swisnl/laravel-static-request-cache
[link-downloads]: https://packagist.org/packages/swisnl/laravel-static-request-cache
[link-author]: https://github.com/swisnl
[link-contributors]: ../../contributors
