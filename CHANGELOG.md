# Changelog

All notable changes to `swisnl/laravel-static-request-cache` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## Unreleased

* Nothing

## [1.2.0] - 2020-03-06

### Added
* Added support for Laravel 6 [#12](https://github.com/swisnl/laravel-static-request-cache/pull/12).

## [1.1.0] - 2020-01-17

### Changed
* Replaced `RuntimeException` with custom `CacheException` [#11](https://github.com/swisnl/laravel-static-request-cache/pull/11).

## [1.0.0] - 2019-10-07

### Changed
* Changed namespace from `Swis\LaravelStaticRequestCache` to `Swis\Laravel\StaticRequestCache`.

## [0.7.0] - 2019-10-01

### Added
* Added support for Laravel 6 [#9](https://github.com/swisnl/laravel-static-request-cache/pull/9).

## [0.6.1] - 2019-05-23

### Fixed
* URLs containing `index.php` are not cached. [#8](https://github.com/swisnl/laravel-static-request-cache/pull/8).
N.B. This requires a change to your `.htaccess` file for a complete fix.

## [0.6.0] - 2019-03-29

### Added
* Added support for Laravel 5.8 [#7](https://github.com/swisnl/laravel-static-request-cache/pull/7).

### Changed
* Dropped Laravel <5.5 support.
* Dropped PHP <7.1 support.

## [0.5.0] - 2018-10-23

### Added

* Graceful mode (see config for explanation) [#6](https://github.com/swisnl/laravel-static-request-cache/pull/6)

## [0.4.1] - 2018-10-17

### Changed

* Prevent race condition when creating the storage directory [#5](https://github.com/swisnl/laravel-static-request-cache/pull/5)

## [0.4.0] - 2018-09-19

### Added

* Added Laravel 5.6 and 5.7 support [#4](https://github.com/swisnl/laravel-static-request-cache/pull/4)

## [0.3.0] - 2018-04-24

### Added

* Added Facade [#3](https://github.com/swisnl/laravel-static-request-cache/pull/3)

## [0.2.0] - 2018-04-24

### Added

* Look for cache-control values in config in order to enable/disable cache [#2](https://github.com/swisnl/laravel-static-request-cache/pull/2)

## [0.1.0] - 2018-04-20

### Added

* The cacher checks the cache-control headers of the response before caching the results. If the cache-control header is set to 'no-cache' or 'private', the results are not cached. [#1](https://github.com/swisnl/laravel-static-request-cache/pull/1) 
* The cacher can be manually disabled per request; ``app(StaticRequestCache::class)->disable();``. [#1](https://github.com/swisnl/laravel-static-request-cache/pull/1) 
