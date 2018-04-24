<?php

namespace Swis\LaravelStaticRequestCache\Facades;

use Illuminate\Support\Facades\Facade;

class StaticRequestCache extends Facade
{
    /**
     * Get the binding in the IoC container
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Swis\LaravelStaticRequestCache\StaticRequestCache::class;
    }
}
