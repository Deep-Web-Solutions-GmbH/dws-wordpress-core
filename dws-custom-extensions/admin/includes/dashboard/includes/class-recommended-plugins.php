<?php

namespace Deep_Web_Solutions\Admin\Dashboard;
use Deep_Web_Solutions\Admin\DWS_Dashboard;
use Deep_Web_Solutions\Core\DWS_Functionality_Template;

if (!defined('ABSPATH')) { exit; }

/**
 * Configures an instance of the TGM Plugin Activation library.
 * 
 * @since   1.2.0
 * @version 1.2.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *          
 * @see     DWS_Functionality_Template
 */
final class DWS_Recommended_Plugins extends DWS_Functionality_Template {
	//region FIELDS AND CONSTANTS

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  private
	 * @var     string  $plugins_page_slug  The slug of the recommended plugins DWS dashboard page.
	 */
	private $plugins_page_slug = DWS_Dashboard::MENU_PAGES_SLUG_PREFIX . 'recommended-plugins';

	//endregion

	//region INHERITED FUNCTIONS

	/**
	 * @since   1.2.0
	 * @version 1.2.0
	 *          
	 * @see     DWS_Functionality_Template::define_functionality_hooks()
	 *                                                                  
	 * @param   \Deep_Web_Solutions\Core\DWS_WordPress_Loader   $loader
	 */
	protected function define_functionality_hooks($loader) {
		$loader->add_filter(DWS_Dashboard::get_hook_name('submenus'), $this, 'register_submenu_page');
		$loader->add_action('tgmpa_register', $this, 'register_recommended_plugins');
	}

	//endregion
	
	//region COMPATIBILITY LOGIC

