<?php

namespace Deep_Web_Solutions\Front;
use Deep_Web_Solutions\Admin\DWS_Admin;
use Deep_Web_Solutions\Core\DWS_Root;

if (!defined('ABSPATH')) { exit; }

/**
 * Orchestrates the DWS Core extensions of the front-end area of the website.
 *
 * @since   1.0.0
 * @version 1.3.4
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Root
 */
final class DWS_Public extends DWS_Root {
	//region FIELDS AND CONSTANTS

	/**
	 * @since   1.2.4
	 * @version 1.2.4
	 *
	 * @var     string  CUSTOM_CSS  The name of the ACF fields which stores CSS to be added globally to the website.
	 */
	const CUSTOM_CSS = 'dws_public_global-css';

	/**
	 * @since   1.2.4
	 * @version 1.2.4
	 *
	 * @var     string  CUSTOM_JS   The name of the ACF fields which stores JS to be added globally to the website.
	 */
	const CUSTOM_JS  = 'dws_public_global-js';

	//endregion

	//region INHERITED FUNCTIONS
	protected function define_hooks( $loader ) {
		$loader->add_action('wp_head', $this, 'add_frontend_support', PHP_INT_MAX);
		$loader->add_action('admin_head', $this, 'add_backend_support', PHP_INT_MAX);
	}


	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Root::load_dependencies()
	 */
	protected function load_dependencies() {
		/** @noinspection PhpIncludeInspection */
		/** This class creates handy shortcodes for customer service related activities. */
		require_once(self::get_includes_base_path() . 'class-customer-service.php');

		/** @noinspection PhpIncludeInspection */
		/** Handles the output of CSS and settings to enclose text in a circle. */
		require_once(self::get_includes_base_path() . 'class-circled-content.php');

		/** @noinspection PhpIncludeInspection */
		/** Loads style for fancy-looking front-end messages. */
		require_once(self::get_includes_base_path() . 'class-fancy-messages.php');
	}

    /**
     * @since   1.1.0
     * @version 1.3.2
     *
     * @see     DWS_Root::admin_options()
     *
     * @return  array
     */
	protected function admin_options() {
		return array(
			array(
				'key'       => 'field_dhsg8h48wegwew',
				'name'      => self::CUSTOM_CSS,
				'label'     => __('Custom global CSS', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'type'      => 'acf_code_field',
				'wrapper'   => array('width' => '50%'),
				'mode'      => 'css',
				'rows'      => 15
			),
			array(
				'key'       => 'field_dsg543ejh98er',
				'name'      => self::CUSTOM_JS,
				'label'     => __('Custom global JS', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'type'      => 'acf_code_field',
				'wrapper'   => array('width' => '50%'),
				'mode'      => 'javascript',
				'rows'      => 15
			)
		);
	}

	/**
	 * @since   1.0.0
	 * @version 1.2.4
	 *
	 * @see     DWS_Root::enqueue_assets()
	 */
	public function enqueue_assets() {
		wp_enqueue_script(self::get_asset_handle(), self::get_assets_base_path(true) . 'scripts.js', array('jquery'), self::get_plugin_version(), true);
		wp_enqueue_script(DWS_Admin::get_asset_handle('public'), DWS_Admin::get_assets_base_path(true) . 'scripts.js', array('jquery'), self::get_plugin_version(), true);
		wp_enqueue_style(self::get_asset_handle(), self::get_assets_base_path(true) . 'style.css', array(), self::get_plugin_version(), 'all');

		wp_enqueue_script(self::get_asset_handle('collapsible-content'), self::get_assets_base_path(true) . 'collapsible-content.js', array('jquery'), self::get_plugin_version(), true);
		wp_enqueue_style(self::get_asset_handle('collapsible-content'), self::get_assets_base_path(true) . 'collapsible-content.css', array(), self::get_plugin_version(), 'all');

		wp_add_inline_style(self::get_asset_handle(), get_field(self::CUSTOM_CSS, 'option'));
		wp_add_inline_script(self::get_asset_handle(), get_field(self::CUSTOM_JS, 'option'));
	}

	/**
	 * Outputs a javascript object in the <head></head> which can be used for frontend functionality support.
	 *
	 * @author  Dushan Terzikj  <d.terzikj@deep-web-solutions.de>
	 *
	 * @since   1.3.4
	 * @version 1.0.0
	 */
	function add_frontend_support(){
		$ajax_url = admin_url('admin-ajax.php');
		?>
		<script type="text/javascript">
            /* <![CDATA[ */
            var dws_frontend_parameters = {
                "ajax_url": "<?php echo $ajax_url; ?>"
            };
            /* ]]> */
		</script>
		<?php
	}

	/**
	 * Outputs a javascript object in the <head></head> which can be used for backend functionality support.
	 *
	 * @author  Dushan Terzikj  <d.terzikj@deep-web-solutions.de>
	 *
	 * @since   1.3.4
	 * @version 1.0.0
	 */
	function add_backend_support(){
		$ajax_url = admin_url('admin-ajax.php');
		?>
		<script type="text/javascript">
            /* <![CDATA[ */
            var dws_backend_parameters = {
                "ajax_url": "<?php echo $ajax_url; ?>"
            };
            /* ]]> */
		</script>
		<?php
	}

	//endregion
}