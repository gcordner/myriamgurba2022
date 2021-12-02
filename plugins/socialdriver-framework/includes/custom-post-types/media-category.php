<?php

    /* ==================================================

    Media Library Category Post Type Functions

    ================================================== */

    /* RESOURCE CATEGORY
    ================================================== */
    if ( !function_exists('sf_media_category_register') ) {
        function sf_media_category_register() {

            $args = array(
                "label"             => __( 'Media Categories', 'swift-framework-plugin' ),
                "singular_label"    => __( 'Media Category', 'swift-framework-plugin' ),
                'public'            => true,
                'hierarchical'      => true,
                'show_ui'           => true,
                'show_in_nav_menus' => false,
                'args'              => array( 'orderby' => 'term_order' ),
                'rewrite'           => array(
                    'slug'          => 'media-category',
                    'with_front'    => false
                ),
                'query_var'         => true
            );

            register_taxonomy( 'media_category', 'attachment', $args );
            register_taxonomy_for_object_type('media_category', 'attachment');
            
        }
        add_action( 'init', 'sf_media_category_register' );
    }

?>