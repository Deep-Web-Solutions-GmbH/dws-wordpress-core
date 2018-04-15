<?php if (!defined('ABSPATH')) { exit; }

require_once('dws-custom-extensions/dws-custom-extensions.php');
if (class_exists('\Deep_Web_Solutions\Custom_Extensions') && Deep_Web_Solutions\Custom_Extensions::is_active()) {
	if (file_exists(WPMU_PLUGIN_DIR . '/dws-custom-extensions-local/dws-custom-extensions-local.php')) {
		require_once('dws-custom-extensions-local/dws-custom-extensions-local.php');
	}
}