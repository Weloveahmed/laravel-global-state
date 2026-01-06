<?php

namespace AhmedZaky\GlobalState\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void define(array $definitions)
 * @method static mixed get(string $key, mixed $default = null)
 * @method static void set(string $key, mixed $value)
 * @method static bool isDefined(string $key)
 *
 * @see \AhmedZaky\GlobalState\GlobalState
 */
class GlobalState extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'global-state';
    }
}
