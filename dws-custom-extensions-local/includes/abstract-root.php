<?php

namespace Deep_Web_Solutions\Local\Core;
use Deep_Web_Solutions\Admin\Settings\DWS_Settings_Pages;
use Deep_Web_Solutions\Base\DWS_Root;
use Deep_Web_Solutions\Local\Custom_Extensions_Local;

if (!defined('ABSPATH')) { exit; }

/**
 * The core root template tailored to the needs of local extensions.
 *
 * @version 1.0.0
 * @since   2.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Root
 */
abstract class DWS_Local_Root extends DWS_Root {
	//region INHERITED FUNCTIONS

	/**
	 * @since   1.0.0
	 * @version 2.0.0
	 *
	 * @see     DWS_Root::local_configure()
	 */
	protected function local_configure() {
		$this->settings_filter = DWS_Settings_Pages::get_page_groups_hook(DWS_Local_Admin::LOCAL_OPTIONS_SLUG);
	}

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Root::get_relative_base_path()
	 *
	 * @return  string
	 */
	public static function get_relative_base_path() {
		return str_replace(DWS_CUSTOM_EXTENSIONS_LOCAL_BASE_PATH, '', self::get_base_path());
	}

	//endregion

    //region INHERITED GETTERS

    /**
     * Gets the current plugin description.
     *
     * @since   1.3.0
     * @version 1.3.0
     *
     * @return  string  plugin description of the current plugin.
     */
    protected static function get_plugin_description(){
        return Custom_Extensions_Local::get_plugin_description();
    }

    /**
     * Gets the current plugin author name.
     *
     * @since   1.3.0
     * @version 1.3.0
     *
     * @return  string  The author's name of the current plugin.
     */
    protected static function get_plugin_author_name(){
        return Custom_Extensions_Local::get_plugin_author_name();
    }

    /**
     * Gets the current plugin author URI.
     *
     * @since   1.3.0
     * @version 1.3.0
     *
     * @return  string  The author's URI of the current plugin.
     */
    protected static function get_plugin_author_uri(){
        return Custom_Extensions_Local::get_plugin_author_uri();
    }

    /**
     * Gets the current plugin name.
     *
     * @since   1.3.0
     * @version 1.3.0
     *
     * @return  string  The name of the current plugin.
     */
    protected static function get_plugin_name() {
        return Custom_Extensions_Local::get_plugin_name();
    }

    /**
     * Gets the current plugin version.
     *
     * @since   1.3.0
     * @version 1.3.0
     *
     * @return  string  The version of the current plugin.
     */
    protected static function get_plugin_version() {
        return Custom_Extensions_Local::get_version();
    }

    //endregion
}