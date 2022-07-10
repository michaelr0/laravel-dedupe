<?php

namespace Michaelr0\LaravelDedupe\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Michaelr0\LaravelDedupe\Skeleton\SkeletonClass
 */
class LaravelDedupeFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-dedupe';
    }
}
