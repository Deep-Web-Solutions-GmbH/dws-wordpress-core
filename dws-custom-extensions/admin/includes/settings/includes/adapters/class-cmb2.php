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
     * @param   string  $page_title
     * @param   string  $menu_title
     * @param   string  $capability
     * @param   string  $menu_slug
     * @param   array   $other
     *
     * @return  false|array     The validated and final page settings.
     */
    public static function register_settings_page($page_title, $menu_title, $capability, $menu_slug, $other = array()) {
        if (!function_exists('new_cmb2_box'))  { return false; }

        $args = wp_parse_args($other, array(
            'id'                        => md5($menu_slug),
            'title'                     => $page_title,
            'object_types'              => array( 'options-page' ),
            'option_key'                => $menu_slug,
            'icon_url'                  => '',
            'menu_title'                => $menu_title,
            'position'                  => '',
            'parent_slug'               => '',
            'capability'                => $capability,
            'display_cb'                => false,
            'save_button'               => __('Save', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
            'disable_settings_errors'   => false,
            'message_cb'                => ''
        ));

        new_cmb2_box($args);

        return $args;
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
        if (!function_exists('new_cmb2_box')) { return false; }

        $args = wp_parse_args($other, array(
            'id'                        => md5($menu_slug),
            'title'                     => $page_title,
            'object_types'              => array( 'options-page' ),
            'option_key'                => $menu_slug,
            'icon_url'                  => '',
            'menu_title'                => $menu_title,
            'parent_slug'               => $parent_slug,
            'capability'                => $capability,
            'position'                  => '',
            'display_cb'                => false,
            'save_button'               => __('Save', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
            'disable_settings_errors'   => false,
            'message_cb'                => ''
        ));

        new_cmb2_box($args);

        return $args;
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
        if (!class_exists('CMB2')) { return; }

        if (isset($other['fields']) && !empty($other['fields'])) {
            $fields = $other['fields'];
            unset($other['fields']);
        }

        $args = wp_parse_args($other, array(
            'id' => $key,
            'type' => 'group',
            'repeatable'  => false,
            'options' => array(
                'group_title'       => $title // {#} gets replaced by row number
            )
        ));

        $cmb = cmb2_get_metabox(md5($location));

        $group_field_id = $cmb->add_field($args);

        if (isset($fields)) {
            foreach ($fields as $field) {
                if ($field['type'] == 'repeater') {
                    $other = array(
                        'id' => $field['key'],
                        'type' => 'group',
                        'repeatable'  => true,
                        'options' => array(
                            'group_title'       => isset($field['title']) ? $field['title'] : $field['label']
                        ),
                        'fields' => $field['sub_fields']
                    );
                    self::register_options_page_group($field['key'], $field['title'], $location, $other);
                } else {
                    self::register_options_group_field($group_field_id, $field['key'], $field['type'], $field, $location);
                }
            }
        }
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
    public static function register_options_group_field($group_id, $key, $type, $parameters, $location) {
        if (!class_exists('CMB2')) { return; }

        $cmb = cmb2_get_metabox(md5($location));

        $cmb->add_group_field($group_id, self::formatting_settings_field($key, $type, $parameters, $location));
    }

    //  TODO: forget about rest for now

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

//        switch($parameters['type']){
//            case 'text_small':
//            case 'text_medium':
//            case 'text_email':
//            case 'text_money':
//            case 'textarea':
//            case 'textarea_small':
//            case 'textarea_code':
//            case 'oembed':
//            case 'checkbox':
//            case 'hidden':
//            case 'select_timezone':
//            case 'text':
//                $location->add_field(array_merge(self::formatting_settings_field($parameters), array()));
//                break;
//            case 'text_time':
//                $location->add_field(array_merge(self::formatting_settings_field($parameters), array(
//                    'time_format' => isset($parameters['time_format']) ? $parameters['time_format'] : ''
//                )));
//                break;
//            case 'text_url':
//                $location->add_field(array_merge(self::formatting_settings_field($parameters), array(
//                    'protocols' => isset($parameters['protocols']) ? $parameters['protocols'] : array(),
//                )));
//                break;
//            case 'text_date_timestamp':
//            case 'text_datetime_timestamp':
//            case 'text_datetime_timestamp_timezone':
//            case 'text_date':
//                $location->add_field(array_merge(self::formatting_settings_field($parameters), array(
//                    'timezone_meta_key'   => isset($parameters['timezone_meta_key']) ? $parameters['timezone_meta_key'] : '',
//                    'date_format'   => isset($parameters['date_format']) ? $parameters['date_format'] : 'l jS \of F Y'
//                )));
//                break;
//            case 'colorpicker':
//            case 'wysiwyg':
//            case 'multicheck':
//            case 'multicheck_inline':
//            case 'radio':
//            case 'radio_inline':
//            case 'select':
//                $location->add_field(array_merge(self::formatting_settings_field($parameters), array(
//                    'options' => isset($parameters['options']) ? $parameters['options'] : array(),
//                    'options_cb' => isset($parameters['options_cb']) ? $parameters['options_cb'] : ''
//                )));
//                break;
//            case 'taxonomy_radio_inline':
//            case 'taxonomy_radio_hierarchical':
//            case 'taxonomy_multicheck':
//            case 'taxonomy_multicheck_inline':
//            case 'taxonomy_multicheck_hierarchical':
//            case 'taxonomy_radio':
//                $location->add_field(array_merge(self::formatting_settings_field($parameters), array(
//                    'remove_default'    => isset($parameters['remove_default']) ? $parameters['remove_default'] : true,
//                    'query_args' => isset($parameters['query_args']) ? $parameters['query_args'] : '',
//                    'text' => isset($parameters['text']) ? $parameters['text'] : array()
//                )));
//                break;
//            case 'taxonomy_select':
//                $location->add_field(array_merge(self::formatting_settings_field($parameters), array(
//                    'remove_default'    => isset($parameters['remove_default']) ? $parameters['remove_default'] : true,
//                    'query_args' => isset($parameters['query_args']) ? $parameters['query_args'] : ''
//                )));
//                break;
//            case 'image':
//            case 'file':
//                $location->add_field(array_merge(self::formatting_settings_field($parameters), array(
//                    'text' => isset($parameters['text']) ? $parameters['text'] : array(),
//                    'options' => isset($parameters['options']) ? $parameters['options'] : array(),
//                    'options_cb' => isset($parameters['options_cb']) ? $parameters['options_cb'] : ''
//                )));
//                break;
//            case 'gallery':
//            case 'file_list':
//                $location->add_field(array_merge(self::formatting_settings_field($parameters), array(
//                    'preview_size' => isset($parameters['preview_size']) ? $parameters['preview_size'] : array(50, 50),
//                    'query_args' => isset($parameters['query_args']) ? $parameters['query_args'] : '',
//                    'text' => isset($parameters['text']) ? $parameters['text'] : array()
//                )));
//                break;
//        }
    }

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   string      $field_id
     * @param   string      $location_id
     *
     * @return  false|mixed Option value.
     */
    public static function get_field_value($field_id, $location_id) {
        if (!class_exists('CMB2') || empty($field_id) || empty($location_id)) { error_log("here1"); return; }

        $value = cmb2_get_field_value($field_id, $field_id);
        return $value;
    }

    //endregion

    //region HELPERS

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   string              $key
     * @param   string              $type
     * @param   array               $parameters
     * @param   string              $location
     *
     * @return  array   Formatted array for registering generic ACF field
     */
    public static function formatting_settings_field($key, $type, $parameters, $location) {

//        if (isset($parameters['conditional_logic']) && !empty($parameters['conditional_logic'])) {
//            if(sizeof($parameters['conditional_logic']) > 1 || sizeof($parameters['conditional_logic'][0]) > 1) {
//                error_log("CMB2 adapter only supports one conditional logic for displaying a field. Field " . $key . " will display and disregard the conditional logic.");
//            }
//
//            $loaction_id = md5($location);
//
//            $field = get_post_meta( $loaction_id, $parameters['conditional_logic'][0][0]['field'], 1 );
//            $operator = $parameters['conditional_logic'][0][0]['operator'];
//            $value = $parameters['conditional_logic'][0][0]['value'];
//            $return = self::compare($field, $operator, $value);
//
//            $parameters['show_on_cb'] = $return ? 'return_true' : 'return_false';
//
//            unset($parameters['conditional_logic']);
//        }

        $args = wp_parse_args($parameters, array(
            'name'          => $parameters['label'],
            'desc'          => $parameters['instructions'],
            'id'            => $key,
            'type'          => $type,
            'attributes'    => array(),
            'repeatable'    => false,
            'default'       => $parameters['default_value'],
            'show_names'    => true,
            'show_on_cb'    =>'return_true'
        ));

        $args['name'] = $parameters['label'];

        switch($type){
            case 'wysiwyg':
            case 'multicheck':
            case 'multicheck_inline':
            case 'radio':
            case 'radio_inline':
            case 'image':
            case 'file':
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
                $args['type'] = $type;
                break;
            case 'text_date_timestamp':
            case 'text_datetime_timestamp':
            case 'text_datetime_timestamp_timezone':
            case 'text_date':
                $args['type'] = $type;
                $args = wp_parse_args($parameters, array(
                    'date_format'       => 'l jS \of F Y'
                ));
                break;
            case 'taxonomy_radio_inline':
            case 'taxonomy_radio_hierarchical':
            case 'taxonomy_multicheck':
            case 'taxonomy_multicheck_inline':
            case 'taxonomy_multicheck_hierarchical':
            case 'taxonomy_radio':
            case 'taxonomy_select':
                $args['type'] = $type;
                $args = wp_parse_args($parameters, array(
                    'remove_default'    => true
                ));
                break;
            case 'taxonomy':
                $args['type'] = 'taxonomy_select';
                $args = wp_parse_args($parameters, array(
                    'remove_default'    => true
                ));
                break;
            case 'text_time':
            case 'time_picker':
                $args['type'] = 'text_time';
                break;
            case 'text_url':
            case 'url':
                $args['type'] = 'text_url';
                $args = wp_parse_args($parameters, array(
                    'protocols' => array( 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet' )
                ));
                break;
            case 'gallery':
            case 'file_list':
                $args['type'] = 'file_list';
                $args = wp_parse_args($parameters, array(
                    'preview_size' => array(50, 50)
                ));
                break;
            case 'number':
            case 'password':
                $args['type'] = 'text';
                break;
            case 'email':
                $args['type'] = 'text_email';
                break;
            case 'select':
                $args['type'] = 'select';
                $args['options'] = isset($parameters['choices']) ? $parameters['choices'] : $parameters['options'];
                break;
            case 'true_false':
                $args['type'] = 'select';
                $args['options'] = array(
                    'none'  => __('None', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
                    'true'  => __('True', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
                    'false' => __('False', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
                );
                break;
            case 'colorpicker':
            case 'color_picker':
                $args['type'] = 'colorpicker';
                break;
            case 'acf_code_field':
                $args['type'] = 'textarea_code';
                break;
            default:
                $args['type'] = 'text';
                error_log("The field type \"" . $type . "\" for field " . $key . " is not available in CMB2 and its adapter. Defaulting to text field type.");
        }

        return $args;
    }

//    /**
//     * @since   2.0.0
//     * @version 2.0.0
//     *
//     * @param   $field
//     * @param   $operator
//     * @param   $value
//     *
//     * @return  bool
//     */
//    public static function compare($field, $operator, $value) {
//
//        if (is_numeric($value)) { $value = intval($value); }
//        if ($value === 'false') { $value = false; }
//        if ($value === 'true') { $value = true; }
//
//        switch ($operator) {
//            case '==':
//                $result = $field == $value ? true : false;
//                break;
//            case '===':
//                $result = $field === $value ? true : false;
//                break;
//            case '!=':
//                $result = $field != $value ? true : false;
//                break;
//            case '<>':
//                $result = $field <> $value ? true : false;
//                break;
//            case '!==':
//                $result = $field !== $value ? true : false;
//                break;
//            case '<':
//                $result = $field < $value ? true : false;
//                break;
//            case '>':
//                $result = $field > $value ? true : false;
//                break;
//            case '<=':
//                $result = $field <= $value ? true : false;
//                break;
//            case '>=':
//                $result = $field >= $value ? true : false;
//                break;
//            default:
//                $result = true;
//        }
//        return $result;
//    }
//
//    /**
//     * @since   2.0.0
//     * @version 2.0.0
//     *
//     * @return  bool   True
//     */
//    public static function return_true() {
//        return true;
//    }
//
//    /**
//     * @since   2.0.0
//     * @version 2.0.0
//     *
//     * @return  bool   True
//     */
//    public static function return_false() {
//        return false;
//    }

    //endregion
} DWS_CMB2_Adapter::maybe_initialize_singleton('gm8ugh874hngf87gbcu');