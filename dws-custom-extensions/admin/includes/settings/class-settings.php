<?php

namespace Deep_Web_Solutions\Admin;
use Deep_Web_Solutions\Admin\Settings\DWS_Adapter;
use Deep_Web_Solutions\Admin\Settings\DWS_Settings_Installation;
use Deep_Web_Solutions\Admin\Settings\DWS_Settings_Pages;
use Deep_Web_Solutions\Admin\Settings\Permissions;
use Deep_Web_Solutions\Base\DWS_Functionality_Template;

if (!defined('ABSPATH')) { exit; }

/**
 * Handles all the settings related extensions including the settings pages.
 *
 * @since   2.0.0
 * @version 2.0.2
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
     * @version 2.0.0
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
     * @version 2.0.0
     *
     * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.de>
     *
     * @param   false|string    $slug
     *
     * @return  DWS_Adapter
     */
    public static function get_settings_framework_adapter($slug = false) {
        $selectedFramework = ($slug === false) ? self::get_settings_framework_slug() : $slug;
        return apply_filters(self::get_hook_name('framework_adapter'), null, $selectedFramework);
    }

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   string      $field
     * @param   int|false   $post_id
     *
     * @return  mixed
     */
    public static function get_field($field, $post_id = false) {
        $adapter = DWS_Settings::get_settings_framework_adapter();
        return is_null($adapter) ? null : $adapter::get_field_value($field, $post_id);
    }

    /**
     * @since   2.0.2
     * @version 2.0.2
     *
     * @param   array      $field
     *
     * @return  mixed
     */
    public static function make_field_uneditable($field) {
        $adapter = DWS_Settings::get_settings_framework_adapter();
        return is_null($adapter) ? null : $adapter::make_field_uneditable($field);
    }

    /**
     * @since   2.0.2
     * @version 2.0.2
     *
     * @param   string  $key
     * @param   string  $title
     * @param   array   $location
     * @param   array   $fields
     * @param   array   $other
     */
    public static function register_group($key, $title, $location, $fields, $other = array()) {
        $adapter = DWS_Settings::get_settings_framework_adapter();
        if(is_null($adapter)) { return;}
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
     * @version 2.0.0
     *
     * @return  array   The supported settings frameworks from the remote JSON file.
     */
    public static function get_supported_settings_frameworks() {
        $settings_frameworks = get_transient(self::get_asset_handle('settings-frameworks'));
        if (empty($settings_frameworks)) {
            $auth           = base64_encode('dws-web-project:XOsj2gidQ9GJwYNpMlb4jkqVDkPoE6LR8QPIAxW0NgtiotRslpcYFkXMV6Uj');
            $context        = stream_context_create(['http' => ['header' => "Authorization: Basic $auth"]]);
            $plugins_config = file_get_contents('https://config.deep-web-solutions.de/supported-settings-frameworks.json', false, $context);

            $settings_frameworks = json_decode($plugins_config, true);
            set_transient(self::get_asset_handle('settings-frameworks'), $settings_frameworks, 60 * 60 * 24);
        }

        return $settings_frameworks;
    }

    //endregion
}
