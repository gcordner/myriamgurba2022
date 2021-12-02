<?php
/**
 */
class JWDM_Admin
{

	/**
	 * Constructor
	 */
	public function __construct()
	{
		// Actions
		add_action('admin_init', array(&$this, 'action_admin_init'));
		add_action('admin_enqueue_scripts', array(&$this, 'action_admin_enqueue_scripts'));
		add_action('admin_notices', array(&$this, 'action_admin_notices'));
		
		// Filters
		add_filter('all_plugins', array(&$this, 'filter_all_plugins'));
		add_filter('user_has_cap', array(&$this, 'filter_user_has_cap'), 999, 3);
		add_filter('editable_roles', array(&$this, 'filter_editable_roles'), 999);
	}
	
	/************************
	 * Actions
	 ***********************/
	
	/**
	 * Action: admin_init
	 */
	public function action_admin_init()
	{
		$this->handle_settings();
	}
	
	/**
	 * Action: admin_enqueue_scripts
	 */
	public function action_admin_enqueue_scripts()
	{
		// Styles
		wp_register_style('jwdm_admin', JWDM_URL . '/public/css/admin.css');
		wp_enqueue_style('jwdm_admin');
		
		// Scripts
		wp_register_script('jwdm-admin', JWDM_URL . '/public/js/admin.js', array('jquery'));
		wp_enqueue_script('jquery');
		wp_enqueue_script('jwdm-admin');
	}
	
	/**
	 * Action: admin_notices
	 *
	 * Handle the notices that should be displayed on all admin pages
	 */
	public function action_admin_notices()
	{
		if (isset($_GET['page']) && $_GET['page'] == 'developermode_promoteself') {
			return;
		}
		
		// Helpers
		require_once JWDM_LIBRARY_PATH . '/helper/roles.php';
		require_once JWDM_LIBRARY_PATH . '/helper/strings.php';
		
		if (current_user_can('promote_users') && !JWDM_Helper_Roles::developer_users_exist()) {
			if (empty($developerusers)) {
			?>
				<div class="error">
					<p>
						<?php
						printf(__('No users have been assigned the &quot;Developer&quot; role yet. Until you do so, the functionality of the Developer Mode plugin will be limited. Please go to the %s page and assign the developer role to a user, or %spromote yourself%s to &quot;Developer&quot;.', 'developermode'),
						'<a href="' . get_admin_url(NULL, 'users.php') . '" title="' . esc_attr__('Users') . '">' . esc_html__('Users') . '</a>',
						'<a href="' . get_admin_url(NULL, 'admin.php?page=developermode_promoteself') . '" title="' . esc_attr__('Promote') . '">',
						'</a>'
						);
						?>
					</p>
				</div>
			<?php
			}
		}
	}
	
	/************************
	 * Filters
	 ***********************/
	
	/**
	 * Filter: all_plugins
	 */
	public function filter_all_plugins(array $plugins)
	{
		// Helpers
		require_once JWDM_LIBRARY_PATH . '/helper/roles.php';
		require_once JWDM_LIBRARY_PATH . '/helper/strings.php';
		
		// Check if the current user can view developer-only plugins
		if (current_user_can('view_developer_plugins') || !JWDM_Helper_Roles::developer_users_exist()) {
			return $plugins;
		}
		
		// Get hidden plugins
		$hiddenplugins = get_option('jwdm_plugins_hidden', array());
		
		foreach ($plugins as $index => $plugin) {
			$fullslug = JWDM_Helper_Strings::esc_attr_id($index);
			
			if (in_array($fullslug, $hiddenplugins)) {
				unset($plugins[$index]);
			}
		}
		
		return $plugins;
	}
	
	/**
	 * Filter: editable_roles
	 */
	public function filter_editable_roles($roles)
	{
		$options = get_option('jwdm_options');
		
		if ( !empty( $options['prevent_developeruser_edit'] ) && isset( $roles['developer'] ) ) {
			$user = wp_get_current_user();
			
			if (is_object($user) && !empty($user->ID)) {
				if (JWDM_Helper_Roles::get_user_role($user->ID) != 'developer') {
					unset($roles['developer']);
				}
			}
		}
		
		return $roles;
	}
	
	
	/**
	 * Filter: user_has_cap
	 */
	public function filter_user_has_cap($allcaps, $caps, $args)
	{
		$options = get_option('jwdm_options');
		
		if ( empty( $options['prevent_developeruser_edit'] ) ) {
			return $allcaps;
		}
		
		// Check role
		$user = wp_get_current_user();
		
		if (!is_object($user) || empty($user->ID) || JWDM_Helper_Roles::get_user_role($user->ID) == 'developer') {
			return $allcaps;
		}
		
		// Make sure the requested capability and the user ID are set
		if ( empty( $args[0] ) || !in_array($args[0], array('edit_user', 'delete_user')) || empty( $args[1] ) || empty( $args[2] ) ) {
			return $allcaps;
		}
		
		$access = JWDM_Helper_Roles::get_user_role($args[2]) == 'developer' ? false : true;
		
		foreach ($caps as $index => $cap) {
			$allcaps[$cap] = $access;
		}
		
		return $allcaps;
	}
	
	/************************
	 * Main functionality
	 ***********************/
	
	/**
	 * Handle WordPress settings API functionality
	 */
	public function handle_settings()
	{
		register_setting('jwdm_options', 'jwdm_options');
	}

}

new JWDM_Admin();
?>