<?php

    /* ==================================================

    Team Post Type Functions

    ================================================== */


    /* TEAM CATEGORY
    ================================================== */
    if ( !function_exists('sf_sponsor_category_register') ) {
        function sf_sponsor_category_register() {

            $sponsor_permalinks = get_option( 'sf_sponsor_permalinks' );

            $args = array(
                "label"             => __( 'Categories', 'swift-framework-plugin' ),
                "singular_label"    => __( 'Category', 'swift-framework-plugin' ),
                'public'            => false,
                'hierarchical'      => true,
                'show_ui'           => true,
                'show_in_nav_menus' => false,
                'args'              => array( 'orderby' => 'term_order' ),
                'rewrite'           => array(
                    'slug'       => empty( $sponsor_permalinks['category_base'] ) ? __( 'sponsor-category', 'swift-framework-plugin' ) : __( $sponsor_permalinks['category_base']  , 'swift-framework-plugin' ),
                    'with_front' => false
                ),
                'query_var'         => true
            );

            register_taxonomy( 'sponsor-category', 'sponsor', $args );
        }
        add_action( 'init', 'sf_sponsor_category_register' );
    }


    /* TEAM POST TYPE
    ================================================== */
    if ( !function_exists('sf_sponsor_register') ) {
        function sf_sponsor_register() {

            $sponsor_permalinks = get_option( 'sf_sponsor_permalinks' );
            $sponsor_permalink  = empty( $sponsor_permalinks['sponsor_base'] ) ? __( 'sponsor', 'swift-framework-plugin' ) : __( $sponsor_permalinks['sponsor_base']  , 'swift-framework-plugin' );

            $labels = array(
                'name'               => __( 'Sponsors', 'swift-framework-plugin' ),
                'singular_name'      => __( 'Sponsor', 'swift-framework-plugin' ),
                'add_new'            => __( 'Add New', 'sponsor', 'swift-framework-plugin' ),
                'add_new_item'       => __( 'Add New Sponsor', 'swift-framework-plugin' ),
                'edit_item'          => __( 'Edit Sponsor', 'swift-framework-plugin' ),
                'new_item'           => __( 'New Sponsor', 'swift-framework-plugin' ),
                'view_item'          => __( 'View Sponsor', 'swift-framework-plugin' ),
                'search_items'       => __( 'Search Sponsors', 'swift-framework-plugin' ),
                'not_found'          => __( 'No sponsors have been added yet', 'swift-framework-plugin' ),
                'not_found_in_trash' => __( 'Nothing found in Trash', 'swift-framework-plugin' ),
                'parent_item_colon'  => ''
            );

            $args = array(
                'labels'            => $labels,
                'public'            => true,
                'show_ui'           => true,
                'show_in_menu'      => true,
                'show_in_nav_menus' => true,
                'exclude_from_search' => true,
                'menu_icon'         => 'dashicons-thumbs-up',
                'rewrite'           => $sponsor_permalink != "sponsor" ? array(
                    'slug'       => untrailingslashit( $sponsor_permalink ),
                    'with_front' => false,
                    'feeds'      => false
                )
                    : false,
                'supports'          => array( 'title', 'thumbnail', 'editor', 'excerpt', 'revisions' ),
                'has_archive'       => false,
                'taxonomies'        => array( 'post_tag', 'sponsor-category' )
            );

            register_post_type( 'sponsor', $args );
        }
        add_action( 'init', 'sf_sponsor_register' );
    }


    /* TEAM POST TYPE COLUMNS
    ================================================== */
    if ( !function_exists('sf_sponsor_edit_columns') ) {
        function sf_sponsor_edit_columns( $columns ) {
            $columns = array(
                "cb"                => "<input type=\"checkbox\" />",
                "logo"              => "",
                "title"             => __( "Sponsors", 'swift-framework-plugin' ),
                "description"       => __( "Description", 'swift-framework-plugin' ),
                "sponsor-category"  => __( "Categories", 'swift-framework-plugin' ),
                "tags"              => __( "Tags", 'swift-framework-plugin' ),
                "date"              => __( "Date", 'swift-framework-plugin' )
            );

            return $columns;
        }
        add_filter( "manage_edit-sponsor_columns", "sf_sponsor_edit_columns" );
    }

    /**
     * Custom Post Columns Data
     *
     * @param string $column_name
     * @param int $post_id
     * @return null
     */
    if ( !function_exists('sf_sponsor_columns_data') ) {
        function sf_sponsor_columns_data( $column_name, $post_id ) {
            $taxonomies = array( 'sponsor-category' );
            $post_type = 'sponsor';
            foreach( $taxonomies as $taxonomy ) {
                if( $column_name == $taxonomy ) {
                    $terms = get_the_terms( $post_id, $taxonomy );
                    if ( !empty( $terms ) ) {
                        $output = array();
                        foreach ( $terms as $term )
                            $output[] = '<a href="' . admin_url( 'edit.php?' . $taxonomy . '='.  $term->slug . '&post_type=' . $post_type ) . '">' . $term->name . '</a>';
                        echo join( ', ', $output );
                    }
                    else {
                        _e('—');
                    }
                
                }
            }
        }
        add_action( 'manage_posts_custom_column', 'sf_sponsor_columns_data', 10, 2 );
    }
    /********************* ARTICLE OPTIONS REGISTERING ***********************/


?>