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
    public function set_fields() {
        $this->framework_slug = 'cmb2';
        $this->init_hook = 'cmb2_admin_init';
    }

    //endregion

    //region INTERFACE INHERITED FUNCTIONS

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   array   $other
     *
     * @return  array   The validated and final page settings.
     */
    public static function register_settings_page($page_name, $page_slug, $other = array()) {
        if( !function_exists('new_cmb2_box') || (empty($other['id']) && empty($other['key'])) || ( empty($other['menu_title']) && empty($other['title']) ) || ( empty($other['menu_slug']) && empty($other['option_key']) ) )  { return null; }

        return new_cmb2_box(array(
                    'id'                        => isset($other['id']) ? $other['id'] : $other['key'],
                    'title'                     => isset($other['title']) ? $other['title'] : $other['menu_title'],
                    'object_types'              => array( 'options-page' ),
                    'option_key'                => isset($other['option_key']) ? $other['option_key'] : $other['menu_slug'],
                    'icon_url'                  => isset($other['icon_url']) ? $other['icon_url'] : '',
                    'menu_title'                => isset($other['menu_title']) ? $other['menu_title'] : $other['title'],
                    'parent_slug'               => isset($other['parent_slug']) ? $other['parent_slug'] : '',
                    'capability'                => isset($other['capability']) ? $other['capability'] : '',
                    'position'                  => isset($other['position']) ? $other['position'] : '',
                    'admin_menu_hook'           => isset($other['admin_menu_hook']) ? $other['admin_menu_hook'] : '',
                    'display_cb'                => isset($other['display_cb']) ? $other['display_cb'] : false,
                    'save_button'               => isset($other['save_button']) ? $other['save_button'] : $other['update_button'],
                    'disable_settings_errors'   => isset($other['disable_settings_errors']) ? $other['disable_settings_errors'] : false,
                    'message_cb'                => isset($other['message_cb']) ? $other['message_cb'] : ''
        ));
    }


    //  TODO: forget about rest for now




    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   array   $parameters
     * @param   array   $parent
     *
     * @return  array   The validated and final page settings.
     */
    public static function register_settings_subpage($parameters, $parent = array()) {
        if( !function_exists('new_cmb2_box') || (empty($parameters['id']) && empty($parameters['key'])) || ( empty($parent['menu_slug']) && empty($parameters['parent_slug']) ) || ( empty($parameters['menu_title']) && empty($parameters['title']) ) || ( empty($parameters['menu_slug']) && empty($parameters['option_key']) ) ) { return null; }

        return new_cmb2_box(array(
                    'id'                        => isset($parameters['id']) ? $parameters['id'] : $parameters['key'],
                    'title'                     => isset($parameters['title']) ? $parameters['title'] : $parameters['menu_title'],
                    'object_types'              => array( 'options-page' ),
                    'option_key'                => isset($parameters['option_key']) ? $parameters['option_key'] : $parameters['menu_slug'],
                    'icon_url'                  => isset($parameters['icon_url']) ? $parameters['icon_url'] : '',
                    'menu_title'                => isset($parameters['menu_title']) ? $parameters['menu_title'] : $parameters['title'],
                    'parent_slug'               => isset($parameters['parent_slug']) ? $parameters['parent_slug'] : $parent['menu_slug'],
                    'capability'                => isset($parameters['capability']) ? $parameters['capability'] : '',
                    'position'                  => isset($parameters['position']) ? $parameters['position'] : '',
                    'admin_menu_hook'           => isset($parameters['admin_menu_hook']) ? $parameters['admin_menu_hook'] : '',
                    'display_cb'                => isset($parameters['display_cb']) ? $parameters['display_cb'] : false,
                    'save_button'               => isset($parameters['save_button']) ? $parameters['save_button'] : $parameters['update_button'],
                    'disable_settings_errors'   => isset($parameters['disable_settings_errors']) ? $parameters['disable_settings_errors'] : false,
                    'message_cb'                => isset($parameters['message_cb']) ? $parameters['message_cb'] : ''
        ));
    }

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   array   $parameters
     * @param   object  $location
     * @param   null    $post_type
     *
     * @return  string  The id of the group.
     */
    public static function register_settings_group($parameters, $location, $post_type = null) {
        if( !function_exists('add_field') || (empty($parameters['id']) && empty($parameters['key'])) || empty($location) || ( empty($parameters['menu_title']) && empty($parameters['options']['group_title']) ) ) { return null; }

        return $location->add_field(array(
                'id' => isset($parameters['id']) ? $parameters['id'] : $parameters['key'],
                'type' => 'group',
                'description' => isset($parameters['description']) ? $parameters['description'] : '',
                'repeatable' => isset($parameters['repeatable']) ? $parameters['repeatable'] : false,
                'options' => array(
                    'group_title'       => isset($parameters['options']['group_title']) ? $parameters['options']['group_title'] : 'Entry {#}', // {#} gets replaced by row number
                    'add_button'        => isset($parameters['options']['add_button']) ? $parameters['options']['add_button'] : '',
                    'remove_button'     => isset($parameters['options']['remove_button']) ? $parameters['options']['remove_button'] : '',
                    'sortable'          => isset($parameters['options']['sortable']) ? $parameters['options']['sortable'] : true,
                    'closed'         => isset($parameters['options']['closed']) ? $parameters['options']['closed'] : true,
                    'remove_confirm' => isset($parameters['options']['remove_confirm']) ? $parameters['options']['remove_confirm'] : '',
                ),
                'before_group'   => isset($parameters['before_group']) ? $parameters['before_group'] : '',
                'after_group'   => isset($parameters['after_group']) ? $parameters['after_group'] : '',
                'before_group_row'   => isset($parameters['before_group_row']) ? $parameters['before_group_row'] : '',
                'after_group_row'   => isset($parameters['after_group_row']) ? $parameters['after_group_row'] : ''
        ));
    }

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   string              $group_id
     * @param   array               $parameters
     * @param   object(cmb2_box)    $location
     */
    public static function register_settings_group_field($group_id, $parameters, $location) {
        if( !function_exists('add_group_field') || empty($parameters['type']) || ( empty($group_id) && empty($parameters['parent']) ) || empty($location) || (empty($parameters['id']) && empty($parameters['key'])) ) { return; }

        $location->add_group_field($group_id, self::formatting_settings_field($parameters));
    }

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   string              $parent_id
     * @param   object(cmb2_box)    $location
     * @param   array               $parameters
     */
    public static function register_settings_field($parameters, $location, $parent_id = null) {
        if( !function_exists('add_field') || empty($parameters['type']) || empty($location) || (empty($parameters['id']) && empty($parameters['key'])) ) { return; }

        switch($parameters['type']){
            case 'text_small':
            case 'text_medium':
            case 'text_email':
            case 'text_money':
            case 'textarea':
            case 'textarea_small':
            case 'textarea_code':
            case 'oembed':
            case 'checkbox':
            case 'hidden':
            case 'select_timezone':
            case 'text':
                $location->add_field(array_merge(self::formatting_settings_field($parameters), array()));
                break;
            case 'text_time':
                $location->add_field(array_merge(self::formatting_settings_field($parameters), array(
                    'time_format' => isset($parameters['time_format']) ? $parameters['time_format'] : ''
                )));
                break;
            case 'text_url':
                $location->add_field(array_merge(self::formatting_settings_field($parameters), array(
                    'protocols' => isset($parameters['protocols']) ? $parameters['protocols'] : array(),
                )));
                break;
            case 'text_date_timestamp':
            case 'text_datetime_timestamp':
            case 'text_datetime_timestamp_timezone':
            case 'text_date':
                $location->add_field(array_merge(self::formatting_settings_field($parameters), array(
                    'timezone_meta_key'   => isset($parameters['timezone_meta_key']) ? $parameters['timezone_meta_key'] : '',
                    'date_format'   => isset($parameters['date_format']) ? $parameters['date_format'] : 'l jS \of F Y'
                )));
                break;
            case 'colorpicker':
            case 'wysiwyg':
            case 'multicheck':
            case 'multicheck_inline':
            case 'radio':
            case 'radio_inline':
            case 'select':
                $location->add_field(array_merge(self::formatting_settings_field($parameters), array(
                    'options' => isset($parameters['options']) ? $parameters['options'] : array(),
                    'options_cb' => isset($parameters['options_cb']) ? $parameters['options_cb'] : ''
                )));
                break;
            case 'taxonomy_radio_inline':
            case 'taxonomy_radio_hierarchical':
            case 'taxonomy_multicheck':
            case 'taxonomy_multicheck_inline':
            case 'taxonomy_multicheck_hierarchical':
            case 'taxonomy_radio':
                $location->add_field(array_merge(self::formatting_settings_field($parameters), array(
                    'remove_default'    => isset($parameters['remove_default']) ? $parameters['remove_default'] : true,
                    'query_args' => isset($parameters['query_args']) ? $parameters['query_args'] : '',
                    'text' => isset($parameters['text']) ? $parameters['text'] : array()
                )));
                break;
            case 'taxonomy_select':
                $location->add_field(array_merge(self::formatting_settings_field($parameters), array(
                    'remove_default'    => isset($parameters['remove_default']) ? $parameters['remove_default'] : true,
                    'query_args' => isset($parameters['query_args']) ? $parameters['query_args'] : ''
                )));
                break;
            case 'image':
            case 'file':
                $location->add_field(array_merge(self::formatting_settings_field($parameters), array(
                    'text' => isset($parameters['text']) ? $parameters['text'] : array(),
                    'options' => isset($parameters['options']) ? $parameters['options'] : array(),
                    'options_cb' => isset($parameters['options_cb']) ? $parameters['options_cb'] : ''
                )));
                break;
            case 'gallery':
            case 'file_list':
                $location->add_field(array_merge(self::formatting_settings_field($parameters), array(
                    'preview_size' => isset($parameters['preview_size']) ? $parameters['preview_size'] : array(50, 50),
                    'query_args' => isset($parameters['query_args']) ? $parameters['query_args'] : '',
                    'text' => isset($parameters['text']) ? $parameters['text'] : array()
                )));
                break;
        }
    }

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   string  $field_id
     *
     * @return  mixed   Option value.
     */
    public static function get_field($field_id) {
        if( !function_exists('get_data') || empty($field_id) ) { return; }

        return get_data($field_id);
    }

    //endregion

    //region HELPERS

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   array               $parameters
     *
     * @return  array   Formatted array for registering generic ACF field
     */
    public static function formatting_settings_field($parameters){
        return array(
            'name'          => isset($parameters['name']) ? $parameters['name'] : '',
            'label_cb' => isset($parameters['label_cb']) ? $parameters['label_cb'] : '',
            'description'   => isset($parameters['description']) ? $parameters['description'] : '',
            'id'            => isset($parameters['id']) ? $parameters['id'] : $parameters['key'],
            'type'          => $parameters['type'],
            'repeatable'    => isset($parameters['repeatable']) ? $parameters['repeatable'] : false,
            'default'   => isset($parameters['default']) ? $parameters['default'] : $parameters['placeholder'],
            'default_cb'   => isset($parameters['default_cb']) ? $parameters['default_cb'] : '',
            'show_names'    => isset($parameters['show_names']) ? $parameters['show_names'] : true,
            'classes'   => isset($parameters['classes']) ? $parameters['classes'] : '',
            'classes_cb'   => isset($parameters['classes_cb']) ? $parameters['classes_cb'] : '',
            'on_front'    => isset($parameters['on_front']) ? $parameters['on_front'] : false,
            'attributes'          => isset($parameters['attributes']) ? $parameters['attributes'] : array(),
            'before'   => isset($parameters['before']) ? $parameters['before'] : '',
            'after'   => isset($parameters['after']) ? $parameters['after'] : '',
            'before_row'   => isset($parameters['before_row']) ? $parameters['before_row'] : '',
            'after_row'   => isset($parameters['after_row']) ? $parameters['after_row'] : '',
            'before_field'   => isset($parameters['before_field']) ? $parameters['before_field'] : '',
            'after_field'   => isset($parameters['after_field']) ? $parameters['after_field'] : '',
            'show_on_cb'   => isset($parameters['show_on_cb']) ? $parameters['show_on_cb'] : '',
            'sanitization_cb'    => isset($parameters['sanitization_cb']) ? $parameters['sanitization_cb'] : true,
            'escape_cb'    => isset($parameters['escape_cb']) ? $parameters['escape_cb'] : true,
            'render_row_cb'   => isset($parameters['render_row_cb']) ? $parameters['render_row_cb'] : '',
            'save_field'    => isset($parameters['save_field']) ? $parameters['save_field'] : true,
        );
    }

    //endregion
}