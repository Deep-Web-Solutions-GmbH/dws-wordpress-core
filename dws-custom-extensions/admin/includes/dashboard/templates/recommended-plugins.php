<?php if (!defined('ABSPATH')) { exit; }

/**
 * The HTML content of the recommended plugins DWS dashboard page.
 *
 * @since   1.0.0
 * @version 1.2.0
 *
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 */

$plugins_table = new \Deep_Web_Solutions\Admin\Dashboard\DWS_Plugins_List_Table();

?>

<div class="wrap">
    <h1><?php esc_html_e(get_admin_page_title()); ?></h1>
	<?php $plugins_table->prepare_items(); ?>
	<?php $plugins_table->views(); ?>

    <form id="dws-plugins" method="post">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		<?php $plugins_table->display(); ?>
    </form>
</div>