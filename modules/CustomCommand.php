<?php

namespace teligro;

if ( ! defined( 'ABSPATH' ) )
	exit;

class CustomCommand extends Teligro {
	public static $instance = null;

	public function __construct() {
		parent::__construct();
	}

	function settings() {

	}

	/**
	 * Returns an instance of class
	 * @return CustomCommand
	 */
	static function getInstance() {
		if ( self::$instance == null )
			self::$instance = new CustomCommand();

		return self::$instance;
	}
}