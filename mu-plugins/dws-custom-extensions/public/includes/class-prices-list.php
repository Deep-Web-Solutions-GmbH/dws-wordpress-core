<?php

namespace Deep_Web_Solutions\Front;
use Deep_Web_Solutions\Core\DWS_Functionality_Template;
use Deep_Web_Solutions\Core\DWS_Helper;
use Deep_Web_Solutions\Plugins\WooCommerce\DWS_WC_Helper;

if (!defined('ABSPATH')) { exit; }

/**
 * Provides an automatically compiled prices list.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Functionality_Template
 */
final class DWS_PricesList extends DWS_Functionality_Template {
	//region FIELDS AND CONSTANTS

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string  PRICES_PAGE     The name of the ACF options field that holds the ID of the page which will act
	 *                                  as the website's prices page.
	 */
	const PRICES_PAGE = 'dws_prices-page';

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string  PRODUCTS_TO_EXCLUDE     The name of the ACF options field that holds the products that should be
	 *                                          excluded from the price list.
	 */
	const PRODUCTS_TO_EXCLUDE = 'dws_prices-page_products-to-exclude';
	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string  CATEGORIES_TO_EXCLUDE   The name of the ACF options field that holds the product categories that
	 *                                          should be excluded from the prices list.
	 */
	const CATEGORIES_TO_EXCLUDE = 'dws_prices-page_categories-to-exclude';

	//endregion

