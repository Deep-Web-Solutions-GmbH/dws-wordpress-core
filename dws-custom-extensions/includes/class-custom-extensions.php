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
 * @version 2.0.0
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
	 * @var     \Puc_v4p6_Vcs_BaseChecker   $update_checker     An instance of the VCS updates checker.
	 */
	private $update_checker;

	/**
	 * @since   1.3.0
	 * @version 1.3.0
	 * @author  Dushan Terzikj  <d.terzikj@deep-web-solutions.de>
	 *
	 * @access  private
	 *
	 * @var     array   $author     Information about the author. Matches the plugin's header information.
	 */
	private static $author;

	/**
	 * @since   1.3.0
	 * @version 1.3.0
	 * @author  Dushan Terzikj  <d.terzikj@deep-web-solutions.de>
	 *
	 * @access  private
	 *
	 * @var     string   $description     Description about the plugin.
	 */
	private static $description;

	//endregion

	//region MAGIC METHODS

    /** @noinspection PhpMissingParentConstructorInspection */
    /**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since   1.0.0
	 * @version 1.4.1
	 *
	 * @see     DWS_Singleton::construct()
	 * @see     DWS_Singleton::get_instance()
	 * @see     DWS_Singleton::maybe_initialize_singleton()
	 */
	protected function __construct() {
        // plugin meta
		$plugin_data = \get_file_data(
			trailingslashit(DWS_CUSTOM_EXTENSIONS_BASE_PATH) . 'dws-custom-extensions.php',
            array(
				'Name'          => 'Plugin Name',
				'Version'       => 'Version',
				'Description'   => 'Description',
				'Author'        => 'Author',
				'AuthorURI'     => 'Author URI'
			)
		);

		// plugin meta
		self::$plugin_name = $plugin_data['Name'];
		self::$version     = $plugin_data['Version'];
		self::$author      = array(
			'name'  => $plugin_data['Author'],
			'uri'   => $plugin_data['AuthorURI']
		);
		self::$description = $plugin_data['Description'];

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
        DWS_Helper::load_files(DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'modules'); // modules must load before the plugins so that the DWS plugins can make use of and safely extend the modules classes
		DWS_Helper::load_files(DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'plugins');

		/** Fix an incompatibility with UpdraftPlus' use of the Puc library */
		if(!(isset($_REQUEST['page']) && $_REQUEST['page'] === 'updraftplus') && !wp_doing_ajax()) {
			// make sure we check for updates
			$this->update_checker = \Puc_v4p6_Factory::buildUpdateChecker(
				'https://github.com/Deep-Web-Solutions-GmbH/dws-wordpress-core',
				DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'dws-custom-extensions.php',
				'dws-wordpress-core',
				12,
				'',
				'dws-loader.php'
			);
			$this->update_checker->setAuthentication( DWS_GITHUB_ACCESS_TOKEN );
			$this->update_checker->setBranch( 'master' );
		}

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

	/**
	 * Retrieve author's name.
	 *
	 * @since   1.3.0
	 * @version 1.3.0
	 * @author  Dushan Terzikj  <d.terzikj@deep-web-solutions.de>
	 *
	 * @return  string  Author's name.
	 */
	public static function get_plugin_author_name(){
		return self::$author['name'];
	}

	/**
	 * Retrieve author's URI.
	 *
	 * @since   1.3.0
	 * @version 1.3.0
	 * @author  Dushan Terzikj  <d.terzikj@deep-web-solutions.de>
	 *
	 * @return  string  Author's URI.
	 */
	public static function get_plugin_author_uri(){
		return self::$author['uri'];
	}

	/**
	 * Retrieve the description of the plugin.
	 *
	 * @since   1.3.0
	 * @version 1.3.0
	 * @author  Dushan Terzikj  <d.terzikj@deep-web-solutions.de>
	 *
	 * @return  string  The description of the plugin.
	 */
	public static function get_plugin_description(){
		return self::$description;
	}

	//endregion

	//region HELPERS

	/**
	 * Loads the required files and libraries for this plugin.
	 *
	 * @since   1.0.0
	 * @version 2.0.0
	 */
	private function load_dependencies() {
		//region LIBRARIES

		/** It is important to have the WordPress plugin functions loaded. */
		require_once(ABSPATH . 'wp-admin/includes/plugin.php');

        /** Fix an incompatibility with UpdraftPlus' use of the Puc library */
		if(!(isset($_REQUEST['page']) && $_REQUEST['page'] === 'updraftplus') && !wp_doing_ajax()) {
			/** @noinspection PhpIncludeInspection */
			/** We use this external library to check for new plugin version in GitHub releases. */
			require_once( DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'libraries/plugin-update-checker/plugin-update-checker.php' );
		}

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