<?php

namespace ElementPack\Modules\FancyList\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use ElementPack\Utils;
use ElementPack\Traits\Global_Widget_Controls;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Fancy_List extends Module_Base {
	use Global_Widget_Controls;

	public function get_name() {
		return 'bdt-fancy-list';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Fancy List', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-fancy-list';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_style_depends() {
		return $this->ep_is_edit_mode() ? [ 'ep-styles' ] : [ 'ep-fancy-list' ];
	}

	public function get_keywords() {
		return [ 'fancy', 'list', 'group', 'fl' ];
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/faIeyW7LOJ8';
	}

	public function has_widget_inner_wrapper(): bool {
        return ! \Elementor\Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
    }
	protected function is_dynamic_content(): bool {
		return false;
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_layout',
			[ 
				'label' => esc_html__( 'Fancy List', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'layout_style',
			[ 
				'label'   => esc_html__( 'Layout Style', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'    => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [ 
					'style-1' => '01',
					'style-2' => '02',
					'style-3' => '03',
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'text',
			[ 
				'label'       => esc_html__( 'Title', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => esc_html__( 'List Item', 'bdthemes-element-pack' ),
				'default'     => esc_html__( 'List Item', 'bdthemes-element-pack' ),
				'dynamic'     => [ 
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'text_details',
			[ 
				'label'       => esc_html__( 'Sub Title', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => esc_html__( 'Sub Title', 'bdthemes-element-pack' ),
				'dynamic'     => [ 
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'list_icon',
			[ 
				'label'       => esc_html__( 'Icon', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::ICONS,
				'label_block' => false,
				'skin'        => 'inline',

			]
		);

		$repeater->add_control(
			'img',
			[ 
				'label'   => esc_html__( 'Image', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => [ 
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'link',
			[ 
				'label'       => esc_html__( 'Link', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 
					'active' => true,
				],
				'label_block' => true,
				'placeholder' => esc_html__( 'https://your-link.com', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'icon_list',
			[ 
				'label'       => '',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [ 
					[ 
						'text' => esc_html__( 'List Item #1', 'bdthemes-element-pack' ),
					],
					[ 
						'text' => esc_html__( 'List Item #2', 'bdthemes-element-pack' ),
					],
					[ 
						'text' => esc_html__( 'List Item #3', 'bdthemes-element-pack' ),
					],
				],
				'title_field' => '{{{ elementor.helpers.renderIcon( this, list_icon, {}, "i", "panel" ) || \'<i class="{{ icon }}" aria-hidden="true"></i>\' }}} {{{ text }}}',
			]
		);

		$this->register_fancy_list_controls();
	}

	protected function render() {
		$settings   = $this->get_settings_for_display();
		$icon_list  = isset( $settings['icon_list'] ) ? $settings['icon_list'] : [];
		$layout     = isset( $settings['layout_style'] ) ? $settings['layout_style'] : 'style-1';
		$title_tag  = Utils::get_valid_html_tag( isset( $settings['title_tags'] ) ? $settings['title_tags'] : 'h4' );
		$show_number = ! empty( $settings['show_number_icon'] ) && $settings['show_number_icon'] === 'yes';

		$this->add_render_attribute( 'icon_list', 'class', 'bdt-fancy-list-icon' );
		$this->add_render_attribute( 'list_item', 'class', 'elementor-icon-list-item' );
		$this->add_render_attribute( 'list_title_tags', 'class', 'bdt-fancy-list-title' );
		?>
		<div class="bdt-fancy-list bdt-fancy-list-<?php echo esc_attr( $layout ); ?>">
			<ul class="bdt-list bdt-fancy-list-group" <?php $this->print_render_attribute_string( 'icon_list' ); ?>>
				<?php
				$i = 1;
				foreach ( $icon_list as $index => $item ) :
					$repeater_setting_key = $this->get_repeater_setting_key( 'text', 'icon_list', $index );
					$this->add_render_attribute( $repeater_setting_key, 'class', 'elementor-icon-list-text' );
					$this->add_inline_editing_attributes( $repeater_setting_key );

					$has_content = ! empty( $item['text'] ) || ! empty( $item['text_details'] )
						|| ! empty( $item['list_icon']['value'] ) || ! empty( $item['img']['url'] );
					if ( ! $has_content ) {
						continue;
					}
					?>
					<li>
						<?php
						if ( ! empty( $item['link']['url'] ) ) {
							$link_key = 'link_' . $index;
							$this->add_link_attributes( $link_key, $item['link'] );
							echo '<a class="bdt-fancy-list-wrap" ' . $this->get_render_attribute_string( $link_key ) . '>';
						} else {
							echo '<div class="bdt-fancy-list-wrap">';
						}
						?>
						<div class="bdt-flex flex-wrap">
							<?php
							if ( $show_number ) {
								echo '<div class="bdt-fancy-list-number-icon"><span>' . (int) $i . '</span></div>';
								$i++;
							}
							?>
							<?php if ( ! empty( $item['img']['url'] ) ) : ?>
								<div class="bdt-fancy-list-img">
									<?php
									$img_id  = isset( $item['img']['id'] ) ? $item['img']['id'] : 0;
									$img_alt = isset( $item['text'] ) ? $item['text'] : '';
									if ( $img_id ) {
										echo wp_get_attachment_image(
											$img_id,
											'medium',
											false,
											[ 'alt' => esc_attr( $img_alt ) ]
										);
									} else {
										printf(
											'<img src="%s" alt="%s">',
											esc_url( $item['img']['url'] ),
											esc_attr( $img_alt )
										);
									}
									?>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $item['text'] ) || ! empty( $item['text_details'] ) ) : ?>
								<div class="bdt-fancy-list-content">
									<?php if ( ! empty( $item['text'] ) ) : ?>
										<<?php echo esc_attr( $title_tag ); ?> <?php $this->print_render_attribute_string( 'list_title_tags' ); ?>>
											<?php echo wp_kses_post( $item['text'] ); ?>
										</<?php echo esc_attr( $title_tag ); ?>>
									<?php endif; ?>
									<?php if ( ! empty( $item['text_details'] ) ) : ?>
										<p class="bdt-fancy-list-text">
											<?php echo wp_kses_post( $item['text_details'] ); ?>
										</p>
									<?php endif; ?>
								</div>
							<?php endif; ?>
							<?php if ( ! empty( $item['list_icon']['value'] ) ) : ?>
								<div class="bdt-fancy-list-icon">
									<?php Icons_Manager::render_icon( $item['list_icon'], [ 'aria-hidden' => 'true' ] ); ?>
								</div>
							<?php endif; ?>
						</div>
						<?php
						if ( ! empty( $item['link']['url'] ) ) {
							echo '</a>';
						} else {
							echo '</div>';
						}
						?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}

	protected function content_template() {
		?>
		<#
		var layout     = settings.layout_style || 'style-1';
		var titleTag   = settings.title_tags || 'h4';
		var showNumber = settings.show_number_icon === 'yes';
		var lineNum    = 1;
		#>
		<div class="bdt-fancy-list bdt-fancy-list-{{ layout }}">
			<ul class="bdt-list bdt-fancy-list-group">
				<# _.each( settings.icon_list, function( item ) {
					var hasImg  = item.img && item.img.url;
					var hasText = item.text || item.text_details;
					var hasIcon = item.list_icon && item.list_icon.value;
					if ( ! hasImg && ! hasText && ! hasIcon ) {
						return;
					}
					var iconHTML   = elementor.helpers.renderIcon( view, item.list_icon, { 'aria-hidden': true }, 'i', 'object' );
					var linkUrl    = item.link && item.link.url ? item.link.url : '';
					var currentNum = showNumber ? lineNum++ : 0;
				#>
				<li>
					<# if ( linkUrl ) { #>
					<a class="bdt-fancy-list-wrap" href="{{ linkUrl }}"<# if ( item.link && item.link.is_external ) { #> target="_blank"<# } #><# if ( item.link && item.link.nofollow ) { #> rel="nofollow"<# } #>>
					<# } else { #>
					<div class="bdt-fancy-list-wrap">
					<# } #>
						<div class="bdt-flex flex-wrap">
							<# if ( showNumber ) { #>
							<div class="bdt-fancy-list-number-icon"><span><# print( currentNum ); #></span></div>
							<# } #>
							<# if ( hasImg ) { #>
							<div class="bdt-fancy-list-img">
								<img src="{{ item.img.url }}" alt="{{ item.text }}">
							</div>
							<# } #>
							<# if ( item.text || item.text_details ) { #>
							<div class="bdt-fancy-list-content">
								<# if ( item.text ) { #>
								<{{ titleTag }} class="bdt-fancy-list-title">{{{ item.text }}}</{{ titleTag }}>
								<# } #>
								<# if ( item.text_details ) { #>
								<p class="bdt-fancy-list-text">{{{ item.text_details }}}</p>
								<# } #>
							</div>
							<# } #>
							<# if ( hasIcon ) { #>
							<div class="bdt-fancy-list-icon">
								<# if ( iconHTML && iconHTML.rendered ) { #>
									{{{ iconHTML.value }}}
								<# } else if ( item.list_icon && item.list_icon.value ) { #>
									<i class="{{ item.list_icon.value }}" aria-hidden="true"></i>
								<# } #>
							</div>
							<# } #>
						</div>
					<# if ( linkUrl ) { #>
					</a>
					<# } else { #>
					</div>
					<# } #>
				</li>
				<# } ); #>
			</ul>
		</div>
		<?php
	}
}
