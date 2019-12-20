<?php

namespace Deep_Web_Solutions\Front;
use Deep_Web_Solutions\Admin\Settings\DWS_Settings_Pages;
use Deep_Web_Solutions\Base\DWS_Functionality_Template;

if (!defined('ABSPATH')) { exit; }

/**
 * Provides one centralized place to define contact options.
 *
 * @since   1.0.0
 * @version 2.1.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Functionality_Template
 */
final class DWS_Contact extends DWS_Functionality_Template {
	//region FIELDS AND CONSTANTS

	/**
	 * @since       2.1.0
	 * @version     2.1.0
	 *
	 * @var     string  CONTACT_EMAILS     The id of the option field which holds the repeater of contact emails.
	 */
	private const CONTACT_EMAILS = 'field_dfbuvdigvyhd8vf';

    /**
     * @since       2.1.0
     * @version     2.1.0
     *
     * @var     string  CONTACT_EMAILS_NAME     The id of the option field which holds the name of the email.
     */
    private const CONTACT_EMAIL_NAME = 'field_fgbuinbvdibd8i7fvcb';

	/**
	 * @since       1.0.0
	 * @version     2.1.0
	 *
	 * @var     string  CONTACT_EMAIL  The id of the options field that holds the email address that customers
	 *                                  should send emails to.
	 */
    private const CONTACT_EMAIL = 'field_hg8e7hg8e47ghes';

    /**
     * @since       1.3.3
     * @version     2.1.0
     *
     * @var     string  CONTACT_PHONE_NUMBERS     The id of the option field which holds the repeater of contact phone numbers.
     */
    private const CONTACT_PHONE_NUMBERS = 'field_dfiufbi8d7tgsysyt67';

	/**
	 * @since       2.1.0
	 * @version     2.1.0
	 *
	 * @var     string  CONTACT_PHONE_NUMBER    The id of the options field that holds the phone number that
	 *                                          customers should reach out to.
	 */
    private const CONTACT_PHONE_NUMBER = 'field_595a7fcb23b8a';

    /**
     * @since       2.1.0
     * @version     2.1.0
     *
     * @var     string  CONTACT_PHONE_NUMBER_NAME     The id of the option field which holds the name of the phone number.
     */
    private const CONTACT_PHONE_NUMBER_NAME = 'field_fgrvbisbdiugads87tdg';
	/**
	 * @since       1.0.0
	 * @version     2.1.0
	 *
	 * @var     string PHONE_NUMBER_AVAILABILITY        The id of the repeater field that holds the availability of the
	 *                                                  phone number that customers should reach out to.
	 */
    private const PHONE_NUMBER_AVAILABILITY = 'field_595a80ac23b8d';

	//endregion

	//region INHERITED FUNCTIONS

	/**
	 * @since   1.0.0
	 * @version 2.1.0
	 *
	 * @see     DWS_Contact::define_shortcodes()
	 *
	 * @param   \Deep_Web_Solutions\Core\DWS_Loader   $loader
	 */
	protected function define_shortcodes($loader) {
		$loader->add_shortcode('dws_contact_email', $this, 'get_contact_email');
		$loader->add_shortcode('dws_contact_phone_number', $this, 'get_phone_number');
		$loader->add_shortcode('dws_contact_phone_availability', $this, 'render_opening_hours');
	}

