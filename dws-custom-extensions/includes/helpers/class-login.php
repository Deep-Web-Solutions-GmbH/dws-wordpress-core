<?php

namespace Deep_Web_Solutions\Helpers;
use Deep_Web_Solutions\Base\DWS_Root;

/**
 * Provides a nice interface to interact with the WP login experience.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Root
 */
final class DWS_Login extends DWS_Root {
	//region INHERITED FUNCTIONS

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Root::define_shortcodes()
	 *
	 * @param   \Deep_Web_Solutions\Core\DWS_Loader     $loader
	 */
	protected function define_shortcodes($loader) {
        $loader->add_shortcode('express_login_link', $this, 'get_login_link_sc');
	}

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $name
	 * @param   string  $root
	 * @param   array   $extra
	 *
	 * @return  string
	 */
	public static function get_hook_name($name, $extra = array(), $root = 'login') {
		return parent::get_hook_name($name, $extra, $root);
	}

	//endregion

	//region SHORTCODES

	/**
	 * A shortcode for retrieving an express login URL.
	 *
	 * @param   $atts   array
	 *
	 * @return  string
	 */
	public static function get_login_link_sc($atts) {
		/**
		 * @since   1.0.0
		 * @version 1.0.0
		 *
		 * @param   string  $redirect_link      The link that the user should be redirected to after login.
		 */
		$login_link = apply_filters(self::get_hook_name('default-redirect'), get_home_url());

		$atts = shortcode_atts(
			array(
				'link'      => $login_link,
				'email'     => '',
				'valid_for' => '14 days',
				'link_text' => get_home_url()
			), $atts
		);
		if (empty($atts['email'])) {
			/**
			 * @since   1.0.0
			 * @version 1.0.0
			 *
			 * @param   string  $user_email     The email of the user that the login link should be generated for.
			 */
			$atts['email'] = apply_filters(self::get_hook_name('empty-email'), '');
		}

		return self::get_login_link($atts['link'], $atts['email'], $atts['valid_for'], $atts['link_text']);
	}

	//endregion

	//region HELPERS

	/**
	 * Generate an express link for the customer to login directly (e.g., for emails).
	 *
	 * @param   string          $link       The link the customer must be redirected to after express login.
	 * @param   string          $email      The email of the customer to log in.
	 * @param   string          $valid_for  For how long the link will be valid (e.g., 14 days etc.)
	 * @param   string|null     $link_text  The text inside the anchor.
	 *
	 * @return  string  The express login link, or a regular login link.
	 */
	public static function get_login_link($link, $email, $valid_for, $link_text = null) {
		return is_plugin_active('express-login-for-wordpress/express-login-for-wordpress.php')
			? apply_filters('sa_express_login_link', $link, $email, $valid_for, $link_text)
			: "<a href='$link'>$link_text</a>";
	}

	//endregion
}