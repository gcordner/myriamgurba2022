<?php

// Load main class
require_once(dirname(dirname(__FILE__)).'/core/plugin.php');

/**
 * WP Link Status Pro Core Plugin class
 *
 * @package WP Link Status Pro
 * @subpackage WP Link Status Pro Core
 */
class WPLNST_Core_Pro_Plugin extends WPLNST_Core_Plugin {



	/**
	 * URL to the tools section
	 */
	public static function get_url_tools_url() {
		return self::get_url_scans().'-tools-url';
	}



}