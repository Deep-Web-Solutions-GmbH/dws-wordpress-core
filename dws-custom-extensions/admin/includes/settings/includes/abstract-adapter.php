<?php

namespace Deep_Web_Solutions\Admin\Settings;

if (!defined('ABSPATH')) { exit; }

/**
 * Template for encapsulating some of the most often required abilities of a settings framework.
 *
 * @since   2.0.0
 * @version 2.0.0
 * @author  Fatine Tazi <f.tazi@deep-web-solutions.de>
 */
interface DWS_Adapter {
    //region METHODS

    public static function register_settings_page();


    //endregion
}