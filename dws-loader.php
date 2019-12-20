<?php if (!defined('ABSPATH')) { exit; }

/**
 * The Deep Web Solutions bootstrap file.
 *
 * @since               1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:         DWS Custom Extensions
 * Description:         This plugin loads the DWS Custom Extensions Core and DWS Custom Extensions Local plugins.
 * Version:             2.1.0
 * Author:              Deep Web Solutions GmbH
 * Author URI:          https://www.deep-web-solutions.de
 * License:             GPL-3.0+
 * License URI:         http://www.gnu.org/licenses/gpl-3.0.txt
 */

require_once('dws-custom-extensions/dws-custom-extensions.php');
if (class_exists('\Deep_Web_Solutions\Custom_Extensions') && Deep_Web_Solutions\Custom_Extensions::is_active()) {
	if (file_exists(WPMU_PLUGIN_DIR . '/dws-custom-extensions-local/dws-custom-extensions-local.php')) {
		require_once('dws-custom-extensions-local/dws-custom-extensions-local.php');
	}
}