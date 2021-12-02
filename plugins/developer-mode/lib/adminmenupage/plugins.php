<?php
/**
 * Admin menu page: Plugins
 */
class JWDM_AdminMenuPage_Plugins
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
		add_submenu_page('developermode', __('Developer Mode: Plugins', 'developermode'), __('Plugins', 'developermode'), 'manage_options', 'developermode_plugins', array(&$this, 'page'));
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
		// Helpers
		require_once JWDM_LIBRARY_PATH . '/helper/strings.php';
		
		// Form submitted
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			// Currently hidden menu items
			$hiddenplugins = get_option('jwdm_plugins_hidden', array());
			
			if (!is_array($hiddenplugins)) {
				$hiddenplugins = array();
			}
			
			// Array with menu items and submenu items
			$plugins = get_plugins();
			
			// As unchecked checkboxes are not sent by the browser, we reset the hidden setting for every available menu item so they are not hidden by default
			foreach ($plugins as $index => $plugin) {
				$fullslug = JWDM_Helper_Strings::esc_attr_id($index);
				
				if (($key = array_search($fullslug, $hiddenplugins)) !== false) {
					unset($hiddenplugins[$key]);
				}
			}
			
			// Set hidden menu items
			foreach ($_POST as $index => $postdata) {
				$searchstring = 'jwdm-plugin-hidden-';
				$searchstring_length = strlen($searchstring);
				
				if (substr($index, 0, $searchstring_length) == $searchstring) {
					$hiddenplugins[] = substr($index, $searchstring_length);
				}
			}
			
			$hiddenplugins = array_values(array_unique($hiddenplugins));
			
			// Save
			update_option('jwdm_plugins_hidden', $hiddenplugins);
			
			// Successfully updated
			$this->updated = true;
		}
	}
	
	/**
	 * Output the contents of the page
	 */
	public function display()
	{
		// Helpers
		require_once JWDM_LIBRARY_PATH . '/helper/strings.php';
		
		// Get all plugins
		$plugins = get_plugins();
		
		// Get current hidden plugins
		$option_hiddenplugins = get_option('jwdm_plugins_hidden', array());
		?>
		
		<div class="wrap">
			<h2><?php _e('Developer Mode: Plugins', 'developermode'); ?></h2>
			
			<?php if ($this->updated) : ?>
				<div class="updated">
					<p><?php printf(__('The hidden plugins were successfully updated.', 'developermode'), '<a href="" title="' . __('Refresh') . '">', '</a>'); ?></p>
				</div>
			<?php endif; ?>
			
			<form action="" method="post">
				<h3><?php _e('Plugin visibility', 'developermode'); ?></h3>
				<p><?php _e('You have the option to hide plugins from being displayed on the plugins page when a non-developer is logged in. Please check the boxes for the plugins that you want to hide from the plugin overview when anybody outside the Developer user group (for example, your client) is logged in.', 'developermode'); ?></p>
				
				<p>
					<a href="#jwdm-plugins-plugins" class="jwdm-list-checkall" title="<?php esc_attr_e('Check all', 'developermode'); ?>"><?php _e('Check all', 'developermode'); ?></a>
					-
					<a href="#jwdm-plugins-plugins" class="jwdm-list-uncheckall" title="<?php esc_attr_e('Uncheck all', 'developermode'); ?>"><?php _e('Uncheck all', 'developermode'); ?></a>
					-
					<a href="#jwdm-plugins-plugins" class="jwdm-list-toggleall" title="<?php esc_attr_e('Toggle all', 'developermode'); ?>"><?php _e('Toggle all', 'developermode'); ?></a>
				</p>
				
				<ul id="jwdm-plugins-plugins">
				
				<?php
				foreach ($plugins as $index => $plugin) {
					$fullslug = JWDM_Helper_Strings::esc_attr_id($index);
				?>
					<li>
						<label for="jwdm-plugin-hidden-<?php echo $fullslug; ?>">
							<input type="checkbox" name="jwdm-plugin-hidden-<?php echo $fullslug; ?>" id="jwdm-plugin-hidden-<?php echo $fullslug; ?>" <?php checked(true, in_array($fullslug, $option_hiddenplugins)); ?> />
							<span class="jwdm-plugin-title"><?php echo $plugin['Name']; ?></span>
						</label>
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

new JWDM_AdminMenuPage_Plugins();
?>