<?php

namespace Deep_Web_Solutions\Helpers;

if (!defined('ABSPATH')) { exit; }

/**
 * A way to create a Generator object which can be rewinded to iterate over multiple times.
 *
 * @see     https://github.com/JeroenDeDauw/RewindableGenerator
 *
 * @since   2.0.1
 * @version 2.0.1
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 */
final class DWS_RewindableGenerator implements \Iterator {
    //region FIELDS AND CONSTANTS

    /**
     * @since   2.0.1
     * @version 2.0.1
     *
     * @var     callable
     */
    private $generatorFunction;

    /**
     * @since   2.0.1
     * @version 2.0.1
     *
     * @var     \Generator
     */
    private $generator;

    /**
     * @since   2.0.1
     * @version 2.0.1
     *
     * @var     callable|null
     */
    private $onRewind;

    //endregion

    //region INHERITED FUNCTIONS

    /**
     * DWS_RewindableGenerator constructor.
     *
     * @param   callable    $generatorConstructionFunction
     * @param   callable|null   $onRewind
     */
    public function __construct(callable $generatorConstructionFunction, callable $onRewind = null) {
        $this->generatorFunction = $generatorConstructionFunction;
        $this->onRewind = $onRewind;
        $this->generateGenerator();
    }

    //endregion

    //region METHODS

    /**
     * Return the current element.
     *
     * @since   2.0.1
     * @version 2.0.1
     *
     * @see     Iterator::current
     * @link    http://php.net/manual/en/iterator.current.php
     *
     * @return  mixed
     */
    public function current() {
        return $this->generator->current();
    }

    /**
     * Move forward to next element.
     *
     * @since   2.0.1
     * @version 2.0.1
     *
     * @see     Iterator::next
     * @link    http://php.net/manual/en/iterator.next.php
     */
    public function next() {
        $this->generator->next();
    }

    /**
     * Return the key of the current element.
     *
     * @since   2.0.1
     * @version 2.0.1
     *
     * @see     Iterator::key
     * @link    http://php.net/manual/en/iterator.key.php
     *
     * @return  mixed   Scalar on success, or null on failure.
     */
    public function key() {
        return $this->generator->key();
    }

    /**
     * Checks if current position is valid.
     *
     * @since   2.0.1
     * @version 2.0.1
     *
     * @see     Iterator::rewind
     * @link    http://php.net/manual/en/iterator.valid.php
     *
     * @return  boolean
     */
    public function valid() {
        return $this->generator->valid();
    }

    /**
     * Rewind the Iterator to the first element.
     *
     * @since   2.0.1
     * @version 2.0.1
     *
     * @see     Iterator::rewind
     * @link    http://php.net/manual/en/iterator.rewind.php
     */
    public function rewind() {
        $this->generateGenerator();

        if ( is_callable( $this->onRewind ) ) {
            call_user_func( $this->onRewind );
        }
        if ( defined( 'HHVM_VERSION' ) ) {
            $this->generator->next();
        }
    }

    /**
     * Sets a callable that gets invoked with 0 arguments after the iterator was rewinded.
     * If a callable has been set already, an exception will be thrown.
     *
     * @since   2.0.1
     * @version 2.0.1
     *
     * @param   callable    $onRewind
     *
     * @throws  \InvalidArgumentException
     */
    public function onRewind( callable $onRewind ) {
        if ( $this->onRewind !== null ) {
            throw new \InvalidArgumentException( 'Can only bind a onRewind handler once' );
        }

        $this->onRewind = $onRewind;
    }

    //endregion

    //region HELPERS

    /**
     * @since   2.0.1
     * @version 2.0.1
     */
    private function generateGenerator() {
        $this->generator = call_user_func( $this->generatorFunction );
        if ( !( $this->generator instanceof \Generator ) ) {
            throw new \InvalidArgumentException( 'The callable needs to return a Generator' );
        }
    }

    //endregion
}