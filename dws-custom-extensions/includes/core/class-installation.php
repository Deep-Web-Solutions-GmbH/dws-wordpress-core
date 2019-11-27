<?php

namespace Deep_Web_Solutions\Core;
use Deep_Web_Solutions\Admin\Settings\DWS_Settings_Installation;
use Deep_Web_Solutions\Admin\DWS_Settings;
use Deep_Web_Solutions\Base\DWS_Root;
use Deep_Web_Solutions\Custom_Extensions;
use Deep_Web_Solutions\Helpers\DWS_Permissions;

if (!defined( 'ABSPATH')) { exit; }

/**
 * Provides an "installation" function to this MU-plugin.
 *
 * @since   1.0.0
 * @version 2.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Root
 */
final class DWS_Installation extends DWS_Root {
	//region FIELDS AND CONSTANTS

	/**
	 * @since   1.0.0
	 * @version 2.0.0
	 *
	 * @var     string      INSTALL_ACTION      The name of the AJAX action on which the 'installation' should occur.
	 */
	private const INSTALL_ACTION = 'dws_install_custom_extensions';

	/**
	 * @since   1.4.0
	 * @version 2.0.0
	 *
	 * @var     string  INSTALL_OPTION  The name of the option stored in the database which indicates whether the
	 *                                  core has been installed or not.
	 */
	private const INSTALL_OPTION = 'dws_installed-core-option';

	//endregion

	//region INHERITED FUNCTIONS

	/**
	 * @since   1.0.0
	 * @version 1.4.0
	 *
	 * @see     DWS_Root::define_hooks()
	 *
	 * @param   DWS_Loader    $loader
	 */
	protected function define_hooks($loader) {
		$loader->add_action('wp_ajax_' . self::INSTALL_ACTION, $this, 'run_installation', PHP_INT_MIN);
		$loader->add_action('admin_notices', $this, 'add_install_update_admin_notice', PHP_INT_MAX);
		$loader->add_action('dws_main_page', $this, 'add_reinstall_section', PHP_INT_MAX);
	}

	//endregion

    //region METHODS

	/**
	 * Gathers all installable classes and runs their installation. This is a very expensive operation,
	 * so it should only be triggered by an admin by AJAX.
	 *
	 * @since   1.0.0
	 * @version 1.5.3
	 */
	public static function run_installation() {
		if (wp_doing_ajax() && !DWS_Permissions::has('administrator')) {
			return;
		}

		foreach (get_declared_classes() as $declared_class) {
			if (!in_array('Deep_Web_Solutions\Core\DWS_Installable', class_implements($declared_class))) {
				continue;
			}

			try {
				$class           = new \ReflectionClass($declared_class);
				$install_version = $class->getMethod('get_version')->invoke(null);

				if (get_option($class->getName() . '_install_version') !== $install_version) {
					$class->getMethod('install')->invoke(null);
					update_option($class->getName() . '_install_version', $install_version);
				}
			} catch (\ReflectionException $exception) { /* literally impossible currently */ }
		}

        update_option(self::INSTALL_OPTION, Custom_Extensions::get_version());

		wp_safe_redirect('/wp-admin/');
        status_header(200); wp_die();
	}

	/**
	 * Adds a notice on the admin pages once the DWS core has been copied in the filesystem. This notice indicates
	 * that the core should be installed. It also provides a link to the installation.
	 *
	 * @author  Dushan Terzikj  <d.terzikj@deep-web-solutions.de>
	 *
	 * @since   1.4.0
	 * @version 2.0.0
	 */
	public function add_install_update_admin_notice() {
        if (!DWS_Permissions::has('administrator')) { return; }

        $current_version = self::is_installed();
        $link_to_install = add_query_arg('action', self::INSTALL_ACTION, admin_url('admin-ajax.php'));

        if (!$current_version) {
            //TODO ideally, the user could select right here the framework and it would "auto-magically" install and activate

            // generate HTML for select field
            $supported_options_frameworks = DWS_Settings::get_supported_settings_frameworks();
            $html = '';

            foreach ($supported_options_frameworks as $framework) {
                $framework_name = $framework['name'];
                $html .= '<li> '. $framework_name .'';
                    $html .= DWS_Settings_Installation::generate_framework_dependencies_html($framework, "dws_options_framework_select");
                $html .= '</li>';
            }

            // output the whole notice HTML
            echo '<div class="notice notice-warning" style="padding-bottom: 10px !important;">
                <p>' .
                    sprintf(
                        __('DWS Wordpress Core has been detected! Please click on \'%s\' to install it and then install the required plugins for your settings framework of choice to start using DWS WP Core.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
                        __('Install', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN)
                    )
                . '</p>
                <p>' . __('The following settings framework are available (pick just one):', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN) .'</p>
                <ul>
                    '. $html .'
                </ul>
                <a href="'. $link_to_install .'"><button class="button button-primary button-large">' . __('Install', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN) . '</button></a>
            </div>';
        } else if($current_version != Custom_Extensions::get_version()) { // single equal on purpose !!!
            echo '<div class="notice notice-warning" style="padding-bottom: 10px !important;">
                <p>' . __('Looks like a newer version of the core is available. Update it here!',
                    DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN) . '</p>
                <a href="' . $link_to_install . '"><button class="button button-primary button-large">' . __('Update', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN) . '</button></a>
            </div>';
        }
	}

	/**
	 * Adds a reinstall section with a button that reinstall the core when clicked.
	 *
	 * @since   1.4.0
	 * @version 1.4.0
	 */
	public function add_reinstall_section() {
		if (DWS_Permissions::has('administrator') && get_option(self::INSTALL_OPTION, false)) {
			$link_to_reinstall = add_query_arg('action', self::INSTALL_ACTION, admin_url('admin-ajax.php'));
			echo '<div class="dws-postbox">
						<h2 class="dws-with-subtitle">'. __('Reinstall', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN) .'</h2>
						<p class="dws-subtitle">'. __('Do you want to reinstall the DWS core?', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN) .'</p>
						<a href="'. $link_to_reinstall .'"><button class="button button-primary button-large">' . __('Reinstall', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN) . '</button></a>
					</div>';
		}
	}

	//endregion

    //region HELPERS

	/**
     * Checks if DWS Core is installed.
     *
     * @since   2.0.0
     * @version 2.0.0
     * @author  Fatine Tazi     <f.tazi@deep-web-solutions.de>
     *
     * @return  false|string    False if not installed, version string if installed.
     */
	public static function is_installed() {
        return get_option(self::INSTALL_OPTION, false);
    }

    //endregion
}