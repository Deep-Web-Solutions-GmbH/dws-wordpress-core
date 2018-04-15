<?php

namespace Deep_Web_Solutions\Front\Customer_Shipping;
use Deep_Web_Solutions\Core\DWS_Functionality_Template;
use Deep_Web_Solutions\Front\DWS_CustomerShipping;
use Deep_Web_Solutions\Plugins\ACOF\WC_OrderType;
use Deep_Web_Solutions\Plugins\WooCommerce\DWS_WC_Helper;
use Deep_Web_Solutions\Plugins\WooCommerce\WC_OrderStatus;

if (!defined('ABSPATH')) { exit; }

/**
 * Provides all the piping required for registering and using a customer shipping method.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Functionality_Template
 * @see     DWS_CustomerShipping
 */
abstract class DWS_CustomerShippingMethod_Template extends DWS_Functionality_Template {
	//region FIELDS AND CONSTANTS

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string  OPTIONS_NAME_PREFIX     The prefix of the ACF options group field that holds all the settings for
	 *                                          the current customer shipping method.
	 */
	const OPTIONS_NAME_PREFIX = 'customer_shipping_options_';

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  private
	 * @var     string   $link_shortcode    The shortcode to use for retrieving the link of the current customer shipping method.
	 */
	private $link_shortcode;

	//endregion

	//region MAGIC METHODS

	/**
	 * DWS_CustomerShippingMethod_Template constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Functionality_Template::__construct()
	 *
	 * @param   string      $functionality_id
	 * @param   string|bool $functionality_description
	 * @param   string|bool $functionality_name
	 */
	public function __construct( $functionality_id, $functionality_name = false, $functionality_description = false ) {
		parent::__construct( $functionality_id, true, DWS_CustomerShipping::get_root_id(), 'field_hf87whf81f1', $functionality_description, $functionality_name );
	}

	//endregion

