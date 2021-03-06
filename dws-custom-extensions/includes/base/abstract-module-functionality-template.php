<?php

namespace Deep_Web_Solutions\Base;
use Deep_Web_Solutions\Admin\Settings\DWS_Settings_Pages;

if (!defined('ABSPATH')) { exit; }

/**
 * The DWS Functionality Template adapted to fit a functionality of a DWS Module.
 *
 * @since   1.0.0
 * @version 2.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Functionality_Template
 */
abstract class DWS_Module_Functionality_Template extends DWS_Functionality_Template {
	//region MAGIC METHODS

	/**
	 * DWS_Module_Functionality_Template constructor.
	 *
	 * @since   1.0.0
	 * @version 1.4.0
	 *
	 * @see     DWS_Functionality_Template::__construct()
	 *
	 * @param   string      $functionality_id
	 * @param   bool        $must_use
	 * @param   string      $parent_functionality_id
	 * @param   string      $options_parent_id
	 * @param   bool        $functionality_description
	 * @param   bool        $functionality_name
	 */
	protected function __construct(string $functionality_id, bool $must_use = true, string $parent_functionality_id = '', string $options_parent_id = '', $functionality_description = false, $functionality_name = false) {
		parent::__construct($functionality_id, $must_use, $parent_functionality_id, $options_parent_id, $functionality_description, $functionality_name);
	}

	//endregion

	//region INHERITED FUNCTIONS

	/**
	 * @since   1.0.0
	 * @version 2.0.0
	 *
	 * @see     DWS_Functionality_Template::local_configure()
	 */
	protected function local_configure() {
		parent::local_configure();

		if (!empty(self::get_parent())) {
			$this->settings_filter = $this->children_settings_filter;
		} else {
			$this->settings_filter = DWS_Settings_Pages::get_page_groups_hook(self::get_settings_page_slug());
		}
	}

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @see     DWS_Module_Functionality_Template
     *
     * @return  string  The slug of the options page.
     */
    public static function get_settings_page_slug() {
        return DWS_Settings_Pages::MODULES_SETTINGS_SLUG;
    }

	//endregion
}