<?php

namespace Deep_Web_Solutions\Core;
use Deep_Web_Solutions\Admin\ACF\ACF_Options;
use Deep_Web_Solutions\Custom_Extensions;

if (!defined('ABSPATH')) { exit; }

/**
 * Template for encapsulating some of the most often required abilities of a class.
 *
 * @since   1.0.0
 * @version 1.4.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Singleton
 */
abstract class DWS_Root extends DWS_Singleton {
	//region FIELDS AND CONSTANTS

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  private
	 * @var     array       $root_id        Maintains a list of all IDs of root class instances.
	 */
	private static $root_id = array();
	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  private
	 * @var     array       $root_public_name   Maintains a list of all public names of root class instances.
	 */
	private static $root_public_name = array();

	/**
	 * Modify this for local settings etc.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     string      $settings_filter    The name of the filter on which this class will register its ACF options.
	 */
	protected $settings_filter;

	/**
	 * The access is private to minimize abuse.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  private
	 * @var     DWS_WordPress_Loader    $loader     The instance of the DWS CustomExtensions Core Loader.
	 */
	private $loader;

	//endregion

	//region MAGIC METHODS

	/**
	 * The DWS_Root constructor.
	 *
	 * @since   1.0.0
	 * @version 1.4.0
	 *
	 * @see     DWS_Singleton::__construct()
	 * @see     DWS_Singleton::get_instance()
	 * @see     DWS_Singleton::maybe_initialize_singleton()
	 *
	 * @param   string          $root_id
	 * @param   string|bool     $root_name
	 */
	protected function __construct($root_id, $root_name = false) {
		self::$root_id[static::class]          = $root_id;
		self::$root_public_name[static::class] = $root_name ?: static::class;

		$this->loader = DWS_WordPress_Loader::get_instance();
		$this->loader->add_action('muplugins_loaded', $this, 'configure_class');

		$this->load_dependencies();
		DWS_Helper::load_files(self::get_custom_base_path('plugins'));
        DWS_Helper::load_files(self::get_custom_base_path('modules'));
	}

	//endregion

	//region GETTERS

	/**
	 * Gets the current plugin description.
	 *
	 * @since   1.3.0
	 * @version 1.3.0
	 * @author  Dushan Terzikj  <d.terzikj@deep-web-solutions.de>
	 *
	 * @return  string  plugin description of the current plugin.
	 */
	protected static function get_plugin_description(){
		return Custom_Extensions::get_plugin_description();
	}

	/**
	 * Gets the current plugin author name.
	 *
	 * @since   1.3.0
	 * @version 1.3.0
	 * @author  Dushan Terzikj  <d.terzikj@deep-web-solutions.de>
	 *
	 * @return  string  The author's name of the current plugin.
	 */
	protected static function get_plugin_author_name(){
		return Custom_Extensions::get_plugin_author_name();
	}

	/**
	 * Gets the current plugin author URI.
	 *
	 * @since   1.3.0
	 * @version 1.3.0
	 * @author  Dushan Terzikj  <d.terzikj@deep-web-solutions.de>
	 *
	 * @return  string  The author's URI of the current plugin.
	 */
	protected static function get_plugin_author_uri(){
		return Custom_Extensions::get_plugin_author_uri();
	}

	/**
	 * Gets the current plugin name.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string  The name of the current plugin.
	 */
	protected static function get_plugin_name() {
		return Custom_Extensions::get_plugin_name();
	}

	/**
	 * Gets the current plugin version.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string  The version of the current plugin.
	 */
	protected static function get_plugin_version() {
		return Custom_Extensions::get_version();
	}

	/**
	 * Gets the ID of the current class.
	 *
	 * @since   1.0.0
	 * @version 1.4.0
	 *
	 * @return  string  The ID of the current class.
	 */
	public final static function get_root_id() {
		if(isset(self::$root_id[static::class])) {
			return self::$root_id[static::class];
		}

		error_log('No root ID set for class ' . static::class);
		return null;
	}

	/**
	 * Gets the public name of the current class.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string  The public name of the current class.
	 */
	protected final static function get_root_public_name() {
		return self::$root_public_name[static::class];
	}

	//endregion

	//region METHODS

