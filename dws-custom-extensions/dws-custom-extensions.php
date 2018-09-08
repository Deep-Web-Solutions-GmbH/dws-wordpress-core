<?php if (!defined('ABSPATH')) { exit; }

/**
 * The DWS Custom Extensions bootstrap file.
 *
 * @since               1.0.0
 * @since               1.1.0   Update notifications
 * @since               1.2.0   Updates and installation of plugins, including dws plugins and dws modules.
 *
 * @wordpress-plugin
 * Plugin Name:         DeepWebSolutions Custom Extensions
 * Description:         This plugin handles all the core custom extensions to this WordPress installation.
 * Version:             1.3.2
 * Author:              Deep Web Solutions GmbH
 * Author URI:          https://www.deep-web-solutions.de
 * License:             GPL-3.0+
 * License URI:         http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:         dws_custom-extensions
 * Domain Path:         /languages
 */

define('DWS_CUSTOM_EXTENSIONS_NAME', 'Deep Web Solutions: Custom Extensions');
define('DWS_CUSTOM_EXTENSIONS_MIN_PHP', '7.2');
define('DWS_CUSTOM_EXTENSIONS_MIN_WP', '4.9.8');

define('DWS_CUSTOM_EXTENSIONS_BASE_PATH', plugin_dir_path(__FILE__));
define('DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN', 'dws_custom-extensions');
define('DWS_GITHUB_ACCESS_TOKEN', 'd6e10cc22fce9c7e4d5dd716f93359f529e1b086');

/**
 * Checks if the system requirements are met.
 *
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return  bool    True if system requirements are met, otherwise false.
 */
function dws_custom_extensions_requirements_met() {
	if (version_compare(PHP_VERSION, DWS_CUSTOM_EXTENSIONS_MIN_PHP, '<')) {
		return false;
	} else if (version_compare($GLOBALS['wp_version'], DWS_CUSTOM_EXTENSIONS_MIN_WP, '<')) {
		return false;
	}

	return true;
}

/**
 * Prints an error that the system requirements weren't met.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
function dws_requirements_error() {
	/** @noinspection PhpIncludeInspection */
	require_once(DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'admin/templates/requirements-error.php');
}

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
if (dws_custom_extensions_requirements_met()) {
	/** @noinspection PhpIncludeInspection */
	/**
	 * Abstract class defining the functionality of a singleton. Required because the
	 * main plugin class is a singleton itself.
	 */
	require_once(DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'includes/abstract-singleton.php');

	/** @noinspection PhpIncludeInspection */
	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require_once(DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'includes/class-custom-extensions.php');

	if (class_exists('\Deep_Web_Solutions\Custom_Extensions')) {
		$GLOBALS['dws_custom-extensions'] = \Deep_Web_Solutions\Custom_Extensions::get_instance();
		/**
		 * It is very important that the loader gets ran again after we make sure that all the actions have been
		 * registered, and that's only at the end of this action.
		 *
		 * @see     \Deep_Web_Solutions\Core\DWS_Root::__construct()
		 * @see     \Deep_Web_Solutions\Core\DWS_Root::configure_class()
		 */
		add_action('muplugins_loaded', array($GLOBALS['dws_custom-extensions'], 'run'), PHP_INT_MAX);
	}
} else {
	add_action('admin_notices', 'dws_requirements_error');
}