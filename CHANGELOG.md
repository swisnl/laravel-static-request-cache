# Changelog

All notable changes to `swisnl/laravel-static-request-cache` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## Unreleased

### Added
* Nothing 

### Deprecated
* Nothing

### Fixed
* Nothing

### Removed
* Nothing

### Security
* Nothing

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
