<?php

namespace Deep_Web_Solutions\Admin;
use Deep_Web_Solutions\Admin\Settings\Adapters\DWS_ACFPro_Adapter;
use Deep_Web_Solutions\Admin\Settings\DWS_Adapter;
use Deep_Web_Solutions\Admin\Settings\DWS_Installation;
use Deep_Web_Solutions\Admin\Settings\DWS_Settings_Pages;
use Deep_Web_Solutions\Admin\Settings\Permissions;
use Deep_Web_Solutions\Core\DWS_Functionality_Template;

if (!defined('ABSPATH')) { exit; }

/**
 * Handles all the settings related extensions including the options pages.
 *
 * @since   2.0.0
 * @version 2.0.0
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
        /** Template for encapsulating some of the most often required abilities of a settings framework. */
        require_once(self::get_includes_base_path() . 'abstract-adapter.php');

        /** @noinspection PhpIncludeInspection */
        /** The custom DWS permissions needed to enhance the settings pages. */
        require_once(self::get_includes_base_path() . 'class-permissions.php');
        Permissions::maybe_initialize_singleton('agdsgrhgehiue');

        /** @noinspection PhpIncludeInspection */
        /** Handles the issue of making sure a settings framework is in place and working. */
        require_once(self::get_includes_base_path() . 'class-installation.php');
        DWS_Installation::maybe_initialize_singleton('gh4w87ghew87fgrwed');

        /** @noinspection PhpIncludeInspection */
        /** Handles the settings pages and the options therein. */
        require_once(self::get_includes_base_path() . 'class-settings-pages.php');
        DWS_Settings_Pages::maybe_initialize_singleton('j89hg3854gh3433e');

        // NOW WE'LL LOAD THE INDIVIDUAL ADAPTERS
        // TODO VERY VERY IMPORTANT: WHEN THIS WORKS, MOVE EACH ADAPTER TO ITS OWN DWS PLUGIN
        /** @noinspection PhpIncludeInspection */
        require_once(self::get_includes_base_path() . 'adapters/class-acf-pro.php');
        /** @noinspection PhpIncludeInspection */
        require_once(self::get_includes_base_path() . 'adapters/class-cmb2.php');
    }

    //endregion

    //region HELPERS

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.de>
     *
     * @return  DWS_Adapter
     */
    public static function get_option_framework_adapter() {
        $slug = self::get_option_framework_slug();
        if (empty($slug)) { return null; }

        // TODO: custom logic based on slug, this is ugly hack for demo-ing
        DWS_ACFPro_Adapter::maybe_initialize_singleton('dhg873h8g34g43');
        return DWS_ACFPro_Adapter::get_instance();
    }

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.de>
     *
     * @return  string  The options framework used by the DWS WP Core.
     */
    public static function get_option_framework_slug() {
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
    public static function update_option_framework_slug($new_slug) {
        return update_option(self::SETTINGS_FRAMEWORK, $new_slug);
    }

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @return  bool    True, if option is successfully deleted. False on failure.
     */
    public static function delete_option_framework_slug() {
        return delete_option(self::SETTINGS_FRAMEWORK);
    }

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @return  array   The supported options frameworks from the remote JSON file.
     */
    public static function get_supported_options_frameworks() {
        $auth           = base64_encode('dws-web-project:XOsj2gidQ9GJwYNpMlb4jkqVDkPoE6LR8QPIAxW0NgtiotRslpcYFkXMV6Uj');
        $context        = stream_context_create(['http' => ['header' => "Authorization: Basic $auth"]]);
        $plugins_config = file_get_contents('https://config.deep-web-solutions.de/dws_supported_frameworks.json', false, $context);

        return json_decode($plugins_config, true);
    }

    //endregion
}
