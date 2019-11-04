<?php

namespace Deep_Web_Solutions\Admin\Settings\Adapters;
use Deep_Web_Solutions\Admin\Settings\DWS_Adapter;
use Deep_Web_Solutions\Admin\Settings\DWS_Adapter_Base;

if (!defined('ABSPATH')) { exit; }

/**
 * Adapter for the CMB2 plugin.
 *
 * @since   2.0.0
 * @version 2.0.0
 * @author  Fatine Tazi <f.tazi@deep-web-solutions.de>
 */
final class DWS_CMB2_Adapter extends DWS_Adapter_Base implements DWS_Adapter {
    //region CLASS INHERITED FUNCTIONS

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @see     DWS_Adapter_Base::set_framework_slug()
     */
    public function set_framework_slug() {
        $this->framework_slug = 'CMB2';
    }

    //endregion

    //region INTERFACE INHERITED FUNCTIONS

    /**
     * @since   2.0.0
     * @version 2.0.0
     */
    public static function register_settings_page()
    {
        // TODO: Implement register_settings_page() method.
    }

    /**
     * @since   2.0.0
     * @version 2.0.0
     */
    public static function load_settings_page()
    {
        // TODO: Implement load_settings_page() method.
    }

    /**
     * @since   2.0.0
     * @version 2.0.0
     */
    public static function register_settings_subpage()
    {
        // TODO: Implement register_settings_subpage() method.
    }

    /**
     * @since   2.0.0
     * @version 2.0.0
     */
    public static function load_settings_subpage()
    {
        // TODO: Implement load_settings_subpage() method.
    }

    /**
     * @since   2.0.0
     * @version 2.0.0
     */
    public static function register_settings_group()
    {
        // TODO: Implement register_settings_group() method.
    }

    /**
     * @since   2.0.0
     * @version 2.0.0
     */
    public static function load_settings_group()
    {
        // TODO: Implement load_settings_group() method.
    }

    /**
     * @since   2.0.0
     * @version 2.0.0
     */
    public static function register_settings_field()
    {
        // TODO: Implement register_settings_field() method.
    }

    /**
     * @since   2.0.0
     * @version 2.0.0
     */
    public static function load_settings_field()
    {
        // TODO: Implement load_settings_field() method.
    }

    //endregion
}