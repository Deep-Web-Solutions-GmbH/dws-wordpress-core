<?php

namespace Deep_Web_Solutions\Front;
use Deep_Web_Solutions\Core\DWS_Functionality_Template;
use Deep_Web_Solutions\Core\DWS_Helper;

if (!defined('ABSPATH')) { exit; }

/**
 * Outputs CSS to enable the use of nicely circled content (like numbers etc.).
 *
 * @since   1.0.0
 * @version 1.3.3
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Functionality_Template
 */
final class DWS_CircledContent extends DWS_Functionality_Template {
	//region FIELDS AND CONSTANTS

	/**
	 * @since   1.3.3
	 * @version 1.3.3
	 *
	 * @var     string  DEFAULT_BG_COLOR    This constant stores the hex code of the default background color
	 *                                      of the circled content.
	 */
	const DEFAULT_BG_COLOR = '#C0C0C0';
	/**
	 * @since   1.3.3
	 * @version 1.3.3
	 *
	 * @var     string  DEFAULT_TEXT_COLOR  This constant stores the hex code of the default text color
	 *                                      of the circled content.
	 */
	const DEFAULT_TEXT_COLOR = '#000000';
	/**
	 * @since   1.3.3
	 * @version 1.3.3
	 *
	 * @var     string  SHOW_CIRCLED_CONTENT_SETTINGS   The name of the ACF option field that hold the information
	 *                                                  whether the user wants to modify circled content settings.
	 */
	const SHOW_CIRCLED_CONTENT_SETTINGS = 'dws_circled-content_show-circled-content-settings';
	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string  CIRCLED_CONTENT_BG_COLOR    The name of the ACF options field that holds the background color of
	 *                                              the circled content.
	 */
	const CIRCLED_CONTENT_BG_COLOR = 'dws_circled-content_background-color';
	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string  CIRCLED_CONTENT_TEXT_COLOR      The name of the ACF options field that holds the text color of
	 *                                                  the circled content.
	 */
	const CIRCLED_CONTENT_TEXT_COLOR = 'dws_circled-content_text-color';
	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string  CIRCLED_CONTENT_BG_COLOR_HOVER      The name of the ACF options field that holds the background
	 *                                                      color of the circled content on hover.
	 */
	const CIRCLED_CONTENT_BG_COLOR_HOVER = 'dws_circled-content_background-color_hover';
	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string  CIRCLED_CONTENT_TEXT_COLOR_HOVER    The name of the ACF options field that holds the text color
	 *                                                      of the circled content on hover.
	 */
	const CIRCLED_CONTENT_TEXT_COLOR_HOVER = 'dws_circled-content_text-color_hover';

	//endregion

	//region INHERITED FUNCTIONS

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
				'key'       => 'field_rcpn89q8ec3ufe9p',
				'name'      => self::SHOW_CIRCLED_CONTENT_SETTINGS,
				'label'     => __('Do you want to modify the appearance of the circled content?', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'type'      => 'true_false',
				'ui'        => 1
			),
			array(
				'key'                   => 'field_h487g2g4g4hve',
				'name'                  => self::CIRCLED_CONTENT_BG_COLOR,
				'label'                 => __('Background color', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'type'                  => 'color_picker',
				'required'              => 1,
				'wrapper'               => array('width' => '25%'),
				'conditional_logic'     => array(
					array(
						array(
							'field'     => 'field_rcpn89q8ec3ufe9p',
							'operator'  => '==',
							'value'     => '1'
						)
					)
				)
			),
			array(
				'key'                   => 'field_shg5hrjytjr6',
				'name'                  => self::CIRCLED_CONTENT_TEXT_COLOR,
				'label'                 => __('Text color', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'type'                  => 'color_picker',
				'required'              => 1,
				'wrapper'               => array('width' => '25%'),
				'conditional_logic'     => array(
					array(
						array(
							'field'     => 'field_rcpn89q8ec3ufe9p',
							'operator'  => '==',
							'value'     => '1'
						)
					)
				)
			),
			array(
				'key'                   => 'field_wagawggg5h45h4',
				'name'                  => self::CIRCLED_CONTENT_BG_COLOR_HOVER,
				'label'                 => __('Background color on hover', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'type'                  => 'color_picker',
				'required'              => 1,
				'wrapper'               => array('width' => '25%'),
				'conditional_logic'     => array(
					array(
						array(
							'field'     => 'field_rcpn89q8ec3ufe9p',
							'operator'  => '==',
							'value'     => '1'
						)
					)
				)
			),
			array(
				'key'                   => 'field_sawgawg4gh6h6',
				'name'                  => self::CIRCLED_CONTENT_TEXT_COLOR_HOVER,
				'label'                 => __('Text color on hover', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'type'                  => 'color_picker',
				'required'              => 1,
				'wrapper'               => array('width' => '25%'),
				'conditional_logic'     => array(
					array(
						array(
							'field'     => 'field_rcpn89q8ec3ufe9p',
							'operator'  => '==',
							'value'     => '1'
						)
					)
				)
			)
		);
	}

	/**
	 * @since   1.0.0
	 * @version 1.3.3
	 *
	 * @see     DWS_Functionality_Template::enqueue_assets()
	 */
	public function enqueue_assets() {
		if (!get_field(self::SHOW_CIRCLED_CONTENT_SETTINGS, 'option')){
			$circled_content_bg_color = self::DEFAULT_BG_COLOR;
			$circled_content_text_color = self::DEFAULT_TEXT_COLOR;
			$circled_content_bg_color_hover = self::DEFAULT_BG_COLOR;
			$circled_content_bg_text_color_hover = self::DEFAULT_TEXT_COLOR;
		} else {
            $circled_content_bg_color = get_field(self::CIRCLED_CONTENT_BG_COLOR, 'option');
            $circled_content_text_color = get_field(self::CIRCLED_CONTENT_TEXT_COLOR, 'option');
            $circled_content_bg_color_hover = get_field(self::CIRCLED_CONTENT_BG_COLOR_HOVER, 'option');
            $circled_content_bg_text_color_hover = get_field(self::CIRCLED_CONTENT_TEXT_COLOR_HOVER, 'option');
        }

		DWS_Helper::add_inline_stylesheet_to_false_handle(
			self::get_asset_handle(),
			DWS_Helper::get_stylesheet_with_variables(
				DWS_Public::get_assets_base_path() . 'circled-content.css',
				array(
					'\'{background-color}\''       => $circled_content_bg_color,
					'\'{text-color}\''             => $circled_content_text_color,
					'\'{background-color-hover}\'' => $circled_content_bg_color_hover,
					'\'{text-color-hover}\''       => $circled_content_bg_text_color_hover
				)
			)
		);
	}

	//endregion

} DWS_CircledContent::maybe_initialize_singleton('dhsuihf48wh8weij');