<?php

namespace Deep_Web_Solutions\Admin;
use Deep_Web_Solutions\Core\DWS_Functionality_Template;

if (!defined('ABSPATH')) { exit; }

/**
 * Handles the functionality of our own DeepWebSolutions menu in the WP backend.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Cezar Hegyes   <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Functionality_Template
 */
final class DWS_Dashboard extends DWS_Functionality_Template {
	//region FIELDS AND CONSTANTS

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string  MENU_PAGES_SLUG_PREFIX  The proper prefix of the slug of all DWS dashboard pages.
	 */
	const MENU_PAGES_SLUG_PREFIX = 'dws_custom-extensions_';

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  private
	 * @var     string  $main_page_slug     The slug of the main DWS dashboard page.
	 */
	private $main_page_slug = self::MENU_PAGES_SLUG_PREFIX . 'main';

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  private
	 * @var     string  $plugins_page_slug  The slug of the recommended plugins DWS dashboard page.
	 */
	private $plugins_page_slug = self::MENU_PAGES_SLUG_PREFIX . 'recommended-plugins';

	//endregion

	//region INHERITED FUNCTIONS

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Functionality_Template::define_functionality_hooks()
	 *
	 * @param   \Deep_Web_Solutions\Core\DWS_WordPress_Loader   $loader
	 */
	protected function define_functionality_hooks($loader) {
		$loader->add_filter('admin_menu', $this, 'register_menu_page', PHP_INT_MAX);
	}

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Functionality_Template::load_dependencies()
	 */
	protected function load_dependencies() {
		/** @noinspection PhpIncludeInspection */
		/** Handles the DWS recommended plugins list, installation, and updates. */
		require_once(self::get_includes_base_path() . 'class-recommended-plugins.php');
	}

	//endregion

	//region COMPATIBILITY LOGIC

	/**
	 * Registers the DWS admin menu pages.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function register_menu_page() {
		add_menu_page(
			'Deep Web Solutions', 'Deep Web Solutions',
			'administrator',
			$this->main_page_slug,
			array($this, 'menu_page_screen'),
			'data:image/svg+xml;base64,' . base64_encode(file_get_contents(DWS_Admin::get_assets_base_path() . 'dws_logo.svg')),
			3
		);

		add_submenu_page(
			$this->main_page_slug,
			__('Deep Web Solutions: Custom Extensions Recommended Plugins', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
			__('Recommended Plugins', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
			'administrator',
			$this->plugins_page_slug,
			array($this, 'menu_page_screen')
		);
	}

	/**
	 * Includes the file responsible for outputting the content of each DWS dashboard page.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function menu_page_screen() {
		global $plugin_page;

		$view_file = self::get_templates_base_path() . str_replace(self::MENU_PAGES_SLUG_PREFIX, '', $plugin_page) . '.php';
		if (file_exists($view_file)) {
			/** @noinspection PhpIncludeInspection */
			include($view_file);
		}
	}

	//endregion
}