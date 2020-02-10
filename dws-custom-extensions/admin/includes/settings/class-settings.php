<?php

namespace Deep_Web_Solutions\Admin;
use Deep_Web_Solutions\Admin\Settings\DWS_Adapter;
use Deep_Web_Solutions\Admin\Settings\DWS_noop_Adapter;
use Deep_Web_Solutions\Admin\Settings\DWS_Settings_Installation;
use Deep_Web_Solutions\Admin\Settings\DWS_Settings_Pages;
use Deep_Web_Solutions\Admin\Settings\Permissions;
use Deep_Web_Solutions\Base\DWS_Functionality_Template;
use Deep_Web_Solutions\Helpers\DWS_Permissions;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

if (!defined('ABSPATH')) { exit; }

/**
 * Handles all the settings related extensions including the settings pages.
 *
 * @since   2.0.0
 * @version 2.2.0
 * @author  Fatine Tazi <f.tazi@deep-web-solutions.de>
 *
 * @see     DWS_Functionality_Template
 */
final class DWS_Settings extends DWS_Functionality_Template {
    //region FIELDS AND CONSTANTS

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @var     string  SETTINGS_FRAMEWORK    The name of the option stored in the database which indicates the
     *                                          chosen plugin for custom fields.
     */
    private const SETTINGS_FRAMEWORK = 'dws-core_settings-framework';

    //endregion

    //region INHERITED FUNCTIONS

    /**
     * @since   2.0.0
     * @version 2.0.3
     *
     * @see     DWS_Functionality_Template::load_dependencies()
     */
    protected function load_dependencies() {
        /** @noinspection PhpIncludeInspection */
        /** Interface for interacting with a settings framework. */
        require_once(self::get_includes_base_path() . 'interface-adapter.php');

        /** @noinspection PhpIncludeInspection */
        /** Template for encapsulating some of the most often required abilities of a settings framework. */
        require_once(self::get_includes_base_path() . 'abstract-adapter.php');

        /** @noinspection PhpIncludeInspection */
        /** Settings adapter for when there's no adapter present. */
        require_once(self::get_includes_base_path() . 'class-adapter-noop.php');
        DWS_noop_Adapter::maybe_initialize_singleton('dg3e89hgre87ghee');

        /** @noinspection PhpIncludeInspection */
        /** The custom DWS permissions needed to enhance the settings pages. */
        require_once(self::get_includes_base_path() . 'class-permissions.php');
        Permissions::maybe_initialize_singleton('agdsgrhgehiue');

        /** @noinspection PhpIncludeInspection */
        /** Handles the issue of making sure a settings framework is in place and working. */
        require_once(self::get_includes_base_path() . 'class-installation.php');
        DWS_Settings_Installation::maybe_initialize_singleton('gh4w87ghew87fgrwed');

        /** @noinspection PhpIncludeInspection */
        /** Handles the settings pages and the settings therein. */
        require_once(self::get_includes_base_path() . 'class-settings-pages.php');
        DWS_Settings_Pages::maybe_initialize_singleton('j89hg3854gh3433e');
    }

    //endregion

    //region HELPERS

    /**
     * @since   2.0.0
     * @version 2.0.3
     *
     * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.de>
     *
     * @param   false|string    $slug
     *
     * @return  DWS_Adapter
     */
    public static function get_settings_framework_adapter($slug = false) {
        $selectedFramework = ($slug === false) ? self::get_settings_framework_slug() : $slug;
        $adapter = apply_filters(self::get_hook_name('framework_adapter'), null, $selectedFramework);
        return is_null($adapter) ? DWS_noop_Adapter::get_instance() : $adapter;
    }

    /**
     * @since   2.0.0
     * @version 2.0.3
     *
     * @param   string      $field
     * @param   int|false   $post_id
     *
     * @return  mixed
     */
    public static function get_field($field, $post_id = false) {
        $adapter = DWS_Settings::get_settings_framework_adapter();
        return $adapter::get_field_value($field, $post_id);
    }

    /**
     * @since   2.0.2
     * @version 2.0.3
     *
     * @param   string      $field
     * @param   mixed       $new_value
     * @param   int|false   $post_id
     */
    public static function update_field($field, $new_value, $post_id = false) {
        $adapter = DWS_Settings::get_settings_framework_adapter();
        $adapter::update_field_value($field, $new_value, $post_id);
    }

