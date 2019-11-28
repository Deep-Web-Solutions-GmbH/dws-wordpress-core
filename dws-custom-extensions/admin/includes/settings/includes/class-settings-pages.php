<?php

namespace Deep_Web_Solutions\Admin\Settings;
use Deep_Web_Solutions\Admin\DWS_Settings;
use Deep_Web_Solutions\Admin\DWS_Admin;
use Deep_Web_Solutions\Base\DWS_Functionality_Template;

if (!defined('ABSPATH')) { exit; }

/**
 * Handles the settings pages and the settings therein.
 *
 * @since   2.0.0
 * @version 2.0.2
 * @author  Fatine Tazi <f.tazi@deep-web-solutions.de>
 *
 * @see     DWS_Functionality_Template
 */
final class DWS_Settings_Pages extends DWS_Functionality_Template {
    //region FIELDS AND CONSTANTS

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @var     string      MENU_PAGES_SLUG_PREFIX      The proper prefix of the slug of all settings pages of this plugin.
     */
    public const MENU_PAGES_SLUG_PREFIX = 'dws_custom-extensions-settings_';

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @var     string      MAIN_SETTINGS_SLUG      The slug of the main settings menu.
     */
    public const MAIN_SETTINGS_SLUG = self::MENU_PAGES_SLUG_PREFIX . 'general';

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @var     string      MODULES_SETTINGS_SLUG       The slug of the modules settings menu.
     */
    public const MODULES_SETTINGS_SLUG = self::MENU_PAGES_SLUG_PREFIX . 'modules';

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @var     string      THEME_SETTINGS_SLUG     The slug of the theme settings menu.
     */
    public const THEME_SETTINGS_SLUG = self::MENU_PAGES_SLUG_PREFIX . 'theme';

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @var     string  CLEAR_TRANSIENTS_ACTION     The name of the AJAX action which will clear the settings transients.
     */
    private const CLEAR_TRANSIENTS_ACTION = 'dws-core_settings_clear-transients';

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @access  private
     * @var     array   $pages      Maintains a list of the slugs of all registered settings pages of this plugin.
     */
    private static $pages = array(self::MAIN_SETTINGS_SLUG, self::MODULES_SETTINGS_SLUG, self::THEME_SETTINGS_SLUG);

    //endregion

