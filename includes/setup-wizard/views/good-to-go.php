<?php
/**
 * Complete Step
 */

namespace ElementPack\SetupWizard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$templates_path = BDTEP_INC_PATH . 'setup-wizard/assets/data.json';
$templates      = json_decode( file_get_contents( $templates_path ), true );

?>
<div class="bdt-wizard-step bdt-text-center" data-step="finish">

    <div class="bdt-templates-section">
		<div class="bdt-success-icon">
            <i class="dashicons dashicons-yes-alt"></i>
        </div>

        <h3><?php esc_html_e( 'Ready-to-Use Templates', 'bdthemes-element-pack-lite' ); ?></h3>
        <p><?php esc_html_e( 'Get a head start with these professional templates. Just click on Import to add them to your site.', 'bdthemes-element-pack-lite' ); ?></p>
        
        <div class="template-list">
            <?php foreach ( $templates as $template ) : ?>
            <?php
                $assets_url = plugin_dir_url( dirname( dirname( __FILE__ ) ) ) . 'setup-wizard/assets';
                /**
                 * Base URL for the starter-kit archives.
                 *
                 * WordPress.org does not permit shipping compressed files inside a
                 * plugin, so the .zip kits are hosted remotely while their preview
                 * images stay local. Override this filter to serve them elsewhere.
                 */
                $kit_base_url = apply_filters(
                    'element_pack/setup_wizard/kit_base_url',
                    'https://templates.elementpack.pro/element-pack-lite/setup-wizard'
                );

                // Entries may already be absolute; only prefix the relative ones.
                $importUrl = preg_match( '#^https?://#i', $template['import_url'] )
                    ? $template['import_url']
                    : $kit_base_url . $template['import_url'];
                $thumbnailUrl = preg_match( '#^https?://#i', $template['thumbnail'] )
                    ? $template['thumbnail']
                    : $assets_url . $template['thumbnail'];
                $extension = pathinfo($importUrl, PATHINFO_EXTENSION);
                if (!$extension || !in_array(strtolower($extension), ['json', 'zip'])) {
                    continue;
                }
                $extension = strtolower($extension);
            ?>
                <div class="choose-template <?php echo esc_attr( $extension ); ?> <?php echo $extension =='zip' ? 'bdt-ep-import-temp-zip':'bdt-ep-import-temp-json' ?>" data-import-url="<?php echo esc_url( $importUrl ); ?>">
                    <div class="template-image">
                        <img src="<?php echo esc_url( $thumbnailUrl ); ?>" alt="<?php echo esc_attr( $template['title'] ); ?>">
                        <div class="template-actions">
                            <a href="<?php echo esc_url( $template['demo_url'] ); ?>" target="_blank" class="template-preview">
                                <i class="dashicons dashicons-visibility"></i> <?php esc_html_e( 'Preview', 'bdthemes-element-pack-lite' ); ?>
                            </a>
                            <button class="template-import">
                                <i class="dashicons dashicons-download"></i> <?php esc_html_e( 'Import', 'bdthemes-element-pack-lite' ); ?>
                            </button>
                        </div>
                    </div>
                    <div class="template-title"><?php echo esc_html( $template['title'] ); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div class="bdt-help-resources">
        <h3><?php esc_html_e( 'Helpful Resources', 'bdthemes-element-pack-lite' ); ?></h3>
        
        <div class="bdt-resources-grid">
            <a href="https://bdthemes.com/all-knowledge-base-of-element-pack/" target="_blank" class="bdt-resource-item">
                <div class="resource-icon">
                    <i class="dashicons dashicons-book"></i>
                </div>
                <h4><?php esc_html_e( 'Documentation', 'bdthemes-element-pack-lite' ); ?></h4>
                <p><?php esc_html_e( 'Find detailed guides and documentation', 'bdthemes-element-pack-lite' ); ?></p>
            </a>
            
            <a href="https://bdthemes.com/support/" target="_blank" class="bdt-resource-item">
                <div class="resource-icon">
                    <i class="dashicons dashicons-sos"></i>
                </div>
                <h4><?php esc_html_e( 'Get Support', 'bdthemes-element-pack-lite' ); ?></h4>
                <p><?php esc_html_e( 'Contact our customer support team', 'bdthemes-element-pack-lite' ); ?></p>
            </a>
            
            <a href="https://www.youtube.com/watch?v=97wb3JwAoPM&list=PLP0S85GEw7DOJf_cbgUIL20qqwqb5x8KA" target="_blank" class="bdt-resource-item">
                <div class="resource-icon">
                    <i class="dashicons dashicons-video-alt3"></i>
                </div>
                <h4><?php esc_html_e( 'Video Tutorials', 'bdthemes-element-pack-lite' ); ?></h4>
                <p><?php esc_html_e( 'Watch tutorials on our YouTube channel', 'bdthemes-element-pack-lite' ); ?></p>
            </a>
        </div>
    </div>
    
	<div class="bdt-flex bdt-flex-between bdt-flex-wrap">
		<div class="bdt-wizard-navigation">
			<button class="bdt-button bdt-button-secondary bdt-wizard-prev" data-step="integration">
				<span><i class="dashicons dashicons-arrow-left-alt"></i></span>
				<?php esc_html_e( 'Previous Step', 'bdthemes-element-pack-lite' ); ?>
			</button>
		</div>
	
		<div class="bdt-next-steps">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=element_pack_options' ) ); ?>" class="bdt-button bdt-button-primary">
				<i class="dashicons dashicons-dashboard"></i>
				<?php esc_html_e( 'Go to Element Pack Dashboard', 'bdthemes-element-pack-lite' ); ?>
			</a>
			
			<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=page' ) ); ?>" class="bdt-button bdt-button-secondary">
				<i class="dashicons dashicons-edit"></i>
				<?php esc_html_e( 'Edit Your Pages', 'bdthemes-element-pack-lite' ); ?>
			</a>
		</div>
	</div>

</div>