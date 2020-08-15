<?php
/**
 * The abstract instance
 *
 * @since      	1.0
 */
namespace teligro;

defined( 'ABSPATH' ) || exit;

abstract class Instance
{
    /**
     * Get the current instance object. To be inherited.
     *
     * @since 1.0
     * @access public
     */
    public static function get_instance()
    {
        if ( ! isset( static::$_instance ) ) {
            static::$_instance = new static();
        }

        return static::$_instance;
    }
}