	//region INHERITED FUNCTIONS

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Functionality_Template::define_functionality_hooks()
	 *
	 * @param   \Deep_Web_Solutions\Core\DWS_WordPress_Loader   $loader
	 */
	protected function define_functionality_hooks( $loader ) {
		$loader->add_filter('the_content', $this, 'auto_shortcode', PHP_INT_MIN);
	}

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Functionality_Template::define_shortcodes()
	 *
	 * @param   \Deep_Web_Solutions\Core\DWS_WordPress_Loader   $loader
	 */
	protected function define_shortcodes( $loader ) {
		$loader->add_shortcode('dws_prices', $this, 'output_shop_prices');
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
				'key'               => 'field_hg78egfhawfwafaw',
				'name'              => self::PRICES_PAGE,
				'label'             => __('Prices page', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'instructions'      => __('This is the page that will contain all the standard product prices.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'type'              => 'post_object',
				'required'          => 1,
				'allow_archives'    => 0,
				'post_type'         => 'page',
				'return_format'     => 'id',
				'wrapper'           => array( 'width' => '33%' )
			),
			array(
				'key'           => 'field_djsgh4gheigheriuer',
				'name'          => self::CATEGORIES_TO_EXCLUDE,
				'label'         => __('Categories to exclude from the prices list', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'type'          => 'taxonomy',
				'taxonomy'      => 'product_cat',
				'return_format' => 'id',
				'field_type'    => 'multi_select',
				'add_term'      => 0,
				'multiple'      => 1,
				'allow_null'    => 1,
				'wrapper'       => array('width' => '33%')
			),
			array(
				'key'           => 'field_h8hsv87shg4g4gh4h4rhff',
				'name'          => self::PRODUCTS_TO_EXCLUDE,
				'label'         => __('Products to exclude from the prices list', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'type'          => 'post_object',
				'post_type'     => 'product',
				'return_format' => 'id',
				'multiple'      => true,
				'wrapper'       => array( 'width' => '33%' )
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
	public static function get_hook_name( $name, $extra = array(), $root = 'prices-list' ) {
		return parent::get_hook_name( $name, $extra, $root );
	}

	//endregion

	//region COMPATIBILITY LOGIC

	/**
	 * Make sure that the prices page has the
	 * appropriate shortcode, or adds it automatically.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $content    The content of the page currently being rendered.
	 *
	 * @return  string  The content of the page currently being rendered, guaranteed to have the appropriate prices list
	 *                  shortcode if the page is the prices page.
	 */
	public function auto_shortcode($content) {
		if (is_page(get_field(self::PRICES_PAGE, 'option'))) {
			if ( ! preg_match( '/\[dws_prices\s?(.*)\]/', $content ) ) {
				$content .= '[dws_prices]';
			}
		}

		return $content;
	}

	//endregion

	//region SHORTCODES

	/**
	 * Returns the HTML of an automatic prices list.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string  The HTML content of the prices page.
	 */
	public function output_shop_prices() {
		remove_all_filters('woocommerce_hide_invisible_variations');

		// we should display products by category
		$product_categories = get_categories(
			array(
				'taxonomy'      => 'product_cat',
				'hide_empty'    => 1,
				'post_type'     => 'product',
				'exclude'       =>  get_field(self::CATEGORIES_TO_EXCLUDE, 'option')
			)
		);

		$product_categories = array_values($product_categories); // this is required because otherwise the keys are not continuous necessarily
		$data = array_fill(0, count($product_categories), array());

		// get all the products and figure out in which category they belong to
		$loop = new \WP_Query( array(
			'post_type' => 'product',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'fields' => 'ids',
			'orderby' => 'menu_order', // support for ICPO
			'order' => 'ASC',
			'post__not_in' => array_merge(get_field(self::PRODUCTS_TO_EXCLUDE, 'option'), apply_filters(self::get_hook_name('exclude-products'), array())) // let 3rd parties filter out stuff
		) );
		$product_ids = $loop->get_posts();
		foreach ($product_ids as $product_id) {
			foreach ($data as $index => $products) {
				if (DWS_WC_Helper::product_has_category($product_id, $product_categories[$index]->slug)) {
					$data[$index][] = $product_id;
				}
			}
		}

		// every product should only belong to one category, so simplify things if necessary
		foreach ($data as $index_1 => &$products_1) {
			foreach ($data as $index_2 => &$products_2) {
				$overlapping_products = array_intersect($products_1, $products_2);
				if ($product_categories[$index_1]->category_parent === $product_categories[$index_2]->term_id) {
					//remove overlapping products from category 2
					foreach ($overlapping_products as $product) {
						DWS_Helper::unset_array_element_by_value($products_2, $product);
					}
				} elseif ($product_categories[$index_2]->category_parent === $product_categories[$index_1]->term_id) {
					//remove overlapping products from category 1
					foreach ($overlapping_products as $product) {
						DWS_Helper::unset_array_element_by_value($products_1, $product);
					}
				}
			}
		}

		// time to display every category
		ob_start();
		foreach ($data as $index => $products) {
			// now the real "fun" begins ...
			$has_simple_product = false;
			$relevant_attributes = array();
			$products = apply_filters(self::get_hook_name('add-category-product-objects'), array_map(function($product_id) {
				return wc_get_product($product_id);
			}, $products), $product_categories[$index]);

			if (empty($products)) {
				continue; // skip empty categories
			}

			foreach ($products as $product) {
				if (!($product instanceof \WC_Product_Variable)) {
					$has_simple_product = true;
				} else {
					$available_variations = $product->get_available_variations();
					foreach ($available_variations as $variation) {
						foreach ($variation['attributes'] as $attribute_key => $attribute) {
							if (empty($attribute)) { continue; }
							$relevant_attributes[] = str_replace('attribute_', '', $attribute_key);
						}
					}
				}
			}

			$relevant_attributes = array_unique($relevant_attributes);
			if (count($relevant_attributes) > 1) {
				error_log('Too many relevant attributes for category ' . $product_categories[$index]->name . ': ' . serialize($relevant_attributes));
				continue;
			}

			$header = array_filter(array(
				__('Product', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				$has_simple_product ? __('Price', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN) : null
			));

			$header_extra_slugs = array();
			if (count($relevant_attributes) > 0) {
				foreach (get_terms($relevant_attributes[0]) as $term) {
					$header[] = $term->name;
					$header_extra_slugs[] = $term->slug;
				}
			}

			$body = array();
			/** @var $product \WC_Product_Simple|\WC_Product_Variable|\stdClass */
			foreach ($products as $product) {
				$line = ($product instanceof \WC_Product) ? array($product->get_name()) : array();
				if ($product instanceof \WC_Product_Simple) {
					$line[] = wc_price($product->get_price());
					foreach ($header_extra_slugs as $extra_slug) {
						$line[] = '--';
					}
				} else if (!($product instanceof \WC_Product_Variable)) {
					$line[] = $product->name;
					$line[] = wc_price($product->price);
				} else {
					if ($has_simple_product) { $line[] = '--'; }

					$available_variations = $product->get_available_variations();
					foreach ($header_extra_slugs as $extra_slug) {
						$price = false;
						foreach ($available_variations as $variation) {
							$attribute_value = $variation['attributes']['attribute_' . $relevant_attributes[0]];
							if ($attribute_value === $extra_slug) {
								$price = $variation['display_price'];
								break;
							}
						}

						$line[] = ($price === false) ? '--' : wc_price($price);
					}
				}

				$body[] = $line;
			}

			// time to output the results
			echo '<h3 class="dws_text-center" style="margin-top: 25px; margin-bottom: 15px;">' . $product_categories[$index]->name .  '</h3>';
			extract(array('header' => $header, 'body' => $body));
			include DWS_Public::get_templates_base_path() . 'price-table.php';
		}

		// let 3-rd parties add their own tables, if they want to
		$extra_tables = apply_filters(self::get_hook_name('extra-tables'), array());
		foreach ($extra_tables as $extra_table) {
			echo '<h3 class="dws_text-center" style="margin-top: 25px; margin-bottom: 15px;">' . $extra_table['name'] .  '</h3>';
			extract(array('header' => $extra_table['header'], 'body' => $extra_table['body']));
			include DWS_Public::get_templates_base_path() . 'price-table.php';
		}

		// let 3-rd parties add their own prices to a special list
		$further_items = apply_filters(self::get_hook_name('extra-products'), array());
		if (!empty($further_items)) {
			echo '<h3 class="dws_text-center" style="margin-top: 25px; margin-bottom: 15px;">' . __('Further offers', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN) .  '</h3>';
			extract(array('header' => array(__('Product', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN), __('Price', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN)), 'body' => $further_items));
			include DWS_Public::get_templates_base_path() . 'price-table.php';
		}

		return ob_get_clean();
	}

	//endregion

	//region HELPERS

	/**
	 * Allows 3-rd parties which want to add products
	 * to a certain category which do not exist in the form
	 * of WC_Products to add them in a standardized fashion.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $name
	 * @param   float   $price
	 *
	 * @return  object
	 */
	public static function get_extra_product_object($name, $price) {
		return (object) [ 'name' => $name, 'price' => $price ];
	}

	//endregion
} DWS_PricesList::maybe_initialize_singleton('h78438743f3f34f');