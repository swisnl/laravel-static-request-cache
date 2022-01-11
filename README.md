# Laravel static request cache

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Buy us a tree][ico-treeware]][link-treeware]
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]
[![Made by SWIS][ico-swis]][link-swis]

## Install

Via Composer

``` bash
$ composer require swisnl/laravel-static-request-cache
```

## Setup

Add the middleware to the end of your `Http/Kernel.php` middleware array.
 ```php
protected $middleware = [
    \Swis\Laravel\StaticRequestCache\Http\Middleware\CacheMiddleware::class,
];
```

Add the following snippet into your `.htaccess`
```apacheconfig
# Rewrite to html cache if it exists and the request is for a static page
# (no url query params and only get requests)
RewriteCond %{REQUEST_METHOD} GET
RewriteCond %{QUERY_STRING} !.*=.*
RewriteCond %{DOCUMENT_ROOT}/static/html%{REQUEST_URI} -f
RewriteRule ^(.*)$  /static/html%{REQUEST_URI} [L]

RewriteCond %{REQUEST_METHOD} GET
RewriteCond %{QUERY_STRING} !.*=.*
RewriteCond %{REQUEST_URI} !index.php
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

Or use the Facade:

```php
StaticRequestCache::disable();
```

Please note that this package also checks for Cache-control headers and caches accordingly. You can change this behaviour in the config by editing `non_cacheable_cache_control_values`.

## Clear the files
To clear all the files manually you can use an artisan command.
``` bash
$ php artisan static-html-cache:clear
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email security@swis.nl instead of using the issue tracker.

## Credits

Based on [mscharl/laravel-static-html-cache](https://github.com/mscharl/laravel-static-html-cache). Added configuration for setting cacheable content-type and non-cacheable cache control values.

- [Björn Brala](https://github.com/bbrala)
- [Jasper Zonneveld](https://github.com/JaZo)
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

This package is [Treeware](https://treeware.earth). If you use it in production, then we ask that you [**buy the world a tree**][link-treeware] to thank us for our work. By contributing to the Treeware forest you’ll be creating employment for local families and restoring wildlife habitats.

## SWIS :heart: Open Source

[SWIS][link-swis] is a web agency from Leiden, the Netherlands. We love working with open source software.

[ico-version]: https://img.shields.io/packagist/v/swisnl/laravel-static-request-cache.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-treeware]: https://img.shields.io/badge/Treeware-%F0%9F%8C%B3-lightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/swisnl/laravel-static-request-cache/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/swisnl/laravel-static-request-cache.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/swisnl/laravel-static-request-cache.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/swisnl/laravel-static-request-cache.svg?style=flat-square
[ico-swis]: https://img.shields.io/badge/%F0%9F%9A%80-made%20by%20SWIS-%230737A9.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/swisnl/laravel-static-request-cache
[link-travis]: https://travis-ci.org/swisnl/laravel-static-request-cache
[link-scrutinizer]: https://scrutinizer-ci.com/g/swisnl/laravel-static-request-cache/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/swisnl/laravel-static-request-cache
[link-downloads]: https://packagist.org/packages/swisnl/laravel-static-request-cache
[link-treeware]: https://plant.treeware.earth/swisnl/laravel-static-request-cache
[link-author]: https://github.com/swisnl
[link-contributors]: ../../contributors
[link-swis]: https://www.swis.nl
