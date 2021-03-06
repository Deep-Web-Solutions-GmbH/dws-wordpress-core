<?php

namespace Deep_Web_Solutions\Admin\Dashboard;
use Deep_Web_Solutions\Helpers\DWS_Permissions;
use Deep_Web_Solutions\Helpers\Permissions_Base;

if (!defined('ABSPATH')) { exit; }

/**
 * Collection of permissions for managing access to the DWS back-end menus.
 *
 * @since   1.2.0
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
	 * @var     string  SEE_DWS_MENU_AND_DASHBOARD  The string of the permission which determines whether the current
	 *                                              user can see or not the DWS back-end menu (and therefore also the
	 *                                              dashboard).
	 */
	public const SEE_DWS_MENU_AND_DASHBOARD = DWS_Permissions::CAPABILITY_PREFIX . 'see_dws_menu_and_dashboard';
	/**
	 * @since   1.2.0
	 * @version 1.2.0
	 *
	 * @var     string  SEE_RECOMMENDED_PLUGINS The string of the permissions which determines whether the current
	 *                                          user can see the recommended plugins menu or not.
	 */
    public const SEE_RECOMMENDED_PLUGINS = DWS_Permissions::CAPABILITY_PREFIX . 'see_recommended_plugins';
} Permissions::maybe_initialize_singleton('dsg8e7hgehgidfdhui');
