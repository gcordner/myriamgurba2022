<?php
/**
 * Roles class
 */
class JWDM_Roles
{

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->handle_roles();
		
		// Filters
		add_filter( 'user_has_cap', array( &$this, 'filter_user_has_cap' ), 10, 3 );
	}
	
	/************************
	 * Main functionality
	 ***********************/
	
	/**
	 * Add the developer user role and capabilities if it doesn't exist yet
	 */
	public function handle_roles()
	{
		// Helpers
		require_once JWDM_LIBRARY_PATH . '/helper/roles.php';
		
		// Only try to add the role if it doesn't exist already
		if ( !JWDM_Helper_Roles::role_exists( 'developer' ) ) {
			$roles = JWDM_Helper_Roles::get_roles();
			
			// Get all available capabilities
			$capabilities = array();
			
			foreach ( $roles as $index => $role ) {
				$capabilities = array_merge($capabilities, $role['capabilities']);
			}
			
			// Enable all capabilities
			$capabilities = array_fill_keys( array_keys( $capabilities ), true );
			
			// Add the user role
			$role_developer = add_role( 'developer', 'Developer', $capabilities );
			$role_developer->add_cap( 'view_developer_menu_items', true );
			$role_developer->add_cap( 'view_developer_plugins', true );
			$role_developer->add_cap( 'developer_updates', true );
			$role_developer->add_cap( 'view_developer_content', true );
			$role_developer->add_cap( 'administrator', true );
		}
		else if ( JWDM_Helper_Roles::role_exists( 'administrator' ) ) {
			$role_admin = get_role( 'administrator' );
			$role_developer = get_role( 'developer' );
			
			foreach ( $role_admin->capabilities as $index => $cap ) {
				if ( $cap && !isset( $role_developer->capabilities[ $index ] ) ) {
					$role_developer->add_cap( $index, true );
				}
			}
		}
	}
	
	/************************
	 * Filters
	 ***********************/
	
	/**
	 * Filter: user_has_cap
	 */
	public function filter_user_has_cap($capabilities, $cap, $name)
	{
		if ( empty( $capabilities['developer_updates'] ) ) {
			if ( !empty( $capabilities['update_core'] ) || !empty( $capabilities['update_plugins'] ) || !empty( $capabilities['update_themes'] ) ) {
				$options['general'] = get_option('jwdm_options');
			}
			
			if ( !empty( $options['general']['disable_core_update'] ) && !empty( $capabilities['update_core'] ) ) {
				$capabilities['update_core'] = false;
			}
			
			if ( !empty( $options['general']['disable_plugin_update'] ) && !empty( $capabilities['update_plugins'] ) ) {
				$capabilities['update_plugins'] = false;
			}
			
			if ( !empty( $options['general']['disable_theme_update'] ) && !empty( $capabilities['update_themes'] ) ) {
				$capabilities['update_themes'] = false;
			}
		}
		
		return $capabilities;
	}

}

new JWDM_Roles();
?>