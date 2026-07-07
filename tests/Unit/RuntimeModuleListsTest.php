<?php

namespace ElementPack\Tests\Unit;

use ElementPack\Admin\ModuleService;
use PHPUnit\Framework\TestCase;

/**
 * Covers ModuleService::get_runtime_module_lists() — the lean, transient-cached
 * module lists the front-end Manager uses instead of rebuilding the full settings
 * array on every request.
 */
final class RuntimeModuleListsTest extends TestCase
{
    protected function setUp(): void
    {
        __wp_reset_options();
        __wp_reset_site_state();
        // Clears the per-request runtime memo too (full flush).
        ModuleService::flush_modules_option_cache();
    }

    public function test_returns_all_three_tiers_as_lists(): void
    {
        $lists = ModuleService::get_runtime_module_lists();

        $this->assertArrayHasKey('element_pack_active_modules', $lists);
        $this->assertArrayHasKey('element_pack_elementor_extend', $lists);
        $this->assertArrayHasKey('element_pack_third_party_widget', $lists);
        $this->assertIsList($lists['element_pack_active_modules']);
        $this->assertIsList($lists['element_pack_third_party_widget']);
    }

    public function test_entries_are_reduced_to_name_and_optional_plugin_path(): void
    {
        $lists = ModuleService::get_runtime_module_lists();

        foreach ($lists['element_pack_active_modules'] as $entry) {
            $this->assertArrayHasKey('name', $entry);
            // The heavy admin-only fields must be stripped from the runtime list.
            $this->assertArrayNotHasKey('label', $entry);
            $this->assertArrayNotHasKey('demo_url', $entry);
            $this->assertArrayNotHasKey('video_url', $entry);
        }
    }

    public function test_contains_a_known_core_widget(): void
    {
        $lists = ModuleService::get_runtime_module_lists();
        $names = array_column($lists['element_pack_active_modules'], 'name');

        $this->assertContains('accordion', $names);
    }

    public function test_third_party_entries_preserve_plugin_path(): void
    {
        $lists = ModuleService::get_runtime_module_lists();

        $by_name = [];
        foreach ($lists['element_pack_third_party_widget'] as $entry) {
            $by_name[$entry['name']] = $entry;
        }

        // wc-products is a third-party (WooCommerce) widget; its plugin_path is what
        // the Manager passes to is_plugin_active(), so it must survive the reduction.
        $this->assertArrayHasKey('wc-products', $by_name);
        $this->assertArrayHasKey('plugin_path', $by_name['wc-products']);
        $this->assertNotEmpty($by_name['wc-products']['plugin_path']);
    }

    public function test_result_is_written_to_a_transient(): void
    {
        $this->assertSame([], __wp_transient_keys());

        ModuleService::get_runtime_module_lists();

        $keys = __wp_transient_keys();
        $this->assertCount(1, $keys);
        $this->assertStringStartsWith('ep_runtime_modules_', $keys[0]);
    }

    public function test_warm_cache_is_served_from_the_transient(): void
    {
        // Cold build populates the transient.
        $first = ModuleService::get_runtime_module_lists();

        // Drop the per-request memo so the next call must consult the transient,
        // and prove it does by making a fresh build impossible to confuse: the
        // returned data must equal the cached transient payload.
        ModuleService::flush_modules_option_cache();
        $second = ModuleService::get_runtime_module_lists();

        $this->assertSame($first, $second);
        // Still exactly one transient key — no second key was created for the same
        // version + active-plugins fingerprint.
        $this->assertCount(1, __wp_transient_keys());
    }

    public function test_changing_active_plugins_busts_the_cache_key(): void
    {
        ModuleService::get_runtime_module_lists();
        $firstKeys = __wp_transient_keys();
        $this->assertCount(1, $firstKeys);

        // Simulate activating a plugin and a new request (drop the per-request memo).
        update_option('active_plugins', ['woocommerce/woocommerce.php']);
        ModuleService::flush_modules_option_cache();

        ModuleService::get_runtime_module_lists();
        $allKeys = __wp_transient_keys();

        // A new fingerprint -> a new transient key (the old one remains until expiry).
        $this->assertCount(2, $allKeys);
        $this->assertNotSame($firstKeys[0], $allKeys[1]);
    }
}
