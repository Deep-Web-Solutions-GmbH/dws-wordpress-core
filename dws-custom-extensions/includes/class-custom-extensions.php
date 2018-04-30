<?php

namespace Deep_Web_Solutions;
use Deep_Web_Solutions\Core\DWS_Helper;
use Deep_Web_Solutions\Core\DWS_Singleton;
use Deep_Web_Solutions\Core\DWS_WordPress_Loader;

if (!defined('ABSPATH')) { exit; }

/**
 * The core plugin class that is used to define internationalization, hooks, and all the other extensions.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Singleton
 */
final class Custom_Extensions extends DWS_Singleton {
	//region FIELDS

	/**
	 * @since       1.0.0
	 * @version     1.0.0
	 *
	 * @access      private
	 * @var         string      $plugin_name    The string used to uniquely identify this plugin.
	 */
	private static $plugin_name;

	/**
	 * @since       1.0.0
	 * @version     1.0.0
	 *
	 * @access      private
	 * @var         string      $version    The current version of the plugin.
	 */
	private static $version;

	/**
	 * @since       1.0.0
	 * @version     1.0.0
	 *
	 * @access      private
	 * @var         bool        $is_active  Whether the plugin has been successfully activated or not.
	 */
	private static $is_active = true;

	/**
	 * @since   1.1.0
	 * @version 1.1.0
	 *
	 * @access  private
	 * @var     \Puc_v4p4_Vcs_BaseChecker   $update_checker     An instance of the VCS updates checker.
	 */
	private $update_checker;

	//endregion

	//region MAGIC METHODS

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
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
		self::$plugin_name = 'DWS_custom-extensions';
		self::$version     = '1.0.0';

		// load required files
		$this->load_dependencies();

		// set up the plugin
		Core\DWS_i18n::maybe_initialize_singleton('rhwg872g8723g');
		Core\DWS_Installation::maybe_initialize_singleton('h87h8g743g3g4');
		Core\DWS_WordPress_Cron::maybe_initialize_singleton('uoawg2gh483g3guire');
		Core\DWS_Permissions::maybe_initialize_singleton('kohe87g48ghergiue');
		Core\DWS_Login::maybe_initialize_singleton('hg487g87wgfiwe');

		// finish initializing the plugin
		Admin\DWS_Admin::maybe_initialize_singleton('h84hg874hg3f');
		Front\DWS_Public::maybe_initialize_singleton('85h487g8743f422');
		DWS_Helper::load_files(DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'plugins');
		DWS_Helper::load_files(DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'modules');

		// make sure we check for updates
		$this->update_checker = \Puc_v4_Factory::buildUpdateChecker(
			'https://github.com/Deep-Web-Solutions-GmbH/dws-wordpress-core',
			DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'dws-custom-extensions.php',
			'dws-wordpress-core',
			12,
			'',
			'dws-loader.php'
		);
		$this->update_checker->setAuthentication(DWS_GITHUB_ACCESS_TOKEN);
		$this->update_checker->setBranch('master');

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

	//region GETTERS

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public static function get_plugin_name() {
		return self::$plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since       1.0.0
	 * @version     1.0.0
	 * @return      string    The version number of the plugin.
	 */
	public static function get_version() {
		return self::$version;
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
		//region LIBRARIES

		/** It is important to have the WordPress plugin functions loaded. */
		require_once(ABSPATH . 'wp-admin/includes/plugin.php');

		/** @noinspection PhpIncludeInspection */
		/** Our extensions rely heavily on ACF Pro. We load it first before anything else. */
		require_once(DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'libraries/advanced-custom-fields-pro/acf.php');

		/** @noinspection PhpIncludeInspection */
		/** We use this external library to check for new plugin version in GitHub releases. */
		require_once(DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'libraries/plugin-update-checker/plugin-update-checker.php');

		/** @noinspection PhpIncludeInspection */
		/** We use this external library to install and update plugins. */
		require_once(DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'libraries/TGM-Plugin-Activation/class-tgm-plugin-activation.php');

		//endregion

		//region HELPER CLASSES

		/** @noinspection PhpIncludeInspection */
		/** Abstract implementation of a singleton class. */
		require_once(DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'includes/abstract-singleton.php');

		/** @noinspection PhpIncludeInspection */
		/** Abstract class encapsulating methods usually useful for classes which lie at the root of a folder. */
		require_once(DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'includes/abstract-root.php');

		/** @noinspection PhpIncludeInspection */
		/** Extended version of a root class which allows for nesting and other cool stuff. */
		require_once(DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'includes/abstract-functionality-template.php');

		/** @noinspection PhpIncludeInspection */
		/** Extended version of a functionality template. Used as a module functionality. */
		require_once(DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'includes/abstract-module-functionality-template.php');

		/** @noinspection PhpIncludeInspection */
		/** Extended version of a module functionality template. Used as a module root class. */
		require_once(DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'includes/abstract-module-template.php');

		/** @noinspection PhpIncludeInspection */
		/** Classes which require installation should implement this interface. */
		require_once(DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'includes/interface-installable.php');

		//endregion

		//region CORE

		/** @noinspection PhpIncludeInspection */
		/** The class responsible for making sure that the environment is properly configured when this plugin loads. */
		require_once(DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'includes/class-installation.php');

		/** @noinspection PhpIncludeInspection */
		/** The class responsible for orchestrating the actions and filters of the core plugin. */
		require_once(DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'includes/class-loader.php');

		/** @noinspection PhpIncludeInspection */
		/** The class responsible for defining internationalization functionality of the plugin. */
		require_once(DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'includes/class-i18n.php');

		/** @noinspection PhpIncludeInspection */
		/** The class responsible for handling crons scheduling. */
		require_once(DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'includes/class-crons.php');

		/** @noinspection PhpIncludeInspection */
		/** The class responsible for enhancing the login experience. */
		require_once(DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'includes/class-login.php');

		/** @noinspection PhpIncludeInspection */
		/** A collection of useful helper functions. */
		require_once(DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'includes/class-helpers.php');

		/** @noinspection PhpIncludeInspection */
		/** The class responsible for handling core permissons. */
		require_once(DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'includes/class-permissions.php');

		/** @noinspection PhpIncludeInspection */
		/** The class responsible for managing the core actions that occur in the admin area. */
		require_once(DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'admin/class-admin.php');

		/** @noinspection PhpIncludeInspection */
		/** The class responsible for managing the core actions the occur in the front-end area. */
		require_once(DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'public/class-public.php');

		//endregion
	}

	//endregion
}