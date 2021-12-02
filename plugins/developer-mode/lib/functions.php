<?php
/**
 * Handle plugin updates
 */
function jwdm_handle_update()
{
	$db_version = get_option('jwdm_version');
	
	if (!$db_version || version_compare($db_version, '0.4.1') < 0) {
		$role = get_role('developer');
		$role->add_cap('administrator', true);
		
		$developerusers_query = new WP_User_Query(array('role' => 'developer'));
		
		foreach ($developerusers_query->results as $index => $user) {
			clean_user_cache($user);
		}
	}
	
	if ($db_version != JWDM_VERSION) {
		update_option('jwdm_version', JWDM_VERSION);
	}
}

// Actions
add_action('init', 'jwdm_handle_update');

if (!function_exists('jwdm_maybe_disable_adminbar_frontend')) {
	/**
	 * Disable the admin bar if that option is set
	 */
	function jwdm_maybe_disable_adminbar_frontend()
	{
		$options = get_option('jwdm_options');
		
		if ( !current_user_can( 'view_developer_content' ) && !empty( $options['disable_adminbar_frontend'] ) ) {
			show_admin_bar(false);
		}
	}
}

// Actions
add_action('plugins_loaded', 'jwdm_maybe_disable_adminbar_frontend');
?>