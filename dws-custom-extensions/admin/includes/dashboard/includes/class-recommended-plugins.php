<?php

namespace {
	if (!defined('ABSPATH')) { exit; }

	/**
	 * Helper function to register a collection of required plugins.
	 *
	 * @since   1.2.0
	 * @version 1.2.0
	 *
	 * @see     tgmpa()
	 *
	 * @param   array   $plugins    An array of plugin arrays.
	 * @param   array   $config     Optional. An array of configuration values.
	 */
	function dws_tgmpa($plugins, $config = array()) {
		$instance = call_user_func(array(get_class($GLOBALS['dws_tgmpa']), 'get_instance'));

		foreach ($plugins as $plugin) {
			call_user_func(array($instance, 'register'), $plugin);
		}

		if (!empty($config) && is_array($config)) {
			call_user_func(array($instance, 'config'), $config);
		}
	}

	/**
	 * Important that we do NOT register the default TGMPA instance, so that we can register our own custom one later.
	 * By doing this we avoid potential hook conflicts between the base version and ours.
	 *
	 * @since   1.2.0
	 * @version 1.2.0
	 *
	 * @see     load_tgm_plugin_activation()
	 */
	function load_tgm_plugin_activation() {
		// Do nothing.
	}

	/** @noinspection PhpIncludeInspection */
	/**
	 * We use this external library to install and update plugins. It must be loaded here so that we can overwrite
	 * some of its functions before this point.
	 */
	require_once(DWS_CUSTOM_EXTENSIONS_BASE_PATH . 'libraries/TGM-Plugin-Activation/class-tgm-plugin-activation.php');
}

namespace Deep_Web_Solutions\Admin\Dashboard {
	use Deep_Web_Solutions\Admin\DWS_Dashboard;
	use Deep_Web_Solutions\Core\DWS_Functionality_Template;

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

			$loader->add_action('plugins_loaded', $this, 'load_dws_tgmpa');
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
				'menu_title' => __('Recommended Plugins', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'page_title' => __(
					'Deep Web Solutions: Custom Extensions Recommended Plugins',
					DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN
				)
			);

