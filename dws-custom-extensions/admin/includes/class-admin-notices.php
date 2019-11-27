<?php

namespace Deep_Web_Solutions\Admin;
use Deep_Web_Solutions\Core\DWS_Functionality_Template;

if (!defined('ABSPATH')) { exit; }

/**
 * Handles the registration and display of DWS notices in the admin area.
 *
 * @since   1.0.0
 * @version 2.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Functionality_Template
 */
final class DWS_Admin_Notices extends DWS_Functionality_Template {
	//region FIELDS AND CONSTANTS

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string  ERROR   The slug of admin error notices.
	 */
	const ERROR = 'error';
	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string  WARNING     The slug of admin warning notices.
	 */
	const WARNING = 'warning';
	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string  SUCCESS     The slug of admin success notices.
	 */
	const SUCCESS = 'success';
	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string  INFO    The slug of admin info notices.
	 */
	const INFO = 'info';

	//endregion

	//region INHERITED FUNCTIONS

	/**
     * @since   1.0.0
     * @version 1.0.0
     *
     * @see     DWS_Functionality_Template::define_functionality_hooks()
     *
	 * @param   \Deep_Web_Solutions\Core\DWS_WordPress_Loader   $loader
	 */
	protected function define_functionality_hooks($loader) {
		$loader->add_action('admin_notices', $this, 'print_admin_notices');
	}

	//endregion

	//region COMPATIBILITY LOGIC

	/**
	 * Queries the current user's meta data for admin notices and prints them out.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function print_admin_notices() {
		$user_id  = get_current_user_id();
		$messages = get_user_meta($user_id, 'dws_admin_notices', true);

		foreach (array(self::ERROR, self::WARNING, self::SUCCESS, self::INFO) as $type) {
			if (isset($messages[$type])) {
				foreach ($messages[$type] as $message) {
					if (empty($message)) {
						continue;
					} ?>
                    <div class="notice notice-<?php echo $type; ?> is-dismissible">
                        <p><?php echo $message; ?></p>
                    </div> <?php
				}
			}
		}

		delete_user_meta($user_id, 'dws_admin_notices');
	}

	//endregion

	//region HELPERS

	/**
	 * Adds a message to the current user's meta data that will be displayed to the user
	 * on the next page load.
	 *
	 * @since   1.0.0
	 * @version 2.0.0
	 *
	 * @param   string 	$message 	The message that should be displayed to the current user on the next page load.
	 * @param   string 	$type    	The type of the message to be displayed.
	 */
	public static function add_admin_notice_to_user($message, $type = self::ERROR) {
		$user_id = get_current_user_id();
		$notices = get_user_meta($user_id, 'dws_admin_notices', true);

		if (!is_array($notices)) { $notices = array(); }

		$notices[$type]   = isset($notices[$type]) ? $notices[$type] : array();
		$notices[$type][] = $message;

		update_user_meta($user_id, 'dws_admin_notices', $notices);
	}

	//endregion
}
