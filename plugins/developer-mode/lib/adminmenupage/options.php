<?php
/**
 * Admin menu page: Options
 */
class JWDM_AdminMenuPage_Options
{

	/**
	 * Constructor
	 */
	public function __construct()
	{
		// Actions
		add_action('admin_menu', array(&$this, 'action_admin_menu'));
		add_action('admin_init', array(&$this, 'action_admin_init'));
	}
	
	/************************
	 * Actions
	 ***********************/
	
	/**
	 * Action: admin_menu
	 *
	 * Add menu pages
	 */
	public function action_admin_menu()
	{
		add_menu_page(__('Developer Mode Settings', 'developermode'), __('Developer Mode', 'developermode'), 'manage_options', 'developermode', array(&$this, 'page'));
	}
	
	/**
	 * Action: admin_init
	 */
	public function action_admin_init()
	{
		// Settings sections
		add_settings_section('jwdm_users', __('Users', 'developermode'), array(&$this, 'section_users'), 'developermode');
		add_settings_section('jwdm_updates', __('Updates', 'developermode'), array(&$this, 'section_updates'), 'developermode');
		add_settings_section('jwdm_adminbar', __('Admin bar', 'developermode'), array(&$this, 'section_adminbar'), 'developermode');
		
		// Settings fields
		add_settings_field('jwdm_prevent_developeruser_edit', __('Actively prevent developer user editing', 'developermode'), array(&$this, 'field_prevent_developeruser_edit'), 'developermode', 'jwdm_users');
		add_settings_field('jwdm_disable_core_update', __('Disable core update', 'developermode'), array(&$this, 'field_disable_core_update'), 'developermode', 'jwdm_updates');
		add_settings_field('jwdm_disable_plugin_update', __('Disable plugin update', 'developermode'), array(&$this, 'field_disable_plugin_update'), 'developermode', 'jwdm_updates');
		add_settings_field('jwdm_disable_theme_update', __('Disable theme update', 'developermode'), array(&$this, 'field_disable_theme_update'), 'developermode', 'jwdm_updates');
		add_settings_field('jwdm_disable_adminbar_frontend', __('Disable admin bar', 'developermode'), array(&$this, 'field_disable_adminbar_frontend'), 'developermode', 'jwdm_adminbar');
	}
	
	/************************
	 * Settings sections
	 ***********************/
	
	public function section_users() {}
	public function section_updates() {}
	public function section_adminbar() {}
	
	/************************
	 * Settings fields
	 ***********************/
	
	/**
	 * Field: Actively prevent developer user editing
	 */
	public function field_prevent_developeruser_edit()
	{
		$options = get_option('jwdm_options');
	?>
		<input type="checkbox" name="jwdm_options[prevent_developeruser_edit]" value="1" <?php checked( !empty( $options['prevent_developeruser_edit'] ) ); ?> />
		<span class="description">
			<?php _e('By checking this box, you can prevent anyone that does not have the developer role from creating, editing or deleting users with the developer role.', 'developermode'); ?>
			<br/>
			<?php _e('<strong>Plugin/theme developers only</strong>: To change these permissions per role, please leave this box unchecked and user the <code>editable_roles</code> filter.', 'developermode'); ?>
		</span>
	<?php
	}
	
	/**
	 * Field: Disable core update
	 */
	public function field_disable_core_update()
	{
		$options = get_option('jwdm_options');
	?>
		<input type="checkbox" name="jwdm_options[disable_core_update]" value="1" <?php checked( !empty( $options['disable_core_update'] ) ); ?> />
	<?php
	}
	
	/**
	 * Field: Disable plugin update
	 */
	public function field_disable_plugin_update()
	{
		$options = get_option('jwdm_options');
	?>
		<input type="checkbox" name="jwdm_options[disable_plugin_update]" value="1" <?php checked( !empty( $options['disable_plugin_update'] ) ); ?> />
	<?php
	}
	
	/**
	 * Field: Disable theme update
	 */
	public function field_disable_theme_update()
	{
		$options = get_option('jwdm_options');
	?>
		<input type="checkbox" name="jwdm_options[disable_theme_update]" value="1" <?php checked( !empty( $options['disable_theme_update'] ) ); ?> />
	<?php
	}
	
	/**
	 * Field: Disable admin bar
	 */
	public function field_disable_adminbar_frontend()
	{
		$options = get_option('jwdm_options');
	?>
		<input type="checkbox" name="jwdm_options[disable_adminbar_frontend]" value="1" <?php checked( !empty( $options['disable_adminbar_frontend'] ) ); ?> />
	<?php
	}
	
	/************************
	 * Main functionality
	 ***********************/
	
	/**
	 * Hanlde and display page
	 */
	public function page()
	{
	?>
		<div class="wrap">
			<h2><?php _e('Developer Mode: Settings', 'developermode'); ?></h2>
			
			<p><?php _e('Disable general functionalities for any user that doesn&#39;t have developer access below.', 'developermode'); ?></p>
			
			<form action="options.php" method="post">
				<?php settings_fields('jwdm_options'); ?>
				<?php do_settings_sections('developermode'); ?>
				<?php submit_button(); ?>
			</form>
		</div>
	<?php
	}

}

new JWDM_AdminMenuPage_Options();
?>