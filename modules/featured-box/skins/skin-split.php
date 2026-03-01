<?php
namespace ElementPack\Modules\FeaturedBox\Skins;

use Elementor\Skin_Base as Elementor_Skin_Base;

use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Skin_Split extends Elementor_Skin_Base {

	public function get_id() {
		return 'split';
	}

	public function get_title() {
		return __( 'Split', 'bdthemes-element-pack' );
	}


	public function render() {
		$settings = $this->parent->get_settings_for_display();
		$content_position = isset( $settings['skin_content_position'] ) ? $settings['skin_content_position'] : 'left';

		$this->parent->add_render_attribute( 'featured-box', 'class', [ 'bdt-ep-featured-box', 'bdt-ep-featured-box-skin-split' ] );
		if ( $content_position === 'right' ) {
			$this->parent->add_render_attribute( 'featured-box-content', 'class', [ 'bdt-grid', 'bdt-flex-row', 'bdt-flex-row-reverse' ] );
		} else {
			$this->parent->add_render_attribute( 'featured-box-content', 'class', [ 'bdt-grid' ] );
		}
		?>
		<div <?php $this->parent->print_render_attribute_string( 'featured-box' ); ?>>
			<div <?php $this->parent->print_render_attribute_string( 'featured-box-content' ); ?>>
				<div class="bdt-width-1-1 bdt-width-2-5@s">
					<div class="bdt-ep-featured-box-content bdt-position-z-index bdt-position-center-<?php echo esc_attr( $content_position ); ?> bdt-text-<?php echo esc_attr( $content_position ); ?>">
						<?php $this->parent->render_featured_content( $settings ); ?>
					</div>
				</div>
				<div class="bdt-width-1-1 bdt-width-3-5@s">
					<?php $this->parent->render_featured_image( $settings ); ?>
				</div>
			</div>

		</div>

		<?php
	}
}