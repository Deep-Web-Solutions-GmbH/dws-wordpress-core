<?php if (!defined('ABSPATH')) { exit; }

$plugins_table = new \Deep_Web_Solutions\Admin\Dashboard\DWS_Plugins_List_Table();

?>

<div class="wrap">
    <h1><?php esc_html_e('Recommended Plugins', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN); ?></h1>
	<?php $plugins_table->prepare_items(); ?>
	<?php $plugins_table->views(); ?>

    <form id="dws-plugins" action="" method="post">
		<?php $plugins_table->display(); ?>
    </form>
</div>

