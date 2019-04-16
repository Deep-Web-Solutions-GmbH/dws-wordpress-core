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
 * Version:           1.5.0
 * Author:            Deep Web Solutions GmbH
 * Author URI:        https://www.deep-web-solutions.de
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       dws_custom-extensions-local_custom
 * Domain Path:       /languages
 */
final class DWS_Local extends DWS_Singleton {
    //region MAGIC FUNCTIONS

    /** @noinspection PhpMissingParentConstructorInspection */
    /**
     * DWS_Local constructor.
     *
     * @since   1.0.0
     * @version 1.5.0
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

        $loader->add_filter('dws_wpml_get-mu_plugins', $this, 'properly_register_plugin_with_wpml', 100); // PRIORITY MUST BE HIGHER THAN 10
        $loader->add_filter('dws_wpml_plugin-file-name', $this, 'properly_name_plugin_with_wpml', 10, 2);
        $loader->add_filter('dws_wpml_mu-plugin-data', $this, 'properly_add_plugin_data_to_wpml', 10, 2);

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

    /**
     * Properly registers the DWS WordPress core plugin with WPML.
     *
     * @since   1.5.0
     * @version 1.5.0
     *
     * @see     wp_get_mu_plugins()
     *
     * @param   array   $mu_plugins     The mu-plugins installed.
     *
     * @return  array   The proper mu-plugin of the core.
     */
    public function properly_register_plugin_with_wpml($mu_plugins) {
        $mu_plugins[] = trailingslashit(DWS_CUSTOM_EXTENSIONS_LOCAL_BASE_PATH) . 'local/local.php';
        return $mu_plugins;
    }

    /**
     * Returns the name of the plugin, either only it's file name or it's full path.
     *
     * @since   1.5.0
     * @version 1.5.0
     *
     * @param   string  $plugin_file    The file name of the plugin.
     * @param   string  $full_path      The full path of the plugin.
     *
     * @return  string  Either just the file name of the plugin or the full path of the plugin.
     */
    public function properly_name_plugin_with_wpml($plugin_file, $full_path) {
        if (strpos($full_path, 'local/local.php') !== false) {
            return $full_path;
        }

        return $plugin_file;
    }

    /**
     * Adds some data to the mu-plugins provided.
     *
     * @since   1.5.0
     * @version 1.5.0
     *
     * @param   array   $plugin_info    The information of the mu-plugin currently available.
     * @param   string  $plugin_file    The name of the plugin file as it's registered with WPML.
     *
     * @return  array   Potentially modified mu-plugin information.
     */
    public function properly_add_plugin_data_to_wpml($plugin_info, $plugin_file) {
        if (strpos($plugin_file, 'local/local.php') !== false) {
            $plugin_info['Name']        = 'MU :: ' . Custom_Extensions_Local::get_plugin_name() . ' CUSTOM';
            $plugin_info['Title']       = Custom_Extensions_Local::get_plugin_name();
            $plugin_info['Version']     = Custom_Extensions_Local::get_version();
            $plugin_info['TextDomain']  = DWS_CUSTOM_EXTENSIONS_LOCAL_LANG_DOMAIN . '_custom';
            $plugin_info['DomainPath']  = basename(DWS_CUSTOM_EXTENSIONS_LOCAL_BASE_PATH) . '/local/languages';
            $plugin_info['Author']      = Custom_Extensions_Local::get_plugin_author_name();
            $plugin_info['AuthorName']  = Custom_Extensions_Local::get_plugin_author_name();
            $plugin_info['AuthorURI']   = Custom_Extensions_Local::get_plugin_author_uri();
            $plugin_info['Description'] = Custom_Extensions_Local::get_plugin_description();
        }

        return $plugin_info;
    }

    //endregion
}