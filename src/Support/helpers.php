<?php

use AhmedZaky\GlobalState\Contracts\GlobalStateInterface;

if (!function_exists('global_state')) {
    /**
     * Get the global state manager or a specific value.
     *
     * @param string|null $key
     * @param mixed|null $default
     * @return mixed|\AhmedZaky\GlobalState\Contracts\GlobalStateInterface
     */
    function global_state(?string $key = null, mixed $default = null)
    {
        $manager = app(GlobalStateInterface::class);

        if (is_null($key)) {
            return $manager;
        }

        return $manager->get($key, $default);
    }
}
