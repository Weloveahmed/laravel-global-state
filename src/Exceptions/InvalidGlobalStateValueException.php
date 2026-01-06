<?php

namespace AhmedZaky\GlobalState\Exceptions;

use Exception;

class InvalidGlobalStateValueException extends Exception
{
    public static function invalidType(string $key, string $expectedType, mixed $actualValue): self
    {
        $actualType = gettype($actualValue);
        return new self("Invalid value for global state key [{$key}]. Expected [{$expectedType}], got [{$actualType}].");
    }

    public static function undefinedKey(string $key): self
    {
        return new self("Global state key [{$key}] is not defined.");
    }
}
