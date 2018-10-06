<?php

namespace Deep_Web_Solutions\Admin\ACF;
use Deep_Web_Solutions\Admin\DWS_Admin;
use Deep_Web_Solutions\Core\DWS_Functionality_Template;

if (!defined('ABSPATH')) { exit; }

/**
 * Handles the ACF options pages of the DWS CustomExtensions plugin.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Functionality_Template
 */
final class ACF_Options extends DWS_Functionality_Template {
	//region FIELDS AND CONSTANTS

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string      MENU_PAGES_SLUG_PREFIX      The proper prefix of the slug of all ACF options pages of this
	 *                                                  plugin.
	 */
	const MENU_PAGES_SLUG_PREFIX = 'dws_custom-extensions-settings_';

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string      MAIN_OPTIONS_SLUG       The slug of the main settings menu.
	 */
	const MAIN_OPTIONS_SLUG = self::MENU_PAGES_SLUG_PREFIX . 'general';
	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string      MODULES_OPTIONS_SLUG        The slug of the modules settings menu.
	 */
	const MODULES_OPTIONS_SLUG = self::MENU_PAGES_SLUG_PREFIX . 'modules';
	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string      THEME_OPTIONS_SLUG      The slug of the theme settings menu.
	 */
	const THEME_OPTIONS_SLUG = self::MENU_PAGES_SLUG_PREFIX . 'theme';

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string      GROUP_KEY_PREFIX        All options groups start with this prefix.
	 */
	const GROUP_KEY_PREFIX = 'group_hiurhgvv8gh2v4_';

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  private
	 * @var     array   $pages      Maintains a list of the slugs of all registered ACF options pages of this plugin.
	 */
	private static $pages = array(self::MAIN_OPTIONS_SLUG, self::MODULES_OPTIONS_SLUG, self::THEME_OPTIONS_SLUG);

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
		$loader->add_action('plugins_loaded', $this, 'add_main_page', PHP_INT_MAX);
		$loader->add_action('plugins_loaded', $this, 'add_sub_pages', PHP_INT_MAX);

