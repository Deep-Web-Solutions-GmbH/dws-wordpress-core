<?php

namespace Deep_Web_Solutions\Core;
if (!defined('ABSPATH')) { exit; }

/**
 * Provides a more friendly interface to working with WordPress crons.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 *
 * @see     DWS_Root
 */
final class DWS_WordPress_Cron extends DWS_Root {
	//region FIELDS AND CONSTANTS

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string      MINUTES_5   The slug of the DWS 5 minute cron interval.
	 */
	const MINUTES_5 = 'dws_minutes_5';
	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string      MINUTES_5   The slug of the DWS 10 minute cron interval.
	 */
	const MINUTES_10 = 'dws_minutes_10';
	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string      MINUTES_5   The slug of the DWS 15 minute cron interval.
	 */
	const MINUTES_15 = 'dws_minutes_15';
	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string      MINUTES_5   The slug of the DWS 30 minute cron interval.
	 */
	const MINUTES_30 = 'dws_minutes_30';
	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string      MINUTES_5   The slug of the DWS 1 hour cron interval.
	 */
	const HOURS_1 = 'dws_hours_1';
	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string      MINUTES_5   The slug of the DWS 2 hours cron interval.
	 */
	const HOURS_2 = 'dws_hours_2';
	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string      MINUTES_5   The slug of the DWS 3 hours cron interval.
	 */
	const HOURS_3 = 'dws_hours_3';
	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string      MINUTES_5   The slug of the DWS 4 hours cron interval.
	 */
	const HOURS_4 = 'dws_hours_4';
	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string      MINUTES_5   The slug of the DWS 6 hours cron interval.
	 */
	const HOURS_6 = 'dws_hours_6';

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  private
	 * @var     array       $intervals  List of all WP cron intervals that this plugin registers.
	 */
	private static $intervals;

	//endregion

	//region INHERITED FUNCTIONS

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Root::init()
	 */
	public function init() {
		self::$intervals = array(
			self::MINUTES_5     => array('interval' => 5*60,    'display' => sprintf(__('Every %s minutes', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN), 5)),
			self::MINUTES_10    => array('interval' => 10*60,   'display' => sprintf(__('Every %s minutes', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN), 10)),
			self::MINUTES_15    => array('interval' => 15*60,   'display' => sprintf(__('Every %s minutes', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN), 15)),
			self::MINUTES_30    => array('interval' => 30*60,   'display' => sprintf(__('Every %s minutes', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN), 30)),
			self::HOURS_1       => array('interval' => 1*60*60, 'display' => sprintf(__('Every %s hours', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN), 1)),
			self::HOURS_2       => array('interval' => 2*60*60, 'display' => sprintf(__('Every %s hours', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN), 2)),
			self::HOURS_3       => array('interval' => 3*60*60, 'display' => sprintf(__('Every %s hours', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN), 3)),
			self::HOURS_4       => array('interval' => 4*60*60, 'display' => sprintf(__('Every %s hours', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN), 4)),
			self::HOURS_6       => array('interval' => 6*60*60, 'display' => sprintf(__('Every %s hours', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN), 6)),
		);
	}

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DWS_Root::define_hooks()
	 *
	 * @param   DWS_WordPress_Loader    $loader
	 */
	protected function define_hooks( $loader ) {
		$loader->add_filter('cron_schedules', $this, 'register_new_cron_schedules');
	}

	//endregion

	//region COMPATIBILITY LOGIC

	/**
	 * Registers this plugins cron schedules with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $intervals  The current intervals that WP supports for cron actions.
	 *
	 * @return  array   The list of intervals that WP supports for cron actions including the ones from this plugin.
	 */
	public function register_new_cron_schedules($intervals) {
		return array_merge(self::$intervals, $intervals);
	}

	//endregion

	//region HELPERS

	/**
	 * Third-parties can use this helper to register their own custom cron intervals before
	 * they get registered with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $slug           The slug of the new interval.
	 * @param   int         $interval       How long, in seconds, the interval is.
	 * @param   string|bool $name           The friendly name of the interval. If not given, the slug will be used.
	 */
	public static function add_interval($slug, $interval, $name = false) {
		if (empty($slug)) {
			error_log('Could not add interval with empty slug to cron schedules!');
			return;
		} else if (!is_integer($interval) || $interval < 60) {
			error_log("Invalid interval value for interval with slug $slug.");
			return;
		}

		$name = empty($name) ? $slug : $name;
		self::$intervals[$slug] = array('interval' => $interval, 'display' => $name);
	}

	/**
	 * Third-parties can use this helper to register their own cron events.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string    $hook         The action that should be run on this cron.
	 * @param   string    $recurrence   The interval of how often the cron should be run.
	 * @param   int|bool  $timestamp    When the first occurrence of the cron should be.
	 * @param   array     $args         Arguments to pass to the hook function(s).
	 */
	public static function schedule_event($hook, $recurrence = 'daily', $timestamp = false, $args = array()) {
		if (wp_next_scheduled($hook)) { return; }

		// if the timestamp is not given, start running it at midnight
		$timestamp = is_integer($timestamp)
			? $timestamp
			: strtotime(sprintf('today midnight %s', get_option('timezone_string')));

		wp_schedule_event($timestamp, $recurrence, $hook, $args);
	}

	//endregion
}