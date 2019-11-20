<?php

namespace Deep_Web_Solutions\Admin\Settings\Adapters;
use Deep_Web_Solutions\Admin\Settings\DWS_Adapter;
use Deep_Web_Solutions\Admin\Settings\DWS_Adapter_Base;

if (!defined('ABSPATH')) { exit; }

/**
 * Adapter for the ACF Pro plugin.
 *
 * @since   2.0.0
 * @version 2.0.0
 * @author  Fatine Tazi <f.tazi@deep-web-solutions.de>
 */
final class DWS_ACFPro_Adapter extends DWS_Adapter_Base implements DWS_Adapter {
    //region FIELDS AND CONSTANTS

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @var     string      GROUP_KEY_PREFIX        All group keys must begin with 'field_'
     */
    private const GROUP_KEY_PREFIX = 'group_';

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @var     string      FIELD_KEY_PREFIX        All fields keys must begin with 'field_'
     */
    private const FIELD_KEY_PREFIX = 'field_';

    //endregion

    //region CLASS INHERITED FUNCTIONS

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @see     DWS_Adapter_Base::set_framework_slug()
     */
    public function set_fields() {
        $this->framework_slug = 'acf-pro';
        $this->init_hook = 'acf/init';
    }

    //endregion

    //region INTERFACE INHERITED FUNCTIONS

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
     * @return  false|array     The validated and final page settings.
     */
    public static function register_settings_page($page_title, $menu_title, $capability, $menu_slug, $other = array()) {
        if (!function_exists('acf_add_options_page')) { return false; }

        $args = wp_parse_args($other, array(
            'page_title'        => $page_title,
            'menu_title'        => $menu_title,
            'menu_slug'         => $menu_slug,
            'capability'        => $capability,
            'position'          => '',
            'parent_slug'       => '',
            'icon_url'          => '',
            'redirect'          => false,
            'post_id'           => '',
            'autoload'          => false,
            'update_button'     => '',
            'updated_message'   => ''
        ));

        return acf_add_options_page($args);
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
     * @return  false|array
     */
    public static function register_settings_subpage($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $other = array()) {
        if (!function_exists('acf_add_options_sub_page')) { return false; }

        $args = wp_parse_args($other, array(
            'page_title'        => $page_title,
            'menu_title'        => $menu_title,
            'menu_slug'         => $menu_slug,
            'capability'        => $capability,
            'position'          => '',
            'parent_slug'       => $parent_slug,
            'icon_url'          => '',
            'redirect'          => false,
            'post_id'           => '',
            'autoload'          => false,
            'update_button'     => '',
            'updated_message'   => ''
        ));

        return acf_add_options_sub_page($args);
    }

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   string  $key
     * @param   string  $title
     * @param   string  $location
     * @param   array   $other
     */
    public static function register_options_page_group($key, $title, $location, $other = array()) {
        if (!function_exists('acf_add_local_field_group')) { return; }

        $key = strpos($key, 'group_') === 0 ? $key : self::GROUP_KEY_PREFIX . $key; // Must begin with 'group_'
        $other['key'] = $key;
        $args = wp_parse_args($other, array(
            'key'                   => $key,
            'title'                 => $title,
            'fields'                => array(),
            'location'              => array(
                array(
                    array(
                        'param'     => 'options_page',
                        'operator'  => '==',
                        'value'     => $location
                    ),
                ),
            ),
            'menu_order'            => 0,
            'position'              => 'normal', // Choices of 'acf_after_title', 'normal' or 'side'
            'style'                 => 'default', // Choices of 'default' or 'seamless'
            'label_placement'       => 'top', // Choices of 'top' (Above fields) or 'left' (Beside fields)
            'instruction_placement' => 'label', // Choices of 'label' (Below labels) or 'field' (Below fields)
            'hide_on_screen'        => ''
        ));

        acf_add_local_field_group($args);
    }

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   string              $group_id
     * @param   string              $key
     * @param   string              $type
     * @param   array               $parameters
     * @param   string              $location
     */
    public static function register_options_group_field($group_id, $key, $type, $parameters, $location = null){
        if (!function_exists('acf_add_local_field')) { return; }

        $group_id = (strpos($group_id, 'field_') === 0 || strpos($group_id, 'group_') === 0)
            ? $group_id
            : (self::GROUP_KEY_PREFIX . $group_id);

        $parameters['parent'] = $group_id;

        acf_add_local_field(self::formatting_settings_field($key, $type, $group_id, $parameters));
    }

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
    public static function register_settings_field($key, $type, $parent_id, $parameters, $location = null) {
        if (!function_exists('acf_add_local_field')) { return; }

        acf_add_local_field(self::formatting_settings_field($key, $type, $parent_id, $parameters));
    }

    //  TODO: forget about rest for now

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   string  $field_id
     * @param   string  $location_id
     *
     * @return  mixed   Option value.
     *
     */
    public static function get_field_value($field_id, $location_id = null) {
        if (!function_exists('get_field')) { return; }

        return get_field($field_id, $location_id);
    }

    //endregion

    //region HELPERS

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     *
     * @param   string  $location_id
     * @param   string  $key
     * @param   string  $type
     * @param   array   $parameters
     *
     * @return  array   Formatted array for registering generic ACF field
     */
    public static function formatting_settings_field($key, $type, $location_id, $parameters) {
        $key = strpos($key, 'field_') === 0 ? $key : self::FIELD_KEY_PREFIX . $key; // Must begin with 'field_'
        $parameters['key'] = $key;
        $args = wp_parse_args($parameters, array(
            array(
                'key'               => $key,
                'type'              => $type,
                'required'          => 0,
                'conditional_logic' => 0, // Best to use the ACF UI and export to understand the array structure.
                'parent'            => $location_id
            )
        ));

        // TODO: Check if the string of each case is the same as the one acf uses

        switch($args['type']){
            case 'text':
            case 'textarea':
            case 'number':
            case 'password':
            case 'email':
            case 'oembed':
            case 'select':
            case 'true_false':
            case 'page_link':
            case 'user':
            case 'acf_code_field':
            case 'url':
            case 'wysiwyg':
            case 'file':
            case 'image':
            case 'gallery':
            case 'checkbox':
            case 'radio':
            case 'post_object':
            case 'relationship':
            case 'taxonomy':
                break;
            default:
                $args['type'] = 'text';
                error_log("The field type \"" . $type . "\" for field " . $key . " is not available in ACF Pro and its adapter. Defaulting to text field type.");
        }

        return $args;
    }

    //endregion
} DWS_ACFPro_Adapter::maybe_initialize_singleton('rgfjn87uy4578yhbf67');