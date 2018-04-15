<?php

namespace Deep_Web_Solutions\Front;
use Deep_Web_Solutions\Core\DWS_Functionality_Template;
use Deep_Web_Solutions\Core\DWS_Helper;

if (!defined('ABSPATH')) { exit; }

/**
 * Manages the storage and retrieval of proper links for the customers to send their items in, e.g. in warranty cases etc.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Functionality_Template
 */
final class DWS_CustomerShipping extends DWS_Functionality_Template {
	//region FIELDS AND CONSTANTS

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string  CUSTOMER_SHIPPING_OPTIONS   The name of the ACF options field that holds the shipping options.
	 */
	const CUSTOMER_SHIPPING_OPTIONS = 'dws_customer-shipping_shipping-options';

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string  CUSTOMER_SHIPPING_PRODUCT_CATEGORIES    The name of the ACF options field that stores the product
	 *                                                          categories that require customer shipping for processing.
	 */
	const CUSTOMER_SHIPPING_PRODUCT_CATEGORIES = 'dws_customer-shipping_product-categories';

	//endregion

	//region INHERITED FUNCTIONS

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Functionality_Template::are_prerequisites_fulfilled()
	 *
	 * @return  bool
	 */
	protected static function are_prerequisites_fulfilled() {
		return is_plugin_active('woocommerce/woocommerce.php');
	}

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Functionality_Template::functionality_options()
	 *
	 * @return  array
	 */
	protected function functionality_options() {
		return array(
			array(
				'key'           => 'field_hf87whf81f1',
				'name'          => self::CUSTOMER_SHIPPING_OPTIONS,
				'label'         => __('Customer shipping services', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'instructions'  => __('Fill out your personalized links for the supported shipping methods. Shortcodes are available.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'type'          => 'group',
				'sub_fields'    => array( /** this will be populated by the extensions; @see DWS_CustomerShippingMethod_Template */ ),
				'layout'        => 'row'
			),
			array(
				'key'           => 'field_jgsghe87hgie',
				'name'          => self::CUSTOMER_SHIPPING_PRODUCT_CATEGORIES,
				'label'         => __('Categories of products which require customer shipping', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'type'          => 'taxonomy',
				'taxonomy'      => 'product_cat',
				'field_type'    => 'multi_select',
				'return_format' => 'object'
			)
		);
	}

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Functionality_Template::load_dependencies()
	 */
	protected function load_dependencies() {
		DWS_Helper::load_files(self::get_includes_base_path());
	}

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Functionality_Template::get_hook_name()
	 *
	 * @param   string  $name
	 * @param   array   $extra
	 * @param   string  $root
	 *
	 * @return  string
	 */
	public static function get_hook_name( $name, $extra = array(), $root = 'customer-shipping' ) {
		return parent::get_hook_name( $name, $extra, $root );
	}

	//endregion

	//region HELPERS

	/**
	 * Based on the options configured in the backend and the order passed, returns the first matching
	 * registered customer shipping link.
	 *
	 * @param   \WC_Order   $order  The order for which the returned link is.
	 *
	 * @return  string  The first matching customer shipping link.
	 */
	public static function get_first_matching_shipping_link($order) {
		$GLOBALS['wc_order'] = $order;
		return apply_filters(self::get_hook_name('first-matching-link'), '', $order);
	}

	//endregion
} DWS_CustomerShipping::maybe_initialize_singleton('ehg82gh2g32');