<?php

namespace Deep_Web_Solutions\Core;
if (!defined('ABSPATH')) { exit; }

/**
 * Defines the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since   1.0.0
 * @version 1.3.0
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
	 * @param   DWS_WordPress_Loader    $loader
	 */
	protected function define_hooks($loader) {
		$loader->add_action('plugins_loaded', $this, 'load_muplugin_textdomain');
		$loader->add_action('loco_plugins_data', $this, 'register_with_loco_translate_plugin');

		$loader->add_filter('dws_wpml_get-mu_plugins', $this, 'properly_register_plugin_with_wpml');
		$loader->add_filter('dws_wpml_plugin-file-name', $this, 'properly_name_plugin_with_wpml', 10, 2);
		$loader->add_filter('dws_wpml_mu-plugin-data', $this, 'properly_add_plugin_data_to_wpml', 10, 2);
		$loader->add_filter('wpml_sub_setting', $this, 'wpml_append_mu_plugin_localization_data', 11, 3);
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

	/**
	 * Properly registers the DWS WordPress core plugin with WPML.
	 *
	 * @since   1.3.0
	 * @version 1.3.0
	 *
	 * @author  Dushan Terzikj  <d.terzikj@deep-web-solutions.de>
	 *
	 * @see     wp_get_mu_plugins()
	 *
	 * @param   array   $mu_plugins     The mu-plugins installed.
	 *
	 * @return  array   The proper mu-plugin of the core.
	 */
	public function properly_register_plugin_with_wpml($mu_plugins) {
		$proper_mu_plugins = array();
		foreach ($mu_plugins as $mu_plugin) {
			if (strpos($mu_plugin, 'index.php') !== false && strpos($mu_plugin, 'dws-loader.php') !== false) {
				$proper_mu_plugins[] = $mu_plugin;
			}
		}

		$proper_mu_plugins[] = trailingslashit(DWS_CUSTOM_EXTENSIONS_BASE_PATH) . 'dws-custom-extensions.php';
		return $proper_mu_plugins;
	}

	/**
	 * Returns the name of the plugin, either only it's file name or it's full path.
	 *
	 * @since   1.3.0
	 * @version 1.3.0
	 *
	 * @author  Dushan Terzikj  <d.terzikj@deep-web-solutions.de>
	 *
	 * @param   string  $plugin_file    The file name of the plugin.
	 * @param   string  $full_path      The full path of the plugin.
	 *
	 * @return  string  Either just the file name of the plugin or the full path of the plugin.
	 */
	public function properly_name_plugin_with_wpml($plugin_file, $full_path) {
		if (strpos($full_path, 'dws-custom-extensions.php') !== false) {
			return $full_path;
		}

		return $plugin_file;
	}

	/**
	 * Adds some data to the mu-plugins provided.
	 *
	 * @since   1.3.0
	 * @version 1.3.0
	 *
	 * @author  Dushan Terzikj  <d.terzikj@deep-web-solutions.de>
	 *
	 * @param   array   $plugin_info    The information of the mu-plugin currently available.
	 * @param   string  $plugin_file    The name of the plugin file as it's registered with WPML.
	 *
	 * @return  array   Potentially modified mu-plugin information.
	 */
	public function properly_add_plugin_data_to_wpml ($plugin_info, $plugin_file) {
		if (strpos($plugin_file, 'dws-custom-extensions.php') !== false) {
			$plugin_info['Name']        = 'MU :: ' . self::get_plugin_name();
			$plugin_info['Title']       = self::get_plugin_name();
			$plugin_info['Version']     = self::get_plugin_version();
			$plugin_info['TextDomain']  = DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN;
			$plugin_info['DomainPath']  = basename(DWS_CUSTOM_EXTENSIONS_BASE_PATH) . '/languages';
			$plugin_info['Author']      = self::get_plugin_author_name();
			$plugin_info['AuthorName']  = self::get_plugin_author_name();
			$plugin_info['AuthorURI']   = self::get_plugin_author_uri();
			$plugin_info['Description'] = self::get_plugin_description();
		}

		return $plugin_info;
	}

	/**
	 * Appends localization information for mu-plugins.
	 *
	 * @since   1.3.0
	 * @version 1.3.0
	 *
	 * @author  Dushan Terzikj  <d.terzikj@deep-web-solutions.de>
	 *
	 * @param   array   $localization_data  Already loaded localization data.
	 * @param   string  $st                 Type of translation support: String translation or load mo files.
	 * @param   string  $component_type     Component type. Either plugin or theme.
	 *
	 * @return  array   The expected array containing domain language information.
	 */
	public function wpml_append_mu_plugin_localization_data ($localization_data, $st, $component_type) {
		if($component_type === 'plugin_localization_domains'){
			$mu_plugins = $this->properly_register_plugin_with_wpml(wp_get_mu_plugins());
			foreach ($mu_plugins as $mup) {
				if (strpos($mup, 'dws-custom-extensions.php') !== false) {
					// the actual number doesn't matter, so 0 is as good as any
					$localization_data[$mup] = array(DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN => 0);
					break;
				}
			}
		}

		return $localization_data;
	}

	//endregion
}