    //region INHERITED FUNCTIONS

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @see     DWS_Functionality_Template::define_functionality_hooks()
     *
     * @param   \Deep_Web_Solutions\Core\DWS_Loader   $loader
     */
    protected function define_functionality_hooks($loader) {
        $loader->add_action(DWS_Settings::get_hook_name('init'), $this, 'add_main_page', PHP_INT_MAX - 100);
        $loader->add_action(DWS_Settings::get_hook_name('init'), $this, 'add_sub_pages', PHP_INT_MAX - 100);

        $loader->add_action(DWS_Settings::get_hook_name('init'), $this, 'add_pages_groups', PHP_INT_MAX - 50);
        $loader->add_action(DWS_Settings::get_hook_name('init'), $this, 'add_pages_group_fields', PHP_INT_MAX - 25);

        $loader->add_action('dws_main_page', $this, 'add_settings_postbox');
        $loader->add_action('wp_ajax_' . self::CLEAR_TRANSIENTS_ACTION, $this, 'ajax_clear_transients');
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
    public static function get_hook_name($name, $extra = array(), $root = 'settings-pages') {
        return parent::get_hook_name($name, $extra, $root);
    }

    //endregion

    //region COMPATIBILITY LOGIC

    /**
     * We add the main page which deals with general settings for the whole website.
     *
     * @since   1.0.0
     * @version 2.0.0
     */
    public function add_main_page() {
        $adapter = DWS_Settings::get_settings_framework_adapter();
        $result = $adapter::register_settings_page(
            __('Custom Extensions', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
            __('Custom Extensions', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
            Permissions::SEE_AND_EDIT_DWS_CORE_SETTINGS,
            self::MAIN_SETTINGS_SLUG,
            array(
                'icon_url'   => 'data:image/svg+xml;base64,' . base64_encode(file_get_contents(DWS_Admin::get_assets_base_path() . 'dws_logo.svg')),
                'redirect'   => false,
                'position'   => 3
            )
        );

        if ($result === false) {
            error_log('Failed to register main settings page.');
        } else {
            // we add an "artificial" submenu-page such that the first menu entry is named differently
            add_submenu_page(self::MAIN_SETTINGS_SLUG, __('Deep Web Solutions: Custom Extensions Core Settings', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN), __('Core Settings', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN), Permissions::SEE_AND_EDIT_DWS_CORE_SETTINGS, self::MAIN_SETTINGS_SLUG);
        }
    }

    /**
     * We let our other sub-pages to register here.
     *
     * @since   1.0.0
     * @version 2.0.0
     */
    public function add_sub_pages() {
        $adapter = DWS_Settings::get_settings_framework_adapter();

        /**
         * @since   1.0.0
         * @since   1.2.0   Added 'capability' field.
         * @version 1.2.0
         *
         * @param   array[]     $other_sub_pages    Array of other settings sub-pages to be added.
         *      $other_sub_pages = [
         *          [
         *              'page_title'    =>  (string) The title displayed on the settings page. Optional.
         *              'menu_title'    =>  (string) The title displayed in the menu. Required.
         *              'menu_slug'     =>  (string) The slug of the settings page. Required.
         *              'capability'    =>  (string) The WP capability needed to see and edit the settings. Required.
         *          ]
         *          ...
         *      ]
         */
        $other_sub_pages = apply_filters(self::get_hook_name('subpages'), array());

        $sub_pages = array(
            array(
                'page_title' => __('Deep Web Solutions: Custom Extensions Modules Settings', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
                'menu_title' => __('Modules Settings', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
                'menu_slug'  => self::MODULES_SETTINGS_SLUG,
                'capability' => Permissions::SEE_AND_EDIT_DWS_MODULES_SETTINGS
            ),
            array(
                'page_title' => __('Deep Web Solutions: Custom Extensions Theme Settings', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
                'menu_title' => __('Theme Settings', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
                'menu_slug'  => self::THEME_SETTINGS_SLUG,
                'capability' => Permissions::SEE_AND_EDIT_DWS_THEME_SETTINGS
            )
        );

        $sub_pages = array_merge($sub_pages, $other_sub_pages);
        foreach ($sub_pages as $sub_page) {
            // Make sure the subpage slug is "normalized"
            if (strpos($sub_page['menu_slug'], self::MENU_PAGES_SLUG_PREFIX) !== 0) {
                $sub_page['menu_slug'] = self::MENU_PAGES_SLUG_PREFIX . $sub_page['menu_slug'];
            }

            // Add the current subpage both to WordPress and to our cache
            self::$pages[] = $sub_page['menu_slug'];
            $result = $adapter::register_settings_subpage(self::MAIN_SETTINGS_SLUG, $sub_page['page_title'], $sub_page['menu_title'], $sub_page['capability'], $sub_page['menu_slug'], $sub_page);

            if ($result === false) {
                error_log('Failed to register sub-settings page: ' . $sub_page['menu_title']);
            }
        }

        // Make sure our internal cache is unique
        self::$pages = array_unique(self::$pages);
    }

    /**
     * Adds groups to the settings pages.
     *
     * @since   1.0.0
     * @version 1.5.1
     */
    public function add_pages_groups() {
        foreach (self::$pages as $page) {
            $groups = get_transient(self::get_page_groups_hook($page));
            if ($groups === false) {
                $groups = apply_filters(self::get_page_groups_hook($page), array());
                set_transient(self::get_page_groups_hook($page), $groups, 24 * 60 * 60);
            }

            self::add_groups($groups, $page);
        }
    }

    /**
     * Adds later fields to the groups already added.
     *
     * @since   1.0.0
     * @version 1.5.1
     */
    public function add_pages_group_fields() {
        foreach (self::$pages as $page) {
            $location = self::get_page_groups_fields_hook($page);
            $fields = get_transient($location);

            if ($fields === false) {
                $fields = apply_filters($location, array());
                set_transient($location, $fields, 24 * 60 * 60);
            }

            self::add_fields($fields, $location);
        }
    }

    /**
     * Adds a postbox to the DWS main admin page for settings-related actions.
     *
     * @since   1.5.3
     * @version 2.0.0
     */
    public function add_settings_postbox() {
        $link_to_clear_transients = add_query_arg('action', self::CLEAR_TRANSIENTS_ACTION, admin_url('admin-ajax.php'));
        echo '<div class="dws-postbox">
                    <h2 class="dws-with-subtitle">'. __('Settings options', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN) .'</h2>
                    <p class="dws-subtitle">'. __('Perform various actions related to the settings options', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN) .'</p>
                    <a href="'. $link_to_clear_transients .'"><button class="button button-primary button-large">' . __('Clear transients', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN) . '</button></a>
                </div>';
    }

    /**
     * Clear the transients of the settings.
     *
     * @since   1.5.3
     * @version 1.5.3
     */
    public function ajax_clear_transients() {
        foreach (self::$pages as $page) {
            delete_transient(self::get_page_groups_hook($page));
            delete_transient(self::get_page_groups_fields_hook($page));
        }

        wp_safe_redirect(admin_url('?page=dws_custom-extensions_main'));
    }

    //endregion

    //region HELPERS

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   string  $field
     * @param   string  $settings_page_slug
     *
     * @return  mixed
     */
    public static function get_field($field, $settings_page_slug) {
        $adapter = DWS_Settings::get_settings_framework_adapter();
        return is_null($adapter) ? null : $adapter::get_settings_field_value($field, $settings_page_slug);
    }

    /**
     * Makes sure that we always use the same format for generating the hook on which classes
     * should define their ACF settings groups based on the page on which they want the settings present.
     *
     * @since   1.0.0
     * @version 1.0.0
     *
     * @param   string  $page_slug      The slug of the page on which the ACF groups must be generated on.
     *
     * @return  string  The hook on which the class must define its ACF settings groups.
     */
    public static function get_page_groups_hook($page_slug) {
        return join('_', array(self::get_hook_name($page_slug), 'groups'));
    }

    /**
     * Makes sure that we always use the same format for generating the hook on which classes
     * should define their 'late' settings fields based on the page on which the group was registered on.
     *
     * @since   1.0.0
     * @version 1.0.0
     *
     * @param   string  $page_slug      The slug of the page on which the ACF groups will be generated on.
     *
     * @return  string  The hook on which the class must define its 'late' ACF settings fields.
     */
    public static function get_page_groups_fields_hook($page_slug) {
        return join('_', array(self::get_hook_name($page_slug), 'groups-fields'));
    }

    /**
     * Registers local groups with our settings framework of choice.
     *
     * @since   1.0.0
     * @version 2.0.2
     *
     * @param   array   $groups     A list of ACF-conform groups of fields to be registered with ACF.
     * @param   string  $location   The slug of the settings page on which the groups must appear on.
     */
    private function add_groups($groups, $location) {
        $adapter = DWS_Settings::get_settings_framework_adapter();

        foreach ($groups as $group) {
            if (!isset($group['key'], $group['title'])) {
                error_log('Failed to register group in: ' . $location .  'Make sure the group key and title are set.');
                continue;
            }
            if (empty($group['key']) || empty($group['title'])) {
                error_log('Failed to register group in: ' . $location . 'Make sure the group key and title are not empty.');
                continue;
            }

            $adapter::register_settings_page_group(
                $group['key'],
                $group['title'],
                $location,
                $group['fields'] ?? array(),
                $group
            );
        }
    }

    /**
     * Registers local fields with our settings framework of choice.
     *
     * @since   1.0.0
     * @version 2.0.2
     *
     * @param   array   $fields     A list of ACF-conform fields that must be registered with ACF to an existing group.
     */
    private function add_fields($fields, $location) {
        $adapter = DWS_Settings::get_settings_framework_adapter();

        foreach ($fields as $field) {
            if (!isset($field['parent'], $field['key'], $field['type'])) {
                error_log('Failed to register field in. Make sure the field parent, key, and type are set.');
                continue;
            }
            if (empty($field['parent']) || empty($field['key']) || empty($field['type'])) {
                error_log('Failed to register field in. Make sure the field parent, key, and type are not empty.');
                continue;
            }

            $adapter::register_field(
                $field['key'],
                $field['type'],
                $field['parent'],
                $field,
                $location
            );
        }

    }

	//endregion
}