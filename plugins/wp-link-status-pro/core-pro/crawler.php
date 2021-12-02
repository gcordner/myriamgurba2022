<?php

// Load main class
require_once(dirname(dirname(__FILE__)).'/core/crawler.php');

/**
 * WP Link Status Pro Core Crawler class
 *
 * @package WP Link Status Pro
 * @subpackage WP Link Status Pro Core
 */
class WPLNST_Core_Pro_Crawler extends WPLNST_Core_Crawler {



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Creates a singleton object
	 */
	public static function instantiate($args = null) {
		return self::get_instance(get_class(), $args);
	}



	// Override methods
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Start again the crawler
	 */
	protected function restart() {
		WPLNST_Core_Pro_Alive::run($this->scan->id, $this->scan->hash, $this->thread_id);
	}



	/**
	 * Calls alive activity method to check scans activity
	 */
	protected function activity() {
		WPLNST_Core_Pro_Alive::activity(true);
	}



}