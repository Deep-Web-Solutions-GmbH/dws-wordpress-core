<?php

namespace Deep_Web_Solutions\Front\Customer_Shipping;
use Deep_Web_Solutions\Front\DWS_CustomerShipping;

if (!defined('ABSPATH')) { exit; }

/**
 * Integrates the DHL Paket customer shipping method.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_CustomerShippingMethod_Template
 * @see     DWS_CustomerShipping
 */
final class DHL_Paket extends DWS_CustomerShippingMethod_Template {
	/**
	 * Returns a link that will also auto-fill the form for the customer.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $link   The base link set in the admin area.
	 *
	 * @return  string  Enhanced link which auto-fills the fields based on the current global order, if set, or base field by default.
	 */
	public function filter_link_shortcode($link) {
		$wc_order = self::get_global_order();

		return empty($wc_order) ? esc_url($link) : add_query_arg(array(
			'ADDR_SEND_EMAIL'       => rawurlencode(iconv('UTF8', 'ISO-8859-2', $wc_order->get_billing_email())),
			'ADDR_SEND_FIRST_NAME'  => rawurlencode(iconv('UTF8', 'ISO-8859-2', $wc_order->get_shipping_first_name())),
			'ADDR_SEND_LAST_NAME'   => rawurlencode(iconv('UTF8', 'ISO-8859-2', $wc_order->get_shipping_last_name())),
			'ADDR_SEND_STREET'      => rawurlencode(iconv('UTF8', 'ISO-8859-2', $wc_order->get_shipping_address_1())),
			'ADDR_SEND_ZIP'         => rawurlencode(iconv('UTF8', 'ISO-8859-2', $wc_order->get_shipping_postcode())),
			'ADDR_SEND_CITY'        => rawurlencode(iconv('UTF8', 'ISO-8859-2', $wc_order->get_shipping_city())),
			'ADDR_SEND_STREET_ADD'  => rawurlencode(iconv('UTF8', 'ISO-8859-2', $wc_order->get_order_number()))
		), $link);
	}
} DHL_Paket::maybe_initialize_singleton('ah8g2hg4g4', 'DHL Paket');