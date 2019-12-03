<?php

namespace Deep_Web_Solutions\Admin\Settings;
use Deep_Web_Solutions\Base\DWS_Functionality_Template;
use Deep_Web_Solutions\Core\DWS_Installation;
use Deep_Web_Solutions\Admin\DWS_Admin_Notices;
use Deep_Web_Solutions\Admin\DWS_Settings;
use Deep_Web_Solutions\Helpers\DWS_Permissions;

if (!defined('ABSPATH')) { exit; }

/**
 * Template for encapsulating some of the most often required abilities of a settings framework.
 *
 * @since   2.0.0
 * @version 2.0.0
 * @author  Fatine Tazi <f.tazi@deep-web-solutions.de>
 *
 * @see     DWS_Functionality_Template
 */
final class DWS_Settings_Installation extends DWS_Functionality_Template {
    //region FIELDS AND CONSTANTS

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @var     string  SETTINGS_FRAMEWORK_SAVE_ACTION      The name of the action called when the form is submitted for
     *                                                      saving the settings framework choice.
     */
    private const SETTINGS_FRAMEWORK_SAVE_ACTION = 'dws-core_settings-framework-save';

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @var     string  SETTINGS_FRAMEWORK_SELECT_NAME      The name of the select field storing the settings framework choices.
     */
    private const SETTINGS_FRAMEWORK_SELECT_NAME = 'dws-core_settings-framework';

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
        $loader->add_action('dws_main_page', $this, 'output_settings_framework_dropdown', PHP_INT_MAX);
        $loader->add_action('admin_post_' . self::SETTINGS_FRAMEWORK_SAVE_ACTION, $this, 'save_settings_framework_choice', PHP_INT_MAX);

        $loader->add_action('admin_init', $this, 'maybe_show_misconfigured_plugins_error');
    }

    //endregion

    //region COMPATIBILITY LOGIC

    /**
     * Adds a custom fields plugin section with a dropdown to choose the plugin that should be used.
     *
     * @since   2.0.0
     * @version 2.0.0
     * @author  Fatine Tazi <f.tazi@deep-web-solutions.de>
     */
    public function output_settings_framework_dropdown() {
        // check permissions
        if (!DWS_Permissions::has('administrator')) { return; }

        // generate HTML for select field
        $supported_settings_frameworks = DWS_Settings::get_supported_settings_frameworks();
        $current_framework_slug = DWS_Settings::get_settings_framework_slug();
        $html = empty($current_framework_slug)
            ? '<option value="" ' . selected($current_framework_slug, '', false) . ' ></option>'
            : '';

        foreach ($supported_settings_frameworks as $framework) {
            $name = $framework['name']; $value = $framework['slug'];
            $html .= '<option value="' . $value . '" '. selected($current_framework_slug, $value, false) . ' >' . $name . '</option> ';
        }

        echo '<div class="dws-select">
                    <h2 class="dws-with-subtitle">'. __('Settings Framework', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN) .'</h2>
                    <p class="dws-subtitle">'. __('Please select your desired settings framework.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN) .'</p>
                    <form action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" method="post">
                        <input type="hidden" name="action" value="' . self::SETTINGS_FRAMEWORK_SAVE_ACTION . '">	
                            <select name="' . self::SETTINGS_FRAMEWORK_SELECT_NAME . '">
                                ' . $html . '
                             </select>
                         <button type="submit" class="button button-primary button-large">' . __('Save', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN) . '</button>
                    </form>
             </div>';
    }

    /**
     * Saves the new framework option.
     *
     * @since   2.0.0
     * @version 2.0.0
     * @author  Fatine Tazi <f.tazi@deep-web-solutions.de>
     */
    public function save_settings_framework_choice() {
        if (isset($_POST[self::SETTINGS_FRAMEWORK_SELECT_NAME]) && !empty($_POST[self::SETTINGS_FRAMEWORK_SELECT_NAME])) {
            $result = DWS_Settings::update_settings_framework_slug(esc_sql($_POST[self::SETTINGS_FRAMEWORK_SELECT_NAME]));
            if ($result) {
                DWS_Admin_Notices::add_admin_notice_to_user(
                    __('Successfully set the settings framework.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
                    DWS_Admin_Notices::SUCCESS
                );
            } else {
                DWS_Admin_Notices::add_admin_notice_to_user(
                    __('Failed to set the settings framework. Please try again!', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN)
                );
            }
        }

        wp_safe_redirect(admin_url('admin.php?page=dws_custom-extensions_main'));
        status_header(200); //wp_die();
    }

    /**
     * If the plugins needed for the currently selected framework are not installed or inactive, show an error.
     *
     * @since   2.0.0
     * @version 2.0.0
     */
    public function maybe_show_misconfigured_plugins_error() {
        // check if everything needed is installed
        $selectedFramework = DWS_Settings::get_settings_framework_slug();

        if (empty($selectedFramework)) {
            /** @noinspection PhpIncludeInspection */
            require_once(DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'admin/includes/settings/templates/plugin-required-error.php');
        } else if (DWS_Installation::is_installed() !== false) {
            $supported_settings_frameworks = DWS_Settings::get_supported_settings_frameworks();
            foreach ($supported_settings_frameworks as $framework) {
                if ($framework['slug'] !== $selectedFramework) { continue; }
                if (empty($framework['dependencies'])) { continue; }

                foreach ($framework['dependencies'] as $dependency => $definition) {
                    if (!is_plugin_active($dependency)) {
                        extract(array('html' => self::generate_framework_dependencies_html($framework)));
                        /** @noinspection PhpIncludeInspection */
                        require_once(DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'admin/includes/settings/templates/not-active-plugin-error.php');
                    }
                }
            }
        }
    }

    //endregion

    //region HELPER METHODS

    /**
     * Returns the html of the dependencies of the framework(s).
     *
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   array   $framework
     * @param   string  $class
     *
     * @return  string  The html for the dependencies of the framework(s).
     */
    public static function generate_framework_dependencies_html($framework, $class = '') {
        $html = empty($class) ? '<ul>' : '<ul class="' . $class . '">';

        foreach ($framework['dependencies'] as $dependency => $definition) {
            $url = $definition['url']; $name = $definition['name'];
            $html .= '<li><a href="'. $url .'" target="_blank">'. $name .'</a></li>';
        }

        $html .= '</ul>';

        return $html;
    }

    //endregion
}