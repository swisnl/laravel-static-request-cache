<?php

namespace Swis\Laravel\StaticRequestCache\Facades;

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
        return \Swis\Laravel\StaticRequestCache\StaticRequestCache::class;
    }
}
