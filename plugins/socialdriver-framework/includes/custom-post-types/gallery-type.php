<?php

    /* ==================================================

    Gallery Post Type Functions

    ================================================== */


    /* GALLERY CATEGORY
    ================================================== */
    if ( !function_exists('sf_gallery_category_register') ) {
        function sf_gallery_category_register() {

            $gallery_permalinks = get_option( 'sf_gallery_permalinks' );

            $args = array(
                "label"             => __( 'Categories', 'swift-framework-plugin' ),
                "singular_label"    => __( 'Category', 'swift-framework-plugin' ),
                'public'            => false,
                'hierarchical'      => true,
                'show_ui'           => true,
                'show_in_nav_menus' => false,
                'args'              => array( 'orderby' => 'term_order' ),
                'rewrite'           => array(
                    'slug'       => empty( $gallery_permalinks['category_base'] ) ? __( 'gallery-category', 'swift-framework-plugin' ) : __( $gallery_permalinks['category_base']  , 'swift-framework-plugin' ),
                    'with_front' => false
                ),
                'query_var'         => true
            );

            register_taxonomy( 'gallery-category', 'gallery', $args );
        }
        add_action( 'init', 'sf_gallery_category_register' );
    }


    /* GALLERIES POST TYPE
    ================================================== */
    if ( !function_exists('sf_gallery_register') ) {
        function sf_gallery_register() {

            $gallery_permalinks = get_option( 'sf_gallery_permalinks' );
            $gallery_permalink  = empty( $gallery_permalinks['gallery_base'] ) ? __( 'gallery', 'swift-framework-plugin' ) : __( $gallery_permalinks['gallery_base']  , 'swift-framework-plugin' );

            $labels = array(
                'name'               => __( 'Galleries', 'swift-framework-plugin' ),
                'singular_name'      => __( 'Gallery', 'swift-framework-plugin' ),
                'add_new'            => __( 'Add New', 'swift-framework-plugin' ),
                'add_new_item'       => __( 'Add New Gallery', 'swift-framework-plugin' ),
                'edit_item'          => __( 'Edit Gallery', 'swift-framework-plugin' ),
                'new_item'           => __( 'New Gallery', 'swift-framework-plugin' ),
                'view_item'          => __( 'View Gallery', 'swift-framework-plugin' ),
                'search_items'       => __( 'Search Galleries', 'swift-framework-plugin' ),
                'not_found'          => __( 'No galleries have been added yet', 'swift-framework-plugin' ),
                'not_found_in_trash' => __( 'Nothing found in Trash', 'swift-framework-plugin' ),
                'parent_item_colon'  => ''
            );

            $args = array(
                'labels'            => $labels,
                'public'            => false,
                'show_ui'           => true,
                'show_in_menu'      => true,
                'show_in_nav_menus' => true,
                'menu_icon'         => 'dashicons-format-gallery',
                'hierarchical'      => false,
                'exclude_from_search' => true,
                'rewrite'           => $gallery_permalink != "gallery" ? array(
                    'slug'       => untrailingslashit( $gallery_permalink ),
                    'with_front' => false,
                    'feeds'      => false
                )
                    : false,
                'supports'          => array( 'title', 'revisions' ),
                'has_archive'       => false,
                'taxonomies'        => array( 'gallery-category' )
            );

            register_post_type( 'gallery', $args );
        }
        add_action( 'init', 'sf_gallery_register' );
    }


    /* GALLERIES POST TYPE COLUMNS
    ================================================== */
    if ( !function_exists('sf_gallery_edit_columns') ) {
        function sf_gallery_edit_columns( $columns ) {
            $columns = array(
                "cb"               => "<input type=\"checkbox\" />",
                "thumbnail"        => "",
                "title"            => __( "Gallery", 'swift-framework-plugin' ),
                "gallery-category" => __( "Categories", 'swift-framework-plugin' )
            );

            return $columns;
        }
        add_filter( "manage_edit-gallery_columns", "sf_gallery_edit_columns" );
    }
?>