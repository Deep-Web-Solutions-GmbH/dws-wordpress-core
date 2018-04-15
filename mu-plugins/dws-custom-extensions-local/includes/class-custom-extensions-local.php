<?php

namespace Deep_Web_Solutions\Local;
use Deep_Web_Solutions\Core\DWS_Singleton;
use Deep_Web_Solutions\Core\DWS_WordPress_Loader;
use Deep_Web_Solutions\Local\Core\DWS_Local_Admin;

if (!defined('ABSPATH')) { exit; }

/**
 * The core plugin class that is used to define internationalization, hooks, and local extensions.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 */
final class Custom_Extensions_Local extends DWS_Singleton {
	//region FIELDS

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  private
	 * @var     string      $plugin_name    The string used to uniquely identify this plugin.
	 */
	private static $plugin_name;

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  private
	 * @var     string      $version    The current version of the plugin.
	 */
	private static $version;

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  private
	 * @var     bool        True if the plugin is active, false otherwise.
	 */
	private static $is_active = true;

	//endregion

	//region MAGIC METHODS

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the custom extensions.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Singleton::construct()
	 * @see     DWS_Singleton::get_instance()
	 * @see     DWS_Singleton::maybe_initialize_singleton()
	 */
	protected function __construct() {
		// plugin meta
		self::$plugin_name = 'DWS_custom-extensions-local';
		self::$version = '1.0.0';

		// load required files
		$this->load_dependencies();

		// set up the plugin
		Core\DWS_Local_i18n::maybe_initialize_singleton('rhwg872g8723g');

		// finish initializing the plugin
		DWS_Local_Admin::maybe_initialize_singleton('h8ehge8gririw', __('DWS Custom Extensions Local - Admin', DWS_CUSTOM_EXTENSIONS_LOCAL_LANG_DOMAIN));
		DWS_Local::maybe_initialize_singleton('sajg4hg87heegius', __('DWS Custom Extensions Local - Custom', DWS_CUSTOM_EXTENSIONS_LOCAL_BASE_PATH));

		// plugin has been initialized, now run it
		$this->run();
	}

	//endregion

	//region METHODS

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function run() {
		$loader = DWS_WordPress_Loader::get_instance();
		$loader->run();
	}

	/**
	 * Currently not being used. The plugin is always active by default.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  bool    True if this plugin is active, false otherwise.
	 */
	public static function is_active() {
		return self::$is_active;
	}

	//endregion

	//region HELPERS

	/**
	 * Loads the required files and libraries for this plugin.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	private function load_dependencies() {
		//region ABSTRACT CLASSES

		/** @noinspection PhpIncludeInspection */
		/** The core root template tailored to the needs of local extensions. */
		require_once( DWS_CUSTOM_EXTENSIONS_LOCAL_BASE_PATH . 'includes/abstract-root.php');

		/** @noinspection PhpIncludeInspection */
		/** The core functionality template tailored to the needs of local extensions. */
		require_once(DWS_CUSTOM_EXTENSIONS_LOCAL_BASE_PATH . 'includes/abstract-functionality.php');

		//endregion

		//region CORE CLASSES

		/** @noinspection PhpIncludeInspection */
		/** Responsible for defining internationalization functionality of the plugin. */
		require_once(DWS_CUSTOM_EXTENSIONS_LOCAL_BASE_PATH . 'includes/class-i18n.php');

		/** @noinspection PhpIncludeInspection */
		/** Responsible for setting up the admin area for this plugin. */
		require_once(DWS_CUSTOM_EXTENSIONS_LOCAL_BASE_PATH . 'includes/class-admin.php');

		/** @noinspection PhpIncludeInspection */
		/** Responsible for loading the local extensions. */
		require_once(DWS_CUSTOM_EXTENSIONS_LOCAL_BASE_PATH . 'local/local.php');

		//endregion
	}

	//endregion
}