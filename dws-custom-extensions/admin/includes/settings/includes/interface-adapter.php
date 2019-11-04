<?php

namespace Deep_Web_Solutions\Admin\Settings;

if (!defined('ABSPATH')) { exit; }

/**
 * Interface for interacting with a settings framework.
 *
 * @since   2.0.0
 * @version 2.0.0
 * @author  Fatine Tazi <f.tazi@deep-web-solutions.de>
 */
interface DWS_Adapter {
    //region METHODS

    /**
     * @since   2.0.0
     * @version 2.0.0
     */
    public static function register_settings_page();

    /**
     * @since   2.0.0
     * @version 2.0.0
     */
    public static function load_settings_page();

    /**
     * @since   2.0.0
     * @version 2.0.0
     */
    public static function register_settings_subpage();

    /**
     * @since   2.0.0
     * @version 2.0.0
     */
    public static function load_settings_subpage();

    /**
     * @since   2.0.0
     * @version 2.0.0
     */
    public static function register_settings_group();

    /**
     * @since   2.0.0
     * @version 2.0.0
     */
    public static function load_settings_group();

    /**
     * @since   2.0.0
     * @version 2.0.0
     */
    public static function register_settings_field();

    /**
     * @since   2.0.0
     * @version 2.0.0
     */
    public static function load_settings_field();

    //endregion
}