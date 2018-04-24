# Changelog

All notable changes to `laravel-static-request-cache` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## Unreleased

### Added
- Nothing 

### Deprecated
- Nothing

### Fixed
- Nothing

### Removed
- Nothing

### Security
- Nothing

## 0.3.0

### Added

- Added Facade [#3](https://github.com/swisnl/laravel-static-request-cache/pull/3)

## 0.2.0

### Added

- Look for cache-control values in config in order to enable/disable cache [#2](https://github.com/swisnl/laravel-static-request-cache/pull/2)

## 0.1.0

### Added
- The cacher checks the cache-control headers of the response before caching the results. If the cache-control header is set to 'no-cache' or 'private', the results are not cached. [#1](https://github.com/swisnl/laravel-static-request-cache/pull/1) 
- The cacher can be manually disabled per request; ``app(StaticRequestCache::class)->disable();``. [#1](https://github.com/swisnl/laravel-static-request-cache/pull/1) 
