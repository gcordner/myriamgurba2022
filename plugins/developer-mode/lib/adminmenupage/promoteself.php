<?php
/**
 * Admin menu page: PromoteSelf
 */
class JWDM_AdminMenuPage_PromoteSelf
{

	/**
	 * Constructor
	 */
	public function __construct()
	{
		// Actions
		add_action('admin_menu', array(&$this, 'action_admin_menu'));
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
		global $menu;
		
		// Add menu page
		add_menu_page(__('Developer Mode: Promote Self', 'developermode'), __('Promote to Developer', 'developermode'), 'promote_users', 'developermode_promoteself', array(&$this, 'page'));
		
		// Remove menu page from visible menu
		foreach ($menu as $index => $menuitem) {
			if ($menuitem[2] == 'developermode_promoteself') {
				unset($menu[$index]);
			}
		}
	}
	
	/************************
	 * Menu pages
	 ***********************/
	
	/**
	 * Hanlde and display page
	 */
	public function page()
	{
		// Helpers
		require_once JWDM_LIBRARY_PATH . '/helper/roles.php';
		
		// Get logged in user
		$current_user = wp_get_current_user();
		
		// Whether the user was promoted
		$promoted = false;
		
		if ($current_user->ID && JWDM_Helper_Roles::role_exists('developer') && current_user_can('promote_users')) {
			// Promote self
			$user = new WP_User($current_user->ID);
			$user->set_role('developer');
			
			// Promoted successfully
			$promoted = true;
		}
	?>
		<div class="wrap">
			<h2><?php _e('Developer Mode: Promote Self', 'developermode'); ?></h2>
			
			<?php if ($promoted) : ?>
				<div class="updated">
					<p>
						<?php
						printf(
							__('You have been successfully promoted to &quot;Developer&quot;. The Developer Mode plugin is now fully functional.', 'developermode'),
							'<a href="" title="' . __('Refresh') . '">',
							'</a>'
						);
						?>
					</p>
				</div>
			<?php endif; ?>
		</div>
	<?php
	}

}

new JWDM_AdminMenuPage_PromoteSelf();
?>