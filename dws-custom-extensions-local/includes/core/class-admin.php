<?php

namespace Deep_Web_Solutions\Local\Core;
use Deep_Web_Solutions\Admin\Settings\DWS_Settings_Pages;
use Deep_Web_Solutions\Base\DWS_Root;
use Deep_Web_Solutions\Local\Permissions;

if (!defined('ABSPATH')) { exit; }

/**
 * Orchestrates the DWS Local Extensions of the back-end area of a website.
 *
 * @version 1.0.0
 * @since   2.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Root
 */
final class DWS_Local_Admin extends DWS_Root {
	//region FIELDS AND CONSTANTS

	/**
	 * @since   1.0.0
	 * @version 2.0.0
	 *
	 * @var     string  LOCAL_OPTIONS_SLUG      The slug of the options page for local extensions.
	 */
	public const LOCAL_OPTIONS_SLUG = DWS_Settings_Pages::MENU_PAGES_SLUG_PREFIX . 'local';

	//endregion

	//region INHERITED FUNCTIONS

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Root::define_hooks()
	 *
	 * @param   \Deep_Web_Solutions\Core\DWS_Loader   $loader
	 */
	protected function define_hooks($loader) {
		$loader->add_filter(DWS_Settings_Pages::get_hook_name('subpages'), $this, 'register_local_options_subpage');
	}

	//endregion

	//region COMPATIBILITY LOGIC

	/**
	 * Registers a local extensions options page with the DWS Custom Extensions plugin.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $sub_pages      The other 3rd-party options pages that have been already registered.
	 *
	 * @return  array   The 3rd-party options pages registered so far including the local extensions page.
	 */
	public function register_local_options_subpage($sub_pages) {
		$sub_pages[] = array(
			'page_title' => __('Deep Web Solutions: Custom Extensions Local Settings', DWS_CUSTOM_EXTENSIONS_LOCAL_LANG_DOMAIN),
			'menu_title' => __('Local Settings', DWS_CUSTOM_EXTENSIONS_LOCAL_LANG_DOMAIN),
			'menu_slug'  => self::LOCAL_OPTIONS_SLUG,
			'capability' => Permissions::SEE_AND_EDIT_DWS_LOCAL_SETTINGS
		);

		return $sub_pages;
	}

	//endregion
}