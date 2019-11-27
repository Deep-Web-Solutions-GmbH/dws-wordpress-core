<?php

namespace Deep_Web_Solutions\Front;
use Deep_Web_Solutions\Admin\Settings\DWS_Settings_Pages;
use Deep_Web_Solutions\Base\DWS_Functionality_Template;

if (!defined('ABSPATH')) { exit; }

/**
 * Provides one centralized place to define customer service options.
 *
 * @since   1.0.0
 * @version 2.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Functionality_Template
 */
final class DWS_CustomerService extends DWS_Functionality_Template {
	//region FIELDS AND CONSTANTS

	/**
	 * @since       1.3.3
	 * @version     2.0.0
	 *
	 * @var     string  SHOW_CUSTOMER_EMAIL     The id of the option field which indicates whether there exists
	 *                                          a customer email address.
	 */
	private const SHOW_CUSTOMER_EMAIL = 'field_fjak3jfh80hf3h0';
	/**
	 * @since       1.0.0
	 * @version     2.0.0
	 *
	 * @var     string  CUSTOMER_EMAIL  The id of the options field that holds the email address that customers
	 *                                  should send emails to.
	 */
    private const CUSTOMER_EMAIL = 'field_hg8e7hg8e47ghes';
	/**
	 * @since       1.3.3
	 * @version     2.0.0
	 *
	 * @var     string  SHOW_HOTLINE_PHONE_NUMBER   The id of the option field which indicates whether there exists
	 *                                              a hotline phone number.
	 */
    private const SHOW_HOTLINE_PHONE_NUMBER = 'field_y9vt30y24y02vt09';
	/**
	 * @since       1.0.0
	 * @version     2.0.0
	 *
	 * @var     string  HOTLINE_PHONE_NUMBER    The id of the options field that holds the phone number that
	 *                                          customers should reach out to.
	 */
    private const HOTLINE_PHONE_NUMBER = 'field_595a7fcb23b8a';
	/**
	 * @since       1.3.3
	 * @version     2.0.0
	 *
	 * @var     string  SHOW_HOTLINE_AVAILABILITY   The id of the option field which indicates whether there exists
	 *                                              a hotline availability.
	 */
    private const SHOW_HOTLINE_AVAILABILITY = 'field_wpvb4fwp3wvw3wp3';
	/**
	 * @since       1.0.0
	 * @version     2.0.0
	 *
	 * @var     string HOTLINE_AVAILABILITY     The id of the options field that holds the availability of the
	 *                                          phone number that customers should reach out to.
	 */
    private const HOTLINE_AVAILABILITY = 'field_595a80ac23b8d';

	//endregion

	//region INHERITED FUNCTIONS

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_CustomerService::define_shortcodes()
	 *
	 * @param   \Deep_Web_Solutions\Core\DWS_Loader   $loader
	 */
	protected function define_shortcodes($loader) {
		$loader->add_shortcode('dws_customer_email', $this, 'get_customer_email');
		$loader->add_shortcode('dws_hotline_number', $this, 'get_hotline_number');
		$loader->add_shortcode('dws_hotline_availability', $this, 'render_opening_hours');
	}