	/**
	 * Registers the recommended plugins submenu page with the dashboard parent menu.
	 *
	 * @since   1.2.0
	 * @version 1.2.0
	 *
	 * @param   array   $submenus   All submenus registered so far.
	 *
	 * @return  array
	 */
	public function register_submenu_page($submenus) {
		$submenus[$this->plugins_page_slug] = array(
			'menu_title'    => __('Recommended Plugins', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
			'page_title'    => __('Deep Web Solutions: Custom Extensions Recommended Plugins',
			                      DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN)
		);

		return $submenus;
	}

	/**
	 * Initializes an instance of the TGM Plugin Activation library.
	 * 
	 * @since   1.2.0
	 * @version 1.2.0
	 */
	public function register_recommended_plugins() {
		// get plugins configuration
		$auth           = base64_encode('dws-web-project:XOsj2gidQ9GJwYNpMlb4jkqVDkPoE6LR8QPIAxW0NgtiotRslpcYFkXMV6Uj');
		$context        = stream_context_create(['http' => ['header' => "Authorization: Basic $auth"]]);
		$plugins_config = file_get_contents('https://config.deep-web-solutions.de/wp-plugins.json', false, $context);

		// parse said configuration
		$plugins        = array();
		$parsed_plugins = json_decode($plugins_config, true);
		foreach ($parsed_plugins as $category => $options_plugins) {
				switch($category) {
					case 'wp-repository':
						$plugins = array_merge($plugins, $options_plugins);
					break;
					case 'external-plugins':
						$plugins = array_merge($plugins, $options_plugins);
					break;
				}
		}

		$config = array(
			'id'           => DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN,    // Unique ID for hashing notices for multiple instances of TGMPA.
			'menu'         => $this->plugins_page_slug, // Menu slug.
			'parent_slug'  => 'admin.php?page=' . DWS_Dashboard::$main_page_slug,   // Parent menu slug.
			'capability'   => 'administrator',          // Capability needed to view plugin install page, should be a
														// capability associated with the parent menu used.
			'has_notices'  => true,                     // Show admin notices or not.
			'dismissable'  => true,                     // If false, a user cannot dismiss the nag message.
			'is_automatic' => false,                    // Automatically activate plugins after installation or not.
			'strings'      => array(
				'page_title'                      => __( 'Install Required Plugins', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN ),
				'menu_title'                      => __( 'Install Plugins', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN ),
				'installing'                      => __( 'Installing Plugin: %s', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN ),
				'updating'                        => __( 'Updating Plugin: %s', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN ),
				'oops'                            => __( 'Something went wrong with the plugin API.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN ),
				'notice_can_install_required'     => _n_noop(
					'This theme requires the following plugin: %1$s.',
					'This theme requires the following plugins: %1$s.',
					DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN
				),
				'notice_can_install_recommended'  => _n_noop(
					'This theme recommends the following plugin: %1$s.',
					'This theme recommends the following plugins: %1$s.',
					DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN
				),
				'notice_ask_to_update'            => _n_noop(
					'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.',
					'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.',
					DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN
				),
				'notice_ask_to_update_maybe'      => _n_noop(
					'There is an update available for: %1$s.',
					'There are updates available for the following plugins: %1$s.',
					DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN
				),
				'notice_can_activate_required'    => _n_noop(
					'The following required plugin is currently inactive: %1$s.',
					'The following required plugins are currently inactive: %1$s.',
					DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN
				),
				'notice_can_activate_recommended' => _n_noop(
					'The following recommended plugin is currently inactive: %1$s.',
					'The following recommended plugins are currently inactive: %1$s.',
					DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN
				),
				'install_link'                    => _n_noop(
					'Begin installing plugin',
					'Begin installing plugins',
					DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN
				),
				'update_link' 					  => _n_noop(
					'Begin updating plugin',
					'Begin updating plugins',
					DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN
				),
				'activate_link'                   => _n_noop(
					'Begin activating plugin',
					'Begin activating plugins',
					DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN
				),
				'return'                          => __( 'Return to Required Plugins Installer', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN ),
				'plugin_activated'                => __( 'Plugin activated successfully.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN ),
				'activated_successfully'          => __( 'The following plugin was activated successfully:', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN ),
				'plugin_already_active'           => __( 'No action taken. Plugin %1$s was already active.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN ),
				'plugin_needs_higher_version'     => __( 'Plugin not activated. A higher version of %s is needed for this theme. Please update the plugin.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN ),
				'complete'                        => __( 'All plugins installed and activated successfully. %1$s', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN ),
				'dismiss'                         => __( 'Dismiss this notice', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN ),
				'notice_cannot_install_activate'  => __( 'There are one or more required or recommended plugins to install, update or activate.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN ),
				'contact_admin'                   => __( 'Please contact the administrator of this site for help.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN ),
				'nag_type'                        => 'notice-info', // Determines admin notice type - can only be one of
																	// the typical WP notice classes, such as 'updated', 'update-nag', 'notice-warning', 'notice-info' or
																	// 'error'. Some of which may not work as expected in older WP versions.
			)
		);
		
		tgmpa($plugins, $config);
	}
	
	//endregion
}

/**
 * Extends the functionality of the default plugins table of the TGMPA library.
 *
 * @since   1.2.0
 * @version 1.2.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     \TGMPA_List_Table
 */
final class DWS_Plugins_List_Table extends \TGMPA_List_Table {
	/**
	 * Categorize the plugins which have open actions into views for the TGMPA page.
	 *
	 * @since 2.5.0
	 */
	protected function categorize_plugins_to_views() {
		$plugins = array(
			'all'      => array(), // Meaning: all plugins which still have open actions.
			'install'  => array(),
			'update'   => array(),
			'activate' => array(),
		);

		foreach ( $this->tgmpa->plugins as $slug => $plugin ) {

				$plugins['all'][ $slug ] = $plugin;

				if ( ! $this->tgmpa->is_plugin_installed( $slug ) ) {
					$plugins['install'][ $slug ] = $plugin;
				} else {
					if ( false !== $this->tgmpa->does_plugin_have_update( $slug ) ) {
						$plugins['update'][ $slug ] = $plugin;
					}

					if ( $this->tgmpa->can_plugin_activate( $slug ) ) {
						$plugins['activate'][ $slug ] = $plugin;
					}
				}

		}

		return $plugins;
	}
}