    /**
     * @since   2.0.3
     * @version 2.0.3
     *
     * @param   string  $key
     * @param   string  $location
     */
    public static function remove_field($key, $location) {
        $adapter = DWS_Settings::get_settings_framework_adapter();
        $adapter::remove_field($key, $location);
    }

    /**
     * @since   2.0.4
     * @version 2.2.2
     *
     * @param   array   $field
     * @param   bool    $do_on_ajax
     *
     * @return  array
     */
    public static function css_hide_field($field, $do_on_ajax = false) {
        if (apply_filters(self::get_hook_name('skip-css-hiding-field'), false, $field)) {
            return $field;
        }
        if ((wp_doing_ajax() && !$do_on_ajax) || !is_admin()) {
            return $field;
        }

        $adapter = DWS_Settings::get_settings_framework_adapter();
        return $adapter::css_hide_field($field, $do_on_ajax);
    }

    /**
     * @since   2.0.3
     * @version 2.2.2
     *
     * @param   array   $field
     * @param   bool    $do_on_ajax
     *
     * @return  null|array
     */
    public static function make_field_uneditable($field, $do_on_ajax = false) {
        if (apply_filters(self::get_hook_name('skip-making-field-uneditable'), false, $field)) {
            return $field;
        }
        if ((wp_doing_ajax() && !$do_on_ajax) || !is_admin()) {
            return $field;
        }

        $adapter = DWS_Settings::get_settings_framework_adapter();
        return $adapter::make_field_uneditable($field, $do_on_ajax);
    }

    /**
     * @since   2.0.3
     * @version 2.0.3
     *
     * @param   array   $field
     * @param   string  $permission
     *
     * @return  mixed
     */
    public static function maybe_make_field_uneditable($field, $permission) {
        $adapter = DWS_Settings::get_settings_framework_adapter();
        return !DWS_Permissions::has(array($permission, 'administrator'), null, 'or')
            ? $adapter::make_field_uneditable($field) : $field;
    }

    /**
     * @since   2.0.3
     * @version 2.0.3
     *
     * @param   string  $key
     * @param   string  $title
     * @param   array   $location
     * @param   array   $fields
     * @param   array   $other
     */
    public static function register_group($key, $title, $location, $fields, $other = array()) {
        $adapter = DWS_Settings::get_settings_framework_adapter();
        $adapter::register_generic_group($key, $title, $location, $fields, $other);
    }

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.de>
     *
     * @return  string  The settings framework used by the DWS WP Core.
     */
    public static function get_settings_framework_slug() {
        return get_option(self::SETTINGS_FRAMEWORK, '');
    }

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   string  $new_slug   The value to be stored in the option.
     *
     * @return  bool    False if value was not updated and true if value was updated.
     */
    public static function update_settings_framework_slug($new_slug) {
        return update_option(self::SETTINGS_FRAMEWORK, $new_slug);
    }

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @return  bool    True, if option is successfully deleted. False on failure.
     */
    public static function delete_settings_framework_slug() {
        return delete_option(self::SETTINGS_FRAMEWORK);
    }

    /**
     * @since   2.0.0
     * @version 2.2.0
     *
     * @return  array   The supported settings frameworks from the remote JSON file.
     */
    public static function get_supported_settings_frameworks() {
        $settings_frameworks = get_transient(self::get_asset_handle('settings-frameworks'));
        if (empty($settings_frameworks)) {
            $client = new Client(['base_uri' => 'https://config.deep-web-solutions.de/']);

            try {
                $response = $client->request('GET', '/supported-settings-frameworks.json', array(
                    'auth'  => array('dws-web-project', 'XOsj2gidQ9GJwYNpMlb4jkqVDkPoE6LR8QPIAxW0NgtiotRslpcYFkXMV6Uj')
                ));
                $plugins_config = $response->getBody();
            } catch (GuzzleException $e) {
                $message = __('Error making request. Message: ') . $e->getMessage();

                DWS_Admin_Notices::add_admin_notice_to_user($message);
                error_log($message);

                $plugins_config = json_encode(array());
            }

            $settings_frameworks = json_decode($plugins_config, true);
            set_transient(self::get_asset_handle('settings-frameworks'), $settings_frameworks, 60 * 60 * 24);
        }

        return $settings_frameworks;
    }

    //endregion
}
