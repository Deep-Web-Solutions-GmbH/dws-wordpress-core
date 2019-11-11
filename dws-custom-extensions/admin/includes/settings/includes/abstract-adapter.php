<?php

namespace Deep_Web_Solutions\Admin\Settings;
use Deep_Web_Solutions\Admin\DWS_Settings;
use Deep_Web_Solutions\Core\DWS_Functionality_Template;

if (!defined('ABSPATH')) { exit; }

/**
 * Template for encapsulating some of the most often required abilities of a settings framework.
 *
 * @since   2.0.0
 * @version 2.0.0
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

    //endregion

    //region INHERITED FUNCTIONS

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @see     DWS_Functionality_Template::define_functionality_hooks()
     *
     * @param   \Deep_Web_Solutions\Core\DWS_WordPress_Loader   $loader
     */
    protected function define_functionality_hooks($loader) {
        $loader->add_action('init', $this, 'set_fields', PHP_INT_MIN);
        $loader->add_action('init', $this, 'trigger_init_ready', PHP_INT_MIN + 1);
        $loader->add_filter(DWS_Settings::get_hook_name('framework_adapter'), $this, 'maybe_return_instance', 10, 2);
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
     * Register an action on the init hook of the framework to set everything up.
     *
     * @since   2.0.0
     * @version 2.0.0
     */
    public final function trigger_init_ready() {
        $selected_framework = DWS_Settings::get_option_framework_slug();
        if ($selected_framework !== $this->framework_slug) { return; }

        add_action($this->init_hook, function() {
            do_action(DWS_Settings::get_hook_name('init'));
        });
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