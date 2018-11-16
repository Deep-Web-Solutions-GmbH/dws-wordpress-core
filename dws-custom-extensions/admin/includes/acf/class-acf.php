<?php

namespace Deep_Web_Solutions\Admin;
use Custom_Extensions\Admin\ACF\ACF_Custom_Field_Types;
use Deep_Web_Solutions\Admin\ACF\ACF_Fields;
use Deep_Web_Solutions\Admin\ACF\ACF_Options;
use Deep_Web_Solutions\Core\DWS_Functionality_Template;

if (!defined('ABSPATH')) { exit; }

/**
 * Handles all the ACF related extensions including the options pages.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Functionality_Template
 */
final class DWS_ACF extends DWS_Functionality_Template {
	//region INHERITED FUNCTIONS

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Functionality_Template::load_dependencies()
	 */
	protected function load_dependencies() {
		/** @noinspection PhpIncludeInspection */
		/** The custom DWS permissions needed to enhance the ACF library. */
		require_once(self::get_includes_base_path() . 'class-permissions.php');

		/** @noinspection PhpIncludeInspection */
		/** Handles the registering of options in the back-end of the website. */
		require_once(self::get_includes_base_path() . 'class-acf-options.php');
		ACF_Options::maybe_initialize_singleton('hri8uhg284hg428', true, self::get_root_id());

		/** @noinspection PhpIncludeInspection */
		/** Handles customizations to the ACF fields and their functionalities. */
		require_once(self::get_includes_base_path() . 'class-acf-fields.php');
		ACF_Fields::maybe_initialize_singleton('h7843gh834g4g4', true, self::get_root_id());

		/** @noinspection PhpIncludeInspection */
		/** Register new types of ACF fields. */
		require_once(self::get_includes_base_path() . 'custom-field-types/custom-field-types.php');
		ACF_Custom_Field_Types::maybe_initialize_singleton('h478gh8g2113');
	}

    /**
     * @since   1.4.0
     * @version 1.4.0
     *
     * @see     DWS_Functionality_Template::admin_enqueue_assets()
     *
     * @param   string  $hook
     */
    public function admin_enqueue_assets($hook) {
        wp_enqueue_style(self::get_asset_handle(), self::get_assets_base_path(true) . 'style.css', array(), self::get_plugin_version());
    }

	//endregion
}