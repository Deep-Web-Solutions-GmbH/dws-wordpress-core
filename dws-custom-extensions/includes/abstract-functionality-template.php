<?php

namespace Deep_Web_Solutions\Core;
use Deep_Web_Solutions\Admin\DWS_Settings;
use Deep_Web_Solutions\Admin\Settings\DWS_Settings_Pages;

if (!defined('ABSPATH')) { exit; }

/**
 * Provides all the piping required for developing a DWS Functionality.
 *
 * @since   1.0.0
 * @version 2.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Root
 */
abstract class DWS_Functionality_Template extends DWS_Root {
	//region FIELDS

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string  TEMPLATE_FILES_OVERWRITES       The prefix of the name of the options field which holds the
	 *                                                  options to overwrite templates for the current functionality.
	 */
	const TEMPLATE_FILES_OVERWRITES = 'template_file_overwrites_';
	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string  TEMPLATE_FILE_OVERWRITE_PREIFX      The prefix of the name of options fields for overwriting
	 *                                                      templates of the current functionality.
	 */
	const TEMPLATE_FILE_OVERWRITE_PREFIX = 'template_file_overwrite_';

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  private
	 * @var     array       $functionalities_by_id      Maintains a list of the instances of all functionalities that
	 *                                                  have been instantiated based on their unique root id.
	 */
	private static $functionalities_by_id = array();
	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  private
	 * @var     array       $functionalities_by_name    Maintains a list of the instances of all functionalities that have
	 *                                                  been instantiated based on their unique class name.
	 */
	private static $functionalities_by_name = array();
	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  private
	 * @var     array       $parent_functionality       Maintains a list of parent relations between functionalities.
	 */
	private static $parent_functionality = array();
	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  private
	 * @var     array       $children_functionalities   Maintains a list of children relations between functionalities.
	 */
	private static $children_functionalities = array();

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  private
	 * @var     bool        $must_use       Whether this functionality must be used or whether the admin can turn it off.
	 */
	private static $must_use = array();

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     string      $options_parent     The ID of the parent functionality of the current one.
	 */
	protected $options_parent_id;

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     string      $children_settings_filter       The options filter on which the children of this functionality
	 *                                                      define their own option fields.
	 */
	protected $children_settings_filter;

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     int         $functionality_depth        The 0-indexed depth of the current functionality in the functionality polytree.
	 */
	protected $functionality_depth;

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  private
	 * @var     string      $description    The purpose/description of the current functionality.
	 */
	private $description;

	//endregion

	//region MAGIC METHODS

	/**
	 * DWS_Functionality_Template constructor.
	 *
	 * @since   1.0.0
	 * @version 1.4.0
	 *
	 * @see     DWS_Root::__construct()
	 *
	 * @param   bool            $must_use
	 * @param   string          $functionality_id
	 * @param   string          $parent_functionality_id
	 * @param   string          $options_parent_id
	 * @param   bool|string     $functionality_description
	 * @param   bool|string     $functionality_name
	 */
	protected function __construct($functionality_id, $must_use = true, $parent_functionality_id = '', $options_parent_id = '', $functionality_description = false, $functionality_name = false) {
		// set up the relations between functionalities
		self::$functionalities_by_id[$functionality_id] = $this;
		self::$functionalities_by_name[static::class]   = $this;
		self::$parent_functionality[static::class]      = null;
		self::$children_functionalities[static::class]  = array();

		if (!empty($parent_functionality_id)) {
			self::$parent_functionality[static::class]                                                           = self::$functionalities_by_id[$parent_functionality_id];
			self::$children_functionalities[get_class(self::$functionalities_by_id[$parent_functionality_id])][] = $this;
			$this->functionality_depth                                                                           = self::$parent_functionality[static::class]->functionality_depth + 1;
		} else {
			$this->functionality_depth = 0;
		}

		// make sure the root id and such are set
        parent::__construct($functionality_id, $functionality_name);

		// set up this functionality instance
		self::$must_use[static::class] = $must_use;
		$this->options_parent_id       = empty($options_parent_id) ? $this->get_top_level_parent()::get_root_id() : $options_parent_id;
		$this->description             = $functionality_description;

		// load language files, if available
		if (is_dir(self::get_custom_base_path('languages'))) {
			load_muplugin_textdomain(self::get_language_domain(), str_replace(WPMU_PLUGIN_DIR, '', self::get_custom_base_path('languages')));
			DWS_WordPress_Loader::get_instance()->add_action('loco_plugins_data', $this, 'register_with_loco_translate_plugin');
			DWS_WordPress_Loader::get_instance()->add_action('dws_wpml_get-mu_plugins', $this, 'properly_register_plugin_with_wpml', 100); // priority must be higher than 10 !!!
			DWS_WordPress_Loader::get_instance()->add_action('dws_wpml_plugin-file-name', $this, 'properly_name_plugin_with_wpml', 10, 2);
			DWS_WordPress_Loader::get_instance()->add_action('dws_wpml_mu-plugin-data', $this, 'properly_add_plugin_data_to_wpml', 10, 2);
		}
	}

