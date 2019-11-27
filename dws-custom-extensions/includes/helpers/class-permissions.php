<?php

namespace Deep_Web_Solutions\Helpers;
use Deep_Web_Solutions\Base\DWS_Installable;
use Deep_Web_Solutions\Base\DWS_Root;

if (!defined('ABSPATH')) { exit; }

/**
 * Handles an interface to interact with the WordPress capabilities system.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Root
 * @see     DWS_Installable
 */
final class DWS_Permissions extends DWS_Root implements DWS_Installable {
	//region FIELDS AND CONSTANTS

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string  CAPABILITY_PREFIX   The string that all DWS capabilities must be prefixed with.
	 */
	public const CAPABILITY_PREFIX = 'dws_can_';

	//endregion

	//region INSTALLATION

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Installable::install()
	 */
	public static function install() {
		$admin_role = get_role('administrator');

		$custom_capabilities = array_merge(
			self::get_permission_constants(__CLASS__),
			/**
			 * @since   1.0.0
			 * @version 1.0.0
			 *
			 * @param   array   $permissions    Other permissions that should be installed.
			 */
			apply_filters(self::get_hook_name('custom-permissions'), array())
		);
		$capabilities_to_remove = array_diff(
			array_filter(
				array_map(
					function ($capability) {
						return strpos($capability, self::CAPABILITY_PREFIX) !== false ? $capability : null;
					}, array_keys($admin_role->capabilities)
				)
			), array_values($custom_capabilities)
		);

        // perform the capabilities adding and removal
		foreach ($capabilities_to_remove as $capability) {
			$admin_role->remove_cap($capability);
		}
		foreach ($custom_capabilities as $capability) {
			$admin_role->add_cap($capability);
		}
	}

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Installable::get_version()
	 *
	 * @return  string
	 */
	public static function get_version() {
		$third_party_permissions = apply_filters(self::get_hook_name('custom-permissions'), array());
		return hash('md5', serialize(array_merge(self::get_permission_constants(__CLASS__), $third_party_permissions)));
	}

	//endregion

	//region HELPERS

	/**
	 * Returns all the constants defined in a certain class which have the DWS permissions string as a proper prefix.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $class      The class to be queried for permissions constants.
	 *
	 * @return  array   All the permissions constants inside the queried class.
	 */
	public static function get_permission_constants($class) {
		try {
			$all_constants = (new \ReflectionClass($class))->getConstants();
		} catch (\ReflectionException $e) {
			return array();
		}

		return array_filter(
			array_map(
				function ($constant) {
					return (strpos($constant, self::CAPABILITY_PREFIX) === 0 && $constant !== self::CAPABILITY_PREFIX) ? $constant : null;
				}, $all_constants
			)
		);
	}

	/**
	 * Checks if a certain user is allowed to perform an action or not based on WP capabilities.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string|array    $capabilities   The capability/capabilities to check for.
	 * @param   null|int        $user_id        The ID of the user whose WP capabilities should be checked.
	 * @param   string          $logic          The boolean logic to be used on the list of capabilities.
	 *                                          Accepted values are 'and' and 'or'.
	 *
	 * @return  bool
	 */
	public static function has($capabilities, $user_id = null, $logic = 'and') {
		if (!is_array($capabilities)) {
			$capabilities = array($capabilities);
		}

		if (is_plugin_active('advanced-access-manager/aam.php') && class_exists('AAM_Core_Subject_User')) {
			$user = empty($user_id) ? \AAM::getUser() : (new \AAM_Core_Subject_User($user_id));

			foreach ($capabilities as $capability) {
				if (!$user->hasCapability($capability)) {
					if ($logic === 'and') {
						return false;
					}
				} else if ($logic === 'or') {
					return true;
				}
			}
		} else {
			$user = empty($user_id) ? wp_get_current_user() : get_user_by('id', $user_id);

			foreach ($capabilities as $capability) {
				if (!$user->has_cap($capability)) {
					if ($logic === 'and') {
						return false;
					}
				} else if ($logic === 'or') {
					return true;
				}
			}


		}

		return ($logic === 'and');
	}

	//endregion
}

/**
 * If a certain functionality needs to implement more granular permissions, it must only create a class which inherits
 * from this one and define constants appropriately. Those permissions will be automatically created when the
 * installation is triggered.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Root
 * @see     DWS_Permissions
 */
abstract class Permissions_Base extends DWS_Root {
	//region INHERITED FUNCTIONS

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   \Deep_Web_Solutions\Core\DWS_Loader $loader
	 */
	protected function define_hooks($loader) {
		$loader->add_filter(DWS_Permissions::get_hook_name('custom-permissions'), $this, 'register_custom_permissions');
	}

	//endregion

	//region COMPATIBILITY LOGIC

	/**
	 * Registered the permissions declared inside the current class to be installed by the DWS Core.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $permissions    The 3rd-party permissions registered so far.
	 *
	 * @return  array   The 3rd-party permissions registered so far including the permissions declared inside the
	 *                  current class.
	 */
	public function register_custom_permissions($permissions) {
		return array_merge($permissions, DWS_Permissions::get_permission_constants(static::class));
	}

	//endregion
}