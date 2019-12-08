<?php

namespace Deep_Web_Solutions\Admin\Settings;
use Deep_Web_Solutions\Admin\DWS_Settings;
use Deep_Web_Solutions\Base\DWS_Functionality_Template;
use Deep_Web_Solutions\Core\DWS_Loader;

if (!defined('ABSPATH')) { exit; }

/**
 * Template for encapsulating some of the most often required abilities of a settings framework.
 *
 * @since   2.0.0
 * @version 2.0.4
 * @author  Fatine Tazi <f.tazi@deep-web-solutions.de>
 */
abstract class DWS_Adapter_Base extends DWS_Functionality_Template implements DWS_Adapter {
    //region FIELDS AND CONSTANTS

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @var     string  $framework_slug     The slug of the current framework.
     */
    protected $framework_slug;

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @var     string  $init_hook
     */
    protected $init_hook = 'init';

    /**
     * @since   2.0.4
     * @version 2.0.4
     *
     * @var     string
     */
    protected $update_field_hook = null;

    //endregion

    //region INHERITED FUNCTIONS

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @see     DWS_Functionality_Template::local_configure()
     */
    protected function local_configure() {
        parent::local_configure();
        $this->set_fields(); $this->trigger_custom_hooks();
        add_filter(DWS_Settings::get_hook_name('framework_adapter'), array($this, 'maybe_return_instance'), 10, 2);
    }

    /**
     * DWS_Adapter_Base constructor.
     *
     * @param   string          $functionality_id
     * @param   bool            $must_use
     * @param   string          $parent_functionality_id
     * @param   string          $options_parent_id
     * @param   bool|string     $functionality_description
     * @param   bool|string     $functionality_name
     *
     * @see     DWS_Functionality_Template::__construct
     */
    public function __construct($functionality_id, $must_use = true, $parent_functionality_id = '', $options_parent_id = '', $functionality_description = false, $functionality_name = false) {
        parent::__construct($functionality_id, $must_use, $parent_functionality_id, $options_parent_id, $functionality_description, $functionality_name);

        /** @var DWS_Loader $loader */
        $loader = DWS_Loader::get_instance();
        $loader->remove_action('muplugins_loaded', $this, 'configure_class');
        $loader->add_action('muplugins_loaded', $this, 'configure_class', 0); // this ensures that adapter load before everything else
    }

    //endregion

    //region METHODS

    /**
     * Children classes must define their framework slugs in here.
     *
     * @since   2.0.0
     * @version 2.0.0
     */
    public abstract function set_fields();

    /**
     * Register different actions on the framework specific hooks.
     *
     * @since   2.0.0
     * @version 2.0.4
     */
    private final function trigger_custom_hooks() {
        $selected_framework = DWS_Settings::get_settings_framework_slug();
        if ($selected_framework !== $this->framework_slug) { return; }

        add_action($this->init_hook, function() {
            do_action(DWS_Settings::get_hook_name('init'));
        });
        if (!is_null($this->update_field_hook)) {
            add_filter($this->update_field_hook, function($value, $post_id = false, $field = false, $original = false) {
                return apply_filters(DWS_Settings::get_hook_name('update-field'), $value, $post_id, $field, $original);
            }, 10, 4);
        }
    }

    /**
     * Maybe return the instance of the current class on filter.
     *
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   null|DWS_Adapter_Base   $instance
     * @param   string                  $framework_slug
     *
     * @return  null|DWS_Adapter_Base
     */
    public final function maybe_return_instance($instance, $framework_slug) {
        if ($instance !== null) { return $instance; }
        if ($framework_slug === $this->framework_slug) {
            return self::get_instance(sanitize_key('gh8g7h538grrefe_' . $framework_slug));
        }

        return $instance;
    }

    //endregion
}