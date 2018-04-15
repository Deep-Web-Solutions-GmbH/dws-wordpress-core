<?php

namespace Deep_Web_Solutions\Front;
use Deep_Web_Solutions\Core\DWS_Functionality_Template;
use Deep_Web_Solutions\Core\DWS_Helper;

if (!defined('ABSPATH')) { exit; }

/**
 * Outputs CSS to enable the use of nicely circled content (like numbers etc.).
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Functionality_Template
 */
final class DWS_CircledContent extends DWS_Functionality_Template {
	//region FIELDS AND CONSTANTS

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
	 * @var     string  CIRCLED_CONTENT_BG_COLOR_HOVER      The name of the ACF options field that holds the background color of
	 *                                                      the circled content on hover.
	 */
	const CIRCLED_CONTENT_BG_COLOR_HOVER = 'dws_circled-content_background-color_hover';
	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string  CIRCLED_CONTENT_TEXT_COLOR_HOVER    The name of the ACF options field that holds the text color of
	 *                                                      the circled content on hover.
	 */
	const CIRCLED_CONTENT_TEXT_COLOR_HOVER = 'dws_circled-content_text-color_hover';

	//endregion

	//region INHERITED FUNCTIONS

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
				'key'       => 'field_h487g2g4g4hve',
				'name'      => self::CIRCLED_CONTENT_BG_COLOR,
				'label'     => __('Background color', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'type'      => 'color_picker',
				'required'  => 1,
				'wrapper'   => array( 'width' => '25%' )
			),
			array(
				'key'       => 'field_shg5hrjytjr6',
				'name'      => self::CIRCLED_CONTENT_TEXT_COLOR,
				'label'     => __('Text color', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'type'      => 'color_picker',
				'required'  => 1,
				'wrapper'   => array( 'width' => '25%' )
			),
			array(
				'key'       => 'field_wagawggg5h45h4',
				'name'      => self::CIRCLED_CONTENT_BG_COLOR_HOVER,
				'label'     => __('Background color on hover', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'type'      => 'color_picker',
				'required'  => 1,
				'wrapper'   => array( 'width' => '25%' )
			),
			array(
				'key'       => 'field_sawgawg4gh6h6',
				'name'      => self::CIRCLED_CONTENT_TEXT_COLOR_HOVER,
				'label'     => __('Text color on hover', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
				'type'      => 'color_picker',
				'required'  => 1,
				'wrapper'   => array( 'width' => '25%' )
			)
		);
	}

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Functionality_Template::enqueue_assets()
	 */
	public function enqueue_assets() {
		DWS_Helper::add_inline_stylesheet_to_false_handle(self::get_asset_handle(),
			DWS_Helper::get_stylesheet_with_variables(DWS_Public::get_assets_base_path() . 'circled-content.css',
				array(
					'\'{background-color}\''        => get_field(self::CIRCLED_CONTENT_BG_COLOR, 'option'),
					'\'{text-color}\''              => get_field(self::CIRCLED_CONTENT_TEXT_COLOR, 'option'),
					'\'{background-color-hover}\''  => get_field(self::CIRCLED_CONTENT_BG_COLOR_HOVER, 'option'),
					'\'{text-color-hover}\''        => get_field(self::CIRCLED_CONTENT_TEXT_COLOR_HOVER, 'option')
				)
			)
		);
	}

	//endregion

} DWS_CircledContent::maybe_initialize_singleton('dhsuihf48wh8weij');