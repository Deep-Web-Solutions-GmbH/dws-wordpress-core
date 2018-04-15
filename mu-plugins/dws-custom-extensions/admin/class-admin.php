<?php

namespace Deep_Web_Solutions\Admin;
use Deep_Web_Solutions\Core\DWS_Root;

if (!defined('ABSPATH')) { exit; }

/**
 * Orchestrates the DWS Core extensions of the back-end area of the website.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Root
 */
final class DWS_Admin extends DWS_Root {
	//region INHERITED FUNCTIONS

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Root::admin_enqueue_assets()
	 *
	 * @param   string  $hook
	 */
	public function admin_enqueue_assets($hook) {
		wp_enqueue_style(self::get_asset_handle(), self::get_assets_base_path(true) . 'style.css', array(), self::get_plugin_version(), 'all');
		wp_enqueue_script(self::get_asset_handle(), self::get_assets_base_path(true) . 'scripts.js', array('jquery'), self::get_plugin_version(), false);
	}

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Root::load_dependencies()
	 */
	protected function load_dependencies() {
		/** @noinspection PhpIncludeInspection */
		/** Handles the functionality of our own DeepWebSolutions menu in the WP backend. */
		require_once(self::get_includes_base_path() . 'dashboard/class-dashboard.php');
		DWS_Dashboard::maybe_initialize_singleton('j8e7h87gh87gwie');

		/** @noinspection PhpIncludeInspection */
		/** Handles all the ACF related extensions including the options pages. */
		require_once(self::get_includes_base_path() . 'acf/class-acf.php');
		DWS_ACF::maybe_initialize_singleton('jh87h4g83g43');

		/** @noinspection PhpIncludeInspection */
		/** Handles the registration and display of DWS notices in the admin area. */
		require_once(self::get_includes_base_path() . 'class-admin-notices.php');
		DWS_Admin_Notices::maybe_initialize_singleton('h84872383g4g4');

		/** @noinspection PhpIncludeInspection */
		/** Handles extensions and customizations to the WP users. */
		require_once(self::get_includes_base_path() . 'class-users.php');
		DWS_Users::maybe_initialize_singleton('hsr8gh8e45hrhr');
	}

	//endregion
}