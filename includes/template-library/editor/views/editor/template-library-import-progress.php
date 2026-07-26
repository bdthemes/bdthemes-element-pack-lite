<?php

/**
 * import progress view
 */
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>
<div class="bdt-ep-import-progress">
	<div class="bdt-ep-import-progress__title"><?php esc_html_e('Importing Template', 'bdthemes-element-pack'); ?></div>

	<div class="bdt-ep-import-progress__track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
		<div class="bdt-ep-import-progress__fill"></div>
	</div>

	<div class="bdt-ep-import-progress__meta">
		<span class="bdt-ep-import-progress__label"><?php esc_html_e('Preparing…', 'bdthemes-element-pack'); ?></span>
		<span class="bdt-ep-import-progress__percent">0%</span>
	</div>

	<div class="bdt-ep-import-progress__hint"><?php esc_html_e('Templates with many images can take a while to import. Please keep this window open.', 'bdthemes-element-pack'); ?></div>
</div>
