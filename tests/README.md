# Element Pack — PHP unit tests

Fast, database-free unit tests for the plugin's pure-ish PHP logic. They run in
well under a second and need no WordPress install, no MySQL, and no Elementor.

## Run

```bash
composer install          # one-time: pulls PHPUnit into ./vendor (dev only)
composer test             # run everything
./vendor/bin/phpunit --testsuite unit
./vendor/bin/phpunit --filter test_option_cache_is_invalidated_by_update_option   # one test
```

Nothing in this folder (or `vendor/`, `composer.*`, `phpunit.*`) is shipped in the
distributed plugin — `.buildignore` excludes it from `npm run zip`.

## How it works

- `tests/wp-stubs.php` — a minimal in-memory WordPress: an options store whose
  `update_option()`/`add_option()` fire the same `added_option`/`updated_option`
  actions core fires (so cache-invalidation wiring is exercised for real), a
  unified action/filter registry, and passthrough i18n/escaping helpers.
- `tests/bootstrap.php` — defines `ABSPATH` + the `BDTEP_*` constants, installs the
  stubs, then `require`s the **real** plugin source so tests run the actual code
  (not mocks): `admin/module-settings.php` and `includes/element-pack-filters.php`.
- `tests/Unit/*Test.php` — the tests, in the `ElementPack\Tests\Unit` namespace.

## Writing a new test

```php
namespace ElementPack\Tests\Unit;

use ElementPack\Admin\ModuleService;
use PHPUnit\Framework\TestCase;

final class MyThingTest extends TestCase
{
    protected function setUp(): void
    {
        // Shared statics persist across tests in one process — reset them.
        __wp_reset_options();
        ModuleService::flush_modules_option_cache();
    }

    public function test_something(): void
    {
        __wp_reset_options(['element_pack_active_modules' => ['accordion' => 'on']]);
        $this->assertTrue(element_pack_is_widget_enabled('accordion'));
    }
}
```

Reset helpers from the stub layer: `__wp_reset_options()`,
`__wp_set_option_silently()`, `__wp_clear_hook()`.

## Scope

These cover the module enable/disable lookups and asset/option caching
(`ModuleService`, `element_pack_is_*_enabled()`). Code that needs a live
WordPress/Elementor runtime (widget rendering, REST endpoints, the editor) is out
of scope here and would require a separate integration suite (wp-phpunit + MySQL).