	/**
	 * @since   1.0.0
	 * @version 2.1.0
	 *
	 * @see     DWS_Functionality_Template::functionality_options()
	 *
	 * @return  array
	 */
	protected function functionality_options() {
		return array(
            array(
                'key'          => self::CONTACT_EMAILS,
                'name'         => 'dws_contact_emails',
                'label'        => __('Emails', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
                'instructions' => __('The emails will appear on the website for the customers to write to.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
                'type'         => 'repeater',
                'required'     => 1,
                'min'          => 1,
                'max'          => 7,
                'sub_fields'   => array(
                    array(
                        'key'           => self::CONTACT_EMAIL_NAME,
                        'name'          => 'dws_contact_email-name',
                        'label'         => __('Contact email name', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
                        'instructions'  => __('The name attribute of the shortcode.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
                        'type'          => 'text',
                        'required'      => 1,
                        'wrapper'       => array('width' => '25%')
                    ),
                    array(
                        'key'           => self::CONTACT_EMAIL,
                        'name'          => 'dws_contact_email',
                        'label'         => __('Contact email', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
                        'instructions'  => __('This email will appear on the website for the customers to write to.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
                        'type'          => 'text',
                        'required'      => 1,
                        'wrapper'       => array('width' => '75%')
                    )
                )
            ),
            array(
                'key'          => self::CONTACT_PHONE_NUMBERS,
                'name'         => 'dws_contact_phone-numbers',
                'label'        => __('Phone numbers', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
                'instructions' => __('The phone numbers will appear on the website for the customers to call.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
                'type'         => 'repeater',
                'required'     => 1,
                'min'          => 1,
                'max'          => 7,
                'sub_fields'   => array(
                    array(
                        'key'           => self::CONTACT_PHONE_NUMBER_NAME,
                        'name'          => 'dws_contact_phone-number-name',
                        'label'         => __('Contact phone number name', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
                        'instructions'  => __('The name attribute of the shortcode.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
                        'type'          => 'text',
                        'required'      => 1,
                        'wrapper'       => array('width' => '25%')
                    ),
                    array(
                        'key'           => self::CONTACT_PHONE_NUMBER,
                        'name'          => 'dws_contact_phone-number',
                        'label'         => __('Phone number', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
                        'instructions'  => __('This phone number will appear on the website for the customers to call.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
                        'type'          => 'text',
                        'required'      => 1,
                        'wrapper'       => array('width' => '35%')
                    ),
                    array(
                        'key'           => self::PHONE_NUMBER_AVAILABILITY,
                        'name'          => 'dws_contact_phone-number-availability',
                        'label'         => __('Phone number availability', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
                        'instructions'  => __('The phone numbers availability will appear on the website.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
                        'type'          => 'repeater',
                        'required'      => 1,
                        'min'           => 1,
                        'max'           => 7,
                        'layout'        => 'table',
                        'sub_fields'    => array(
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
                        'wrapper'       => array('width' => '40%')
                    ),
                )
            )
		);
	}

	//endregion

	//region SHORTCODES

	/**
	 * Returns the email address saved.
	 *
	 * @since   1.0.0
	 * @version 2.1.0
	 *
	 * @param   array   $atts       The shortcode options.
	 *
	 * @return  string  The email address.
	 */
	public function get_contact_email($atts = array()) {
		$atts = shortcode_atts(
			array(
                'name'          => '',
				'class'         => '',
				'style'         => '',
				'track_cat'     => __('Contact', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'track_action'  => __('Email', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'track_label'   => '',
				'pre_text'      => '',
				'just_text'     => false
			), $atts
		);
        $address = '';

		$rows = DWS_Settings_Pages::get_field(self::CONTACT_EMAILS, self::get_settings_page_slug());
		if ($rows) {
            foreach ($rows as $row) {
                if ($row['dws_contact_email-name'] == $atts['name']) {
                    $address = $row['dws_contact_email'];
                    break;
                }
            }
        }

		return $atts['just_text']
			? $address
			: "<a 	href='mailto:$address' 
					onclick='if(typeof(ga) !== \"undefined\") { ga(\"send\", \"event\", \"{$atts['track_cat']}}\", \"{$atts['track_action']}\", \"{$atts['track_label']}\"); }'
					class='{$atts['class']}' style='{$atts['style']}'>{$atts['pre_text']} <span class='dws_email_address'>$address</span></a>";
	}

	/**
	 * Returns the phone number saved.
	 *
	 * @since   1.0.0
	 * @version 2.1.0
	 *
	 * @param   array   $atts       The shortcode options.
	 *
	 * @return  string  The phone number.
	 */
	public function get_phone_number($atts = array()) {
		$atts = shortcode_atts(
			array(
                'name'          => '',
				'class'         => '',
				'style'         => '',
				'track_cat'     => __('Contact', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'track_action'  => __('Phone number', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'track_label'   => '',
				'pre_text'      => '',
				'just_text'     => false
			), $atts
		);
		$number = '';

        $rows = DWS_Settings_Pages::get_field(self::CONTACT_PHONE_NUMBERS, self::get_settings_page_slug());
        if ($rows) {
            foreach ($rows as $row) {
                if ($row['dws_contact_phone-number-name'] == $atts['name']) {
                    $number = $row['dws_contact_phone-number'];
                    break;
                }
            }
        }

		return $atts['just_text']
            ? $number
            : "<a 	href='tel:$number' 
                    onclick='if(typeof(ga) !== \"undefined\") { ga(\"send\", \"event\", \"{$atts['track_cat']}\", \"{$atts['track_action']}\", \"{$atts['track_label']}\"); }' 
                    class='{$atts['class']}' style='{$atts['style']}'>{$atts['pre_text']} <span class='dws_phone_number'>$number</span></a>";
	}

	/**
	 * Takes the settings saved for phone number availability and displays them nicely in a table.
	 *
	 * @since   1.0.0
	 * @version 2.1.0
	 *
	 * @param   array   $atts   The shortcode options.
	 *
	 * @return  string  The phone number availability in HTML format.
	 */
	public function render_opening_hours($atts = array()) {
	    $atts = shortcode_atts(
	        array(
                'name'  => '',
                'id'    => '',
                'class' => '',
                'style' => '',
                'table' => 'no'
            ), $atts
        );

        $phone_number_rows = DWS_Settings_Pages::get_field(self::CONTACT_PHONE_NUMBERS, self::get_settings_page_slug());
        $availability_rows = array();

        if ($phone_number_rows) {
            foreach ($phone_number_rows as $row) {
                if ($row['dws_contact_phone-number-name'] == $atts['name']) {
                    $availability_rows = $row['dws_contact_phone-number-availability'];
                    break;
                }
            }
        }

        $availability = array();
        foreach ($availability_rows as $row) {
            $days = (array)$row['day_of_the_week'];
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

            $from         = $row['from'];
            $to           = $row['to'];
            $to           = ($to === '00:00' ? '24:00' : $to);
            $row['hours'] = sprintf(__('%s - %s o\'clock', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN), $from, $to);

            $availability[] = $row;
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
} DWS_Contact::maybe_initialize_singleton('h4g87hg82dege');