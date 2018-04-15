<?php

namespace Deep_Web_Solutions\Admin;
use Deep_Web_Solutions\Core\DWS_Functionality_Template;
use Deep_Web_Solutions\Core\DWS_Installable;
use Deep_Web_Solutions\Core\DWS_WordPress_Cron;

if (!defined('ABSPATH')) { exit; }

/**
 * Handles extensions and customizations to the WP users.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Functionality_Template
 * @see     DWS_Installable
 */
final class DWS_Users extends DWS_Functionality_Template implements DWS_Installable {
	//region INSTALLATION

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Installable::install()
	 */
	public static function install() {
		/**
		 * @since   1.0.0
		 * @version 1.0.0
		 *
		 * @param   array[]     $roles      Array of the new roles to be installed.
		 *      $roles  = [
		 *          {role_slug}     => [
		 *              'label'         =>  (string) The role's display name. Optional.
		 *              'capabilities'  =>  [
		 *                  {capability_name}   =>  true
		 *                  ...
		 *              ]
		 *          ]
		 *          ...
		 *      ]
		 */
		$roles = apply_filters(self::get_hook_name('dws-roles'), array());
		foreach ($roles as $role_slug => $role_options) {
			if (is_array($role_options) && isset($role_options['capabilities'])) {
				add_role(
					$role_slug,
					isset($role_options['label']) ? $role_options['label'] : $role_slug,
					/**
					 * @since   1.0.0
					 *
					 * @param   array   $role_capabilities      The capabilities that should be installed to the current role.
					 *      $role_capabilities = [
					 *          {capability_name}   => true
					 *          ...
					 *      ]
					 */
					apply_filters(self::get_hook_name('extra-capabilities', $role_slug), $role_options['capabilities'])
				);
			}
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
		$roles = apply_filters(self::get_hook_name('dws-roles'), array());
		return hash('md5',self::get_plugin_version() . serialize($roles));
	}

	//endregion

	//region INHERITED FUNCTIONS

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Functionality_Template::init()
	 */
	public function init() {
		DWS_WordPress_Cron::schedule_event(self::get_hook_name('auto-logout'));
	}

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Functionality_Template::define_functionality_hooks()
	 *
	 * @param   \Deep_Web_Solutions\Core\DWS_WordPress_Loader   $loader
	 */
	protected function define_functionality_hooks( $loader ) {
		$loader->add_action('wp_ajax_is_user_logged_in', $this, 'ajax_check_user_logged_in');
		$loader->add_action('wp_ajax_nopriv_is_user_logged_in', $this, 'ajax_check_user_logged_in');

		$loader->add_filter('register_post_type_args', $this, 'remove_addNew_from_admin_bar', PHP_INT_MAX);
		$loader->add_action(self::get_hook_name('auto-logout'), $this, 'logout_users');
	}

	//endregion

	//region COMPATIBILITY LOGIC

	/**
	 * Quick AJAX check for user logged in status.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function ajax_check_user_logged_in() {
		echo is_user_logged_in() ? 'yes' : 'no';
		die;
	}

	/**
	 * By default, for every registered post type there will be a shortcut in
	 * the admin bar to create the post. It's easier if we just disable that than
	 * making sure that custom permissions don't display the links to the wrong users.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $args   The arguments of a soon-to-be-registered WP post type.
	 *
	 * @return  array   The arguments of a soon-to-be-registered WP post type that guarantee there will be no options in the admin bar.
	 */
	public function remove_addNew_from_admin_bar($args) {
		$args['show_in_admin_bar'] = false;
		return $args;
	}

	/**
	 * Every day at midnight, perform a logout task for security reasons.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function logout_users() {
		/**
		 * @since   1.0.0
		 * @version 1.0.0
		 *
		 * @see     WP_User_Query
		 * @link    https://codex.wordpress.org/Function_Reference/get_users
		 *
		 * @param   array   $logout_users_args      Array of arguments for retrieving the users to be logged out.
		 */
		$logout_users_args = apply_filters(self::get_hook_name('logout-users', 'args'), array());
		$logout_users = get_users($logout_users_args);

		/** @var    $user   \WP_User */
		foreach ($logout_users as $user) {
			$sessions = \WP_Session_Tokens::get_instance($user->ID);
			$sessions->destroy_all();
		}
	}

	//endregion

	//region HELPERS

	/**
	 * Gets the roles of a  given user.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   int|null    $user_id    The id of the user whose roles shall be retrieved.
	 *
	 * @return  array|null  The roles the user is part of or null if the user can not be found.
	 */
	public static function get_role($user_id) {
		$user = (empty($user_id)) ? wp_get_current_user() : get_user_by('id', $user_id);
		return $user ? $user->roles : null;
	}

	/**
	 * Checks if a given user has a given role.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $role       The slug of the role to check for.
	 * @param   int|null    $user_id    The ID of the user to be checked for the role.
	 *
	 * @return  bool        True if the user has the role, or false otherwise.
	 */
	public static function has_role($role, $user_id = null) {
		$user_roles = self::get_role($user_id);
		return is_array($user_roles) ? in_array($role, $user_roles) : false;
	}

	//endregion
}