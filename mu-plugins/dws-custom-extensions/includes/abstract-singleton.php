<?php

namespace Deep_Web_Solutions\Core;
if (!defined('ABSPATH')) { exit; }

/**
 * A template for a very powerful singleton class. Almost all the files inherit from this class
 * one way or another.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 */
abstract class DWS_Singleton {
	//region FIELDS

	/**
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  private
	 * @var     array       Maintains a list of all singleton instances created.
	 */
	private static $instances = array();

	//endregion

	//region MAGIC METHODS

	/**
	 * DWS_Singleton constructor. Children classes can overwrite this and perform custom actions.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $args   Array of all the arguments that the constructor of the end class needs.
	 */
	protected function __construct(...$args) { }

	/**
	 * Prevent cloning.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	private function __clone() { }

	/**
	 * Prevent serialization.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	private function __sleep() { }

	/**
	 * Prevent unserialization.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	private function __wakeup() { }

	//endregion

	//region METHODS

	/**
	 * Returns a singleton instance of the calling class.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  mixed   The instance of the calling class.
	 */
	public final static function get_instance() {
		self::maybe_initialize_singleton();
		return self::$instances[static::class];
	}

	/**
	 * If the singleton has not been initialized yet, this function instantiates it. Also allows passing
	 * of parameters.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public final static function maybe_initialize_singleton() {
		if (isset(self::$instances[static::class])) { return; }
		self::$instances[static::class] = new static(...func_get_args());
	}

	//endregion
}
