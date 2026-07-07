<?php

namespace ElementPack\Tests\Unit;

use ElementPack\Admin\ModuleService;
use PHPUnit\Framework\TestCase;

/**
 * Covers element_pack_is_widget_enabled() / _extend_enabled() / _third_party_enabled():
 *  - default-activation fallback and explicit on/off
 *  - the filter still overrides the result (has_filter short-circuit stays correct)
 *  - the per-request option cache is invalidated through the real updated_option wiring
 *    registered in includes/element-pack-filters.php
 */
final class EnabledHelpersTest extends TestCase
{
    protected function setUp(): void
    {
        __wp_reset_options();
        ModuleService::flush_modules_option_cache();
        __wp_clear_hook('elementpack/widget/accordion');
        __wp_clear_hook('elementpack/extend/tooltip');
    }

    protected function tearDown(): void
    {
        __wp_clear_hook('elementpack/widget/accordion');
        __wp_clear_hook('elementpack/extend/tooltip');
    }

    public function test_widget_enabled_when_option_is_on(): void
    {
        __wp_reset_options(['element_pack_active_modules' => ['accordion' => 'on']]);
        $this->assertTrue(element_pack_is_widget_enabled('accordion'));
    }

    public function test_widget_disabled_when_option_is_off(): void
    {
        __wp_reset_options(['element_pack_active_modules' => ['accordion' => 'off']]);
        $this->assertNull(element_pack_is_widget_enabled('accordion'));
    }

    public function test_widget_uses_module_default_when_option_missing(): void
    {
        // accordion's module.info.php defaults to active.
        __wp_reset_options(['element_pack_active_modules' => []]);
        $this->assertTrue(element_pack_is_widget_enabled('accordion'));
    }

    public function test_returns_strict_true_when_no_filter_is_registered(): void
    {
        // Guards the has_filter() short-circuit: the contract is a strict `true`,
        // not merely a truthy value.
        __wp_reset_options(['element_pack_active_modules' => ['accordion' => 'on']]);
        $this->assertSame(true, element_pack_is_widget_enabled('accordion'));
    }

    public function test_filter_can_still_disable_an_enabled_widget(): void
    {
        __wp_reset_options(['element_pack_active_modules' => ['accordion' => 'on']]);
        add_filter('elementpack/widget/accordion', static fn ($enabled) => false);

        $this->assertFalse(element_pack_is_widget_enabled('accordion'));
    }

    public function test_explicit_options_argument_bypasses_the_cache(): void
    {
        __wp_reset_options(['element_pack_active_modules' => ['accordion' => 'off']]);
        // Passing options explicitly must take precedence over the stored option.
        $this->assertTrue(element_pack_is_widget_enabled('accordion', ['accordion' => 'on']));
    }

    public function test_option_cache_is_invalidated_by_update_option(): void
    {
        __wp_reset_options(['element_pack_active_modules' => ['accordion' => 'on']]);
        ModuleService::flush_modules_option_cache();

        // Prime the cache via the helper.
        $this->assertTrue(element_pack_is_widget_enabled('accordion'));

        // A real settings save goes through update_option(), which fires the
        // updated_option action that element-pack-filters.php hooks to flush the cache.
        update_option('element_pack_active_modules', ['accordion' => 'off']);

        $this->assertNull(element_pack_is_widget_enabled('accordion'));
    }

    public function test_extend_helper_respects_explicit_option(): void
    {
        $this->assertTrue(element_pack_is_extend_enabled('tooltip', ['tooltip' => 'on']));
        $this->assertNull(element_pack_is_extend_enabled('tooltip', ['tooltip' => 'off']));
    }

    public function test_third_party_helper_respects_explicit_option(): void
    {
        $this->assertTrue(element_pack_is_third_party_enabled('wc-products', ['wc-products' => 'on']));
        $this->assertNull(element_pack_is_third_party_enabled('wc-products', ['wc-products' => 'off']));
    }
}
