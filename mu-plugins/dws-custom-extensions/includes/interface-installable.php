<?php

namespace Deep_Web_Solutions\Core;
if (!defined('ABSPATH')) { exit; }

/**
 * If a class implements this interface, then every time that the 'install' action will be triggered,
 * the 'install' method will be called if the current version is newer than the one previously installed.
 * 
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 */
interface DWS_Installable {
	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	public static function install();

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	public static function get_version();
}