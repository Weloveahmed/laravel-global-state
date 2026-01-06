<?php

namespace AhmedZaky\GlobalState\Contracts;

interface GlobalStateInterface
{
    /**
     * Define the global state keys and their metadata.
     *
     * @param array $definitions
     * @return void
     */
    public function define(array $definitions): void;

    /**
     * Get the value of a global state key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Set the value of a global state key.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     *
     * @throws \AhmedZaky\GlobalState\Exceptions\InvalidGlobalStateValueException
     */
    public function set(string $key, mixed $value): void;

    /**
     * Check if a global state key is defined.
     *
     * @param string $key
     * @return bool
     */
    public function isDefined(string $key): bool;
}
