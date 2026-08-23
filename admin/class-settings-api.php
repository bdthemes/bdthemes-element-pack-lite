<?php

use ElementPack\Admin\AssetMinifier\Asset_Minifier;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if (!class_exists('ElementPack_Settings_API')) :

    class ElementPack_Settings_API {

        /**
         * settings sections array
         *
         * @var array
         */
        protected $settings_sections = array();

        /**
         * Settings fields array
         *
         * @var array
         */
        protected $settings_fields = array();

        public function __construct() {
            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

            add_action('wp_ajax_element_pack_settings_save', [$this, "element_pack_settings_save"]);
        }

        /**
         * Enqueue scripts and styles
         */
        function admin_enqueue_scripts() {
            wp_enqueue_script('jquery');
        }

        /**
         * Set settings sections
         *
         * @param array   $sections setting sections array
         */
        function set_sections($sections) {
            $this->settings_sections = $sections;

            return $this;
        }

        /**
         * Add a single section
         *
         * @param array   $section
         */
        function add_section($section) {
            $this->settings_sections[] = $section;

            return $this;
        }

        /**
         * Set settings fields
         *
         * @param array   $fields settings fields array
         */
        function set_fields($fields) {
            $this->settings_fields = $fields;

            return $this;
        }

        function add_field($section, $field) {
            $defaults = array(
                'name'  => '',
                'label' => '',
                'desc'  => '',
                'type'  => 'text'
            );

            $arg = wp_parse_args($field, $defaults);
            $this->settings_fields[$section][] = $arg;

            return $this;
        }

        function do_settings_sections($page) {
			global $wp_settings_sections, $wp_settings_fields;

			if (!isset($wp_settings_sections[$page])) {
				return;
			}

			$matched_height = ' bdt-grid bdt-height-match="target: > div > .ep-option-item-inner"';
			$data_settings = '';

			foreach ((array) $wp_settings_sections[$page] as $section) {

				if ($section['id'] == 'element_pack_api_settings') {
					$section_class = ' bdt-grid-medium bdt-child-width-1-3@xl';
				} elseif ($section['id'] == 'element_pack_other_settings') {
					// $data_settings = $matched_height;
					$section_class = ' bdt-grid-medium bdt-child-width-1-3@xl';
				} else {
					$section_class = ' bdt-grid-small bdt-child-width-1-3@xl';
				}



				if ($section['callback']) {
					call_user_func($section['callback'], $section);
				}

				if (!isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section['id']])) {
					continue;
				}
				echo '<div class="ep-options" role="presentation" ' . esc_attr($data_settings) . '>';

				echo '<p class="ep-no-result bdt-text-center bdt-width-1-1 bdt-margin-small-top bdt-padding bdt-h4">' . esc_html__('Ops! Your Searched widget not found! Do you have any idea? If yes, ', 'bdthemes-element-pack-lite') . '<a href="https://feedback.elementpack.pro/b/3v2gg80n/feature-requests/idea/new" target="_blank">' . esc_html__('Submit here', 'bdthemes-element-pack-lite') . '</a></p>';

				$this->do_settings_fields($page, $section['id']);

				echo '</div>';
			}
		}


        function do_settings_fields($page, $section) {
            global $wp_settings_fields;

            if (!isset($wp_settings_fields[$page][$section])) {
                return;
            }


            foreach ((array) $wp_settings_fields[$page][$section] as $field) {
                $class = '';

                if (!empty($field['args']['class'])) {
                    $class .= ' ' . $field['args']['class'];
                }

                if (!empty($field['args']['widget_type'])) {
                    $class .= ' ep-widget-' . $field['args']['widget_type'];
                }

                if (!empty($field['args']['widget_type']) && 'pro' == $field['args']['widget_type'] && true !== element_pack_pro_activated()) {
					$class .= ' ep-pro-inactive';
				}

                $used_widgets = self::get_used_widgets_obj();
                $widget_name = 'bdt-' . str_replace(' ', '-', strtolower($field['args']['id']));
                $used_widgets_count = 0;

                if (isset($used_widgets)) {
                    $used_widgets_count = (in_array($widget_name, array_keys($used_widgets)) ? $used_widgets[$widget_name] : 0);
                    if ($used_widgets_count === 0) {
                        $widget_name  = str_replace('_', '-', $widget_name);
                        $used_widgets_count = (in_array($widget_name, array_keys($used_widgets)) ? $used_widgets[$widget_name] : 0);
                    }
                }

                $widget_used_status = ' ep-used';
                if ($used_widgets_count === 0) {
                    $widget_used_status = ' ep-unused';
                }



                $widget_type  = isset($field['args']['widget_type']) ? $field['args']['widget_type'] : '';
                $content_type = isset($field['args']['content_type']) ? $field['args']['content_type'] : '';
                $widget_label = isset($field['args']['name']) ? strtolower($field['args']['name']) : '';

                $tooltip = '';
                if ('pro' === $widget_type && true !== element_pack_pro_activated()) {
                    $tooltip = __('Pro widget only works with Pro version.', 'bdthemes-element-pack-lite');
                }

                printf(
                    '<div class="%1$s" data-widget-type="%2$s" data-content-type="%3$s" data-widget-name="%4$s"%5$s>',
                    esc_attr(trim('ep-option-item ' . $class . ' ' . $widget_used_status)),
                    esc_attr($widget_type),
                    esc_attr($content_type . $widget_used_status),
                    esc_attr($widget_label),
                    '' === $tooltip ? '' : ' bdt-tooltip="' . esc_attr($tooltip) . '"'
                );




                call_user_func($field['callback'], $field['args']);



                echo '</div>';
            }
        }

        /**
         * Initialize and registers the settings sections and fileds to WordPress
         *
         * Usually this should be called at `admin_init` hook.
         *
         * This function gets the initiated settings sections and fields. Then
         * registers them to WordPress and ready for use.
         */
        function admin_init() {
            //register settings sections
            foreach ($this->settings_sections as $section) {
                if (false == get_option($section['id'])) {
                    add_option($section['id']);
                }

                if (isset($section['desc']) && !empty($section['desc'])) {
                    $section['desc'] = '<div class="inside">' . $section['desc'] . '</div>';
                    $callback = function () use ($section) {
                        echo wp_kses_post( str_replace('"', '\"', $section['desc']) );
                    };
                } else if (isset($section['callback'])) {
                    $callback = $section['callback'];
                } else {
                    $callback = null;
                }

                add_settings_section($section['id'], $section['title'], $callback, $section['id']);
            }

            //register settings fields
            foreach ($this->settings_fields as $section => $field) {
                foreach ($field as $option) {

                    $name = $option['name'];
                    $type = isset($option['type']) ? $option['type'] : 'text';
                    $label = isset($option['label']) ? $option['label'] : '';
                    $callback = isset($option['callback']) ? $option['callback'] : array($this, 'callback_' . $type);

                    $args = array(
                        'id'                => $name,
                        'class'             => isset($option['class']) ? 'ep-' . $name . ' ' . $option['class'] : 'ep-' . $name,
                        'label_for'         => "ep-{$section}[{$name}]",
                        'desc'              => isset($option['desc']) ? $option['desc'] : '',
                        'name'              => $label,
                        'section'           => $section,
                        'size'              => isset($option['size']) ? $option['size'] : null,
                        'options'           => isset($option['options']) ? $option['options'] : '',
                        'std'               => isset($option['default']) ? $option['default'] : '',
                        'sanitize_callback' => isset($option['sanitize_callback']) ? $option['sanitize_callback'] : '',
                        'type'              => $type,
                        'placeholder'       => isset($option['placeholder']) ? $option['placeholder'] : '',
                        'min'               => isset($option['min']) ? $option['min'] : '',
                        'max'               => isset($option['max']) ? $option['max'] : '',
                        'step'              => isset($option['step']) ? $option['step'] : '',
                        'plugin_name'       => !empty($option['plugin_name']) ? $option['plugin_name'] : null,
                        'plugin_path'       => !empty($option['plugin_path']) ? $option['plugin_path'] : null,
                        'paid'              => !empty($option['paid']) ? $option['paid'] : null,
                        'widget_type'       => !empty($option['widget_type']) ? $option['widget_type'] : null,
                        'content_type'      => !empty($option['content_type']) ? $option['content_type'] : null,
                        'demo_url'          => !empty($option['demo_url']) ? $option['demo_url'] : null,
                        'video_url'         => !empty($option['video_url']) ? $option['video_url'] : null,
                    );

                    add_settings_field("{$section}[{$name}]", $label, $callback, $section, $section, $args);
                }
            }

            // creates our settings in the options table
            foreach ($this->settings_sections as $section) {
                register_setting(
                    $section['id'],
                    $section['id'],
                    array(
                        'type'              => 'array',
                        'sanitize_callback' => array($this, 'sanitize_options'),
                    )
                );
            }
        }

        /**
         * Allowed HTML for settings-field markup, used by wp_kses() on output.
         *
         * @return array
         */
        protected function get_allowed_field_html() {
            $attr = array(
                'class' => array(), 'id' => array(), 'name' => array(), 'value' => array(),
                'type' => array(), 'for' => array(), 'href' => array(), 'target' => array(),
                'title' => array(), 'placeholder' => array(), 'min' => array(), 'max' => array(),
                'step' => array(), 'rows' => array(), 'cols' => array(), 'scope' => array(),
                'checked' => array(), 'selected' => array(), 'disabled' => array(),
                'readonly' => array(), 'multiple' => array(), 'style' => array(),
                'aria-hidden' => array(), 'bdt-tooltip' => array(), 'data-default-color' => array(),
            );

            return array(
                'fieldset' => $attr, 'div' => $attr, 'span' => $attr, 'label'  => $attr,
                'input'    => $attr, 'select' => $attr, 'option' => $attr, 'textarea' => $attr,
                'a'        => $attr, 'i'      => $attr, 'p'      => $attr, 'br'    => $attr,
                'hr'       => $attr, 'h3'     => $attr, 'h4'     => $attr, 'strong' => $attr,
                'em'       => $attr, 'code'   => $attr, 'small'  => $attr,
            );
        }

        /**
         * Get field description for display
         *
         * @param array   $args settings field args
         */
        public function get_field_description($args) {
            if (!empty($args['desc'])) {
                $desc = sprintf('<p class="description">%s</p>', $args['desc']);
            } else {
                $desc = '';
            }

            return $desc;
        }

        /**
         * Displays a text field for a settings field
         *
         * @param array   $args settings field args
         */
        function callback_text($args) {

            $value       = esc_attr($this->get_option($args['id'], $args['section'], $args['std']));
            $class       = 'bdt-input';
            $type        = isset($args['type']) ? $args['type'] : 'text';
            $placeholder = empty($args['placeholder']) ? '' : ' placeholder="' . $args['placeholder'] . '"';
            $html = '';


            $html .= '<div class="ep-option-item-inner">';
            if ($args['video_url']) {
                $html .= '<a href="' . esc_url($args['video_url']) . '" target="_blank" class="ep-option-video" bdt-tooltip="View ' . esc_attr($args['name']) . ' Video Tutorial"><i class="bdt-wi-tutorial" aria-hidden="true"></i></a>';
            }
            $html  .= sprintf('<label for="bdt_ep_%1$s[%2$s]">', esc_attr($args['section']), esc_attr($args['id']));
            $html .= '<span scope="row" class="ep-option-label">' . $args['name'] . '</span>';
            $html  .= '</label>';


            $html .= sprintf('<input type="%1$s" class="%2$s" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s/>', $type, $class, esc_attr($args['section']), esc_attr($args['id']), $value, $placeholder);

            $html  .= $this->get_field_description($args);

            $html .= '</div>';

            echo wp_kses( $html, $this->get_allowed_field_html() );
        }

        /**
         * Displays a url field for a settings field
         *
         * @param array   $args settings field args
         */
        function callback_url($args) {
            $this->callback_text($args);
        }

        /**
         * Displays a number field for a settings field
         *
         * @param array   $args settings field args
         */
        function callback_number($args) {
            $value       = esc_attr($this->get_option($args['id'], $args['section'], $args['std']));
            $size        = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';
            $type        = isset($args['type']) ? $args['type'] : 'number';
            $placeholder = empty($args['placeholder']) ? '' : ' placeholder="' . $args['placeholder'] . '"';
            $min         = ($args['min'] == '') ? '' : ' min="' . $args['min'] . '"';
            $max         = ($args['max'] == '') ? '' : ' max="' . $args['max'] . '"';
            $step        = ($args['step'] == '') ? '' : ' step="' . $args['step'] . '"';

            $html        = sprintf('<input type="%1$s" class="%2$s-number" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s%7$s%8$s%9$s/>', $type, $size, esc_attr($args['section']), esc_attr($args['id']), $value, $placeholder, $min, $max, $step);
            $html       .= $this->get_field_description($args);

            echo wp_kses( $html, $this->get_allowed_field_html() );
        }

        /**
         * Get used widgets.
         *
         * @access public
         * @since 6.0.0
         *
         * @return array
         */

        public static function get_used_widgets_obj() {
            return ElementPack_Admin_Settings::get_used_widgets();
        }

        /**
         * Get unused widgets.
         *
         * @access public
         * @since 6.0.0
         *
         * @return array
         */

        public static function get_unused_widgets_obj() {
            return ElementPack_Admin_Settings::get_unused_widgets();
        }

        /**
		 * Displays a checkbox for a settings field
		 *
		 * @param array   $args settings field args
		 */
		function callback_checkbox($args) {

			$value = esc_attr($this->get_option($args['id'], $args['section'], $args['std']));
			$plugin_name = isset($args['plugin_name']) ? $args['plugin_name'] : '';
			$plugin_path = isset($args['plugin_path']) ? $args['plugin_path'] : '';
			$paid = isset($args['paid']) ? $args['paid'] : '';

			$parent_class = isset($args['ep_parent_switcher']) ? ' ep-feature-option-parent' : '';

			$used_widgets = self::get_used_widgets_obj();
			$widget_name = 'bdt-' . $args['id'];
			$used_widgets_count = 0;


			if (isset($used_widgets)) {
				$used_widgets_count = (in_array($widget_name, array_keys($used_widgets)) ? $used_widgets[$widget_name] : 0);
				if ($used_widgets_count === 0) {
					$widget_name = str_replace('_', '-', $widget_name);
					$used_widgets_count = (in_array($widget_name, array_keys($used_widgets)) ? $used_widgets[$widget_name] : 0);
				}
			}

			$html = '';

			$html .= '<div class="ep-option-item-inner">';
			$html .= '<div class="bdt-grid bdt-grid-collapse bdt-flex bdt-flex-middle">';

			$html .= '<div class="bdt-width-expand bdt-flex-inline bdt-flex-middle">';


			$html .= '<i class="bdt-wi-' . esc_attr($args['id']) . '" aria-hidden="true"></i>';
			$html .= '<div class="ep-option-label-wrap">';
			$html .= sprintf('<label for="bdt_ep_%1$s[%2$s]">', esc_attr($args['section']), esc_attr($args['id']));
			$html .= '<span scope="row" class="ep-option-label">' . $args['name'] . '</span>';
			$html .= '</label>';

			$html .= '<div class="ep-option-links">';
			if ($args['demo_url']) {
				/* translators: %s: Widget name. */
				$demo_title = sprintf( __( 'View %s Widget Demo', 'bdthemes-element-pack-lite' ), $args['name'] );
				$html .= '<a href="' . esc_url( $args['demo_url'] ) . '" target="_blank" class="ep-option-demo" title="' . esc_attr( $demo_title ) . '">' . esc_html__( 'Demo', 'bdthemes-element-pack-lite' ) . '<i class="bdt-wi-preview" aria-hidden="true"></i></a>';
			}
			if ($args['video_url']) {
				/* translators: %s: Widget name. */
				$video_title = sprintf( __( 'View %s Video Tutorial', 'bdthemes-element-pack-lite' ), $args['name'] );
				$html .= '<a href="' . esc_url( $args['video_url'] ) . '" target="_blank" class="ep-option-video" title="' . esc_attr( $video_title ) . '">' . esc_html__( 'Video', 'bdthemes-element-pack-lite' ) . '<i class="bdt-wi-tutorial" aria-hidden="true"></i></a>';
			}
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';

			$html .= '<div class="bdt-width-auto">';



			// 3rd party widgets 
			if ($plugin_name and $plugin_path) {

				if ($this->_is_plugin_installed($plugin_name, $plugin_path)) {
					if (!current_user_can('activate_plugins')) {
						return;
					}
					if (!is_plugin_active($plugin_path)) {
						$active_link = wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $plugin_path . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin_path);
						$html .= '<a href="' . esc_url($active_link) . '" class="element-pack-3pp-active" bdt-tooltip="' . esc_html__('Activate the plugin first then you can activate this widget.', 'bdthemes-element-pack-lite') . '"><span class="dashicons dashicons-admin-plugins"></span></a>';
					}
				} else {
					if ($paid) {
						$html .= '<a href="' . esc_url($paid) . '" class="element-pack-3pp-download" bdt-tooltip="' . esc_html__('Download and install plugin first then you can activate this widget.', 'bdthemes-element-pack-lite') . '"><span class="dashicons dashicons-download"></span></a>';
					} else {
						$install_link = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . $plugin_name), 'install-plugin_' . $plugin_name);
						$html .= '<a href="' . esc_url($install_link) . '" class="element-pack-3pp-install" bdt-tooltip="' . esc_html__('Install the plugin first then you can activate this widget.', 'bdthemes-element-pack-lite') . '"><span class="dashicons dashicons-download"></span></a>';
					}
				}
				if ($this->_is_plugin_installed($plugin_name, $plugin_path) and is_plugin_active($plugin_path)) {

					$html .= '<fieldset>';
					$html .= sprintf('<label for="bdt_ep_%1$s[%2$s]">', esc_attr($args['section']), esc_attr($args['id']));
					$html .= sprintf('<input type="hidden" name="%1$s[%2$s]" value="off" />', esc_attr($args['section']), esc_attr($args['id']));
					$html .= sprintf('<input type="checkbox" class="checkbox' . $parent_class . '" id="bdt_ep_%1$s[%2$s]" name="%1$s[%2$s]" value="on" %3$s />', esc_attr($args['section']), esc_attr($args['id']), checked($value, 'on', false));
					$html .= '<span class="switch"></span>';
					$html .= '</label>';
					$html .= '</fieldset>';
				}
			} else { // core widgets

				$html .= '<fieldset>';
				$html .= sprintf('<label for="bdt_ep_%1$s[%2$s]">', esc_attr($args['section']), esc_attr($args['id']));
				$html .= sprintf('<input type="hidden" name="%1$s[%2$s]" value="off" />', esc_attr($args['section']), esc_attr($args['id']));
				$html .= sprintf('<input type="checkbox" class="checkbox" id="bdt_ep_%1$s[%2$s]" name="%1$s[%2$s]" value="on" %3$s />', esc_attr($args['section']), esc_attr($args['id']), checked($value, 'on', false));
				$html .= '<span class="switch"></span>';
				$html .= '</label>';
				$html .= '</fieldset>';
			}

			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';

			echo wp_kses($html, array(
				'div' => array(
					'class' => array(),
				),
				'span' => array(
					'class' => array(),
				),
				'label' => array(
					'for' => array(),
				),
				'input' => array(
					'type' => array(),
					'class' => array(),
					'id' => array(),
					'name' => array(),
					'value' => array(),
					'checked' => array(),
				),
				'i' => array(
					'class' => array(),
					'aria-hidden' => array(),
				),
				'a' => array(
					'href' => array(),
					'target' => array(),
					'class' => array(),
					'bdt-tooltip' => array(),
				),
				'fieldset' => array(),
			));
		}

        function _is_plugin_installed($plugin, $plugin_path) {
            $installed_plugins = get_plugins();
            return isset($installed_plugins[$plugin_path]);
        }


        /**
         * Displays a multicheckbox for a settings field
         *
         * @param array   $args settings field args
         */
        function callback_multicheck($args) {

            $value = $this->get_option($args['id'], $args['section'], $args['std']);
            $html  = '<fieldset>';
            $html .= sprintf('<input type="hidden" name="%1$s[%2$s]" value="" />', esc_attr($args['section']), esc_attr($args['id']));
            foreach ($args['options'] as $key => $label) {
                $checked = isset($value[$key]) ? $value[$key] : '0';
                $html    .= sprintf('<label for="bdt_ep_%1$s[%2$s][%3$s]">', esc_attr($args['section']), esc_attr($args['id']), esc_attr($key));
                $html    .= sprintf('<input type="checkbox" class="checkbox" id="bdt_ep_%1$s[%2$s][%3$s]" name="%1$s[%2$s][%3$s]" value="%3$s" %4$s />', esc_attr($args['section']), esc_attr($args['id']), esc_attr($key), checked($checked, esc_attr($key), false));
                $html    .= '<span class="switch"></span>';
                $html    .= sprintf('%1$s</label><br>', wp_kses_post($label));
            }

            $html .= $this->get_field_description($args);
            $html .= '</fieldset>';

            echo wp_kses( $html, $this->get_allowed_field_html() );
        }

        /**
         * Displays a radio button for a settings field
         *
         * @param array   $args settings field args
         */
        function callback_radio($args) {

            $value = $this->get_option($args['id'], $args['section'], $args['std']);
            $html  = '<fieldset>';

            foreach ($args['options'] as $key => $label) {
                $html .= sprintf('<label for="bdt_ep_%1$s[%2$s][%3$s]">', esc_attr($args['section']), esc_attr($args['id']), esc_attr($key));
                $html .= sprintf('<input type="radio" class="radio" id="bdt_ep_%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s" %4$s />', esc_attr($args['section']), esc_attr($args['id']), esc_attr($key), checked($value, esc_attr($key), false));
                $html .= sprintf('%1$s</label><br>', wp_kses_post($label));
            }

            $html .= $this->get_field_description($args);
            $html .= '</fieldset>';

            echo wp_kses( $html, $this->get_allowed_field_html() );
        }

        /**
         * Displays a selectbox for a settings field
         *
         * @param array   $args settings field args
         */
        function callback_select($args) {

            $value = esc_attr($this->get_option($args['id'], $args['section'], $args['std']));
            $size  = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';
            $html  = sprintf('<select class="%1$s" name="%2$s[%3$s]" id="%2$s[%3$s]">', $size, esc_attr($args['section']), esc_attr($args['id']));

            foreach ($args['options'] as $key => $label) {
                $html .= sprintf('<option value="%s"%s>%s</option>', esc_attr($key), selected($value, esc_attr($key), false), wp_kses_post($label));
            }

            $html .= sprintf('</select>');
            $html .= $this->get_field_description($args);

            echo wp_kses( $html, $this->get_allowed_field_html() );
        }

        /**
         * Displays a textarea for a settings field
         *
         * @param array $args settings field args
         */
        function callback_textarea($args) {

            $value       = esc_textarea($this->get_option($args['id'], $args['section'], $args['std']));
            $size        = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';
            $placeholder = empty($args['placeholder']) ? '' : ' placeholder="' . $args['placeholder'] . '"';

            $html  = '';
            $html .= sprintf('<label for="bdt_ep_%1$s[%2$s]">', esc_attr($args['section']), esc_attr($args['id']));
            $html .= '<span scope="row" class="ep-option-label">' . $args['name'] . '</span>';
            $html .= '</label>';

            $html .= sprintf('<textarea rows="5" cols="55" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]" %4$s >%5$s</textarea>', $size, esc_attr($args['section']), esc_attr($args['id']), $placeholder, $value);
            $html .= $this->get_field_description($args);

            echo wp_kses( $html, $this->get_allowed_field_html() );
        }

        /**
         * Displays the html for a settings field
         *
         * @param array   $args settings field args
         * @return string
         */
        function callback_html($args) {
            echo wp_kses_post($args['desc']);
        }

        /**
         * Displays a file upload field for a settings field
         *
         * @param array   $args settings field args
         */
        function callback_file($args) {

            $value = esc_attr($this->get_option($args['id'], $args['section'], $args['std']));
            $size  = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';
            $id    = $args['section']  . '[' . $args['id'] . ']';
            $label = isset($args['options']['button_label']) ? $args['options']['button_label'] : __('Choose File', 'bdthemes-element-pack-lite');

            $html  = sprintf('<input type="text" class="%1$s-text wpsa-url" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, esc_attr($args['section']), esc_attr($args['id']), $value);
            $html  .= '<input type="button" class="button wpsa-browse" value="' . $label . '" />';
            $html  .= $this->get_field_description($args);

            echo wp_kses( $html, $this->get_allowed_field_html() );
        }

        /**
         * Displays a password field for a settings field
         *
         * @param array   $args settings field args
         */
        function callback_password($args) {

            $value = esc_attr($this->get_option($args['id'], $args['section'], $args['std']));
            $size  = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';

            $html  = sprintf('<input type="password" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, esc_attr($args['section']), esc_attr($args['id']), $value);
            $html  .= $this->get_field_description($args);

            echo wp_kses( $html, $this->get_allowed_field_html() );
        }

        /**
         * Displays a color picker field for a settings field
         *
         * @param array   $args settings field args
         */
        function callback_color($args) {

            $value = esc_attr($this->get_option($args['id'], $args['section'], $args['std']));
            $size  = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';

            $html  = sprintf('<input type="text" class="%1$s-text wp-color-picker-field" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s" data-default-color="%5$s" />', $size, esc_attr($args['section']), esc_attr($args['id']), $value, $args['std']);
            $html  .= $this->get_field_description($args);

            echo wp_kses( $html, $this->get_allowed_field_html() );
        }

        /**
         * Displays a  2 colspan subheading field for a settings field
         *
         * @param array $args settings field args
         */
        function callback_subheading($args) {

            $html  = '<h3 class="setting_subheading column-merge">' . $args['name'] . '</h3>';
            $html .= $this->get_field_description($args);
            $html .= '<hr class="setting_separator">';

            echo wp_kses( $html, $this->get_allowed_field_html() );
        }

        function callback_start_group($args) {

            $html  = '<div class="ep-option-item-inner ep-option-group">';

            $html  .= sprintf('<label for="bdt_ep_%1$s[%2$s]">', esc_attr($args['section']), esc_attr($args['id']));
            $html .= '<span scope="row" class="ep-option-label">' . $args['name'] . '</span>';
            $html  .= '</label>';

            if ($args['video_url']) {
                $html .= '<a href="' . esc_url($args['video_url']) . '" target="_blank" class="ep-option-video" bdt-tooltip="View ' . esc_attr($args['name']) . ' Video Tutorial"><i class="bdt-wi-tutorial" aria-hidden="true"></i></a>';
            }

            $html .= $this->get_field_description($args);

            $html .= '<div class="bdt-grid" bdt-grid>';

            echo wp_kses( $html, $this->get_allowed_field_html() );
        }

        function callback_end_group($args) {

            $html  = '</div>';
            $html  .= '</div>';

            echo wp_kses( $html, $this->get_allowed_field_html() );
        }

        /**
         * Displays a  2 colspan separator field for a settings field
         *
         * @param array $args settings field args
         */
        function callback_separator($args) {

            $html  = '<hr class="setting_separator column-merge">';
            $html .= $this->get_field_description($args);


            echo wp_kses( $html, $this->get_allowed_field_html() );
        }


        /**
         * Displays a select box for creating the pages select box
         *
         * @param array   $args settings field args
         */
        function callback_pages($args) {

            $dropdown_args = array(
                'selected' => esc_attr($this->get_option($args['id'], $args['section'], $args['std'])),
                'name'     => $args['section'] . '[' . $args['id'] . ']',
                'id'       => $args['section'] . '[' . $args['id'] . ']',
                'echo'     => 0
            );
            // 'echo' => 0 above, so nothing is printed here; the returned markup
            // is escaped through wp_kses() on the next line.
            $html = wp_dropdown_pages($dropdown_args); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Returns markup, escaped below.
            echo wp_kses( $html, $this->get_allowed_field_html() );
        }

        /**
         * Sanitize callback for Settings API
         *
         * @return mixed
         */
        /**
         * Default recursive sanitizer for settings values without their own callback.
         *
         * @param mixed $value raw option value.
         * @return mixed
         */
        protected function sanitize_value($value) {
            if (is_array($value)) {
                return array_map(array($this, 'sanitize_value'), $value);
            }

            if (is_bool($value) || is_int($value) || is_float($value) || is_null($value)) {
                return $value;
            }

            // sanitize_textarea_field() keeps newlines intact for multi-line fields
            // while still stripping tags and invalid UTF-8.
            return sanitize_textarea_field($value);
        }

        function sanitize_options($options) {

            if (!$options) {
                return $options;
            }

            foreach ($options as $option_slug => $option_value) {
                $sanitize_callback = $this->get_sanitize_callback($option_slug);

                // If callback is set, call it
                if ($sanitize_callback) {
                    $options[$option_slug] = call_user_func($sanitize_callback, $option_value);
                    continue;
                }

                // No field-specific callback: fall back to a type-appropriate default so
                // nothing reaches the options table unsanitized.
                $options[$option_slug] = $this->sanitize_value($option_value);
            }

            return $options;
        }

        /**
         * Get sanitization callback for given option slug
         *
         * @param string $slug option slug
         *
         * @return mixed string or bool false
         */
        function get_sanitize_callback($slug = '') {
            if (empty($slug)) {
                return false;
            }

            // Iterate over registered fields and see if we can find proper callback
            foreach ($this->settings_fields as $section => $options) {
                foreach ($options as $option) {
                    if ($option['name'] != $slug) {
                        continue;
                    }

                    // Return the callback name
                    return isset($option['sanitize_callback']) && is_callable($option['sanitize_callback']) ? $option['sanitize_callback'] : false;
                }
            }

            return false;
        }

        /**
         * Get the value of a settings field
         *
         * @param string  $option  settings field name
         * @param string  $section the section name this field belongs to
         * @param string  $default default text if it's not found
         * @return string
         */
        function get_option($option, $section, $default = '') {

            $options = get_option($section);

            if (isset($options[$option])) {
                return $options[$option];
            }

            return $default;
        }

        /**
         * Show navigations as tab
         *
         * Shows all the settings section labels as tab
         */
        function show_navigation() {

            $html = '<div class="bdt-dashboard-navigation">';
            $html .= '<ul class="bdt-tab bdt-flex-column" bdt-tab="animation: bdt-animation-slide-bottom-small;connect: .bdt-tab-container;">';

            $html .= sprintf('<li><a href="#%1$s" class="bdt-tab-item" id="bdt-%1$s" data-tab-index="0"><i class="dashicons dashicons-admin-home"></i>%2$s</a></li>', 'element_pack_welcome', esc_html__('Dashboard', 'bdthemes-element-pack-lite'));

            $count = 1;

		// Get all sections including manually created ones
		$all_sections = $this->get_all_sections();

            foreach ($all_sections as $tab) {
                $html .= sprintf('<li><a href="#%1$s" class="bdt-tab-item" id="bdt-%1$s" data-tab-index="%2$s"><i class="%4$s"></i>%3$s</a></li>', $tab['id'], $count++, $tab['title'], $tab['icon']);
            }
            $html .= sprintf('<li><a href="#%1$s" class="bdt-tab-item" id="bdt-%1$s" data-tab-index="%2$s">👑 %3$s</a></li>', 'element_pack_get_pro', $count++, esc_html__('Get Pro', 'bdthemes-element-pack-lite'));

            $html .= '</ul>';
            $html .= '</div>';            echo wp_kses($html, array(
				'div' => array(
					'class' => true,
				),
				'ul' => array(
					'class' => true,
					'bdt-tab' => true,
				),
				'li' => array(
					'class' => true,
				),
				'a' => array(
					'href' => true,
					'class' => true,
					'id' => true,
					'data-tab-index' => true,
				),
				'i' => array(
					'class' => true,
                ),
				'span' => array(
					'class' => true,
				)
			));
        }

		/**
		 * Get all sections including manually created content pages
		 */
		private function get_all_sections() {
			// Start with the settings sections that have forms
			$all_sections = $this->settings_sections;
			
			// Add manually created content sections that don't have settings forms
			$content_only_sections = [
				[
					'id' => 'element_pack_analytics_system_req',
					'title' => esc_html__('System Status', 'bdthemes-element-pack-lite'),
					'icon' => 'dashicons dashicons-chart-bar',
				],
				[
					'id' => 'element_pack_other_plugins',
					'title' => esc_html__('Other Plugins', 'bdthemes-element-pack-lite'),
					'icon' => 'dashicons dashicons-admin-plugins',
				],
			];
			
			// Check if each content section exists in settings sections, if not add it
			foreach ($content_only_sections as $content_section) {
				$exists = false;
				foreach ($all_sections as $existing_section) {
					if ($existing_section['id'] === $content_section['id']) {
						$exists = true;
						break;
					}
				}
				if (!$exists) {
					$all_sections[] = $content_section;
				}
			}
			
			return $all_sections;
		}

        function element_pack_settings_save() {

            if (!check_ajax_referer('element-pack-settings-save-nonce')) {
                wp_send_json_error();
            }

            if (!current_user_can('manage_options')) {
                return;
            }

            $moudle_id = isset($_POST['id']) ? sanitize_text_field(wp_unslash($_POST['id'])) : '';

            unset($_POST['id']);

            // Only ever write options inside this plugin's own namespace. Without
            // this the option name was fully attacker-chosen, letting a request
            // overwrite arbitrary core options (default_role, siteurl, ...).
            if ('' === $moudle_id || 0 !== strpos($moudle_id, 'element_pack')) {
                wp_send_json_error();
            }

            if (isset($_POST[$moudle_id])) {
                // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized on the next statement.
                $raw_value = wp_unslash($_POST[$moudle_id]);

                // Route the value through the registered per-field sanitizers
                // instead of storing raw request data.
                $value = is_array($raw_value)
                    ? $this->sanitize_options($raw_value)
                    : sanitize_text_field($raw_value);

                update_option($moudle_id, $value);
            }

            if (element_pack_is_asset_optimization_enabled()) {
                $optimize_assets = new Asset_Minifier();
                $optimize_assets->minifyCss();
                $optimize_assets->minifyJs();
                update_option('element-pack-minified-asset-manager-version', time());
            } else {
                delete_option('element-pack-minified-asset-manager-version');
            }

            wp_send_json_success();
        }

		/**
		 * Show the section settings forms
		 *
		 * This function displays every sections in a different form
		 */
		function show_forms() {
			?>

			<?php $i = 0;
			foreach ($this->settings_sections as $form) {
				$i++; ?>
				<div id="<?php echo esc_attr($form['id']); ?>_page" class="ep-option-page">

					<div bdt-filter="target: .ep-options" class="ep-options-parent" id="ep-options-parent-<?php echo esc_attr($i); ?>">


						<?php if ($form['id'] == 'element_pack_active_modules' or $form['id'] == 'element_pack_third_party_widget' or $form['id'] == 'element_pack_elementor_extend'): ?>

							<div class="bdt-widget-filter-wrapper bdt-flex bdt-flex-column bdt-flex-wrap"
								bdt-sticky="end: !.ep-dashboard-container; offset: 115; animation: bdt-animation-slide-top-small; duration: 300">

								<!-- Filter Shape Elements -->
								<div class="ep-filter-elements">
									<span class="ep-filter-element ep-filter-circle"></span>
									<span class="ep-filter-element ep-filter-dots"></span>
									<span class="ep-filter-element ep-filter-wave"></span>
									<span class="ep-filter-element ep-filter-hexagon"></span>
									<span class="ep-filter-element ep-filter-zigzag"></span>
								</div>

								<div class="bdt-widget-filter-header">

									<div class="bdt-flex bdt-flex-wrap">

										<div class="bdt-width-expand@l ep-widget-filter-nav bdt-visible@l">
											<div class="bdt-flex-inline bdt-flex-middle">

												<div>
													<ul
														class="bdt-subnav bdt-subnav-pill ep-widget-filter bdt-widget-type-content bdt-flex-inline">
														<li class="ep-widget-all" bdt-filter-control="*"><a
																href="#"><?php esc_html_e('All', 'bdthemes-element-pack-lite'); ?></a></li>
														<li class="ep-widget-free bdt-active"
															bdt-filter-control="filter: [data-widget-type='free']; group: data-content-type">
															<a href="#"><?php esc_html_e('Free', 'bdthemes-element-pack-lite'); ?></a>
														</li>
														<li class="ep-widget-pro"
															bdt-filter-control="filter: [data-widget-type='pro']; group: data-content-type">
															<a href="#"><?php esc_html_e('Pro', 'bdthemes-element-pack-lite'); ?></a>
														</li>

													</ul>
												</div>

												<?php if ($form['id'] == 'element_pack_active_modules' or $form['id'] == 'element_pack_third_party_widget'): ?>


													<?php if ($form['id'] != 'element_pack_elementor_extend' or $form['id'] == 'element_pack_third_party_widget'): ?>

														<div>
															<ul
																class="bdt-subnav bdt-subnav-pill ep-widget-filter ep-used-unused-widgets bdt-flex-inline">
																<li class="ep-widget--"
																	bdt-filter-control="filter: [data-content-type*='ep-used']; group: data-content-type">
																	<a href="#"><?php esc_html_e('Used', 'bdthemes-element-pack-lite'); ?>
																		<span class="bdt-badge ep-used-widget"></span>
																	</a>
																</li>
																<li class="ep-widget--"
																	bdt-filter-control="filter: [data-content-type*='ep-unused']; group: data-content-type">
																	<a href="#"
																		bdt-tooltip="<?php esc_html_e('Don\'t need unused widget? Click on the Deactivate All button.', 'bdthemes-element-pack-lite'); ?>"><?php esc_html_e('Unused', 'bdthemes-element-pack-lite'); ?>
																		<span class="bdt-badge ep-unused-widget bdt-danger"></span>
																	</a>
																</li>
															</ul>

														</div>
													<?php endif; ?>

												<?php endif; ?>
											</div>
										</div>


										<div class="bdt-width-auto@l bdt-search-active-wrap bdt-flex bdt-flex-middle bdt-flex-between">
											<div class="bdt-widget-search">
												<input data-id="ep-options-parent-<?php echo esc_attr($i); ?>" onkeyup="filterSearch(this);"
													bdt-filter-control="" class="bdt-search-input bdt-flex-middle" type="search"
													placeholder="<?php esc_html_e('Search widget...', 'bdthemes-element-pack-lite'); ?>"
													autofocus>
											</div>

											<?php //if ($form['id'] == 'element_pack_active_modules' or $form['id'] == 'element_pack_third_party_widget' ) : 
																?>
											<div>
												<ul class="bdt-subnav bdt-subnav-pill ep-widget-onoff">
													<li>
														<a href="#" class="ep-active-all-widget">
															<?php esc_html_e('Activate All', 'bdthemes-element-pack-lite'); ?>
														</a>
													</li>
													<li>
														<a href="#" class="ep-deactive-all-widget">
															<?php esc_html_e('Deactivate All', 'bdthemes-element-pack-lite'); ?>
														</a>
													</li>
												</ul>
											</div>
										</div>
									</div>

									<?php if ($form['id'] == 'element_pack_active_modules' or $form['id'] == 'element_pack_third_party_widget'): ?>
										<div class="ep-content-type-filter bdt-margin-top">
											<div class="bdt-flex bdt-flex-wrap bdt-flex-middle bdt-visible@l">
												<div class="ep-filter-by-text bdt-visible@xl">
													<?php esc_html_e('Filter By: ', 'bdthemes-element-pack-lite'); ?>
												</div>
												<ul
													class="bdt-nav xbdt-subnav-pill xbdt-dropdown-nav ep-widget-filter ep-widget-content-type bdt-flex bdt-flex-wrap ">
													<li class="ep-widget-new"
														bdt-filter-control="filter: [data-content-type*='new']; group: data-widget-type"><a
															href="#"><?php esc_html_e('New', 'bdthemes-element-pack-lite'); ?></a></li>
													<li class="ep-widget-post"
														bdt-filter-control="filter: [data-content-type*='post']; group: data-widget-type"><a
															href="#"><?php esc_html_e('Post', 'bdthemes-element-pack-lite'); ?></a></li>
													<?php if ($form['id'] == 'element_pack_active_modules'): ?>
														<li class="ep-widget-custom"
															bdt-filter-control="filter: [data-content-type*='custom']; group: data-widget-type">
															<a href="#"><?php esc_html_e('Custom', 'bdthemes-element-pack-lite'); ?></a>
														</li>
													<?php endif; ?>
													<li class="ep-widget-gallery"
														bdt-filter-control="filter: [data-content-type*='gallery']; group: data-widget-type">
														<a href="#"><?php esc_html_e('Gallery', 'bdthemes-element-pack-lite'); ?></a>
													</li>
													<li class="ep-widget-slider"
														bdt-filter-control="filter: [data-content-type*='slider']; group: data-widget-type">
														<a href="#"><?php esc_html_e('Slider', 'bdthemes-element-pack-lite'); ?></a>
													</li>
													<li class="ep-widget-carousel"
														bdt-filter-control="filter: [data-content-type*='carousel']; group: data-widget-type">
														<a href="#"><?php esc_html_e('Carousel', 'bdthemes-element-pack-lite'); ?></a>
													</li>
													<?php if ($form['id'] == 'element_pack_third_party_widget'): ?>
														<li class="ep-widget-acf"
															bdt-filter-control="filter: [data-content-type*='acf']; group: data-widget-type">
															<a href="#"><?php esc_html_e('ACF', 'bdthemes-element-pack-lite'); ?></a>
														</li>
														<li class="ep-widget-forms"
															bdt-filter-control="filter: [data-content-type*='forms']; group: data-widget-type">
															<a href="#"><?php esc_html_e('Forms', 'bdthemes-element-pack-lite'); ?></a>
														</li>
														<li class="ep-widget-ecommerce"
															bdt-filter-control="filter: [data-content-type*='ecommerce']; group: data-widget-type">
															<a href="#"><?php esc_html_e('eCommerce', 'bdthemes-element-pack-lite'); ?></a>
														</li>
													<?php endif; ?>
													<?php if ($form['id'] == 'element_pack_active_modules'): ?>
														<li class="ep-widget-template-builder"
															bdt-filter-control="filter: [data-content-type*='template-builder']; group: data-widget-type">
															<a href="#"><?php esc_html_e('Template Builder', 'bdthemes-element-pack-lite'); ?></a>
														</li>
													<?php endif; ?>
													<li class="ep-widget-others"
														bdt-filter-control="filter: [data-content-type*='others']; group: data-widget-type">
														<a href="#"><?php esc_html_e('Others', 'bdthemes-element-pack-lite'); ?></a>
													</li>
												</ul>
											</div>
										</div>
									<?php endif; ?>

								</div>

							</div>

						<?php endif; ?>

						<form class="settings-save" method="post" action="admin-ajax.php?action=element_pack_settings_save">
							<input type="hidden" name="id" value="<?php echo esc_attr($form['id']); ?>">

							<?php

							if (!current_user_can('manage_options')) {
								return;
							}

							wp_nonce_field('element-pack-settings-save-nonce');

							do_action('wsa_form_top_' . $form['id'], $form);

							$this->do_settings_sections($form['id']);

							do_action('wsa_form_bottom_' . $form['id'], $form);

							?>

						</form>
					</div>
				</div>
			<?php }
		}
    }

endif;