		$loader->add_action('acf/init', $this, 'add_pages_groups', PHP_INT_MAX - 1);
		$loader->add_action('acf/init', $this, 'add_pages_group_fields', PHP_INT_MAX);
		$loader->add_action('acf/init', $this, 'add_floating_update_button', PHP_INT_MAX);
	}

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Functionality_Template::get_hook_name()
	 *
	 * @param   string      $name
	 * @param   string      $root
	 * @param   array       $extra
	 *
	 * @return  string
	 */
	public static function get_hook_name($name, $extra = array(), $root = 'acf-options') {
		return parent::get_hook_name($name, $extra, $root);
	}

	//endregion

	//region COMPATIBILITY LOGIC

	/**
	 * We add the main page which deals with general settings for the whole website.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function add_main_page() {
		acf_add_options_page(
			array(
				'menu_title' => __('Custom Extensions', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'page_title' => __('Deep Web Solutions: Custom Extensions Core Settings', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'menu_slug'  => self::MAIN_OPTIONS_SLUG,
				'capability' => Permissions::SEE_AND_EDIT_DWS_CORE_OPTIONS,
				'icon_url'   => 'data:image/svg+xml;base64,' . base64_encode(file_get_contents(DWS_Admin::get_assets_base_path() . 'dws_logo.svg')),
				'redirect'   => false,
				'position'   => 3
			)
		);

		// we add an "artificial" submenu-page such that the first menu entry is named differently
		add_submenu_page(self::MAIN_OPTIONS_SLUG, '', __('Core Settings', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN), 'administrator', self::MAIN_OPTIONS_SLUG);
	}

	/**
	 * We let our other sub-pages to register here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function add_sub_pages() {
		/**
		 * @since   1.0.0
		 * @since   1.2.0   Added 'capability' field.
		 * @version 1.2.0
		 *
		 * @param   array[]     $other_sub_pages    Array of other options sub-pages to be added.
		 *      $other_sub_pages = [
		 *          [
		 *              'page_title'    =>  (string) The title displayed on the options page. Optional.
		 *              'menu_title'    =>  (string) The title displayed in the menu. Required.
		 *              'menu_slug'     =>  (string) The slug of the options page. Required.
		 *              'capability'    =>  (string) The WP capability needed to see and edit the options. Required.
		 *          ]
		 *          ...
		 *      ]
		 */
		$other_sub_pages = apply_filters(self::get_hook_name('subpages'), array());
		$sub_pages       = array_merge(
			array(
				array(
					'page_title' => __('Deep Web Solutions: Custom Extensions Modules Settings', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
					'menu_title' => __('Modules Settings', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
					'menu_slug'  => self::MODULES_OPTIONS_SLUG,
					'capability' => Permissions::SEE_AND_EDIT_DWS_MODULES_OPTIONS
				),
				array(
					'page_title' => __('Deep Web Solutions: Custom Extensions Theme Settings', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
					'menu_title' => __('Theme Settings', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
					'menu_slug'  => self::THEME_OPTIONS_SLUG,
					'capability' => Permissions::SEE_AND_EDIT_DWS_THEME_OPTIONS
				)
			), $other_sub_pages
		);

		foreach ($sub_pages as $sub_page) {
			if (!isset($sub_page['menu_title'], $sub_page['menu_slug'], $sub_page['capability'])) {
				continue;
			}

			// make sure the subpage slug is "normalized"
			if (strpos($sub_page['menu_slug'], self::MENU_PAGES_SLUG_PREFIX) !== 0) {
				$sub_page['menu_slug'] = self::MENU_PAGES_SLUG_PREFIX . $sub_page['menu_slug'];
			}

			// add the current subpage both to WordPress and to our cache
			self::$pages[] = $sub_page['menu_slug'];
			acf_add_options_sub_page(
				array(
					'page_title'  => isset($sub_page['page_title']) ? $sub_page['page_title'] : $sub_page['menu_title'],
					'menu_title'  => $sub_page['menu_title'],
					'menu_slug'   => $sub_page['menu_slug'],
					'capability'  => $sub_page['capability'],
					'parent_slug' => self::MAIN_OPTIONS_SLUG,
				)
			);
		}

		// make sure our internal cache is unique
		self::$pages = array_unique(self::$pages);
	}

	/**
	 * Adds groups to the options pages.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function add_pages_groups() {
		foreach (self::$pages as $page) {
			$groups = apply_filters(self::get_page_groups_hook($page), array());
			self::add_groups($groups, $page);
		}
	}

	/**
	 * Adds later fields to the groups already added.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function add_pages_group_fields() {
		foreach (self::$pages as $page) {
			$fields = apply_filters(self::get_page_groups_fields_hook($page), array());
			self::add_fields($fields);
		}
	}

	public function add_floating_update_button(){
		echo '<button onclick="dws_update()" class="dws_floating-update-button" style="border-radius: 3px;
    border: 0;
    background: #0085BA;
    position: fixed;
    width: 100px;
    height: 50px;
    top: 200px;
    right: 20px;
    z-index: 10;">Update</button>';
	}

	//endregion

	//region HELPERS

	/**
	 * Makes sure that we always use the same format for generating the hook on which classes
	 * should define their ACF options groups based on the page on which they want the options present.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $page_slug      The slug of the page on which the ACF groups must be generated on.
	 *
	 * @return  string  The hook on which the class must define its ACF options groups.
	 */
	public static function get_page_groups_hook($page_slug) {
		return join('_', array(self::get_hook_name($page_slug), 'groups'));
	}

	/**
	 * Makes sure that we always use the same format for generating the hook on which classes
	 * should define their 'late' ACF options fields based on the page on which the group was registered on.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $page_slug      The slug of the page on which the ACF groups will be generated on.
	 *
	 * @return  string  The hook on which the class must define its 'late' ACF options fields.
	 */
	public static function get_page_groups_fields_hook($page_slug) {
		return join('_', array(self::get_hook_name($page_slug), 'groups-fields'));
	}

	/**
	 * Registers local groups with ACF.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $groups     A list of ACF-conform groups of fields to be registered with ACF.
	 * @param   string  $location   The slug of the options page on which the groups must appear on.
	 */
	private function add_groups($groups, $location) {
		foreach ($groups as $group) {
			if (!isset($group['key'], $group['title'], $group['fields'])) {
				continue;
			}

			acf_add_local_field_group(
				array(
					'key'      => self::GROUP_KEY_PREFIX . $group['key'],
					'title'    => $group['title'],
					'fields'   => $group['fields'],
					'location' => array(
						array(
							array(
								'param'    => 'options_page',
								'operator' => '==',
								'value'    => $location
							),
						),
					)
				)
			);
		}
	}

	/**
	 * Registers local fields with ACF.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $fields     A list of ACF-conform fields that must be registered with ACF to an existing group.
	 */
	private function add_fields($fields) {
		// there is a bug in ACF ... if group fields are added after the 'acf/include_fields' action,
		// then they are not registered properly ... this way, we do the action ourselves and everything is fine
		global $wp_actions;
		unset($wp_actions['acf/include_fields']);

		foreach ($fields as $field) {
			if (!isset($field['parent'])) {
				continue;
			}

			$field['parent'] = (strpos($field['parent'], 'field_') === 0 || strpos($field['parent'], 'group_') === 0)
				? $field['parent']
				: (self::GROUP_KEY_PREFIX . $field['parent']);

			acf_add_local_field($field);
		}

		if (!doing_action('acf/include_fields')) {
			do_action('acf/include_fields', 5);
		} else {
			$wp_actions['acf/include_fields'] = 1;
		}
	}

	//endregion
}