	/**
	 * A late constructor.
	 *
	 * We wait until after the whole custom extension files have been loaded before fully configuring and
	 * enabling the functionality of this class just to make sure that we don't get any "file not found" errors.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public final function configure_class() {
		$this->local_configure();
		if (empty($this->settings_filter)) {
			// this ensures that if the overwritten 'local_configure' class does not initialize the settings filter,
			// there is a sensible fallback to go to
			_doing_it_wrong(
				__FUNCTION__,
				sprintf(__('You did not properly configure the class %s', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN), self::get_root_public_name()),
				self::get_plugin_version()
			);
			self::local_configure();
		}

		// always define a place where children classes can add their own settings and enqueue their assets
		$this->loader->add_action('acf/init', $this, 'init'); // by doing this on the acf/init, we make sure that ACF is loaded and that we can perform actions before defining the DWS options
		$this->loader->add_filter($this->settings_filter, $this, 'define_options');
		$this->loader->add_action('admin_enqueue_scripts', $this, 'admin_enqueue_assets');
		$this->loader->add_action('wp_enqueue_scripts', $this, 'enqueue_assets', PHP_INT_MAX);

		$this->define_hooks($this->loader);
		$this->define_shortcodes($this->loader);
	}

	/**
	 * Child classes should define their dependencies in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	protected function load_dependencies() { /* child classes can overwrite this */ }

	/**
	 * Allows children classes to overwrite the default class settings. If they fail to properly do so,
	 * these defaults will be used.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	protected function local_configure() {
		$this->settings_filter = ACF_Options::get_page_groups_hook(ACF_Options::MAIN_OPTIONS_SLUG);
	}

	/**
	 * Children classes can run code here after all the plugins have been loaded.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function init() { /* child classes can overwrite this */ }

	/**
	 * If the child class has defined some options, they will be added in their own group in the back-end
	 * inside the page of the settings filter.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $groups     The ACF options groups registered so far.
	 *
	 * @return  array   The ACF options groups registered so far including the group of the current class, if
	 *                  applicable.
	 */
	public function define_options($groups) {
		$options = $this->admin_options();
		if (!empty($options)) {
			$groups[] = array(
				'key'    => self::get_root_id(),
				'title'  => sprintf(__('\'%s\' options', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN), self::get_root_public_name()),
				'fields' => $options
			);
		}

		return $groups;
	}

	/**
	 * Children classes should enqueue their admin-side assets in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   $hook   string  Name of the php file currently being rendered.
	 */
	public function admin_enqueue_assets($hook) { /* child classes can overwrite this */ }

	/**
	 * Children classes should enqueue their public-side assets in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function enqueue_assets() { /* child classes can overwrite this */ }

	/**
	 * Children classes should define their back-end options for admins to customize in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array   The options (in ACF-format) that this root class wants to register for admins to manipulate.
	 */
	protected function admin_options() { return array(); }

	/**
	 * Children classes should define their hooks in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   DWS_WordPress_Loader    $loader     The DWS Custom Extensions Core Loader.
	 */
	protected function define_hooks($loader) { /* child classes can overwrite this */ }

	/**
	 * Children classes should define their shortcodes in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   DWS_WordPress_Loader    $loader     The DWS Custom Extensions Core Loader.
	 */
	protected function define_shortcodes($loader) { /* child classes can overwrite this */ }

	//endregion

	//region HELPERS

	/**
	 * Returns the path to the current folder of the class which inherits this class.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   bool    $relative           If true, return the URL path, otherwise the file system path.
	 * @param   bool    $keep_file_name     If true, then returns the path including the end filename.
	 *
	 * @return  string
	 */
	public final static function get_base_path($relative = false, $keep_file_name = false) {
		try {
			$file_name = (new \ReflectionClass(static::class))->getFileName();
		} catch (\ReflectionException $e) {
			/* this situations is literally impossible */
		}

		if ($keep_file_name) {
			return $relative ? str_replace(ABSPATH, '', trailingslashit($file_name)) : trailingslashit($file_name);
		} else {
			return $relative ? trailingslashit(plugin_dir_url($file_name)) : trailingslashit(plugin_dir_path($file_name));
		}
	}

	/**
	 * Returns the base path of the folder of the file which inherits this class relative to the current plugin.
	 *
	 * @return  string
	 */
	public static function get_relative_base_path() {
		return str_replace(DWS_CUSTOM_EXTENSIONS_BASE_PATH, '', self::get_base_path());
	}

	/**
	 * Returns the path to a custom file or directory prepended by the path
	 * to the calling class' path.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $path       The path to append to the current file's base path.
	 * @param   bool    $relative   True if the path should be relative to the WP installation, false otherwise.
	 *
	 * @return  string
	 */
	public final static function get_custom_base_path($path, $relative = false) {
		return trailingslashit(self::get_base_path($relative) . $path);
	}

	/**
	 * Returns the path to the assets folder of the current class.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   bool    $relative   True if the path should be relative to the WP installation, false otherwise.
	 *
	 * @return  string
	 */
	public final static function get_assets_base_path($relative = false) {
		return self::get_custom_base_path('assets', $relative);
	}

	/**
	 * Returns the path to the templates folder of the current class.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   bool    $relative   True if the path should be relative to the WP installation, false otherwise.
	 *
	 * @return  string
	 */
	public final static function get_templates_base_path($relative = false) {
		return self::get_custom_base_path('templates', $relative);
	}

	/**
	 * Returns the path to the classes folder of the current class.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   bool    $relative   True if the path should be relative to the WP installation, false otherwise.
	 *
	 * @return  string
	 */
	public final static function get_includes_base_path($relative = false) {
		return self::get_custom_base_path('includes', $relative);
	}

	/**
	 * Returns a meaningful probably unique name for an internal hook.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string          $name       The actual descriptor of the hook's purpose.
	 * @param   string|array    $extra      Further descriptor of the hook's purpose.
	 * @param   string          $root       Prepended to all hooks inside the same class.
	 *
	 * @return  string  The resulting internal hook.
	 */
	public static function get_hook_name($name, $extra = array(), $root = '') {
		return join(
			'_',
            array_filter(
                array_merge(
                    array(static::get_plugin_name(), $root, $name),
                    is_array($extra) ? $extra : array($extra)
                )
		    )
		);
	}

	/**
	 * Returns a meaningful potentially unique handle for an asset.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $name   The actual descriptor of the asset's purpose. Leave blank for default.
	 *
	 * @return  string  A valid asset handle.
	 */
	public static function get_asset_handle($name = '') {
		return join(
			'_',
			array_filter(
				array(
					static::get_plugin_name(),
					self::get_root_public_name(),
					$name
				)
			)
		);
	}

	//endregion
}