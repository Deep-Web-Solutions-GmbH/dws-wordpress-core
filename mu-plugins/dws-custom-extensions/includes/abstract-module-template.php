<?php

namespace Deep_Web_Solutions\Core;

if (!defined('ABSPATH')) { exit; }

/**
 * The DWS Functionality Template adapted to fit the requirements of a DWS Module's top-level file.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Module_Functionality_Template
 */
abstract class DWS_Module_Template extends DWS_Module_Functionality_Template {
	// region MAGIC FUNCTIONS

	/**
	 * DWS_Module_Template constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Module_Functionality_Template::__construct()
	 *
	 * @param   string          $module_id
	 * @param   string|bool     $module_description
	 * @param   string|bool     $module_name
	 */
	protected function __construct( $module_id, $module_description = false, $module_name = false ) {
		parent::__construct( $module_id, false, '', '', $module_description, $module_name );
	}

	//endregion
}