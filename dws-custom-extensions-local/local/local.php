<?php

namespace Deep_Web_Solutions\Local;
use Deep_Web_Solutions\Base\DWS_Singleton;
use Deep_Web_Solutions\Core\DWS_Loader;
use Deep_Web_Solutions\Helpers\DWS_Helper;

if (!defined('ABSPATH')) { exit; }

/**
 * The bootstrap file for all the local custom extensions.
 *
 * @since             1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       DeepWebSolutions Custom Extensions Local - Custom
 * Description:       Loads all the DWS local custom extensions made to this WordPress installation.
 * Version:           2.0.0
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
        $loader = DWS_Loader::get_instance();
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

    //endregion
}