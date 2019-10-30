<?php

namespace Deep_Web_Solutions\Admin\Settings;
use Deep_Web_Solutions\Core\DWS_Functionality_Template;

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
final class DWS_Installation extends DWS_Functionality_Template {
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
        return; // temporary
        $loader->add_action('dws_main_page', $this, 'choose_custom_fields_plugin', PHP_INT_MAX);
        $loader->add_action('admin_post_choose_custom_fields_plugin', $this, 'check_plugin_installed_and_active', PHP_INT_MAX);
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
    public function choose_custom_fields_plugin() {
        if (DWS_Permissions::has('administrator')) {
            $supported_options_frameworks = apply_filters(self::get_hook_name('options-frameworks'), array());
            $html = '';

            foreach($supported_options_frameworks as $supported_options_framework){
                $html = $html . '<option value="' . $supported_options_framework . '" '. selected(get_option(self::CUSTOM_FIELDS_PLUGIN), "' . $supported_options_framework . '") . ' ></option> ';
            }

            echo '<div class="dws-select">
                        <h2 class="dws-with-subtitle">'. __('Options Framework', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN) .'</h2>
                        <p class="dws-subtitle">'. __('Please select your desired custom field plugin.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN) .'</p>
                        <form action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" method="post">
                            <input type="hidden" name="action" value="choose_custom_fields_plugin">	
                            <select name="custom_field_plugin_options">
                                ' . $html . '
                             </select>
                             <button type="submit" class="button button-primary button-large">' . __('Submit', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN) . '</button>
                        </form>
                 </div>';

            $selectOption = $_POST['custom_field_plugin_options'];
            update_option( self::CUSTOM_FIELDS_PLUGIN, $selectOption);
        }
    }

    /**
     * Checks if the plugin selected for custom fields is installed and active.
     *
     * @since   2.0.0
     * @version 2.0.0
     * @author  Fatine Tazi <f.tazi@deep-web-solutions.de>
     */
    public function check_plugin_installed_and_active() {
        $selectedPlugin = get_option(self::CUSTOM_FIELDS_PLUGIN, false);

        if ($selectedPlugin == "ACF"){
            if (!is_plugin_active('advanced-settings-pro/acf.php') || !is_plugin_active('acf-code-field/acf-code-field.php')) {
                add_action('admin_notices', function() {
                    /** @noinspection PhpIncludeInspection */
                    require_once(DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'admin/templates/not-active-plugin-error.php');
                });
            }
        } elseif ($selectedPlugin == "CMB2"){
            if (!is_plugin_active('cmb2/init.php')) {
                add_action('admin_notices', function() {
                    /** @noinspection PhpIncludeInspection */
                    require_once(DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'admin/templates/not-active-plugin-error.php');
                });
            }
        } else {
            add_action('admin_notices', function() {
                /** @noinspection PhpIncludeInspection */
                require_once(DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'admin/templates/plugin-required-error.php');
            });
        }

        wp_safe_redirect(admin_url('admin.php?page=dws_custom-extensions_main'));
        status_header(200);
        die("Server received '{$_REQUEST['custom_field_plugin_options']}' from your browser.");
    }

    //endregion
}