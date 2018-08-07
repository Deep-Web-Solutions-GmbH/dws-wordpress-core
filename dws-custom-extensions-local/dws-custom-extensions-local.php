<?php if (!defined('ABSPATH')) { exit; }

/**
 * The DWS Custom Local Extensions bootstrap file.
 *
 * @link              https://www.deep-web-solutions.de
 * @since             1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       DeepWebSolutions Custom Extensions Local
 * Description:       Handles all the local custom extensions to this WordPress installation.
 * Version:           1.1.0
 * Author:            Deep Web Solutions GmbH
 * Author URI:        https://www.deep-web-solutions.de
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       dws_custom-extensions-local
 * Domain Path:       /languages
 */

define('DWS_CUSTOM_EXTENSIONS_NAME_LOCAL', 'Deep Web Solutions: Custom Extensions Local');

define('DWS_CUSTOM_EXTENSIONS_LOCAL_BASE_PATH', plugin_dir_path(__FILE__));
define('DWS_CUSTOM_EXTENSIONS_LOCAL_LANG_DOMAIN', 'dws_custom-extensions-local');

/** @noinspection PhpIncludeInspection */
/** The core plugin class that is used to define internationalization, hooks, and local extensions. */
require_once(DWS_CUSTOM_EXTENSIONS_LOCAL_BASE_PATH . 'includes/class-custom-extensions-local.php');

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
if (class_exists('\Deep_Web_Solutions\Local\Custom_Extensions_Local')) {
	$GLOBALS['dws_custom-extensions-local'] = \Deep_Web_Solutions\Local\Custom_Extensions_Local::get_instance();
}