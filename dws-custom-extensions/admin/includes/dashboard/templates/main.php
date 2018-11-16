<?php if (!defined( 'ABSPATH')) { exit; }
use Deep_Web_Solutions\Custom_Extensions;

/**
 * The HTML content of the DWS dashboard page.
 *
 * @since   1.4.0
 * @version 1.4.0
 *
 * @author  Dushan Terzikj <d.terzikj@deep-web-solutions.de>
 */

?>

<div id="dws-dashboard" class="wrap">
	<div class="dws-welcome">
		<div class="dws-logo">
			<div class="dws-version">
                <?php echo sprintf(__('v.%s', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN), Custom_Extensions::get_version()); ?>
            </div>
		</div>
		<h1><?php _e('Welcome to the DWS Core!', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN); ?></h1>
		<p class="dws-subtitle"><?php _e('The DWS Core is installed and ready to go!', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN); ?></p>
	</div>

	<?php do_action('dws_main_page'); ?>
</div>