<?php

namespace Deep_Web_Solutions\Admin\Settings\Adapters;
use Deep_Web_Solutions\Admin\Settings\DWS_Adapter;
use Deep_Web_Solutions\Core\DWS_Functionality_Template;

if (!defined('ABSPATH')) { exit; }

/**
 * Adapter for the ACF Pro plugin.
 *
 * @since   2.0.0
 * @version 2.0.0
 * @author  Fatine Tazi <f.tazi@deep-web-solutions.de>
 */
final class DWS_ACFPro_Adapter extends DWS_Functionality_Template implements DWS_Adapter {
    public static function register_settings_page()
    {
        // TODO: Implement register_settings_page() method.
    }
}