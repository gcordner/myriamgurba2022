<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
	die();
}

// Change the role of developer users to administrator
$users = get_users(array(
	'role' => 'developer'
));

foreach ($users as $index => $user) {
	wp_update_user(array(
		'ID' => $user->ID,
		'role' => 'administrator'
	));
}

// Remove the developer role
remove_role('developer');

// Remove options
delete_option('jwdm_plugins_hidden');
delete_option('jwdm_menuitems_hidden');
delete_option('jwdm_options');
?>