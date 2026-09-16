<?php
/**
 * Welcome Step
 */

namespace ElementPack\SetupWizard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="bdt-wizard-step bdt-text-center active" data-step="welcome">
    <div class="bdt-welcome-header">
        <div class="bdt-logo-container">
            <img src="<?php echo esc_url( BDTEP_ASSETS_URL . 'images/logo.svg' ); ?>" alt="Element Pack Logo" class="bdt-logo">
        </div>
        <h2><?php esc_html_e( 'Welcome to Element Pack', 'bdthemes-element-pack-lite' ); ?></h2>
    </div>

    <div class="bdt-welcome-scroll">
    <div class="bdt-welcome-features">
        <div class="bdt-features-grid">
            <div class="bdt-feature-item">
                <div class="bdt-feature-icon">
                    <span class="dashicons dashicons-admin-customizer"></span>
                </div>
                <h3><?php esc_html_e( '230+ Widgets', 'bdthemes-element-pack-lite' ); ?></h3>
                <p><?php esc_html_e( 'Powerful elements for unlimited design possibilities', 'bdthemes-element-pack-lite' ); ?></p>
            </div>
            <div class="bdt-feature-item">
                <div class="bdt-feature-icon">
                    <span class="dashicons dashicons-layout"></span>
                </div>
                <h3><?php esc_html_e( 'Ready Templates', 'bdthemes-element-pack-lite' ); ?></h3>
                <p><?php esc_html_e( 'Professional templates to jumpstart your projects', 'bdthemes-element-pack-lite' ); ?></p>
            </div>
            <div class="bdt-feature-item">
                <div class="bdt-feature-icon">
                    <span class="dashicons dashicons-performance"></span>
                </div>
                <h3><?php esc_html_e( 'Fast & Optimized', 'bdthemes-element-pack-lite' ); ?></h3>
                <p><?php esc_html_e( 'Built with performance in mind for lightning-fast websites', 'bdthemes-element-pack-lite' ); ?></p>
            </div>
            <div class="bdt-feature-item">
                <div class="bdt-feature-icon">
                    <span class="dashicons dashicons-welcome-widgets-menus"></span>
                </div>
                <h3><?php esc_html_e( 'Theme Builder', 'bdthemes-element-pack-lite' ); ?></h3>
                <p><?php esc_html_e( 'Design headers, footers and archive pages without code', 'bdthemes-element-pack-lite' ); ?></p>
            </div>
            <div class="bdt-feature-item">
                <div class="bdt-feature-icon">
                    <span class="dashicons dashicons-cart"></span>
                </div>
                <h3><?php esc_html_e( 'WooCommerce Ready', 'bdthemes-element-pack-lite' ); ?></h3>
                <p><?php esc_html_e( 'Build shop and product layouts exactly the way you want', 'bdthemes-element-pack-lite' ); ?></p>
            </div>
            <div class="bdt-feature-item">
                <div class="bdt-feature-icon">
                    <span class="dashicons dashicons-sos"></span>
                </div>
                <h3><?php esc_html_e( 'Dedicated Support', 'bdthemes-element-pack-lite' ); ?></h3>
                <p><?php esc_html_e( 'Regular updates and expert help whenever you need a hand', 'bdthemes-element-pack-lite' ); ?></p>
            </div>
        </div>
    </div>

    <?php require plugin_dir_path( BDTEP__FILE__ ) . 'includes/setup-wizard/views/subscribe.php'; ?>
    </div>

    <div class="bdt-wizard-navigation">
        <button class="bdt-button bdt-button-primary bdt-wizard-next" data-step="features">
            <?php esc_html_e( 'Get Started', 'bdthemes-element-pack-lite' ); ?>
            <span><i class="dashicons dashicons-arrow-right-alt"></i></span>
        </button>
    </div>
</div>
