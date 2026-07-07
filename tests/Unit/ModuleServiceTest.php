<?php

namespace ElementPack\Tests\Unit;

use ElementPack\Admin\ModuleService;
use PHPUnit\Framework\TestCase;

/**
 * Covers the ModuleService caching/lookup helpers that the perf work touched:
 *  - module.info.php read memoization (is_module_active / has_module_style / has_module_script)
 *  - the per-request option cache (get_modules_option / flush_modules_option_cache)
 *  - get_widget_settings() per-request memoization
 *
 * Uses the real `accordion` module, whose module.info.php declares
 * default_activation => true, has_style => true, has_script => true.
 */
final class ModuleServiceTest extends TestCase
{
    protected function setUp(): void
    {
        __wp_reset_options();
        ModuleService::flush_modules_option_cache();
    }

    public function test_default_activation_is_read_from_module_info_when_option_absent(): void
    {
        // 'accordion' is not in the (empty) options array, so the default applies.
        $this->assertTrue(ModuleService::is_module_active('accordion', []));
    }

    public function test_explicit_option_overrides_module_default(): void
    {
        $this->assertFalse(ModuleService::is_module_active('accordion', ['accordion' => 'off']));
        $this->assertTrue(ModuleService::is_module_active('accordion', ['accordion' => 'on']));
    }

    public function test_unknown_module_is_inactive(): void
    {
        $this->assertNull(ModuleService::is_module_active('this-module-does-not-exist', []));
    }

    public function test_has_module_style_and_script_reflect_module_info(): void
    {
        $this->assertTrue(ModuleService::has_module_style('accordion'));
        $this->assertTrue(ModuleService::has_module_script('accordion'));
    }

    public function test_has_module_style_is_null_for_unknown_module(): void
    {
        $this->assertNull(ModuleService::has_module_style('this-module-does-not-exist'));
        $this->assertNull(ModuleService::has_module_script('this-module-does-not-exist'));
    }

    public function test_module_info_cache_is_stable_across_repeated_calls(): void
    {
        // Guards the memoization: repeated reads must agree with the first read.
        for ($i = 0; $i < 5; $i++) {
            $this->assertTrue(ModuleService::is_module_active('accordion', []));
            $this->assertTrue(ModuleService::has_module_style('accordion'));
        }
    }

    public function test_option_cache_returns_stored_value(): void
    {
        __wp_reset_options(['element_pack_active_modules' => ['accordion' => 'on']]);
        ModuleService::flush_modules_option_cache();

        $this->assertSame(
            ['accordion' => 'on'],
            ModuleService::get_modules_option('element_pack_active_modules')
        );
    }

    public function test_option_cache_shields_a_silent_external_change(): void
    {
        __wp_reset_options(['element_pack_active_modules' => ['accordion' => 'on']]);
        ModuleService::flush_modules_option_cache();

        // Prime the cache.
        ModuleService::get_modules_option('element_pack_active_modules');

        // Change the underlying option WITHOUT firing any action.
        __wp_set_option_silently('element_pack_active_modules', ['accordion' => 'off']);

        // The cached value is intentionally still returned.
        $this->assertSame(
            ['accordion' => 'on'],
            ModuleService::get_modules_option('element_pack_active_modules')
        );

        // After an explicit flush, the fresh value is read.
        ModuleService::flush_modules_option_cache('element_pack_active_modules');
        $this->assertSame(
            ['accordion' => 'off'],
            ModuleService::get_modules_option('element_pack_active_modules')
        );
    }

    public function test_get_widget_settings_passes_three_tier_structure_to_callback(): void
    {
        $captured = null;
        ModuleService::get_widget_settings(function ($settings) use (&$captured) {
            $captured = $settings;
            return $settings;
        });

        $this->assertIsArray($captured);
        $this->assertArrayHasKey('settings_fields', $captured);
        $this->assertArrayHasKey('element_pack_active_modules', $captured['settings_fields']);
        $this->assertArrayHasKey('element_pack_elementor_extend', $captured['settings_fields']);
        $this->assertArrayHasKey('element_pack_third_party_widget', $captured['settings_fields']);

        $names = array_column($captured['settings_fields']['element_pack_active_modules'], 'name');
        $this->assertContains('accordion', $names);
    }

    public function test_get_widget_settings_returns_callback_result(): void
    {
        $result = ModuleService::get_widget_settings(static fn ($settings) => 'sentinel');
        $this->assertSame('sentinel', $result);
    }

    public function test_get_widget_settings_is_memoized_to_an_identical_array(): void
    {
        $first = $second = null;
        ModuleService::get_widget_settings(function ($s) use (&$first) { $first = $s; return null; });
        ModuleService::get_widget_settings(function ($s) use (&$second) { $second = $s; return null; });

        // Same content on every call (the build happens at most once per request).
        $this->assertSame($first, $second);
    }
}
