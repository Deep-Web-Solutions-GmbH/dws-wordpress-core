<?php

namespace Deep_Web_Solutions\Front;
use Deep_Web_Solutions\Admin\DWS_Admin;
use Deep_Web_Solutions\Core\DWS_Root;

if (!defined('ABSPATH')) { exit; }

/**
 * Orchestrates the DWS Core extensions of the front-end area of the website.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Root
 */
final class DWS_Public extends DWS_Root {
	//region INHERITED FUNCTIONS

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Root::load_dependencies()
	 */
	protected function load_dependencies() {
		/** This class creates automatically a prices list for the website based on all the products and their possible variations. */
		require_once(self::get_includes_base_path() . 'class-prices-list.php');

		/** This class creates handy shortcodes for customer service related activities. */
		require_once(self::get_includes_base_path() . 'class-customer-service.php');

		/** This class provides a handy way to get links for the customer to send stuff to the shop, for example in warranty cases or when performing a service. */
		require_once(self::get_includes_base_path() . 'customer-shipping/class-customer-shipping.php');

		/** Handles the output of CSS and settings to enclose text in a circle. */
		require_once(self::get_includes_base_path() . 'class-circled-content.php');

		/** Loads style for fancy-looking front-end messages. */
		require_once(self::get_includes_base_path() . 'class-fancy-messages.php');
	}

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Root::enqueue_assets()
	 */
	public function enqueue_assets() {
		wp_enqueue_script( self::get_asset_handle(), self::get_assets_base_path(true) . 'scripts.js', array('jquery'), self::get_plugin_version(), true );
		wp_enqueue_script( DWS_Admin::get_asset_handle('public'), DWS_Admin::get_assets_base_path(true) . 'scripts.js', array('jquery'), self::get_plugin_version(), true );
		wp_enqueue_style( self::get_asset_handle(), self::get_assets_base_path(true) . 'style.css', array(), self::get_plugin_version(), 'all' );

		wp_enqueue_script( self::get_asset_handle('collapsible-content'), self::get_assets_base_path(true) . 'collapsible-content.js', array('jquery'), self::get_plugin_version(), true);
		wp_enqueue_style( self::get_asset_handle('collapsible-content'), self::get_assets_base_path(true) . 'collapsible-content.css', array(), self::get_plugin_version(), 'all');
	}

	//endregion
}