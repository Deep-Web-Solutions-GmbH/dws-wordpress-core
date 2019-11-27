<?php

namespace Deep_Web_Solutions\Core;
use Deep_Web_Solutions\Base\DWS_Root;

if (!defined('ABSPATH')) { exit; }

/**
 * Defines the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since   1.0.0
 * @version 2.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Root
 */
final class DWS_i18n extends DWS_Root {
	//region INHERITED FUNCTIONS

	/**
	 * @since   1.0.0
	 * @version 1.3.0
	 *
	 * @see     DWS_Root::define_hooks()
	 *
	 * @param   DWS_Loader    $loader
	 */
	protected function define_hooks($loader) {
		$loader->add_action('plugins_loaded', $this, 'load_muplugin_textdomain');
		$loader->add_action('loco_plugins_data', $this, 'register_with_loco_translate_plugin');
	}

	//endregion

	//region METHODS

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function load_muplugin_textdomain() {
		load_muplugin_textdomain(
			DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN,
			basename(DWS_CUSTOM_EXTENSIONS_BASE_PATH) . '/languages'
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
	 * @return  array   The plugins registered with Loco Translate including the DWS CustomExtensions plugin.
	 */
	public function register_with_loco_translate_plugin(array $plugins) {
		// we know the plugin by this handle, even if WordPress doesn't
		$handle = 'dws-custom-extensions/dws-custom-extensions.php';

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