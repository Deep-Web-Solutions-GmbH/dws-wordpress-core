<?php

namespace Deep_Web_Solutions\Admin;
use Deep_Web_Solutions\Admin\Dashboard\DWS_Recommended_Plugins;
use Deep_Web_Solutions\Admin\Dashboard\Permissions;
use Deep_Web_Solutions\Core\DWS_Functionality_Template;
use Deep_Web_Solutions\Core\DWS_Installation;

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
	 * @since   1.2.0   New public and static modifiers.
	 * @version 1.2.0
	 *
	 * @access  public
	 * @var     string  $main_page_slug     The slug of the main DWS dashboard page.
	 */
	public static $main_page_slug = self::MENU_PAGES_SLUG_PREFIX . 'main';

	//endregion

	//region INHERITED FUNCTIONS

	/**
	 * @since   1.0.0
	 * @version 1.3.4
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
		require_once(self::get_includes_base_path() . 'class-permissions.php');

		/** @noinspection PhpIncludeInspection */
		/** Handles the DWS recommended plugins list, installation, and updates. */
		require_once(self::get_includes_base_path() . 'class-recommended-plugins.php');
		DWS_Recommended_Plugins::maybe_initialize_singleton('sdfnhgi8he8gheife', true, self::get_root_id());
	}

	/**
	 * @since   1.2.0
	 * @version 1.2.0
	 *
	 * @param   string  $name
	 * @param   array   $extra
	 * @param   string  $root
	 *
	 * @return  string
	 */
	public static function get_hook_name($name, $extra = array(), $root = 'dws-dashboard') {
		return parent::get_hook_name($name, $extra, $root);
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
			Permissions::SEE_DWS_MENU_AND_DASHBOARD,
			self::$main_page_slug,
			array($this, 'menu_page_screen'),
			'data:image/svg+xml;base64,' . base64_encode(file_get_contents(DWS_Admin::get_assets_base_path() . 'dws_logo.svg')),
			3
		);

		/**
		 * @since   1.2.0
		 * @version 1.2.0
		 *
		 * @var     array   $submenu_pages  The submenu pages that should be registered.
		 *      $submenu_pages  = [
		 *          {$page_slug}  => [
		 *              'menu_title'    =>  (string) The menu title. Required.
		 *              'page_title'    =>  (string) The page title. Optional.
		 *              'capability'    =>  (string) The required WP capability. Required.
		 *          ]
		 *          ...
		 *      ]
		 */
		$submenu_pages = apply_filters(self::get_hook_name('submenus'), array());
		foreach ($submenu_pages as $page_slug => $options) {
			if (!isset($options['menu_title'], $options['capability'])) {
				continue;
			}

			add_submenu_page(
				self::$main_page_slug,
				isset($options['page_title']) ? $options['page_title'] : $options['menu_title'],
				$options['menu_title'],
				$options['capability'],
				$page_slug,
				array($this, 'menu_page_screen')
			);
		}
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
			error_log($view_file);
			include($view_file);
		}
	}

	//endregion
}