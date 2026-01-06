<?php

namespace AhmedZaky\GlobalState;

use AhmedZaky\GlobalState\Contracts\GlobalStateInterface;
use AhmedZaky\GlobalState\Exceptions\InvalidGlobalStateValueException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class GlobalState implements GlobalStateInterface
{
    /**
     * Cache for the definitions.
     *
     * @var array
     */
    protected array $definitions = [];

    /**
     * Cache for resolved values.
     *
     * @var array
     */
    protected array $resolvedValues = [];

    /**
     * The cache key for global state.
     *
     * @var string
     */
    protected string $cacheKey;

    /**
     * The table name for global state.
     *
     * @var string
     */
    protected string $table;

    /**
     * Create a new GlobalState instance.
     */
    public function __construct()
    {
        $this->cacheKey = config('global-state.cache_key', 'laravel_global_state');
        $this->table = config('global-state.table', 'global_states');
    }

    /**
     * {@inheritdoc}
     */
    public function define(array $definitions): void
    {
        foreach ($definitions as $key => $meta) {
            $this->definitions[$key] = [
                'type' => $meta['type'] ?? 'string',
                'default' => $meta['default'] ?? null,
                'editable' => $meta['editable'] ?? true,
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if (!$this->isDefined($key)) {
            return $default;
        }

        if (array_key_exists($key, $this->resolvedValues)) {
            return $this->resolvedValues[$key];
        }

        // 1. ENV override
        $envKey = 'GLOBAL_STATE_' . strtoupper($key);
        if (($envValue = env($envKey)) !== null) {
            return $this->resolvedValues[$key] = $this->castValue($key, $envValue);
        }

        // 2. Cache
        $allCached = $this->getCachedStates();
        if (array_key_exists($key, $allCached)) {
            return $this->resolvedValues[$key] = $this->castValue($key, $allCached[$key]);
        }

        // 3. Database
        $dbValue = DB::table($this->table)->where('key', $key)->value('value');
        if ($dbValue !== null) {
            $value = json_decode($dbValue, true);
            $this->updateCache($key, $value);
            return $this->resolvedValues[$key] = $this->castValue($key, $value);
        }

        // 4. Default value
        return $this->resolvedValues[$key] = $this->definitions[$key]['default'];
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $key, mixed $value): void
    {
        if (!$this->isDefined($key)) {
            throw InvalidGlobalStateValueException::undefinedKey($key);
        }

        $definition = $this->definitions[$key];

        if (!$definition['editable']) {
            throw new \Exception("Global state key [{$key}] is not editable.");
        }

        $this->validateType($key, $value);

        DB::table($this->table)->updateOrInsert(
            ['key' => $key],
            [
                'value' => json_encode($value),
                'updated_at' => now(),
            ]
        );

        $this->updateCache($key, $value);
        $this->resolvedValues[$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function isDefined(string $key): bool
    {
        return isset($this->definitions[$key]);
    }

    /**
     * Validate the type of the value based on definition.
     */
    protected function validateType(string $key, mixed $value): void
    {
        $expectedType = $this->definitions[$key]['type'];
        $actualType = gettype($value);

        $valid = match ($expectedType) {
            'string' => is_string($value),
            'int', 'integer' => is_int($value),
            'float', 'double' => is_float($value) || is_int($value),
            'bool', 'boolean' => is_bool($value),
            'array', 'json' => is_array($value),
            default => true,
        };

        if (!$valid) {
            throw InvalidGlobalStateValueException::invalidType($key, $expectedType, $value);
        }
    }

    /**
     * Cast the value to the defined type.
     */
    protected function castValue(string $key, mixed $value): mixed
    {
        $type = $this->definitions[$key]['type'];

        return match ($type) {
            'string' => (string) $value,
            'int', 'integer' => (int) $value,
            'float', 'double' => (float) $value,
            'bool', 'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'array', 'json' => is_string($value) ? json_decode($value, true) : $value,
            default => $value,
        };
    }

    /**
     * Get all cached states.
     */
    protected function getCachedStates(): array
    {
        return Cache::get($this->cacheKey, []);
    }

    /**
     * Update the cache for a specific key.
     */
    protected function updateCache(string $key, mixed $value): void
    {
        $cached = $this->getCachedStates();
        $cached[$key] = $value;

        $ttl = config('global-state.cache_ttl');
        if ($ttl === null) {
            Cache::forever($this->cacheKey, $cached);
        } else {
            Cache::put($this->cacheKey, $cached, $ttl);
        }
    }

    /**
     * Magic getter for easy access.
     */
    public function __get(string $key)
    {
        return $this->get($key);
    }
}
