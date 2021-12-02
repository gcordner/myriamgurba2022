<?php
/**
 * Admin menu page: Admin Menu
 */
class JWDM_AdminMenuPage_AdminMenu
{

	/**
	 * Whether the plugins were updated
	 */
	public $updated = false;
	
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
		add_submenu_page('developermode', __('Developer Mode: Admin Menu', 'developermode'), __('Admin Menu', 'developermode'), 'manage_options', 'developermode_adminmenu', array(&$this, 'page'));
	}
	
	/************************
	 * Main functionality
	 ***********************/
	
	/**
	 * Hanlde and display page
	 */
	public function page()
	{
		$this->handle();
		$this->display();
	}
	
	/**
	 * Handle business logic for this page
	 */
	public function handle()
	{
		global $JWDM_AdminMenu;
		
		// Helpers
		require_once JWDM_LIBRARY_PATH . '/helper/strings.php';
		require_once JWDM_LIBRARY_PATH . '/helper/adminmenu.php';
		
		// Form submitted
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			// Currently hidden menu items
			$hiddenmenuitems = get_option('jwdm_menuitems_hidden', array());
			
			if (!is_array($hiddenmenuitems)) {
				$hiddenmenuitems = array();
			}
			
			// Array with menu items and submenu items
			$menuitems = JWDM_Helper_AdminMenu::merge_menuitems($JWDM_AdminMenu->menu_original, $JWDM_AdminMenu->submenu_original);
			
			// As unchecked checkboxes are not sent by the browser, we reset the hidden setting for every available menu item so they are not hidden by default
			foreach ($menuitems as $index => $menuitem) {
				$fullslug = JWDM_Helper_Strings::esc_attr_id($menuitem->slug);
				
				if (($key = array_search($fullslug, $hiddenmenuitems)) !== false) {
					unset($hiddenmenuitems[$key]);
				}
				
				foreach ($menuitem->submenuitems as $index => $submenuitem) {
					$fullslug = JWDM_Helper_Strings::esc_attr_id($menuitem->slug . '-' . $submenuitem->slug);
					
					if (($key = array_search($fullslug, $hiddenmenuitems)) !== false) {
						unset($hiddenmenuitems[$key]);
					}
				}
			}
			
			// Set hidden menu items
			foreach ($_POST as $index => $postdata) {
				$searchstring = 'jwdm-menuitem-hidden-';
				$searchstring_length = strlen($searchstring);
				
				if (substr($index, 0, $searchstring_length) == $searchstring) {
					$hiddenmenuitems[] = substr($index, $searchstring_length);
				}
			}
			
			$hiddenmenuitems = array_values(array_unique($hiddenmenuitems));
			
			// Save
			update_option('jwdm_menuitems_hidden', $hiddenmenuitems);
			
			// Successfully updated
			$this->updated = true;
		}
	}
	
	/**
	 * Output the contents of the page
	 */
	public function display()
	{
		global $JWDM_AdminMenu;
		
		// Helpers
		require_once JWDM_LIBRARY_PATH . '/helper/strings.php';
		require_once JWDM_LIBRARY_PATH . '/helper/adminmenu.php';
		
		// Get all menu items
		$menuitems = JWDM_Helper_AdminMenu::merge_menuitems($JWDM_AdminMenu->menu_original, $JWDM_AdminMenu->submenu_original);
		
		// Get current hidden menu items
		$option_hiddenmenuitems = get_option('jwdm_menuitems_hidden', array());
		?>
		
		<div class="wrap">
			<h2><?php _e('Developer Mode: Admin Menu', 'developermode'); ?></h2>
			
			<?php if ($this->updated) : ?>
				<div class="updated">
					<p><?php printf(__('The hidden menu items were successfully updated.', 'developermode'), '<a href="" title="' . __('Refresh') . '">', '</a>'); ?></p>
				</div>
			<?php endif; ?>
			
			<form action="" method="post">
				<h3><?php _e('Menu item visibility', 'developermode'); ?></h3>
				<p><?php _e('You have the option to hide menu items from being displayed when a non-developer is logged in. Please check the boxes for the pages that you want to hide when anybody outside the Developer user group (for example, your client) is logged in.', 'developermode'); ?></p>
				
				<p>
					<a href="#jwdm-adminmenu-menuitems" class="jwdm-list-checkall" title="<?php esc_attr_e('Check all', 'developermode'); ?>"><?php _e('Check all', 'developermode'); ?></a>
					-
					<a href="#jwdm-adminmenu-menuitems" class="jwdm-list-uncheckall" title="<?php esc_attr_e('Uncheck all', 'developermode'); ?>"><?php _e('Uncheck all', 'developermode'); ?></a>
					-
					<a href="#jwdm-adminmenu-menuitems" class="jwdm-list-toggleall" title="<?php esc_attr_e('Toggle all', 'developermode'); ?>"><?php _e('Toggle all', 'developermode'); ?></a>
				</p>
				
				<ul id="jwdm-adminmenu-menuitems">
				
				<?php
				foreach ($menuitems as $index => $menuitem) {
					$fullslug = JWDM_Helper_Strings::esc_attr_id($menuitem->slug);
				?>
					<li>
						<label for="jwdm-menuitem-hidden-<?php echo $fullslug; ?>">
							<input type="checkbox" name="jwdm-menuitem-hidden-<?php echo $fullslug; ?>" id="jwdm-menuitem-hidden-<?php echo $fullslug; ?>" <?php checked(true, in_array($fullslug, $option_hiddenmenuitems)); ?> />
							<span class="jwdm-menuitem-title"><?php echo $menuitem->title; ?></span>
						</label>
						
						<?php
						if (!empty($menuitem->submenuitems)) {
						?>
							<ul>
								<?php
								foreach ($menuitem->submenuitems as $index => $submenuitem) {
									$fullslug = JWDM_Helper_Strings::esc_attr_id($menuitem->slug . '-' . $submenuitem->slug);
								?>
									<li>
										<label for="jwdm-menuitem-hidden-<?php echo $fullslug; ?>">
											<input type="checkbox" name="jwdm-menuitem-hidden-<?php echo $fullslug; ?>" id="jwdm-menuitem-hidden-<?php echo $fullslug; ?>" value="1" <?php checked(true, in_array($fullslug, $option_hiddenmenuitems)); ?> />
											<span class="jwdm-menuitem-title"><?php echo $submenuitem->title; ?></span>
										</label>
									</li>
								<?php
								}
								?>
							</ul>
						<?php
						}
						?>
					</li>
				<?php
				}
				?>
				
				</ul>
				
				<?php submit_button(); ?>
			</form>
		</div>
		
		<?php
	}

}

new JWDM_AdminMenuPage_AdminMenu();
?>