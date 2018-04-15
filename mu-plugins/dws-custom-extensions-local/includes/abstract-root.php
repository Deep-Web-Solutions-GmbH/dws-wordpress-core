<?php

namespace Deep_Web_Solutions\Local\Core;
use Deep_Web_Solutions\Admin\ACF\ACF_Options;
use Deep_Web_Solutions\Core\DWS_Root;

if (!defined('ABSPATH')) { exit; }

/**
 * The core root template tailored to the needs of local extensions.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Root
 */
abstract class DWS_Local_Root extends DWS_Root {
	//region INHERITED FUNCTIONS

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Root::local_configure()
	 */
	protected function local_configure() {
		$this->settings_filter = ACF_Options::get_page_groups_hook(DWS_Local_Admin::LOCAL_OPTIONS_SLUG);
	}

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Root::local_configure()
	 *
	 * @return  string
	 */
	public static function get_relative_base_path() {
		return str_replace(DWS_CUSTOM_EXTENSIONS_LOCAL_BASE_PATH, '', self::get_base_path());
	}

	//endregion
}