	/**
	 * @since   1.0.0
	 * @version 1.3.3
	 *
	 * @see     DWS_Functionality_Template::functionality_options()
	 *
	 * @return  array
	 */
	protected function functionality_options() {
		return array(
			array(
				'key'                   => self::SHOW_CUSTOMER_EMAIL,
				'name'                  => 'dws_customer-service_show-customer-email',
				'label'                 => __('Do you have customer email?', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'instructions'          => __('In case you have an email for the customers to write to, activate this option.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'type'                  => 'true_false',
				'ui'                    => 1
			),
			array(
				'key'          => self::CUSTOMER_EMAIL,
				'name'         => 'dws_customer-service_customer-email',
				'label'        => __('Customer email', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'instructions' => __('This email will appear on the website for the customers to write to.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'type'         => 'text',
				'required'     => 1,
				'conditional_logic'     => array(
					array(
						array(
							'field'     => self::SHOW_CUSTOMER_EMAIL,
							'operator'  => '==',
							'value'     => '1'
						)
					)
				)
			),
			array(
				'key'                   => self::SHOW_HOTLINE_PHONE_NUMBER,
				'name'                  => 'dws_customer-service_show-hotline-phone-number',
				'label'                 => __('Do you have hotline phone number', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'instructions'          => __('In case you have a hotline phone number for the customers to call you on, activate this option.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'type'                  => 'true_false',
				'ui'                    => 1
			),
			array(
				'key'          => self::HOTLINE_PHONE_NUMBER,
				'name'         => 'dws_customer-service_hotline-phone-number',
				'label'        => __('Hotline phone number', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'instructions' => __('This phone number will appear on the website for the customers to call.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'type'         => 'text',
				'required'     => 1,
				'conditional_logic'     => array(
					array(
						array(
							'field'     => self::SHOW_HOTLINE_PHONE_NUMBER,
							'operator'  => '==',
							'value'     => '1'
						)
					)
				)
			),
			array(
				'key'                   => self::SHOW_HOTLINE_AVAILABILITY,
				'name'                  => 'dws_customer-service_show-hotline-availability',
				'label'                 => __('Do you want to add hotline availability?', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'instructions'          => __('In case you want to indicate your hotline availability, activate this option.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'type'                  => 'true_false',
				'ui'                    => 1
			),
			array(
				'key'          => self::HOTLINE_AVAILABILITY,
				'name'         => 'dws_customer-service_hotline-availability',
				'label'        => __('Hotline availability', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'instructions' => __('Select the times when the hotline is available.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'type'         => 'repeater',
				'required'     => 1,
				'min'          => 1,
				'max'          => 7,
				'layout'       => 'table',
				'sub_fields'   => array(
					array(
						'key'           => 'field_595a80bc23b8e',
						'name'          => 'day_of_the_week',
						'label'         => __('Day of the week', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
						'instructions'  => __('Select the day(s) for which the times should apply.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
						'type'          => 'select',
						'required'      => 1,
						'choices'       => array(
							'1' => __('Monday', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
							'2' => __('Tuesday', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
							'3' => __('Wednesday', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
							'4' => __('Thursday', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
							'5' => __('Friday', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
							'6' => __('Saturday', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
							'7' => __('Sunday', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
						),
						'multiple'      => 1,
						'return_format' => 'array',
					),
					array(
						'key'            => 'field_595a819223b8f',
						'name'           => 'from',
						'label'          => __('From', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
						'type'           => 'time_picker',
						'display_format' => 'H:i',
						'return_format'  => 'H:i',
						'required'       => 1
					),
					array(
						'key'            => 'field_595a81ae23b91',
						'name'           => 'to',
						'label'          => __('To', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
						'type'           => 'time_picker',
						'required'       => 1,
						'display_format' => 'H:i',
						'return_format'  => 'H:i',
					)
				),
				'conditional_logic'     => array(
					array(
						array(
							'field'     => self::SHOW_HOTLINE_AVAILABILITY,
							'operator'  => '==',
							'value'     => '1'
						)
					)
				)
			)
		);
	}

	//endregion

	//region SHORTCODES

	/**
	 * Returns the email address saved as the customer email address.
	 *
	 * @since   1.0.0
	 * @version 2.0.0
	 *
	 * @param   array   $atts   The shortcode options.
	 *
	 * @return  string  The email address for customers.
	 */
	public function get_customer_email($atts = array()) {
		if (!DWS_Settings_Pages::get_field(self::SHOW_CUSTOMER_EMAIL, self::get_settings_page_slug())){
			error_log('Shortcode [dws_customer_email] is used on object ID ' . get_queried_object_id() . ' even though functionality is turned off! Please turn on the functionality first.');
			return '[dws_customer_email]';
		}

		$atts = shortcode_atts(
			array(
				'class'        => '',
				'style'        => '',
				'track_cat'    => __('Contact', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'track_action' => __('Email', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'track_label'  => '',
				'pre_text'     => '',
				'just_text'    => false
			), $atts
		);

		$address = DWS_Settings_Pages::get_field(self::CUSTOMER_EMAIL, self::get_settings_page_slug());
		return $atts['just_text']
			? $address
			: "<a 	href='mailto:$address' 
					onclick='if(typeof(ga) !== \"undefined\") { ga(\"send\", \"event\", \"{$atts['track_cat']}}\", \"{$atts['track_action']}\", \"{$atts['track_label']}\"); }'
					class='{$atts['class']}' style='{$atts['style']}'>{$atts['pre_text']} <span class='dws_email_address'>$address</span></a>";
	}

	/**
	 * Returns the phone number saved as the hotline number.
	 *
	 * @since   1.0.0
	 * @version 2.0.0
	 *
	 * @param   array   $atts   The shortcode options.
	 *
	 * @return  string  The phone number for customers.
	 */
	public function get_hotline_number($atts = array()) {
		if (!DWS_Settings_Pages::get_field(self::SHOW_HOTLINE_PHONE_NUMBER, self::get_settings_page_slug())){
			error_log('Shortcode [dws_hotline_number] is used on object ID ' . get_queried_object_id() . ' even though functionality is turned off! Please turn on the functionality first.');
			return '[dws_hotline_number]';
		}

		$atts = shortcode_atts(
			array(
				'class'        => '',
				'style'        => '',
				'track_cat'    => __('Contact', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'track_action' => __('Phone number', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'track_label'  => '',
				'pre_text'     => '',
				'just_text'    => false
			), $atts
		);

		$number = DWS_Settings_Pages::get_field(self::HOTLINE_PHONE_NUMBER, self::get_settings_page_slug());
		return $atts['just_text']
			? $number
			: "<a 	href='tel:$number' 
                    onclick='if(typeof(ga) !== \"undefined\") { ga(\"send\", \"event\", \"{$atts['track_cat']}\", \"{$atts['track_action']}\", \"{$atts['track_label']}\"); }' 
                    class='{$atts['class']}' style='{$atts['style']}'>{$atts['pre_text']} <span class='dws_hotline_number'>$number</span></a>";
	}

	/**
	 * Takes the settings saved for hotline availability
	 * and displays them nicely in a table.
	 *
	 * @since   1.0.0
	 * @version 2.0.0
	 *
	 * @param   array   $atts   The shortcode options.
	 *
	 * @return  string  The phone number availability in HTML format.
	 */
	public function render_opening_hours($atts = array()) {
		if (!DWS_Settings_Pages::get_field(self::SHOW_HOTLINE_AVAILABILITY, self::get_settings_page_slug())){
			error_log('Shortcode [dws_hotline_availability] is used on object ID ' . get_queried_object_id() . ' even though functionality is turned off! Please turn on the functionality first.');
			return '[dws_hotline_availability]';
		}

		$atts = shortcode_atts(
			array(
				'table' => 'yes',
				'id'    => '',
				'class' => '',
				'style' => ''
			), $atts
		);

		$availability = array();
		if (have_rows(self::HOTLINE_AVAILABILITY, 'option')) {
			while (have_rows(self::HOTLINE_AVAILABILITY, 'option')) {
				the_row();
				$row = array('label' => '', 'hours' => '');

				$days = (array)get_sub_field('day_of_the_week');
				if (count($days) === 1) {
					$row['label'] = $days[0]['label'];
				} else {
					$is_continuous = true;
					for ($i = 0; $i < (count($days) - 1); $i++) {
						if ($days[$i]['value'] + 1 != $days[$i + 1]['value']) {
							//first check for 1-7 discontinuities
							if ($days[$i]['value'] == '7' && $days[$i + 1]['value'] == '1') {
								continue;
							}
							// the line is discontinuous, just print all of them
							$is_continuous = false;
							break;
						}
					}

					if ($is_continuous) {
						$row['label'] = $days[0]['label'] . ' - ' . $days[count($days) - 1]['label'];
					} else {
						$length = count($days);
						for ($i = 0; $i < $length; $i++) {
							$row['label'] .= $days[$i]['label'] . ($i !== ($length - 1) ? ', ' : '');
						}
					}
				}

				$from         = get_sub_field('from');
				$to           = get_sub_field('to');
				$to           = ($to === '00:00' ? '24:00' : $to);
				$row['hours'] = sprintf(__('%s - %s o\'clock', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN), $from, $to);

				$availability[] = $row;
			}
		}

		if ($atts['table'] === 'yes') {
			$html = "<table id='{$atts['id']}' class='{$atts['class']}' style='{$atts['style']}'>";
			foreach ($availability as $row) {
				$html .= "<tr><td>{$row['label']}</td><td>{$row['hours']}</td></tr>";
			}
			$html .= '</table>';
		} else {
			$html = "<span id='{$atts['id']}' class='{$atts['class']}' style='{$atts['style']}'>";
			foreach ($availability as $row) {
				$html .= "<span>{$row['label']} ({$row['hours']})</span>; ";
			}
			$html .= '</span>';
		}

		return $html;
	}

	//endregion
} DWS_CustomerService::maybe_initialize_singleton('h4g87hg82dege');