<?php
/**
 * Understrap Child Theme functions and definitions
 *
 * @package MyriamGurbaChild
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;



/**
 * Removes the parent themes stylesheet and scripts from inc/enqueue.php
 */
function understrap_remove_scripts() {
	wp_dequeue_style( 'understrap-styles' );
	wp_deregister_style( 'understrap-styles' );

	wp_dequeue_script( 'understrap-scripts' );
	wp_deregister_script( 'understrap-scripts' );
}
add_action( 'wp_enqueue_scripts', 'understrap_remove_scripts', 20 );



/**
 * Enqueue our stylesheet and javascript file
 */
function theme_enqueue_styles() {

	// Get the theme data.
	$the_theme = wp_get_theme();

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	// Grab asset urls.
	$theme_styles  = "/css/child-theme{$suffix}.css";
	$theme_scripts = "/js/child-theme{$suffix}.js";

	wp_enqueue_style( 'child-understrap-styles', get_stylesheet_directory_uri() . $theme_styles, array(), $the_theme->get( 'Version' ) );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'child-understrap-scripts', get_stylesheet_directory_uri() . $theme_scripts, array(), $the_theme->get( 'Version' ), true );
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );


/**
 * Load the child theme's text domain
 */
function add_child_theme_textdomain() {
	load_child_theme_textdomain( 'understrap-child', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'add_child_theme_textdomain' );

/**
 * Include theme code files.
 */

// Custom post types and taxonomies.
require_once 'inc/custom-posts.php';



/**
 * Overrides the theme_mod to default to Bootstrap 5
 *
 * This function uses the `theme_mod_{$name}` hook and
 * can be duplicated to override other theme settings.
 *
 * @param string $current_mod The current value of the theme_mod.
 * @return string
 */
function understrap_default_bootstrap_version( $current_mod ) {
	return 'bootstrap5';
}
add_filter( 'theme_mod_understrap_bootstrap_version', 'understrap_default_bootstrap_version', 20 );



/**
 * Loads javascript for showing customizer warning dialog.
 */
function mg_customize_controls_js() {
	wp_enqueue_script(
		'understrap_child_customizer',
		get_stylesheet_directory_uri() . '/js/customizer-controls.js',
		array( 'customize-preview' ),
		'20130508',
		true
	);
}
add_action( 'customize_controls_enqueue_scripts', 'mg_customize_controls_js' );

/**
 * Removes edit link from footer
 */

if ( ! function_exists( 'understrap_edit_post_link' ) ) {
	/**
	 * Displays the edit post link for post.
	 */
	function understrap_edit_post_link() {
		echo '<!-- EDIT LINK USED TO BE HERE -->';
	}
}
/**
 * Alter the posts query:
 * - include writing post type
 */
function myriamgurba_multiple_post_type( $query ) {
	// Abort if we're in the admin or if this is not the main query.
	if ( ! $query->is_main_query() || is_admin() ) {
		return;
	}

	if ( is_archive() ) {
		set_query_var( 'post_type', array( 'post', 'writing' ) );
	}
}




add_action( 'pre_get_posts', 'myriamgurba_multiple_post_type' );

/**
 * Removes the word archive from archives title
 */

add_filter(
	'get_the_archive_title',
	function ( $title ) {
		if ( is_category() ) {
			$title = single_cat_title( '', false );
		} elseif ( is_tag() ) {
			$title = single_tag_title( '', false );
		} elseif ( is_author() ) {
			$title = '<span class="vcard">' . get_the_author() . '</span>';
		} else {
			$title = post_type_archive_title( '<h1 class="page-title">', '</h1>' );

		}

		return $title;
	}
);


add_action( 'admin_head', 'theme_styles' );
/**
 * Loads child theme styles in admin.
 */
function theme_styles() {
	echo '<style>
    .napoleon {
		font-size: 100px;
    } 
  </style>';
}

/**
 * Block patterns.
 */
require_once get_stylesheet_directory() . '/patterns/myriam-blurbs.php';
