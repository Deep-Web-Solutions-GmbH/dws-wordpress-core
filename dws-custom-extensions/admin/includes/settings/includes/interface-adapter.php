<?php

namespace Deep_Web_Solutions\Admin\Settings;

if (!defined('ABSPATH')) { exit; }

/**
 * Interface for interacting with a settings framework.
 *
 * @since   2.0.0
 * @version 2.0.2
 * @author  Fatine Tazi <f.tazi@deep-web-solutions.de>
 */
interface DWS_Adapter {
    //region METHODS

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   string  $page_title
     * @param   string  $menu_title
     * @param   string  $capability
     * @param   string  $menu_slug
     * @param   array   $other
     *
     * @return  false|array Returns false or the array of parameters the chosen framework takes.
     */
    public static function register_settings_page($page_title, $menu_title, $capability, $menu_slug, $other = array());

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   string  $parent_slug
     * @param   string  $page_title
     * @param   string  $menu_title
     * @param   string  $capability
     * @param   string  $menu_slug
     * @param   array   $other
     *
     * @return  false|array Returns false or the array of parameters the chosen framework takes.
     */
    public static function register_settings_subpage($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $other = array());

    /**
     * @since   2.0.0
     * @version 2.0.2
     *
     * @param   string  $key
     * @param   string  $title
     * @param   string  $location
     * @param   array   $fields
     * @param   array   $other
     */
    public static function register_settings_page_group($key, $title, $location, $fields, $other = array());

    /**
     * @since   2.0.2
     * @version 2.0.2
     *
     * @param   string  $key
     * @param   string  $title
     * @param   array   $location
     * @param   array   $fields
     * @param   array   $other
     */
    public static function register_generic_group($key, $title, $location, $fields, $other = array());

    /**
     * @since   2.0.0
     * @version 2.0.2
     *
     * @param   string  $group_id
     * @param   string  $key
     * @param   string  $type
     * @param   array   $parameters
     * @param   string  $location
     */
    public static function register_field_to_group($group_id, $key, $type, $parameters, $location);

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   string  $key
     * @param   string  $type
     * @param   string  $parent_id
     * @param   array   $parameters
     * @param   null    $location
     */
    public static function register_field($key, $type, $parent_id, $parameters, $location);

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   string  $field
     * @param   string  $option_page_slug
     *
     * @return  mixed
     */
    public static function get_settings_field_value($field, $option_page_slug);

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   string      $field
     * @param   int|false   $post_id
     *
     * @return  mixed
     */
    public static function get_field_value($field, $post_id = false);

    /**
     * @since   2.0.2
     * @version 2.0.2
     *
     * @param   array   $field
     * @param   bool    $do_on_ajax
     *
     * @return  array
     */
    public static function css_hide_field($field, $do_on_ajax = false);

    /**
     * @since   2.0.2
     * @version 2.0.2
     *
     * @param   array   $field
     * @param   bool    $do_on_ajax
     *
     * @return  array
     */
    public static function make_field_uneditable($field, $do_on_ajax = false);

    /**
     * @since   2.0.2
     * @version 2.0.2
     *
     * @param   array   $field
     * @param   string  $permission
     *
     * @return  array
     */
    public static function maybe_make_field_uneditable($field, $permission);

    //endregion
}