	//region INHERITED FUNCTIONS

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Functionality_Template::local_configure()
	 */
	protected function local_configure() {
		parent::local_configure();
		$this->link_shortcode = str_replace(' ', '_', strtolower(self::get_root_public_name())) . '_link';
	}

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Functionality_Template::define_functionality_hooks()
	 *
	 * @param   \Deep_Web_Solutions\Core\DWS_WordPress_Loader   $loader
	 */
	final protected function define_functionality_hooks( $loader ) {
		$loader->add_filter(self::get_hook_name('customer-link', self::get_root_id()), $this, 'filter_link_shortcode');
		$loader->add_filter(DWS_CustomerShipping::get_hook_name('first-matching-link'), $this, 'maybe_return_shipping_link', 10, 2);

		$loader->add_action('woocommerce_my_account_my_orders_actions', $this, 'maybe_add_shipping_link', 10, 2);
	}

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Functionality_Template::define_shortcodes()
	 *
	 * @param   \Deep_Web_Solutions\Core\DWS_WordPress_Loader   $loader
	 */
	final protected function define_shortcodes($loader) {
		$loader->add_shortcode($this->link_shortcode, $this, 'get_full_link');
	}

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Functionality_Template::functionality_options()
	 *
	 * @return  array
	 */
	final protected function functionality_options() {
		return array(
			array(
				'key'           => 'field_gh48hg82g2_' . self::get_root_id(),
				'name'          => self::OPTIONS_NAME_PREFIX . self::get_root_id(),
				'label'         => self::get_root_public_name(),
				'type'          => 'group',
				'sub_fields'    => array(
					array(
						'key'           => 'field_dg3871gfisdgq_' . self::get_root_id(),
						'name'          => 'link',
						'label'         => sprintf(__('Link for %s', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN), self::get_root_public_name()),
						'instructions'  => sprintf(__('This link can be used together with the shortcode [%s].', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN), $this->link_shortcode),
						'type'          => 'text'
					),
					array(
						'key'       => 'field_gj4hr789gh4287g24_' . self::get_root_id(),
						'name'      => 'location',
						'label'     => __('Used in which country/countries?', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
						'type'      => 'select',
						'choices'   => WC()->countries->get_allowed_countries(),
						'multiple'  => 1,
						'ui'    => 1
					)
				),
				'layout'        => 'table'
			)
		);
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
	final public static function get_hook_name( $name, $extra = array(), $root = 'customer-shipping-method' ) {
		return parent::get_hook_name( $name, $extra, $root );
	}

	//endregion

	//region COMPATIBILITY LOGIC

	/**
	 * If an order contains a product which requires customer shipping, adds a button in the customer's account area
	 * which redirects to the shipping link.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array       $actions    The order actions registered with WC so far.
	 * @param   \WC_Order   $order      The order for which the actions are being registered.
	 *
	 * @return  array   The order actions registered so far and maybe including an action for the customer shipping link.
	 */
	final public function maybe_add_shipping_link($actions, $order) {
		$valid_order_status = apply_filters(self::get_hook_name('valid-order-status'), WC_OrderStatus::$ORDER_DONE);
		if ( $order->has_status($valid_order_status) || WC_OrderType::get($order->get_id()) !== WC_OrderType::ORIGINAL) { return $actions; }

		$valid_categories = get_field(DWS_CustomerShipping::CUSTOMER_SHIPPING_PRODUCT_CATEGORIES, 'option');
		$valid_categories = apply_filters(self::get_hook_name('valid-product-categories'), $valid_categories);

		$should_display_action = false;

		/** @var    \WC_Order_Item_Product  $line_item */
		foreach ($order->get_items() as $line_item) {
			/** @var    \WP_Term    $category */
			foreach ($valid_categories as $category) {
				$product_id = DWS_WC_Helper::product_get_parent_id($line_item->get_product());
				if (DWS_WC_Helper::product_has_category($product_id, $category->slug)) { $should_display_action = true; break 2; }
			}
		}

		if (apply_filters(self::get_hook_name('order-needs-customer-shipping'), !$should_display_action, $order)) { return $actions; }

		// now check if this shipping method is available for this order
		$options = self::get_shipping_method_options();
		if (!is_array($options['location']) || !in_array($order->get_billing_country(), $options['location'])) {
			return $actions;
		}

		// all good, add the action
		return array_merge($actions, array(array(
			'url' => $this->get_full_link(array('order' => $order)),
			'name' => sprintf(__('Send package with %s', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN), self::get_root_public_name())
		)));
	}

	/**
	 * Given a WC order, returns the first matching customer shipping link based on the admin settings.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $link       The link returned by the filter so far.
	 * @param   \WC_Order   $order      The order for which the shipping link should be returned.
	 *
	 * @return  string  The first matching customer shipping link for the given order.
	 */
	public function maybe_return_shipping_link($link, $order) {
		// check if there already is a better match
		if ($link !== '') { return $link; }

		// now check if this shipping method is available for this order
		$options = self::get_shipping_method_options();
		if (!is_array($options['location']) || !in_array($order->get_billing_country(), $options['location'])) {
			return $link;
		}

		return $this->get_full_link();
	}

	/**
	 * Children classes are free to overwrite this to provide their own enhanced link.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $link   The base version of the customer shipping link.
	 *
	 * @return  string  The final version of the customer shipping link.
	 */
	public function filter_link_shortcode($link) {
		return $link;
	}

	//endregion

	//region SHORTCODES

	/**
	 * Outputs the base link, and allows child classes to filter that.
	 *
	 * @param   array   $atts   The shortcode attributes.
	 *
	 * @return  string
	 */
	final public function get_full_link($atts = array()) {
		$atts = shortcode_atts(array(), $atts);
		if (isset($atts['order_id'])) { $GLOBALS['wc_order_id'] = $atts['order_id']; }
		if (isset($atts['order'])) { $GLOBALS['wc_order'] = $atts['order']; }

		return apply_filters(self::get_hook_name('customer-link', self::get_root_id()), $this->get_base_link());
	}

	//endregion

	//region HELPERS

	/**
	 * Gets the options defined in the admin area for the current customer shipping method.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array   The options defined in the admin area for the current customer shipping method.
	 */
	final private function get_shipping_method_options() {
		$all_options = get_field(DWS_CustomerShipping::CUSTOMER_SHIPPING_OPTIONS, 'option');
		return $all_options[self::OPTIONS_NAME_PREFIX . self::get_root_id()];
	}

	/**
	 * Gets the base link defined in the admin area for the current customer shipping method.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string  The base link defined in the admin area for the current customer shipping method.
	 */
	final private function get_base_link() {
		$options = self::get_shipping_method_options();
		return isset($options['link']) ? esc_url($options['link']) : '';
	}

	/**
	 * Checks the global variable space for a reference to the current order.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  null|\WC_Order      If a WC order is in the global space, the order object, otherwise null.
	 */
	final protected function get_global_order() {
		global $wc_order; global $wc_order_id;
		if (empty($wc_order) && is_numeric($wc_order_id)) {
			$wc_order = DWS_WC_Helper::get_order($wc_order_id);
		}

		return (empty($wc_order) || !($wc_order instanceof \WC_Order)) ? null : $wc_order;
	}

	//endregion
}