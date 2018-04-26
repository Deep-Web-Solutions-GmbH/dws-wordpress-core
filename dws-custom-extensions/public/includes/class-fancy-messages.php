<?php

namespace Deep_Web_Solutions\Front;
use Deep_Web_Solutions\Core\DWS_Functionality_Template;
use Deep_Web_Solutions\Core\DWS_Helper;

if (!defined('ABSPATH')) { exit; }

/**
 * Outputs CSS to uniformize the look and feel of front-end messages.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Functionality_Template
 */
final class DWS_FancyMessages extends DWS_Functionality_Template {
	//region FIELDS AND CONSTANTS

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string  MESSAGES_OVERWRITE_PLUGINS      The name of the ACF options field that determines whether the
	 *                                                  stylesheet for fancy messages will be loaded or not.
	 */
	const MESSAGES_OVERWRITE_PLUGINS = 'dws_fancy-messages_overwrite-plugins-messages';
	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string  MESSAGE_BG_COLOR    The suffix of the name of the ACF options fields which hold the background
	 *                                      colors for the different message types.
	 */
	const MESSAGE_BG_COLOR = 'dws_fancy-messages_bg-color';
	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string  MESSAGE_ICON_COLOR  The suffix of the name of the ACF options fields which hold the icon
	 *                                      colors for the different message types.
	 */
	const MESSAGE_ICON_COLOR = 'dws_fancy-messages_icon-color';
	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string  MESSAGE_BORDER_COLOR    The suffix of the name of the ACF options fields which hold the border
	 *                                          colors for the different message types.
	 */
	const MESSAGE_BORDER_COLOR = 'dws_fancy-messages_border-color';
	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string  MESSAGE_TEXT_COLOR  The suffix of the name of the ACF options fields which hold the text
	 *                                      colors for the different message types.
	 */
	const MESSAGE_TEXT_COLOR = 'dws_fancy-messages_text-color';

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
		return array_merge(
			array(
				array(
					'key'     => 'field_h7843eghfy7834f43g44hg4',
					'name'    => self::MESSAGES_OVERWRITE_PLUGINS,
					'message' => __('Overwrite compatible plugin\'s message styles?', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
					'type'    => 'true_false',
					'ui'      => 1
				)
			),
			call_user_func_array(
				'array_merge',
				array_map(
					function ($message_type) {
						return array(
							array(
								'key'     => 'field_h7483g743gh5h5h5_' . $message_type,
								'name'    => join('_', array($message_type, self::MESSAGE_BG_COLOR)),
								'label'   => sprintf(__('Choose the color for %s messages background', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN), $message_type),
								'type'    => 'color_picker',
								'wrapper' => array('width' => '25%')
							),
							array(
								'key'     => 'field_agagr45gh34h345h5_' . $message_type,
								'name'    => join('_', array($message_type, self::MESSAGE_TEXT_COLOR)),
								'label'   => sprintf(__('Choose the color for %s messages text', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN), $message_type),
								'type'    => 'color_picker',
								'wrapper' => array('width' => '25%')
							),
							array(
								'key'     => 'field_gsagageh5h5h5h5_' . $message_type,
								'name'    => join('_', array($message_type, self::MESSAGE_BORDER_COLOR)),
								'label'   => sprintf(__('Choose the color for %s messages border', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN), $message_type),
								'type'    => 'color_picker',
								'wrapper' => array('width' => '25%')
							),
							array(
								'key'     => 'field_sag4g53eggdsgse5_' . $message_type,
								'name'    => join('_', array($message_type, self::MESSAGE_ICON_COLOR)),
								'label'   => sprintf(__('Choose the color for %s messages icon', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN), $message_type),
								'type'    => 'color_picker',
								'wrapper' => array('width' => '25%')
							)
						);
					},
					array('info', 'success', 'error')
				)
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
		$overwrite_plugins_messages = get_field(self::MESSAGES_OVERWRITE_PLUGINS, 'option');
		$css                        = DWS_Helper::get_stylesheet_with_variables(
			DWS_Public::get_assets_base_path() . 'fancy-messages.css',
			array(
				'\'{plugin_messages_classes}\''                => $overwrite_plugins_messages ? join(',', ($plugin_messages_classes = array('.message', '.message-info', '.message-success', '.message-error', '.wpas-alert', '.woocommerce-message', '.woocommerce-error', '.woocommerce-info'))) . ',' : '',
				'\'{plugin_messages_classes_before}\''         => $overwrite_plugins_messages ? join(',', array_map(function ($class) { return $class . ':before'; }, $plugin_messages_classes)) . ',' : '',
				'\'{plugin_messages_info_classes}\''           => $overwrite_plugins_messages ? join(',', $plugin_messages_info_classes = array('.message-info', '.wpas-alert-info', '.woocommerce-info')) . ',' : '',
				'\'{plugin_messages_info_classes_before}\''    => $overwrite_plugins_messages ? join(',', array_map(function ($class) { return $class . ':before'; }, $plugin_messages_info_classes)) . ',' : '',
				'\'{plugin_messages_success_classes}\''        => $overwrite_plugins_messages ? join(',', $plugin_messages_success_classes = array('.message-success', '.wpas-alert-success', '.woocommerce-message')) . ',' : '',
				'\'{plugin_messages_success_classes_before}\'' => $overwrite_plugins_messages ? join(',', array_map(function ($class) { return $class . ':before'; }, $plugin_messages_success_classes)) . ',' : '',
				'\'{plugin_messages_error_classes}\''          => $overwrite_plugins_messages ? join(',', $plugin_messages_error_classes = array('.message-error', '.wpas-alert-danger', '.woocommerce-error')) . ',' : '',
				'\'{plugin_messages_error_classes_before}\''   => $overwrite_plugins_messages ? join(',', array_map(function ($class) { return $class . ':before'; }, $plugin_messages_error_classes)) . ',' : '',
				'\'{message_info_text_color}\''                => get_field('info_' . self::MESSAGE_TEXT_COLOR, 'option') ?: '#424242',
				'\'{message_info_background_color}\''          => get_field('info_' . self::MESSAGE_BG_COLOR, 'option') ?: '#ecf6fa',
				'\'{message_info_border_color}\''              => get_field('info_' . self::MESSAGE_BORDER_COLOR, 'option') ?: '#bcdeed',
				'\'{message_info_icon_color}\''                => get_field('info_' . self::MESSAGE_ICON_COLOR, 'option') ?: '#c1d9ef',
				'\'{message_success_text_color}\''             => get_field('success_' . self::MESSAGE_TEXT_COLOR, 'option') ?: '#424242',
				'\'{message_success_background_color}\''       => get_field('success_' . self::MESSAGE_BG_COLOR, 'option') ?: '#eff8e8',
				'\'{message_success_border_color}\''           => get_field('success_' . self::MESSAGE_BORDER_COLOR, 'option') ?: '#d8ecc2',
				'\'{message_success_icon_color}\''             => get_field('success_' . self::MESSAGE_ICON_COLOR, 'option') ?: '#d6eebd',
				'\'{message_error_text_color}\''               => get_field('error_' . self::MESSAGE_TEXT_COLOR, 'option') ?: '#424242',
				'\'{message_error_background_color}\''         => get_field('error_' . self::MESSAGE_BG_COLOR, 'option') ?: '#ffe6e5',
				'\'{message_error_border_color}\''             => get_field('error_' . self::MESSAGE_BORDER_COLOR, 'option') ?: '#ffc5c2',
				'\'{message_error_icon_color}\''               => get_field('error_' . self::MESSAGE_ICON_COLOR, 'option') ?: '#ffc5c2',
			)
		);

		DWS_Helper::add_inline_stylesheet_to_false_handle(self::get_asset_handle(), $css);
	}

	//endregion
} DWS_FancyMessages::maybe_initialize_singleton('dhg84w7hg8w4giuw');