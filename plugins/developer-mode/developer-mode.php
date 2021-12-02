<?php
/*
Plugin Name: Developer Mode
Description: Limit access to the WordPress admin panel for your clients. Block functionality like updating plugins and viewing menu items for administrators, while keeping all these options for the developer users. The developer mode plugin automatically adds a developer user role, allowing you to keep in control of the entire system while making sure your clients can only use what they need.
Version: 0.4.1.3
Author: Jesper van Engelen
Author URI: http://www.jepps.nl
License: GPLv2 or later
*/

// Plugin information
define('JWDM_VERSION', '0.4.1.3');

// Paths
define('JWDM_PATH', dirname(__FILE__));
define('JWDM_LIBRARY_PATH', JWDM_PATH . '/lib');
define('JWDM_URL', untrailingslashit(plugins_url('', __FILE__)));

// Library
require_once JWDM_LIBRARY_PATH . '/roles.php';
require_once JWDM_LIBRARY_PATH . '/functions.php';

if (is_admin()) {
	// Admin-specific library
	require_once JWDM_LIBRARY_PATH . '/admin.php';
	require_once JWDM_LIBRARY_PATH . '/adminmenu.php';
}

// Localization
load_plugin_textdomain('developermode', false, dirname(plugin_basename(__FILE__)) . '/languages/');
?>