			return $submenus;
		}

		/**
		 * Stores the singleton instance of the DWS TGMPA extension in a global variable.
		 *
		 * @since   1.2.0
		 * @version 1.2.0
		 */
		public function load_dws_tgmpa() {
			$GLOBALS['dws_tgmpa'] = DWS_TGMPA::get_instance();
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
				if (in_array($category, array('dws-core-plugins', 'dws-core-modules'))) {
					foreach ($options_plugins as $plugin) {
						if (empty($plugin['dependency']) || is_plugin_active($plugin['dependency'])) {
							$plugin['category'] = $category;
							$plugins[] = $plugin;
						}
					}
				} else {
					$plugins = array_merge($plugins, $options_plugins);
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
					'page_title'                      => __('Install Required Plugins', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
					'menu_title'                      => __('Install Plugins', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
					'installing'                      => __('Installing Plugin: %s', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
					'updating'                        => __('Updating Plugin: %s', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
					'oops'                            => __('Something went wrong with the plugin API.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
					'notice_can_install_required'     => _n_noop(
						'DWS Custom Extensions requires the following plugin: %1$s.',
						'DWS Custom Extensions requires the following plugins: %1$s.',
						DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN
					),
					'notice_can_install_recommended'  => _n_noop(
						'DWS Custom Extensions recommends the following plugin: %1$s.',
						'DWS Custom Extensions recommends the following plugins: %1$s.',
						DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN
					),
					'notice_ask_to_update'            => _n_noop(
						'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this DWS Custom Extensions: %1$s.',
						'The following plugins need to be updated to their latest version to ensure maximum compatibility with this DWS Custom Extensions: %1$s.',
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
					'update_link'                     => _n_noop(
						'Begin updating plugin',
						'Begin updating plugins',
						DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN
					),
					'activate_link'                   => _n_noop(
						'Begin activating plugin',
						'Begin activating plugins',
						DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN
					),
					'return'                          => __('Return to Required Plugins Installer', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
					'plugin_activated'                => __('Plugin activated successfully.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
					'activated_successfully'          => __('The following plugin was activated successfully:', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
					'plugin_already_active'           => __('No action taken. Plugin %1$s was already active.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
					'plugin_needs_higher_version'     => __('Plugin not activated. A higher version of %s is needed for DWS Custom Extensions. Please update the plugin.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
					'complete'                        => __('All plugins installed and activated successfully. %1$s', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
					'dismiss'                         => __('Dismiss this notice', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
					'notice_cannot_install_activate'  => __('There are one or more required or recommended plugins to install, update or activate.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
					'contact_admin'                   => __('Please contact the administrator of this site for help.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
					'nag_type'                        => 'notice-info', // Determines admin notice type - can only be one of
																		// the typical WP notice classes, such as 'updated', 'update-nag', 'notice-warning', 'notice-info' or
																		// 'error'. Some of which may not work as expected in older WP versions.
				)
			);

			dws_tgmpa($plugins, $config);
		}

		//endregion
	}

	/**
	 * @since   1.2.0
	 * @version 1.2.0
	 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
	 */
	final class DWS_TGMPA extends \TGM_Plugin_Activation {
		//region INHERITED FUNCTIONS

		/**
		 * Make it such that upon upgrading plugins, the old version is fully removed before installing the new version.
		 *
		 * @since   1.2.0
		 * @version 1.2.0
		 *
		 * @see     \TGM_Plugin_Activation::do_plugin_install()
		 *
		 * @return  bool
		 */
		protected function do_plugin_install() {
			add_filter('upgrader_package_options', array($this, 'adjust_plugin_install_options'));
			$installation_result = parent::do_plugin_install();
			remove_filter('upgrader_package_options', array($this, 'adjust_plugin_install_options'));

			return $installation_result;
		}

		/**
		 * Makes it such that the TGMPA library doesn't register its own page, so we can do it ourselves with
		 * overwritten logic.
		 *
		 * @since   1.2.0
		 * @version 1.2.0
		 *
		 * @see     \TGM_Plugin_Activation::admin_menu()
		 */
		public function admin_menu() {
			// Do nothing.
		}

		/**
		 * Retrieve the URL to the TGMPA Install page for a specific plugins view.
		 *
		 * I.e. depending on the config settings passed something along the lines of:
		 * http://example.com/wp-admin/themes.php?page=tgmpa-install-plugins&plugin_category=install
		 *
		 * @since   1.2.0
		 * @version 1.2.0
		 *
		 * @see     \TGM_Plugin_Activation::get_tgmpa_status_url()
		 *
		 * @param   array   $parameter  Contains the parameter name and value to be appended.
		 *
		 * @return  string  Properly encoded URL (not escaped).
		 */
		public function get_tgmpa_status_url($parameter) {
			if (!is_array($parameter)) {
				$parameter = array(
					'param' => 'plugin_status',
					'value' => $parameter
				);
			}

			return add_query_arg(
				array(
					$parameter['param'] => urlencode($parameter['value'])
				),
				$this->get_tgmpa_url()
			);
		}

		/**
		 * Returns the singleton instance of the class.
		 *
		 * @since   1.2.0
		 * @version 1.2.0
		 *
		 * @see     \TGM_Plugin_Activation::get_instance()
		 *
		 * @return  DWS_TGMPA   Singleton instance.
		 */
		public static function get_instance() {
			if (!isset(self::$instance) && !(self::$instance instanceof self)) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * We need to add custom logic for our own internal plugins and modules.
		 *
		 * @since   1.2.0
		 * @version 1.2.0
		 *
		 * @see     \TGM_Plugin_Activation::is_plugin_installed()
		 *
		 * @param   string  $slug
		 *
		 * @return  bool
		 */
		public function is_plugin_installed($slug) {
			if (strpos($slug, 'dws-') === 0) {
				$base_path = $this->get_dws_plugins_base_path($slug);
				$slug = $this->get_dws_plugin_slug($slug);

				return is_dir($base_path . $slug);
			} else {
				return parent::is_plugin_installed($slug);
			}
		}

		/**
		 * We need to add custom logic for our own internal plugins and modules.
		 *
		 * @since   1.2.0
		 * @version 1.2.0
		 *
		 * @see     \TGM_Plugin_Activation::is_plugin_active()
		 *
		 * @param   string  $slug
		 *
		 * @return  bool
		 */
		public function is_plugin_active($slug) {
			if (strpos($slug, 'dws-') === 0) {
				return self::is_plugin_installed($slug);
			} else {
				return parent::is_plugin_active($slug);
			}
		}

		/**
		 * We need to add custom logic for our own internal plugins and modules.
		 *
		 * @since   1.2.0
		 * @version 1.2.0
		 *
		 * @see     \TGM_Plugin_Activation::is_plugin_updatetable()
		 *
		 * @param   string  $slug
		 *
		 * @return  bool
		 */
		public function is_plugin_updatetable($slug) {
			if (strpos($slug, 'dws-') === 0) {
				return $this->does_plugin_have_update($slug);
			} else {
				return parent::is_plugin_updatetable($slug);
			}
		}

		/**
		 * We need to add custom logic for our own internal plugins and modules.
		 *
		 * @since   1.2.0
		 * @version 1.2.0
		 *
		 * @see     \TGM_Plugin_Activation::does_plugin_have_update()
		 *
		 * @param   string  $slug
		 *
		 * @return  false|string
		 */
		public function does_plugin_have_update($slug) {
			if (strpos($slug, 'dws-') === 0) {
				$base_path = $this->get_dws_plugins_base_path($slug);
				$dws_slug = $this->get_dws_plugin_slug($slug);

				$update_checker = \Puc_v4_Factory::buildUpdateChecker(
					$this->plugins[$slug]['source'],
					$base_path . $dws_slug . "/$dws_slug.php"
				);
				$update_checker->setAuthentication(DWS_GITHUB_ACCESS_TOKEN);
				$update_checker->setBranch('master');

				$update = $update_checker->checkForUpdates();
				return is_null($update) ? false : $update->version;
			} else {
				return parent::does_plugin_have_update($slug);
			}
		}

		/**
		 * We need to add custom logic for our own internal plugins and modules.
		 *
		 * @since   1.2.0
		 * @version 1.2.0
		 *
		 * @see     \TGM_Plugin_Activation::get_download_url()
		 *
		 * @param   string  $slug
		 *
		 * @return  string
		 */
		public function get_download_url($slug) {
			if (strpos($slug, 'dws-') === 0) {
				$GLOBALS['dws_plugin_slug'] = $slug;

				$repo = new \Puc_v4p4_Vcs_GitHubApi($this->plugins[$slug]['source'], DWS_GITHUB_ACCESS_TOKEN);
				$release = $repo->getLatestRelease();

				return $release->downloadUrl;
			} else {
				return parent::get_download_url($slug);
			}
		}

		/**
		 * We need to add custom logic for our own internal plugins and modules.
		 *
		 * @since   1.2.0
		 * @version 1.2.0
		 *
		 * @see     \TGM_Plugin_Activation::get_installed_version()
		 *
		 * @param   string  $slug
		 *
		 * @return  string
		 */
		public function get_installed_version($slug) {
			if (strpos($slug, 'dws-') === 0) {
				$base_path = $this->get_dws_plugins_base_path($slug);
				$dws_slug = $this->get_dws_plugin_slug($slug);

				$plugin_data  = get_plugin_data($base_path . $dws_slug . "/$dws_slug.php");
				return $plugin_data['Version'];
			} else {
				return parent::get_installed_version($slug);
			}
		}

		//endregion

		//region COMPATIBILITY LOGIC

		/**
		 * Public plugin install action.
		 *
		 * @since   1.2.0
		 * @version 1.2.0
		 *
		 * @see     \TGM_Plugin_Activation::do_plugin_install()
		 */
		public function public_do_plugin_install() {
			return $this->do_plugin_install();
		}

		/**
		 * Filter $options to clear plugin destination before installation.
		 *
		 * @since   1.2.0
		 * @version 1.2.0
		 *
		 * @see     DWS_TGMPA::do_plugin_install()
		 *
		 * @param   array   $options    The current installation options.
		 *
		 * @return  array   Installation options which request clearing the plugin before installation.
		 */
		public function adjust_plugin_install_options($options) {
			$options['clear_destination'] = true;

			if (isset($GLOBALS['dws_plugin_slug'])) {
				$dws_slug = $this->get_dws_plugin_slug($GLOBALS['dws_plugin_slug']);
				$options['destination'] = DWS_CUSTOM_EXTENSIONS_BASE_PATH;

				if (strpos($GLOBALS['dws_plugin_slug'], 'plugin')) {
					$directory = str_replace('dws-wordpress-plugins-', '', $dws_slug);
					$options['destination'] .= "plugins/$directory";
				} else {
					$directory = str_replace('dws-wordpress-modules-', '', $dws_slug);
					$options['destination'] .= "modules/$directory";
				}

				unset($GLOBALS['dws_plugin_slug']);
			}

			return $options;
		}

		//endregion

		//region HELPERS

		/**
		 * Gets the full system path to either the DWS modules or plugins sub-folder.
		 *
		 * @since   1.2.0
		 * @version 1.2.0
		 *
		 * @param   string  $slug   The slug of the plugin in question.
		 *
		 * @return  string  The full system path to where the plugin files should be.
		 */
		private function get_dws_plugins_base_path($slug) {
			$is_module = (strpos($slug, 'module') === 4);
			return trailingslashit(DWS_CUSTOM_EXTENSIONS_BASE_PATH . ($is_module ? 'modules' : 'plugins'));
		}

		/**
		 * Gets rid of the external slug prefix of internal DWS modules or plugins.
		 *
		 * @since   1.2.0
		 * @version 1.2.0
		 *
		 * @param   string  $slug   The slug of the plugin in question.
		 *
		 * @return  string  The proper internal slug, no prefixes.
		 */
		private function get_dws_plugin_slug($slug) {
			return substr($slug, strpos($slug, '_') + 1);
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
		//region MAGIC FUNCTIONS

		/**
		 * DWS_Plugins_List_Table constructor.
		 * Required so that we overwrite the TGMPA instance with our own extension.
		 *
		 * @since   1.2.0
		 * @version 1.2.0
		 */
		public function __construct() {
			parent::__construct();

			$this->tgmpa        = call_user_func(array(get_class($GLOBALS['dws_tgmpa']), 'get_instance'));
			$this->view_context = isset($_REQUEST['plugin_status']) ? sanitize_key($_REQUEST['plugin_status'])
				: (isset($_REQUEST['plugin_category']) ? $_REQUEST['plugin_category'] : 'all');
		}

		//endregion

		//region INHERITED FUNCTIONS

		/**
		 * Categorize the plugins which have open actions into views for the TGMPA page.
		 *
		 * @since   1.2.0
		 * @version 1.2.0
		 *
		 * @see     \TGMPA_List_Table::categorize_plugins_to_views()
		 *
		 * @return  array
		 */
		protected function categorize_plugins_to_views() {
			$plugins = array(
				'all'      => array(),
				'install'  => array(),
				'update'   => array(),
				'activate' => array(),
				'Others'   => array()
			);

			foreach ($this->tgmpa->plugins as $slug => $plugin) {
				$plugins['all'][$slug] = $plugin;

				if (isset($plugin['category'])) {
					$categories = explode(',', $plugin['category']);
					foreach ($categories as $category) {
						$plugins[$category][$slug] = $plugin;
					}
				} else {
					$plugins['Others'][$slug] = $plugin;
				}

				if (!$this->tgmpa->is_plugin_installed($slug)) {
					$plugins['install'][$slug] = $plugin;
				} else {
					if (false !== $this->tgmpa->does_plugin_have_update($slug)) {
						$plugins['update'][$slug] = $plugin;
					}

					if ($this->tgmpa->can_plugin_activate($slug)) {
						$plugins['activate'][$slug] = $plugin;
					}
				}
			}

			return $plugins;
		}

		/**
		 * Get an associative array ( id => link ) of the views available on this table.
		 *
		 * @since   1.2.0
		 * @version 1.2.0
		 *
		 * @see     \TGMPA_List_Table::views()
		 *
		 * @return  array
		 */
		public function get_views() {
			$category_links = array();

			foreach ($this->view_totals as $type => $count) {
				if ($count < 1 || in_array($type, array('all', 'install', 'update', 'activate'))) {
					continue;
				}

				$category_links[$type] = sprintf(
					'<a href="%s"%s>%s</a>',
					esc_url($this->tgmpa->get_tgmpa_status_url(array('param' => 'plugin_category', 'value' => $type))),
					($type === $this->view_context) ? ' class="current"' : '',
					sprintf("$type <span class='count'>(%s)</span>", number_format_i18n($count))
				);
			}

			return array_merge(parent::get_views(), $category_links);
		}

		/**
		 * Output all the column information within the table.
		 *
		 * @since 2.2.0
		 *
		 * @return array $columns The column names.
		 */
		public function get_columns() {
			return array_merge(parent::get_columns(), array(
				'version'   => __('Version', 'tgmpa'),
				'status'    => __('Status', 'tgmpa')
			));
		}

		//endregion
	}
}