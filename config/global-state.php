<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Cache Key
    |--------------------------------------------------------------------------
    |
    | This key will be used to store and retrieve the unified global state cache.
    |
    */
    'cache_key' => 'laravel_global_state',

    /*
    |--------------------------------------------------------------------------
    | Database Table
    |--------------------------------------------------------------------------
    |
    | The table name where global states are stored.
    |
    */
    'table' => 'global_states',

    /*
    |--------------------------------------------------------------------------
    | Cache Duration
    |--------------------------------------------------------------------------
    |
    | Duration in seconds for caching the global state. Set to null for forever.
    |
    */
    'cache_ttl' => null,
];
