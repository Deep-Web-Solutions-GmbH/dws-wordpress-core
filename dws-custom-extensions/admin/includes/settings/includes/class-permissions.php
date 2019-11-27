<?php

namespace Deep_Web_Solutions\Admin\Settings;
use Deep_Web_Solutions\Helpers\DWS_Permissions;
use Deep_Web_Solutions\Helpers\Permissions_Base;

if (!defined('ABSPATH')) { exit; }

/**
 * The custom DWS permissions needed to enhance the custom field's plugin library.
 *
 * @since   1.0.0
 * @version 2.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     Permissions_Base
 * @see     DWS_Permissions
 */
final class Permissions extends Permissions_Base {
	/**
	 * @since   1.2.0
	 * @version 1.2.0
	 *
	 * @var     string  SEE_AND_EDIT_DWS_CORE_SETTINGS   Determines whether the current user has access to edit the
	 *                                                  DWS Core settings.
	 */
	public const SEE_AND_EDIT_DWS_CORE_SETTINGS = DWS_Permissions::CAPABILITY_PREFIX . 'see_and_edit_dws_core_settings';
	/**
	 * @since   1.2.0
	 * @version 1.2.0
	 *
	 * @var     string  SEE_AND_EDIT_DWS_MODULES_SETTINGS    Determines whether the current user has access to edit the
	 *                                                      DWS Modules settings.
	 */
    public const SEE_AND_EDIT_DWS_MODULES_SETTINGS = DWS_Permissions::CAPABILITY_PREFIX . 'see_and_edit_dws_modules_settings';
	/**
	 * @since   1.2.0
	 * @version 1.2.0
	 *
	 * @var     string  SEE_AND_EDIT_DWS_THEME_SETTINGS      Determines whether the current user has access to edit the
	 *                                                      DWS Theme settings.
	 */
    public const SEE_AND_EDIT_DWS_THEME_SETTINGS = DWS_Permissions::CAPABILITY_PREFIX . 'see_and_edit_dws_theme_settings';
}