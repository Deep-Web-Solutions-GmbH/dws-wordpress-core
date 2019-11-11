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
        if( !function_exists('acf_add_options_sub_page') || ( empty($parent['menu_slug']) && empty($parameters['parent_slug']) ) || empty($parameters['menu_title']) || empty($parameters['menu_slug']) ) { return null; }

        return acf_add_options_sub_page(array(
                'page_title'        => isset($parameters['page_title']) ? $parameters['page_title'] : $parameters['menu_title'],
                'menu_title'        => $parameters['menu_title'],
                'menu_slug'         => $parameters['menu_slug'],
                'capability'        => isset($parameters['capability']) ? $parameters['capability'] : '',
                'position'          => isset($parameters['position']) ? $parameters['position'] : '',
                'parent_slug'       => isset($parameters['parent_slug']) ? $parameters['parent_slug'] : $parent['menu_slug'],
                'icon_url'          => isset($parameters['icon_url']) ? $parameters['icon_url'] : '',
                'redirect'          => isset($parameters['redirect']) ? $parameters['redirect'] : false,
                'post_id'           => isset($parameters['post_id']) ? $parameters['post_id'] : '',
                'autoload'          => isset($parameters['autoload']) ? $parameters['autoload'] : false,
                'update_button'     => isset($parameters['update_button']) ? $parameters['update_button'] : '',
                'updated_message'   => isset($parameters['updated_message']) ? $parameters['updated_message'] : ''
        ));
    }

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   array   $parameters
     * @param   string  $post_type
     * @param   string  $location
     */
    public static function register_settings_group($parameters, $post_type, $location) {
        if( !function_exists('acf_add_local_field_group') || empty($location) || empty($parameters['key']) || empty($parameters['title']) ) { return; }

        acf_add_local_field_group(array(
            'key' => strpos($parameters['key'], 'group_') === 0 ? $parameters['key'] : self::GROUP_KEY_PREFIX . $parameters['key'], // Must begin with 'group_'
            'title' => $parameters['title'],
            'fields' => isset($parameters['fields']) ? $parameters['fields'] : array(),
            'location' => array(
                array(
                    array(
                        'param' => isset($post_type) ? $post_type : 'options_page',
                        'operator' => '==',
                        'value' => $location
                    ),
                ),
            ),
            'menu_order' => isset($parameters['menu_order']) ? $parameters['menu_order'] : 0,
            'position' => isset($parameters['position']) ? $parameters['position'] : '', // Choices of 'acf_after_title', 'normal' or 'side'
            'style' => isset($parameters['style']) ? $parameters['style'] : '', // Choices of 'default' or 'seamless'
            'label_placement' => isset($parameters['label_placement']) ? $parameters['label_placement'] : '', // Choices of 'top' (Above fields) or 'left' (Beside fields)
            'instruction_placement' => isset($parameters['instruction_placement']) ? $parameters['instruction_placement'] : '', // Choices of 'label' (Below labels) or 'field' (Below fields)
            'hide_on_screen' => isset($parameters['hide_on_screen']) ? $parameters['hide_on_screen'] : ''
        ));
    }

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   string  $group_id
     * @param   array   $parameters
     * @param   null    $location
     */
    public static function register_settings_group_field($group_id, $parameters, $location = null){
        if( !function_exists('acf_add_local_field') || empty($parameters['key']) || empty($parameters['type']) || ( empty($group_id) && empty($parameters['parent']) ) ) { return; }

        acf_add_local_field(self::formatting_settings_field($group_id, $parameters));
    }

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   string  $parent_id
     * @param   array   $parameters
     * @param   null    $location
     */
    public static function register_settings_field($parent_id, $parameters, $location = null) {
        // TODO: Check if the string of each case is the same as the one acf uses

        if( !function_exists('acf_add_local_field') || empty($parameters['key']) || empty($parameters['type']) || ( empty($parent_id) && empty($parameters['parent']) ) ) { return; }

        switch($parameters['type']){
            case 'text':
                acf_add_local_field(array_merge(self::formatting_settings_field($parent_id, $parameters), array(
                    'placeholder'   => isset($parameters['placeholder']) ? $parameters['placeholder'] : '',
                    'prepend'       => isset($parameters['prepend']) ? $parameters['prepend'] : '',
                    'append'        => isset($parameters['append']) ? $parameters['append'] : '',
                    'maxlength'     => isset($parameters['maxlength']) ? $parameters['maxlength'] : '',
                    'readonly'      => isset($parameters['readonly']) ? $parameters['readonly'] : 0,
                    'disabled'      => isset($parameters['disabled']) ? $parameters['disabled'] : 0
                )));
                break;
            case 'textarea':
                acf_add_local_field(array_merge(self::formatting_settings_field($parent_id, $parameters), array(
                    'placeholder'   => isset($parameters['placeholder']) ? $parameters['placeholder'] : '',
                    'maxlength'     => isset($parameters['maxlength']) ? $parameters['maxlength'] : '',
                    'rows'          => isset($parameters['rows']) ? $parameters['rows'] : '',
                    'new_lines'     => isset($parameters['new_lines']) ? $parameters['new_lines'] : 'wpautop', // Choices of 'wpautop' (Automatically add paragraphs), 'br' (Automatically add <br>) or '' (No Formatting)
                    'readonly'      => isset($parameters['readonly']) ? $parameters['readonly'] : 0,
                    'disabled'      => isset($parameters['disabled']) ? $parameters['disabled'] : 0
                )));
                break;
            case 'number':
                acf_add_local_field(array_merge(self::formatting_settings_field($parent_id, $parameters), array(
                    'placeholder'   => isset($parameters['placeholder']) ? $parameters['placeholder'] : '',
                    'prepend'       => isset($parameters['prepend']) ? $parameters['prepend'] : '',
                    'append'        => isset($parameters['append']) ? $parameters['append'] : '',
                    'min'           => isset($parameters['min']) ? $parameters['min'] : '',
                    'max'           => isset($parameters['max']) ? $parameters['max'] : '',
                    'step'          => isset($parameters['step']) ? $parameters['step'] : ''
                )));
                break;
            case 'password':
            case 'email':
                acf_add_local_field(array_merge(self::formatting_settings_field($parent_id, $parameters), array(
                    'placeholder'   => isset($parameters['placeholder']) ? $parameters['placeholder'] : '',
                    'prepend'       => isset($parameters['prepend']) ? $parameters['prepend'] : '',
                    'append'        => isset($parameters['append']) ? $parameters['append'] : ''
                )));
                break;
            case 'url':
                acf_add_local_field(array_merge(self::formatting_settings_field($parent_id, $parameters), array(
                    'placeholder'   => isset($parameters['placeholder']) ? $parameters['placeholder'] : ''
                )));
                break;
            case 'wysiwyg':
                acf_add_local_field(array_merge(self::formatting_settings_field($parent_id, $parameters), array(
                    'tabs'          => isset($parameters['tabs']) ? $parameters['tabs'] : 'all', // Choices of 'all' (Visual & Text), 'visual' (Visual Only) or text (Text Only)
                    'toolbar'       => isset($parameters['toolbar']) ? $parameters['toolbar'] : 'full', // Choices of 'full' (Full), 'basic' (Basic) or a custom toolbar
                    'media_upload'  => isset($parameters['media_upload']) ? $parameters['media_upload'] : 1
                )));
                break;
            case 'oembed':
                acf_add_local_field(array_merge(self::formatting_settings_field($parent_id, $parameters), array(
                    'width'     => isset($parameters['width']) ? $parameters['width'] : '',
                    'height'    => isset($parameters['height']) ? $parameters['height'] : ''
                )));
                break;
            case 'image':
                acf_add_local_field(array_merge(self::formatting_settings_field($parent_id, $parameters), array(
                    'return_format' => isset($parameters['return_format']) ? $parameters['return_format'] : 'array', // Choices of 'array' (Image Array), 'url' (Image URL), 'id' (Image ID)
                    'preview_size'  => isset($parameters['preview_size']) ? $parameters['preview_size'] : 'thumbnail',
                    'library'       => isset($parameters['library']) ? $parameters['library'] : 'all', // Choices of 'all' (All Images) or 'uploadedTo' (Uploaded to post)
                    'min_width'     => isset($parameters['min_width']) ? $parameters['min_width'] : 0,
                    'min_height'    => isset($parameters['min_height']) ? $parameters['min_height'] : 0,
                    'min_size'      => isset($parameters['min_size']) ? $parameters['min_size'] : 0,
                    'max_width'     => isset($parameters['max_width']) ? $parameters['max_width'] : 0,
                    'max_height'    => isset($parameters['max_height']) ? $parameters['max_height'] : 0,
                    'max_size'      => isset($parameters['']) ? $parameters[''] : 0,
                    'mime_types'    => isset($parameters['mime_types']) ? $parameters['mime_types'] : ''
                )));
                break;
            case 'file':
                acf_add_local_field(array_merge(self::formatting_settings_field($parent_id, $parameters), array(
                    'return_format' => isset($parameters['return_format']) ? $parameters['return_format'] : 'array', // Choices of 'array' (File Array), 'url' (File URL) or 'id' (File ID)
                    'preview_size'  => isset($parameters['preview_size']) ? $parameters['preview_size'] : 'thumbnail',
                    'library'       => isset($parameters['library']) ? $parameters['library'] : 'all', // Choices of 'all' (All Images) or 'uploadedTo' (Uploaded to post)
                    'min_size'      => isset($parameters['min_size']) ? $parameters['min_size'] : 0,
                    'max_size'      => isset($parameters['']) ? $parameters[''] : 0,
                    'mime_types'    => isset($parameters['mime_types']) ? $parameters['mime_types'] : ''
                )));
                break;
            case 'gallery':
                acf_add_local_field(array_merge(self::formatting_settings_field($parent_id, $parameters), array(
                    'min'           => isset($parameters['min']) ? $parameters['min'] : 0,
                    'max'           => isset($parameters['max']) ? $parameters['max'] : 0,
                    'preview_size'  => isset($parameters['preview_size']) ? $parameters['preview_size'] : 'thumbnail',
                    'library'       => isset($parameters['library']) ? $parameters['library'] : 'all', // Choices of 'all' (All Images) or 'uploadedTo' (Uploaded to post)
                    'min_width'     => isset($parameters['min_width']) ? $parameters['min_width'] : 0,
                    'min_height'    => isset($parameters['min_height']) ? $parameters['min_height'] : 0,
                    'min_size'      => isset($parameters['min_size']) ? $parameters['min_size'] : 0,
                    'max_width'     => isset($parameters['max_width']) ? $parameters['max_width'] : 0,
                    'max_height'    => isset($parameters['max_height']) ? $parameters['max_height'] : 0,
                    'max_size'      => isset($parameters['']) ? $parameters[''] : 0,
                    'mime_types'    => isset($parameters['mime_types']) ? $parameters['mime_types'] : ''
                )));
                break;
            case 'select':
                acf_add_local_field(array_merge(self::formatting_settings_field($parent_id, $parameters), array(
                    'choices'       => isset($parameters['']) ? $parameters[''] : array(),
                    'allow_null'    => isset($parameters['']) ? $parameters[''] : 0,
                    'multiple'      => isset($parameters['']) ? $parameters[''] : 0,
                    'ui'            => isset($parameters['']) ? $parameters[''] : 0,
                    'ajax'          => isset($parameters['']) ? $parameters[''] : 0,
                    'placeholder'   => isset($parameters['placeholder']) ? $parameters['placeholder'] : ''
                )));
                break;
            case 'checkbox':
                acf_add_local_field(array_merge(self::formatting_settings_field($parent_id, $parameters), array(
                    'choices'       => isset($parameters['choices']) ? $parameters['choices'] : array(),
                    'layout'        => isset($parameters['layout']) ? $parameters['layout'] : 'vertical', // Choices of 'vertical' or 'horizontal'
                    'allow_custom'  => isset($parameters['allow_custom']) ? $parameters['allow_custom'] : false,
                    'save_custom'   => isset($parameters['save_custom']) ? $parameters['save_custom'] : false,
                    'toggle'        => isset($parameters['toggle']) ? $parameters['toggle'] : false,
                    'return_format' => isset($parameters['return_format']) ? $parameters['return_format'] : 'value' // Choices of 'value', 'label' or 'array'
                )));
                break;
            case 'radio':
                acf_add_local_field(array_merge(self::formatting_settings_field($parent_id, $parameters), array(
                    'choices'           => isset($parameters['choices']) ? $parameters['choices'] : array(),
                    'other_choice'      => isset($parameters['other_choice']) ? $parameters['other_choice'] : 0,
                    'save_other_choice' => isset($parameters['save_other_choice']) ? $parameters['save_other_choice'] : 0,
                    'layout'            => isset($parameters['layout']) ? $parameters['layout'] : 'vertical' // Choices of 'vertical' or 'horizontal'
                )));
                break;
            case 'true_false':
                acf_add_local_field(array_merge(self::formatting_settings_field($parent_id, $parameters), array(
                    'message'   => isset($parameters['message']) ? $parameters['message'] : 0,
                )));
                break;
            case 'post_object':
                acf_add_local_field(array_merge(self::formatting_settings_field($parent_id, $parameters), array(
                    'post_type'     => isset($parameters['post_type']) ? $parameters['post_type'] : '',
                    'taxonomy'      => isset($parameters['taxonomy']) ? $parameters['taxonomy'] : '',
                    'allow_null'    => isset($parameters['allow_null']) ? $parameters['allow_null'] : 0,
                    'multiple'      => isset($parameters['multiple']) ? $parameters['multiple'] : 0,
                    'return_format' => isset($parameters['return_format']) ? $parameters['return_format'] : 'object', // Choices of 'object' (Post object) or 'id' (Post ID)
                )));
                break;
            case 'page_link':
                acf_add_local_field(array_merge(self::formatting_settings_field($parent_id, $parameters), array(
                    'post_type'     => isset($parameters['post_type']) ? $parameters['post_type'] : '',
                    'taxonomy'      => isset($parameters['taxonomy']) ? $parameters['taxonomy'] : '',
                    'allow_null'    => isset($parameters['allow_null']) ? $parameters['allow_null'] : 0,
                    'multiple'      => isset($parameters['multiple']) ? $parameters['multiple'] : 0
                )));
                break;
            case 'relationship':
                acf_add_local_field(array_merge(self::formatting_settings_field($parent_id, $parameters), array(
                    'post_type'     => isset($parameters['post_type']) ? $parameters['post_type'] : '',
                    'taxonomy'      => isset($parameters['taxonomy']) ? $parameters['taxonomy'] : '',
                    'filters'       => isset($parameters['filters']) ? $parameters['filters'] : array('search', 'post_type', 'taxonomy'), // Choices of 'search' (Search input), 'post_type' (Post type select) and 'taxonomy' (Taxonomy select)
                    'elements'      => isset($parameters['elements']) ? $parameters['elements'] : array(), // Choices of 'featured_image' (Featured image icon)
                    'min'           => isset($parameters['min']) ? $parameters['min'] : 0,
                    'max'           => isset($parameters['max']) ? $parameters['max'] : 0,
                    'return_format' => isset($parameters['return_format']) ? $parameters['return_format'] : 'object', // Choices of 'object' (Post object) or 'id' (Post ID)
                )));
                break;
            case 'taxonomy':
                acf_add_local_field(array_merge(self::formatting_settings_field($parent_id, $parameters), array(
                    'taxonomy'          => isset($parameters['taxonomy']) ? $parameters['taxonomy'] : '',
                    'field_type'        => isset($parameters['field_type']) ? $parameters['field_type'] : 'checkbox', // Choices of 'checkbox' (Checkbox inputs), 'multi_select' (Select field - multiple), 'radio' (Radio inputs) or 'select' (Select field)
                    'allow_null'        => isset($parameters['']) ? $parameters[''] : 0,
                    'load_save_terms'   => isset($parameters['']) ? $parameters[''] : 0,
                    'return_format'		=> isset($parameters['']) ? $parameters[''] : 'id', // Choices of 'object' (Term object) or 'id' (Term ID)
                    'add_term'			=> isset($parameters['']) ? $parameters[''] : 1
                )));
                break;
            case 'user':
                acf_add_local_field(array_merge(self::formatting_settings_field($parent_id, $parameters), array(
                    'role'          => isset($parameters['role']) ? $parameters['role'] : array(),
                    'allow_null'    => isset($parameters['allow_null']) ? $parameters['allow_null'] : 0,
                    'multiple'      => isset($parameters['multiple']) ? $parameters['multiple'] : 0
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
        if( !function_exists('get_field') || empty($field_id) ) { return; }

        return get_field($field_id);
    }

    //endregion

    //region HELPERS

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   string  $location_id
     * @param   array   $parameters
     *
     * @return  array   Formatted array for registering generic ACF field
     */
    public static function formatting_settings_field($location_id, $parameters) {
        return array(
            'key'               => strpos($parameters['key'], 'field_') === 0 ? $parameters['key'] : self::FIELD_KEY_PREFIX . $parameters['key'],  // Must begin with 'field_'
            'label'             => isset($parameters['label']) ? $parameters['label'] : '',
            'name'              => isset($parameters['name']) ? $parameters['name'] : '',
            'type'              => $parameters['type'],
            'instructions'      => isset($parameters['instructions']) ? $parameters['instructions'] : '',
            'required'          => isset($parameters['required']) ? $parameters['required'] : 0,
            'conditional_logic' => isset($parameters['conditional_logic']) ? $parameters['conditional_logic'] : 0, // Best to use the ACF UI and export to understand the array structure.
            'wrapper'           => array (
                'width' => isset($parameters['wrapper']['width']) ? $parameters['wrapper']['width'] : '',
                'class' => isset($parameters['wrapper']['class']) ? $parameters['wrapper']['class'] : '',
                'id'    => isset($parameters['wrapper']['id']) ? $parameters['wrapper']['id'] : ''
            ),
            'default_value'     => isset($parameters['default_value']) ? $parameters['default_value'] : '',
            'parent'            => isset($parameters['parent']) ? $parameters['parent'] : $location_id
        );
    }

    //endregion
} DWS_ACFPro_Adapter::maybe_initialize_singleton('rgfjn87uy4578yhbf67');