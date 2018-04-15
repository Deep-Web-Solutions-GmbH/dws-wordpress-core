<?php

namespace Deep_Web_Solutions\Core;
if (!defined('ABSPATH')) { exit; }

/**
 * Provides an "installation" function to this MU-plugin.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Root
 */
final class DWS_Installation extends DWS_Root {
	//region FIELDS AND CONSTANTS

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string      INSTALL_ACTION      The name of the AJAX action on which the 'installation' should occur.
	 */
	const INSTALL_ACTION = 'dws_install_custom_extensions';

	//endregion

	//region INHERITED FUNCTIONS

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Root::define_hooks()
	 *
	 * @param   DWS_WordPress_Loader    $loader
	 */
	protected function define_hooks( $loader ) {
		$loader->add_action('wp_ajax_' . self::INSTALL_ACTION, $this, 'run_installation', PHP_INT_MIN);
	}

	//endregion

	/**
	 * Gathers all installable classes and runs their installation. This is a very expensive operation,
	 * so it should only be triggered by an admin by AJAX.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @throws  \ReflectionException
	 */
	public function run_installation() {
		if (!DWS_Permissions::has('administrator')) { return; }

		foreach (get_declared_classes() as $declared_class) {
			if (!in_array('Deep_Web_Solutions\Core\DWS_Installable', class_implements($declared_class))) { continue; }

			$class = new \ReflectionClass($declared_class);
			$install_version = $class->getMethod('get_version')->invoke(null);
			if (get_option($class->getName() . '_install_version') === $install_version) { continue; }

			$class->getMethod('install')->invoke(null);
			update_option($class->getName() . '_install_version', $install_version);
		}

		die;
	}
}