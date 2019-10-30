<?php

namespace Deep_Web_Solutions\Admin\Settings;
use Deep_Web_Solutions\Admin\DWS_Settings;
use Deep_Web_Solutions\Admin\DWS_Admin;
use Deep_Web_Solutions\Core\DWS_Functionality_Template;

if (!defined('ABSPATH')) { exit; }

/**
 * Handles the settings pages and the options therein.
 *
 * @since   2.0.0
 * @version 2.0.0
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
     * @var     string      MENU_PAGES_SLUG_PREFIX      The proper prefix of the slug of all CF options pages of this
     *                                                  plugin.
     */
    public const MENU_PAGES_SLUG_PREFIX = 'dws_custom-extensions-settings_';

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @var     string      MAIN_OPTIONS_SLUG       The slug of the main settings menu.
     */
    public const MAIN_OPTIONS_SLUG = self::MENU_PAGES_SLUG_PREFIX . 'general';
    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @var     string      MODULES_OPTIONS_SLUG        The slug of the modules settings menu.
     */
    public const MODULES_OPTIONS_SLUG = self::MENU_PAGES_SLUG_PREFIX . 'modules';
    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @var     string      THEME_OPTIONS_SLUG      The slug of the theme settings menu.
     */
    public const THEME_OPTIONS_SLUG = self::MENU_PAGES_SLUG_PREFIX . 'theme';

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @var     string      GROUP_KEY_PREFIX        All options groups start with this prefix.
     */
    private const GROUP_KEY_PREFIX = 'group_hiurhgvv8gh2v4_';

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @var     string  CLEAR_TRANSIENTS_ACTION     The name of the AJAX action which will clear the CF options transients.
     */
    private const CLEAR_TRANSIENTS_ACTION = 'dws-core_settings_clear-transients';

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @access  private
     * @var     array   $pages      Maintains a list of the slugs of all registered CF options pages of this plugin.
     */
    private static $pages = array(self::MAIN_OPTIONS_SLUG, self::MODULES_OPTIONS_SLUG, self::THEME_OPTIONS_SLUG);

    //endregion

    //region INHERITED FUNCTIONS

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @see     DWS_Functionality_Template::define_functionality_hooks()
     *
     * @param   \Deep_Web_Solutions\Core\DWS_WordPress_Loader   $loader
     */
    protected function define_functionality_hooks($loader) {
        $loader->add_action('plugins_loaded', $this, 'add_main_page', PHP_INT_MAX);
        $loader->add_action('plugins_loaded', $this, 'add_sub_pages', PHP_INT_MAX);

        return; // CHECK THINGS AFTER THIS ...
        $adaptor_name = DWS_General_Adaptor::framework_namespace();

        try {
            $adaptor = new \ReflectionClass($adaptor_name);

            error_log($adaptor);

            $loader->add_action($adaptor::FRAMEWORK_INIT_HOOK_NAME, $this, 'add_pages_groups', PHP_INT_MAX - 1);
            $loader->add_action($adaptor::FRAMEWORK_INIT_HOOK_NAME, $this, 'add_pages_group_fields', PHP_INT_MAX);
            $loader->add_action($adaptor::FRAMEWORK_INIT_HOOK_NAME, $this, 'add_floating_update_button', PHP_INT_MAX);
        } catch (\ReflectionException $exception) { /* literally impossible currently */ }



        $loader->add_action('dws_main_page', $this, 'add_options_postbox');
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
     * @version 1.0.0
     */
    public function add_main_page() {
        $adaptor = DWS_Settings::get_option_framework_adapter();

        return;
//
//        $adaptor = DWS_General_Adaptor::framework_namespace();
//
//        $page = array(
//            'menu_title' => __('Custom Extensions', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
//            'page_title' => __('Deep Web Solutions: Custom Extensions Core Settings', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
//            'menu_slug'  => self::MAIN_OPTIONS_SLUG,
//            'capability' => Permissions::SEE_AND_EDIT_DWS_CORE_OPTIONS,
//            'icon_url'   => 'data:image/svg+xml;base64,' . base64_encode(file_get_contents(DWS_Admin::get_assets_base_path() . 'dws_logo.svg')),
//            'redirect'   => false,
//            'position'   => 3
//        );
//        $page = $adaptor::format_page($page);
//        $adaptor::add_page($page);
//
//        // we add an "artificial" submenu-page such that the first menu entry is named differently
//        add_submenu_page(self::MAIN_OPTIONS_SLUG, '', __('Core Settings', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN), 'administrator', self::MAIN_OPTIONS_SLUG);
    }

    /**
     * We let our other sub-pages to register here.
     *
     * @since   1.0.0
     * @version 1.0.0
     */
    public function add_sub_pages() {
        return;
        //$adaptor = DWS_General_Adaptor::framework_namespace();
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
//        $other_sub_pages = apply_filters(self::get_hook_name('subpages'), array());
//        $other_sub_pages = $adaptor::format_subpage($other_sub_pages);
//
//        $sub_pages = $adaptor::format_subpage(array(
//            array(
//                'page_title' => __('Deep Web Solutions: Custom Extensions Modules Settings', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
//                'menu_title' => __('Modules Settings', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
//                'menu_slug'  => self::MODULES_OPTIONS_SLUG,
//                'capability' => Permissions::SEE_AND_EDIT_DWS_MODULES_OPTIONS
//            ),
//            array(
//                'page_title' => __('Deep Web Solutions: Custom Extensions Theme Settings', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
//                'menu_title' => __('Theme Settings', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
//                'menu_slug'  => self::THEME_OPTIONS_SLUG,
//                'capability' => Permissions::SEE_AND_EDIT_DWS_THEME_OPTIONS
//            )
//        ));
//
//        $sub_pages = array_merge($sub_pages, $other_sub_pages);
//
//        foreach ($sub_pages as $sub_page) {
//            if (!isset($sub_page['menu_title'], $sub_page['menu_slug'], $sub_page['capability'])) {
//                continue;
//            }
//
//            // make sure the subpage slug is "normalized"
//            if (strpos($sub_page['menu_slug'], self::MENU_PAGES_SLUG_PREFIX) !== 0) {
//                $sub_page['menu_slug'] = self::MENU_PAGES_SLUG_PREFIX . $sub_page['menu_slug'];
//            }
//
//            // add the current subpage both to WordPress and to our cache
//            self::$pages[] = $sub_page['menu_slug'];
//            $subpage = $adaptor::format_subpage(
//                array(
//                    'page_title'  => isset($sub_page['page_title']) ? $sub_page['page_title'] : $sub_page['menu_title'],
//                    'menu_title'  => $sub_page['menu_title'],
//                    'menu_slug'   => $sub_page['menu_slug'],
//                    'capability'  => $sub_page['capability'],
//                    'parent_slug' => self::MAIN_OPTIONS_SLUG,
//                )
//            );
//
//            $adaptor::add_subpage($subpage);
//        }
//
//        // make sure our internal cache is unique
//        self::$pages = array_unique(self::$pages);
    }

    /**
     * Adds groups to the options pages.
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
            $fields = get_transient(self::get_page_groups_fields_hook($page));
            if ($fields === false) {
                $fields = apply_filters(self::get_page_groups_fields_hook($page), array());
                set_transient(self::get_page_groups_fields_hook($page), $fields, 24 * 60 * 60);
            }

            self::add_fields($fields);
        }
    }

    /**
     * If the current page is the General Settings in DWS Custom Extensions then enqueue some scripts.
     *
     * @author  Dushan Terzikj  <d.terzikj@deep-web-solutions.de>
     *
     * @since   1.3.3
     * @version 1.4.0
     */
    public function add_floating_update_button(){
        if (isset($_REQUEST['page']) && strpos($_REQUEST['page'], self::MENU_PAGES_SLUG_PREFIX) === 0) {
            wp_enqueue_script(self::get_asset_handle('floating-button'), DWS_Settings::get_assets_base_path( true ) . 'floating-update-button.js', array( 'jquery' ), self::get_plugin_version(), true);
        }
    }

    /**
     * Adds a postbox to the DWS main admin page for "ACF options"-related actions.
     *
     * @since   1.5.3
     * @version 1.5.3
     */
    public function add_options_postbox() {
        return;
//        $adaptor = DWS_General_Adaptor::framework_namespace();
//
//        $link_to_clear_transients = add_query_arg('action', self::CLEAR_TRANSIENTS_ACTION, admin_url('admin-ajax.php'));
//        echo '<div class="dws-postbox">
//                    <h2 class="dws-with-subtitle">'. __($adaptor::OPTIONS_FRAMEWORK_NAME_UPPERCASE . ' options', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN) .'</h2>
//                    <p class="dws-subtitle">'. __('Perform various actions related to the '. $adaptor::OPTIONS_FRAMEWORK_NAME_UPPERCASE . ' options', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN) .'</p>
//                    <a href="'. $link_to_clear_transients .'"><button class="button button-primary button-large">' . __('Clear transients', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN) . '</button></a>
//                </div>';
    }

    /**
     * Clear the transients of the ACF options.
     *
     * @since   1.5.3
     * @version 1.5.3
     */
    public function ajax_clear_transients() {
        foreach (self::$pages as $page) {
            delete_transient(self::get_page_groups_hook($page));
            delete_transient(self::get_page_groups_fields_hook($page));
        }

        wp_safe_redirect('/wp-admin/');
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
        return;
//        $adaptor = DWS_General_Adaptor::framework_namespace();
//
//        foreach ($groups as $group) {
//            if (!isset($group['key'], $group['title'], $group['fields'])) {
//                continue;
//            }
//
//            $group = array(
//                'key'      => self::GROUP_KEY_PREFIX . $group['key'],
//                'title'    => $group['title'],
//                'fields'   => $group['fields'],
//                'location' => array(
//                    array(
//                        array(
//                            'param'    => 'options_page',
//                            'operator' => '==',
//                            'value'    => $location
//                        ),
//                    ),
//                )
//            );
//            $group = $adaptor::format_group($group);
//            $adaptor::add_group($group);
//        }
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
        return;
//        $adaptor = DWS_General_Adaptor::framework_namespace();
//
//        if($adaptor == 'ACF_Adaptor') {
//            // there is a bug in ACF ... if group fields are added after the 'acf/include_fields' action,
//            // then they are not registered properly ... this way, we do the action ourselves and everything is fine
//            global $wp_actions;
//            unset($wp_actions['acf/include_fields']);
//        }
//
//        foreach ($fields as $field) {
//            if (!isset($field['parent'])) {
//                continue;
//            }
//
//            $field['parent'] = (strpos($field['parent'], 'field_') === 0 || strpos($field['parent'], 'group_') === 0)
//                ? $field['parent']
//                : (self::GROUP_KEY_PREFIX . $field['parent']);
//
//            $field = $adaptor::format_fields($field);
//            $adaptor::add_fields($field);
//        }
//
//        if($adaptor == 'ACF_Adaptor') {
//            if (!doing_action('acf/include_fields')) {
//                do_action('acf/include_fields', 5);
//            } else {
//                $wp_actions['acf/include_fields'] = 1;
//            }
//        }

    }

    public static function get_field() {

    }

    public static function update_field() {

    }

	//endregion
}