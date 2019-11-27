<?php

namespace Deep_Web_Solutions\Admin\Settings;
use Deep_Web_Solutions\Core\DWS_Permissions;
use Deep_Web_Solutions\Core\Permissions_Base;

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
	 * @var     string  SEE_AND_EDIT_DWS_CORE_OPTIONS   Determines whether the current user has access to edit the
	 *                                                  DWS Core options.
	 */
	const SEE_AND_EDIT_DWS_CORE_OPTIONS = DWS_Permissions::CAPABILITY_PREFIX . 'see_and_edit_dws_core_options';
	/**
	 * @since   1.2.0
	 * @version 1.2.0
	 *
	 * @var     string  SEE_AND_EDIT_DWS_MODULES_OPTIONS    Determines whether the current user has access to edit the
	 *                                                      DWS Modules options.
	 */
	const SEE_AND_EDIT_DWS_MODULES_OPTIONS = DWS_Permissions::CAPABILITY_PREFIX . 'see_and_edit_dws_modules_options';
	/**
	 * @since   1.2.0
	 * @version 1.2.0
	 *
	 * @var     string  SEE_AND_EDIT_DWS_THEME_OPTIONS      Determines whether the current user has access to edit the
	 *                                                      DWS Theme options.
	 */
	const SEE_AND_EDIT_DWS_THEME_OPTIONS = DWS_Permissions::CAPABILITY_PREFIX . 'see_and_edit_dws_theme_options';
}