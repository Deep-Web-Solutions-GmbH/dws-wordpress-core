<?php if (!defined('ABSPATH')) { exit; }

/**
 * The HTML content of the recommended plugins DWS dashboard page.
 *
 * @since   1.0.0
 * @version 1.2.0
 *
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 */

use Deep_Web_Solutions\Admin\Dashboard\DWS_Recommended_Plugins;
use Deep_Web_Solutions\Admin\Dashboard\DWS_Plugins_List_Table;

/**
 * @var    \Deep_Web_Solutions\Admin\Dashboard\DWS_TGMPA    $dws_tgmpa
 */
global $dws_tgmpa;

$plugin_table = new DWS_Plugins_List_Table();

// Return early if processing a plugin installation action.
if ((('tgmpa-bulk-install' === $plugin_table->current_action() || 'tgmpa-bulk-update' === $plugin_table->current_action())
		&& $plugin_table->process_bulk_actions()) || $dws_tgmpa->public_do_plugin_install()) {
	return;
}

// Force refresh of available plugin information so we'll know about manual updates/deletes.
wp_clean_plugins_cache(false);
DWS_Recommended_Plugins::delete_updates_transient();

?>

<div id="dws-dashboard" class="wrap">
    <h1><?php esc_html_e(get_admin_page_title()); ?></h1>
	<?php $plugin_table->prepare_items(); ?>
	<?php $plugin_table->views(); ?>

    <form id="dws-plugins" action="" method="post">
        <input type="hidden" name="tgmpa-page" value="<?php echo esc_attr($dws_tgmpa->menu); ?>"/>
        <input type="hidden" name="plugin_status" value="<?php echo esc_attr($plugin_table->view_context); ?>"/>
		<?php $plugin_table->display(); ?>
    </form>
</div>