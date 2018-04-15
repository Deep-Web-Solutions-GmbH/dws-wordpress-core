<?php

namespace Deep_Web_Solutions\Core;
if (!defined('ABSPATH')) { exit; }

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Singleton
 */
final class DWS_WordPress_Loader extends DWS_Singleton {
	//region FIELDS

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  private
	 * @var     array   $actions    The actions registered with WordPress to fire when the plugin loads.
	 */
	private $actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  private
	 * @var     array   $filters    The filters registered with WordPress to fire when the plugin loads.
	 */
	private $filters;

	//endregion

	//region MAGIC METHODS

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Singleton::__construct()
	 */
	protected function __construct() {
		$this->actions = array();
		$this->filters = array();
	}

	//endregion

	//region METHODS

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 *
	 * @param    string     $hook           The name of the WordPress action that is being registered.
	 * @param    object     $component      A reference to the instance of the object on which the action is defined.
	 * @param    string     $callback       The name of the function definition on the $component.
	 * @param    int        $priority       Optional. he priority at which the function should be fired. Default is 10.
	 * @param    int        $accepted_args  Optional. The number of arguments that should be passed to the $callback.
	 *                                      Default is 1.
	 */
	public function add_action($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
		$this->actions = $this->add($this->actions, $hook, $component, $callback, $priority, $accepted_args);
	}

	/**
	 * Remove an action from the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 *
	 * @param    string     $hook           The name of the WordPress action that is being registered.
	 * @param    object     $component      A reference to the instance of the object on which the action is defined.
	 * @param    string     $callback       The name of the function definition on the $component.
	 * @param    int        $priority       Optional. he priority at which the function should be fired. Default is 10.
	 * @param    int        $accepted_args  Optional. The number of arguments that should be passed to the $callback.
	 *                                      Default is 1.
	 */
	public function remove_action($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
		$this->actions = $this->remove($this->actions, $hook, $component, $callback, $priority, $accepted_args);
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $hook           The name of the WordPress filter that is being registered.
	 * @param   object  $component      A reference to the instance of the object on which the filter is defined.
	 * @param   string  $callback       The name of the function definition on the $component.
	 * @param   int     $priority       Optional. he priority at which the function should be fired. Default is 10.
	 * @param   int     $accepted_args  Optional. The number of arguments that should be passed to the $callback. Default
	 *                                  is 1
	 */
	public function add_filter($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
		$this->filters = $this->add($this->filters, $hook, $component, $callback, $priority, $accepted_args);
	}

	/**
	 * Remove a filter to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 *
	 * @param    string     $hook           The name of the WordPress filter that is being registered.
	 * @param    object     $component      A reference to the instance of the object on which the filter is defined.
	 * @param    string     $callback       The name of the function definition on the $component.
	 * @param    int        $priority       Optional. he priority at which the function should be fired. Default is 10.
	 * @param    int        $accepted_args  Optional. The number of arguments that should be passed to the $callback.
	 *                                      Default is 1
	 */
	public function remove_filter($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
		$this->filters = $this->remove($this->filters, $hook, $component, $callback, $priority, $accepted_args);
	}

	/**
	 * Registers a new shortcode with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $tag        The tag of the new shortcode.
	 * @param   object  $component  A reference to the instance of the object on which the shortcode is defined.
	 * @param   string  $callback   The name of the function definition on the $component.
	 */
	public function add_shortcode($tag, $component, $callback) {
		if (empty($component)) {
			add_shortcode($tag, $callback);
		} else {
			add_shortcode($tag, array($component, $callback));
		}
	}

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function run() {
		foreach ($this->filters as $hook) {
			if (empty($hook['component'])) {
				add_filter($hook['hook'], $hook['callback'], $hook['priority'], $hook['accepted_args']);
			} else {
				add_filter($hook['hook'], array($hook['component'], $hook['callback']), $hook['priority'], $hook['accepted_args']);
			}
		}
		foreach ($this->actions as $hook) {
			if (empty($hook['component'])) {
				add_action($hook['hook'], $hook['callback'], $hook['priority'], $hook['accepted_args']);
			} else {
				add_action($hook['hook'], array($hook['component'], $hook['callback']), $hook['priority'], $hook['accepted_args']);
			}
		}

		// these hooks have already been registered, so forget them
		$this->filters = array();
		$this->actions = array();
	}

	//endregion

	//region HELPERS

	/**
	 * A utility function that is used to register the actions and hooks into a single collection.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 *
	 * @access   private
	 *
	 * @param    array      $hooks          The collection of hooks that is being registered (that is, actions or filters).
	 * @param    string     $hook           The name of the WordPress filter that is being registered.
	 * @param    object     $component      A reference to the instance of the object on which the filter is defined.
	 * @param    string     $callback       The name of the function definition on the $component.
	 * @param    int        $priority       The priority at which the function should be fired.
	 * @param    int        $accepted_args  The number of arguments that should be passed to the $callback.
	 *
	 * @return   array                                  The collection of actions and filters registered with WordPress.
	 */
	private function add($hooks, $hook, $component, $callback, $priority, $accepted_args) {
		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args
		);
		return $hooks;
	}

	/**
	 * A utility function that is used to remove the actions and hooks from the single collection.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 *
	 * @access   private
	 *
	 * @param    array      $hooks          The collection of hooks that is being registered (that is, actions or filters).
	 * @param    string     $hook           The name of the WordPress filter that is being registered.
	 * @param    object     $component      A reference to the instance of the object on which the filter is defined.
	 * @param    string     $callback       The name of the function definition on the $component.
	 * @param    int        $priority       The priority at which the function should be fired.
	 * @param    int        $accepted_args  The number of arguments that should be passed to the $callback.
	 *
	 * @return   array                                  The collection of actions and filters registered with WordPress.
	 */
	private function remove($hooks, $hook, $component, $callback, $priority, $accepted_args) {
		foreach ($hooks as $index => $hook_info) {
			if ($hook_info['hook'] === $hook && $hook_info['component'] === $component && $hook_info['callback'] === $callback && $hook_info['priority'] === $priority && $hook_info['accepted_args'] === $accepted_args) {
				unset($hooks[$index]);
				break;
			}
		}

		return $hooks;
	}

	//endregion
}