<?php

namespace Deep_Web_Solutions\Local;
use Deep_Web_Solutions\Helpers\DWS_Permissions;
use Deep_Web_Solutions\Helpers\Permissions_Base;

if (!defined('ABSPATH')) { exit; }

/**
 * The custom DWS permissions needed to enhance the local extensions.
 *
 * @since   1.1.0
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
	 *                                                  DWS Local settings.
	 */
	public const SEE_AND_EDIT_DWS_LOCAL_SETTINGS = DWS_Permissions::CAPABILITY_PREFIX . 'see_and_edit_dws_local_settings';
} Permissions::maybe_initialize_singleton('dshg78h8743ehge');