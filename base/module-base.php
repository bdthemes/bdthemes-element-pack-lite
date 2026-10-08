<?php

namespace ElementPack\Base;

use Elementor\Widget_Base;
use ElementPack\Element_Pack_Loader;

if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly.
}

abstract class Module_Base extends Widget_Base {

    public function get_style_depends() {

        if ( method_exists($this, '_get_style_depends') ) {
            if ( $this->ep_is_edit_mode() ) {
                return ['ep-all-styles'];
            }
            return $this->_get_style_depends();
        }
        // UIkit and the helper CSS no longer load on every page, so a widget that
        // declares no style handle of its own still has to ask for them.
        return ['bdt-uikit', 'ep-helper'];
    }

    public function get_script_depends() {
        return ['bdt-uikit'];
    }

    protected function ep_is_edit_mode() {

        if ( Element_Pack_Loader::elementor()->preview->is_preview_mode() || Element_Pack_Loader::elementor()->editor->is_edit_mode() ) {
            return true;
        }

        return false;
    }
}

