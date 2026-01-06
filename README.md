# Laravel Global State

A standardized way to manage Global Mutable Application Data in Laravel. This package provides a unified, validated, and cached Global State layer for your Laravel application.

## Purpose

Developers often rely on scattered solutions like settings tables, helpers, env misuse, and configs. This package provides a unified way to handle:
- Settings
- Rates
- Constants
- Flags
- Metadata

## Installation

You can install the package via composer:

```bash
composer require ahmedzaky/laravel-global-state
```

Publish the config and migrations:

```bash
php artisan vendor:publish --provider="AhmedZaky\GlobalState\GlobalStateServiceProvider"
```

Run the migrations:

```bash
php artisan migrate
```

## Usage

### Define Global Keys

Global keys must be explicitly defined, typically in a Service Provider's `boot` method:

```php
use AhmedZaky\GlobalState\Facades\GlobalState;

GlobalState::define([
    'tax_rate' => [
        'type' => 'float',
        'default' => 14,
        'editable' => true,
    ],
    'currency' => [
        'type' => 'string',
        'default' => 'EGP',
    ],
]);
```

### Accessing Global State

#### Getter

```php
// Via Facade
GlobalState::get('tax_rate');

// Via Helper
global('tax_rate');

// Via Helper access as property
global()->tax_rate;
```

#### Setter

```php
// Via Facade
GlobalState::set('tax_rate', 15);

// Via Helper
global()->set('tax_rate', 15);
```

### Resolution Priority

1. **ENV override**: `GLOBAL_STATE_KEY_NAME` (e.g., `GLOBAL_STATE_TAX_RATE`)
2. **Cache**: Cached forever by default (invalidated on update).
3. **Database**: Persistent storage in `global_states` table.
4. **Default value**: Defined in the definition.

### Validation behavior

The package strictly validates types before saving. If a value does not match the defined type, an `InvalidGlobalStateValueException` will be thrown.

Supported types: `string`, `int`, `float`, `bool`, `array`.

## Roadmap

- [ ] UI for managing global state.
- [ ] Multi-tenant support.
- [ ] Scopes (e.g., user-specific or team-specific states).
- [ ] Audit logs for changes.
- [ ] Permissions/Roles for editing keys.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
