<?php

namespace Deep_Web_Solutions\Admin\Settings;

if (!defined('ABSPATH')) { exit; }

/**
 * Settings adapter for when there's no adapter present.
 *
 * @since   2.0.3
 * @version 2.0.3
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Adapter_Base
 * @see     DWS_Adapter
 */
final class DWS_noop_Adapter extends DWS_Adapter_Base implements DWS_Adapter {
    //region CLASS INHERITED FUNCTIONS

    /**
     * @since   2.0.3
     * @version 2.0.3
     *
     * @see     DWS_Adapter_Base::set_framework_slug()
     */
    public function set_fields() {
        $this->framework_slug = null;
    }

    //endregion

    //region INTERFACE INHERITED FUNCTIONS

    /**
     * @since   2.0.3
     * @version 2.0.3
     *
     * @param   string  $page_title
     * @param   string  $menu_title
     * @param   string  $capability
     * @param   string  $menu_slug
     * @param   array   $other
     *
     * @return  false|array Returns false or the array of parameters the chosen framework takes.
     */
    public static function register_settings_page($page_title, $menu_title, $capability, $menu_slug, $other = array()) {
        return array();
    }

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
    public static function register_settings_subpage($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $other = array()) {
        return array();
    }

    /**
     * @since   2.0.3
     * @version 2.0.3
     *
     * @param   string  $key
     * @param   string  $title
     * @param   string  $location
     * @param   array   $fields
     * @param   array   $other
     */
    public static function register_settings_page_group($key, $title, $location, $fields, $other = array()) { /* empty */ }

    /**
     * @since   2.0.3
     * @version 2.0.3
     *
     * @param   string  $key
     * @param   string  $title
     * @param   array   $location
     * @param   array   $fields
     * @param   array   $other
     */
    public static function register_generic_group($key, $title, $location, $fields, $other = array()) { /* empty */ }

    /**
     * @since   2.0.3
     * @version 2.0.3
     *
     * @param   string  $group_id
     * @param   string  $key
     * @param   string  $type
     * @param   array   $parameters
     * @param   string  $location
     */
    public static function register_field_to_group($group_id, $key, $type, $parameters, $location) { /* empty */ }

    /**
     * @since   2.0.3
     * @version 2.0.3
     *
     * @param   string  $key
     * @param   string  $type
     * @param   string  $parent_id
     * @param   array   $parameters
     * @param   string  $location
     */
    public static function register_field($key, $type, $parent_id, $parameters, $location) { /* empty */ }

    /**
     * @since   2.0.3
     * @version 2.0.3
     *
     * @param   string  $key
     * @param   string    $location
     */
    public static function remove_field($key, $location) { /* empty */ }

    /**
     * @since   2.0.3
     * @version 2.0.3
     *
     * @param   string  $field
     * @param   string  $option_page_slug
     *
     * @return  mixed
     */
    public static function get_settings_field_value($field, $option_page_slug) { return null; }

    /**
     * @since   2.0.3
     * @version 2.0.3
     *
     * @param   string      $field
     * @param   mixed       $new_value
     * @param   string      $option_page_slug
     *
     * @return  bool        True on successful update, false on failure.
     */
    public static function update_settings_field_value($field, $new_value, $option_page_slug) { return true; }

    /**
     * @since   2.0.3
     * @version 2.0.3
     *
     * @param   string      $field
     * @param   int|false   $post_id
     *
     * @return  mixed
     */
    public static function get_field_value($field, $post_id = false) { return null; }

    /**
     * @since   2.0.3
     * @version 2.0.3
     *
     * @param   string      $field
     * @param   mixed       $new_value
     * @param   int|false   $post_id
     *
     * @return  bool        True on successful update, false on failure.
     */
    public static function update_field_value($field, $new_value, $post_id = false) { return true; }

    /**
     * @since   2.0.3
     * @version 2.0.3
     *
     * @param   array   $field
     * @param   bool    $do_on_ajax
     *
     * @return  array
     */
    public static function css_hide_field($field, $do_on_ajax = false) { return $field; }

    /**
     * @since   2.0.3
     * @version 2.0.3
     *
     * @param   array   $field
     * @param   bool    $do_on_ajax
     *
     * @return  array
     */
    public static function make_field_uneditable($field, $do_on_ajax = false) { return $field; }

    //endregion
}