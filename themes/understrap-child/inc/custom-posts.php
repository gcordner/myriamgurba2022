<?php
/**
 * Establish custom post types for writing and books.
 *
 * @package MyriamGurba
 */

 //** REGISTER WRITING CUSTOM POST TYPE */

function register_custom_post_type_for_writing() {

	$labels = [
		"name" => __( "writing", "understrap-child" ),
		"singular_name" => __( "writing", "understrap-child" ),
		"menu_name" => __( "Writing", "understrap-child" ),
	];

	$args = [
		"label" => __( "writing", "understrap-child" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive" => "writing",
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => true,
		"can_export" => false,
		"rewrite" => [ "slug" => "writing", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail", "excerpt", "custom-fields" ],
		"taxonomies" => [ "post_tag" ],
		"show_in_graphql" => false,
	];

	register_post_type( "writing", $args );
  
  }
  add_action( 'init', __NAMESPACE__ . '\register_custom_post_type_for_writing', 0 );


  function register_custom_post_type_for_books() {

	/**
	 * Post Type: Books.
	 */

	$labels = [
		"name" => __( "Books", "understrap-child" ),
		"singular_name" => __( "book", "understrap-child" ),
	];

	$args = [
		"label" => __( "Books", "understrap-child" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => [ "slug" => "book", "with_front" => false ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail", "excerpt", "custom-fields" ],
		"show_in_graphql" => false,
	];

	register_post_type( "book", $args );
}

// add_action( 'init', 'cptui_register_my_cpts_book' );
add_action( 'init', __NAMESPACE__ . '\register_custom_post_type_for_books', 0 );