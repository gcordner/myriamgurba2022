<?php
/**
 * Understrap Child Theme functions and definitions
 *
 * @package UnderstrapChild
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

// /**
//  * Typekit fonts
//  */
// function adobe_fonts() {
//     wp_enqueue_style( 'adobe-fonts', 'https://use.typekit.net/qnb6jpd.css', false );
// }
// add_action( 'wp_enqueue_scripts', 'adobe_fonts' );

/**
 * Load the child theme's text domain
 */
function add_child_theme_textdomain() {
	load_child_theme_textdomain( 'understrap-child', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'add_child_theme_textdomain' );



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
function understrap_child_customize_controls_js() {
	wp_enqueue_script(
		'understrap_child_customizer',
		get_stylesheet_directory_uri() . '/js/customizer-controls.js',
		array( 'customize-preview' ),
		'20130508',
		true
	);
}
add_action( 'customize_controls_enqueue_scripts', 'understrap_child_customize_controls_js' );

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
 * MULTIPLE POST TYPES IN TAG OR CATEGORY ARCHIVE
 */

 /**
 * Add snippets CPT to category archive page
 */

// function myriamgurba_multiple_post_type_in_tag($query)
// {
//     if($query->is_main_query() && is_tag()){
//         $query->set('post_type', ['post', 'writing']);
//         return;
//     }
// 	elseif ($query->is_main_query() && is_category()){
//         $query->set('post_type', ['post', 'writing']);
//         return;
//     }
// }
// add_action('pre_get_posts', 'myriamgurba_multiple_post_type_in_tag');

function myriamgurba_multiple_post_type($query)
{
    
if($query->is_main_query() && is_tag()){
        $query->set('post_type', ['post', 'writing']);
        return;
    }
	elseif ($query->is_main_query() && is_category()){
        $query->set('post_type', ['post', 'writing']);
        return;
    }
	elseif($query->is_main_query()){
        $query->set('post_type', ['writing', 'post']);
        return;
    }

}
add_action('pre_get_posts', 'myriamgurba_multiple_post_type');

/**
 * REMOVES THE WORD ARCHIVE FROM ARCHIVES TITLE
 */

add_filter( 'get_the_archive_title', function ($title) {
	if ( is_category() ) {
	$title = single_cat_title( '', false );
	} elseif ( is_tag() ) {
	$title = single_tag_title( '', false );
	} elseif ( is_author() ) {
	$title = '<span class="vcard">' . get_the_author() . '</span>' ;
	}
	else {
		$title = post_type_archive_title( '<h1 class="page-title">', '</h1>' );
	
	}
	
	return $title;

   });

/**
 * Loads child theme styles in admin.
 */
add_action('admin_head', 'theme_styles');

function theme_styles() {
  echo '<style>
    .napoleon {
		font-size: 100px;
    } 
  </style>';
}