	//endregion

	//region GETTERS

	/**
	 * Gets the parent functionality of the current one.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  DWS_Functionality_Template|null     The instance of the parent functionality of the current one or null
	 *                                              if n/a.
	 */
	public final static function get_parent() {
		return self::$parent_functionality[static::class];
	}

	/**
	 * Gets the children functionalities of the current one.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  DWS_Functionality_Template[]    The instances of the children functionalities of the current one.
	 */
	public final static function get_children() {
		return self::$children_functionalities[static::class];
	}

	/**
	 * Gets whether the current functionality is MU or not.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  bool    True if the current functionality is MU, otherwise false.
	 */
	public final static function is_must_use() {
		return self::$must_use[static::class];
	}

	/**
	 * Gets the language domain of the current functionality.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string      The language domain of the current functionality.
	 */
	public static function get_language_domain() {
		$parent = self::get_parent();
		return !empty($parent)
			? $parent->get_language_domain()
			: join('_', array(DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN, self::get_root_id()));
	}

	//endregion

	//region INHERITED FUNCTIONS

	/**
	 * @since   1.0.0
	 * @version 2.0.0
	 *
	 * @see     DWS_Root::local_configure()
	 */
	protected function local_configure() {
		parent::local_configure();

		$this->children_settings_filter = DWS_Settings_Pages::get_page_groups_fields_hook(DWS_Settings_Pages::MAIN_OPTIONS_SLUG);
		if (!empty(self::get_parent())) {
			$this->settings_filter = $this->children_settings_filter;
		}
	}

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Root::define_hooks()
	 *
	 * @param   DWS_WordPress_Loader    $loader
	 */
	protected final function define_hooks($loader) {
		if (static::is_active()) {
			$this->define_functionality_hooks($loader);
		} else {
			$loader->remove_action('admin_enqueue_scripts', $this, 'admin_enqueue_assets');
			$loader->remove_action('wp_enqueue_scripts', $this, 'enqueue_assets', PHP_INT_MAX);
		}
	}

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Root::define_options()
	 *
	 * @param   array   $groups_or_fields
	 *
	 * @return  array
	 */
	public function define_options($groups_or_fields) {
		if (!empty(self::get_parent())) {
			$options_parent_id = $this->options_parent_id;
			return array_merge(
				$groups_or_fields, array_map(
				function ($option) use ($options_parent_id) {
					$option['parent'] = $options_parent_id;
					return $option;
				}, $this->admin_options()
			)
			);
		} else {
			$functionality_options = $this->admin_options();
			$children_options      = array();

			if (empty($functionality_options)) {
				// check if children classes have any fields to be included
				$parent_functionality_id = self::get_root_id();
				$children_options        = array_filter(
					array_map(
						function ($field) use ($parent_functionality_id) {
							return (isset($field['parent']) && $field['parent'] === $parent_functionality_id)
								? $field : null;
						},
						apply_filters($this->children_settings_filter, array())
					)
				);
			}

			return (empty($functionality_options) && empty($children_options))
				? $groups_or_fields
				: array_merge(
					$groups_or_fields,
					array(
						array(
							'key'    => self::get_root_id(),
							'title'  => sprintf(__('\'%s\' options', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN), self::get_root_public_name()),
							'fields' => $functionality_options
						)
					)
				);
		}
	}

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Root::admin_options()
	 *
	 * @return  array
	 */
	protected final function admin_options() {
		if (!static::are_prerequisites_fulfilled()) {
			return array();
		}

		$must_use_functionality = self::$must_use[static::class];
		$must_use_options       = array();

		// if this is not a must-use functionality, we need an option to let the admin activate it
		if (!$must_use_functionality) {
			$active_checkbox = array(
				'key'     => 'field_rhgoegererg_' . self::get_root_id(),
				'name'    => 'functionality_' . self::get_root_id(),
				'label'   => __('Is functionality active?', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'message' => $this->description,
				'type'    => 'true_false',
				'ui'      => 1
			);

			if (!empty(self::get_parent())) {
				$active_checkbox['message']           = $this->description;
				$active_checkbox['label']             = sprintf(__('Is sub-functionality \'%s\' active?', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN), self::get_root_public_name());
				$active_checkbox['conditional_logic'] = array($this->get_option_conditional_logic(self::get_parent()));
			}

			$must_use_options[] = $active_checkbox;
		}

		// maybe add options for overwriting templates
		$template_file_options_array = array_filter(
			array_map(
				function ($template_file) {
					return array(
						'key'           => join('_', array('field_h4748g3g34g34g', self::get_root_id(), $template_file)),
						'name'          => join('_', array(self::TEMPLATE_FILE_OVERWRITE_PREFIX, $template_file)),
						'label'         => $template_file,
						'type'          => 'true_false',
						'message'       => sprintf(__('Overwrite this template?', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN), $template_file),
						'instructions'  => DWS_Helper::extract_file_header(static::get_templates_base_path() . 'overrides/'. $template_file),
						'wrapper'       => array('width' => '20%')
					);
				}, $this->maybe_overridable_templates()
			)
		);
		if (!empty($template_file_options_array)) {
			$must_use_options[] = array_filter(
				array(
					'key'               => join('_', array('field_dsg4gh4j64jj65', self::get_root_id())),
					'name'              => self::TEMPLATE_FILES_OVERWRITES . self::get_root_id(),
					'label'             => __('Template files', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
					'type'              => 'group',
					'instructions'      => __(
						'If unchecked, the respective template will NOT be used from the Custom Extensions plugin.
					Note that if checked, a file found inside the template folder or inside the local Custom Extensions will 
					have precedence.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN
					),
					'sub_fields'        => $template_file_options_array,
					'conditional_logic' => $must_use_functionality
						? null
						: array(
							array(
								array(
									'field'    => 'field_rhgoegererg_' . self::get_root_id(),
									'operator' => '==',
									'value'    => '1'
								)
							)
						)
				)
			);
		}

		// if this is not a must-use functionality, then hide the fields if the "must-use-checkbox" is unchecked
		$functionality_options = $this->functionality_options();
		foreach ($functionality_options as &$option) {
			$conditional_logic = isset($option['conditional_logic']) ? $option['conditional_logic'] : array();
			$conditional_logic = is_array($conditional_logic) ? $conditional_logic : array();

			$conditional_logic_local = array();
			if (!$must_use_functionality) {
				$conditional_logic_local[] = array(
					'field'    => 'field_rhgoegererg_' . self::get_root_id(),
					'operator' => '==',
					'value'    => '1'
				);
			}
			$conditional_logic_local = array_merge($conditional_logic_local, $this->get_option_conditional_logic(self::get_parent()));
			if (!empty($conditional_logic_local)) {
				array_unshift($conditional_logic, $conditional_logic_local);
			}

			$option['conditional_logic'] = $conditional_logic;
		}

		return array_merge($must_use_options, $functionality_options);
	}

	//endregion

	//region METHODS

	/**
	 * By default, there are no prerequisites for a functionality. Each functionality can
	 * define its own though.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  bool    True if the the functionality's prerequisites are fulfilled, otherwise false.
	 */
	protected static function are_prerequisites_fulfilled() { return true; }

	/**
	 * Child classes should define their hooks in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   DWS_WordPress_Loader    $loader     The DWS Custom Extensions core loader.
	 */
	protected function define_functionality_hooks($loader) { /* child classes can overwrite this */ }

	/**
	 * Lets each child class define the options it want to register in the backend.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array[]     The ACF-conform definition of the option fields of the current functionality.
	 */
	protected function functionality_options() { return array(); }

	/**
	 * Determines in which folder the overridable templates should be stored in.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array   A list of all the files stored inside the overridable templates folder, relative to the folder
	 *                  itself.
	 */
	private final function maybe_overridable_templates() {
		return DWS_Helper::list_files(static::get_templates_base_path() . 'overrides');
	}

	/**
	 * Checks whether the current functionality is currently active on this website.
	 *
	 * @since   1.0.0
	 * @version 2.0.0
	 *
	 * @return  bool    True if the current functionality is active, otherwise false.
	 */
	protected final static function is_active() {
		$current = self::get_instance();

		do {
			$current = get_class($current);

			if (!self::$must_use[$current] && !DWS_Settings_Pages::get_field('field_rhgoegererg_' . self::$functionalities_by_name[$current]::get_root_id(), self::get_options_page_slug())) {
				return false;
			}

			$current = self::$functionalities_by_name[$current]::get_parent();
		} while ($current !== null);

		return static::are_prerequisites_fulfilled();
	}

	/**
	 * Registers the current functionality with Loco Translate to enable translation, if top-level.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $plugins    The plugins that have already been successfully registered with Loco Translate.
	 *
	 * @return  array   The plugins registered with Loco Translate including the current top-level functionality.
	 */
	public final function register_with_loco_translate_plugin($plugins) {
		// we know the plugin by this handle, even if WordPress doesn't
		$handle = untrailingslashit(str_replace(WPMU_PLUGIN_DIR, '', self::get_base_path(false, true)));
		$handle = ltrim($handle, '/');

		// fetch the plugin's meta data from the would-be plugin file
		$data = get_plugin_data(trailingslashit(WPMU_PLUGIN_DIR) . $handle);

		// extra requirement of Loco - $handle must be resolvable to full path
		$data['basedir'] = WPMU_PLUGIN_DIR;

		// add to array and return back to Loco Translate
		$plugins[$handle] = $data;
		return $plugins;
	}

    /**
     * Properly registers the DWS WordPress core plugin with WPML.
     *
     * @since   1.3.0
     * @version 1.3.0
     *
     * @author  Dushan Terzikj  <d.terzikj@deep-web-solutions.de>
     *
     * @see     wp_get_mu_plugins()
     *
     * @param   array   $mu_plugins     The mu-plugins installed.
     *
     * @return  array   The proper mu-plugin of the core.
     */
    public function properly_register_plugin_with_wpml($mu_plugins) {
        $mu_plugins[] = self::get_base_path(false, true);
        return $mu_plugins;
    }

    /**
     * Returns the name of the plugin, either only it's file name or it's full path.
     *
     * @since   1.3.0
     * @version 1.3.0
     *
     * @author  Dushan Terzikj  <d.terzikj@deep-web-solutions.de>
     *
     * @param   string  $plugin_file    The file name of the plugin.
     * @param   string  $full_path      The full path of the plugin.
     *
     * @return  string  Either just the file name of the plugin or the full path of the plugin.
     */
    public function properly_name_plugin_with_wpml($plugin_file, $full_path) {
        if (strpos($full_path, self::get_base_path()) !== false) {
            return $full_path;
        }

        return $plugin_file;
    }

    /**
     * Adds some data to the mu-plugins provided.
     *
     * @since   1.3.0
     * @version 1.3.0
     *
     * @author  Dushan Terzikj  <d.terzikj@deep-web-solutions.de>
     *
     * @param   array   $plugin_info    The information of the mu-plugin currently available.
     * @param   string  $plugin_file    The name of the plugin file as it's registered with WPML.
     *
     * @return  array   Potentially modified mu-plugin information.
     */
    public function properly_add_plugin_data_to_wpml ($plugin_info, $plugin_file) {
        if (strpos($plugin_file, self::get_base_path()) !== false) {
            $plugin_info['Name']        = 'MU :: ' . self::get_root_public_name();
            $plugin_info['TextDomain']  = self::get_language_domain();
            $plugin_info['DomainPath']  = self::get_custom_base_path('languages');
        }

        return $plugin_info;
    }

	//endregion

	//region HELPERS

	/**
	 * Checks whether there is an option for overwriting a certain file through this plugin, and if so,
	 * it it's active or not.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $file   The name of the template file that is currently being searched for,
	 *                          relative to the templates' folder path of the plugin searching for it.
	 *
	 * @return  bool    True if the file should be overwritten by this functionality, otherwise false.
	 */
	protected final function can_overwrite_file($file) {
		return in_array($file, $this->maybe_overridable_templates())
			? boolval(get_field(self::TEMPLATE_FILES_OVERWRITES . join('_', array(self::get_root_id(),
			                                                               self::TEMPLATE_FILE_OVERWRITE_PREFIX,
			                                                                      $file)), 'option'))
			: false;
	}

	/**
	 * Generates an ACF-compliant conditional logic group for hiding functionality options based on whether the parent
	 * functionalities (all the way to the top-level one) are active or not.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   DWS_Functionality_Template  $parent     The immediate parent of functionality for which the current
	 *                                                  options are being generated.
	 *
	 * @return  array   An ACF-compliant group for conditional logic.
	 */
	private function get_option_conditional_logic($parent) {
		if (empty($parent)) {
			return array();
		}

		$level_result = null;
		if (!$parent->is_must_use()) {
			$level_result = array(
				'field'    => 'field_rhgoegererg_' . $parent->get_root_id(),
				'operator' => '==',
				'value'    => '1'
			);
		}

		return array_filter(array_merge(array($level_result), $this->get_option_conditional_logic($parent->get_parent())));
	}

	/**
	 * Gets the top-level parent of the current functionality.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  DWS_Functionality_Template  The top-level parent of the current functionality.
	 */
	private function get_top_level_parent() {
		$current = $this;
		while ($current::get_parent() !== null) {
			$current = $current::get_parent();
		}

		return $current;
	}

	//endregion
}