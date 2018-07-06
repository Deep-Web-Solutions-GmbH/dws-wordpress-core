<?php

namespace Deep_Web_Solutions\Local;
use Deep_Web_Solutions\Core\DWS_Helper;
use Deep_Web_Solutions\Core\DWS_Singleton;
use Deep_Web_Solutions\Core\DWS_WordPress_Loader;

if (!defined('ABSPATH')) { exit; }

/**
 * The bootstrap file for all the local custom extensions.
 *
 * @since             1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       DeepWebSolutions Custom Extensions Local - Custom
 * Description:       Loads all the DWS local custom extensions made to this WordPress installation.
 * Version:           1.0.0
 * Author:            Deep Web Solutions GmbH
 * Author URI:        https://www.deep-web-solutions.de
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       dws_custom-extensions-local_custom
 * Domain Path:       /languages
 */
final class DWS_Local extends DWS_Singleton {
	//region MAGIC FUNCTIONS

	/**
	 * DWS_Local constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Singleton::__construct()
	 * @see     DWS_Singleton::get_instance()
	 * @see     DWS_Singleton::maybe_initialize_singleton()
	 */
	protected function __construct() {
		// define internationalization
		$loader = DWS_WordPress_Loader::get_instance();
		$loader->add_action('plugins_loaded', $this, 'load_muplugin_textdomain');
		$loader->add_action('loco_plugins_data', $this, 'register_with_loco_translate_plugin');

		// load local extensions files
		DWS_Helper::load_files(DWS_CUSTOM_EXTENSIONS_LOCAL_BASE_PATH . 'local/admin/');
		DWS_Helper::load_files(DWS_CUSTOM_EXTENSIONS_LOCAL_BASE_PATH . 'local/public/');
		DWS_Helper::load_files(DWS_CUSTOM_EXTENSIONS_LOCAL_BASE_PATH . 'local/plugins/');
	}

	//endregion

	//region COMPATIBILITY METHODS

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function load_muplugin_textdomain() {
		load_muplugin_textdomain(
			DWS_CUSTOM_EXTENSIONS_LOCAL_LANG_DOMAIN . '_custom',
			basename(DWS_CUSTOM_EXTENSIONS_LOCAL_BASE_PATH) . '/local/languages'
		);
	}

	/**
	 * MU plugins inside directories are not returned in `get_mu_plugins`.
	 * This filter modifies the array obtained from Wordpress when Loco Translate grabs it.
	 *
	 * Note that this filter only runs once per script execution, because the value is cached.
	 * Define the function *before* Loco Translate plugin is even included by WP.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $plugins    The plugins that have already been successfully registered with Loco Translate.
	 *
	 * @return  array   The plugins registered with Loco Translate including the pseudo-plugin of the local extensions.
	 */
	public function register_with_loco_translate_plugin(array $plugins) {
		// we know the plugin by this handle, even if WordPress doesn't
		$handle = 'dws-custom-extensions-local/local/local.php';

		// fetch the plugin's meta data from the would-be plugin file
		$data = get_plugin_data(trailingslashit(WPMU_PLUGIN_DIR) . $handle);

		// extra requirement of Loco - $handle must be resolvable to full path
		$data['basedir'] = WPMU_PLUGIN_DIR;

		// add to array and return back to Loco Translate
		$plugins[$handle] = $data;
		return $plugins;
	}

	//endregion
}