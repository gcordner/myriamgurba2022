<?php

    /* ==================================================

    Timeline Post Type Functions

    ================================================== */

    /* Timeline CATEGORY
    ================================================== */
    if ( !function_exists('sf_timeline_category_register') ) {
        function sf_timeline_category_register() {

            $timeline_permalinks = get_option( 'sf_timeline_permalinks' );

            $args = array(
                "label"             => __( 'Timeline Categories', 'swift-framework-plugin' ),
                "singular_label"    => __( 'Timeline Category', 'swift-framework-plugin' ),
                'public'            => false,
                'hierarchical'      => true,
                'show_ui'           => true,
                'show_in_nav_menus' => false,
                'args'              => array( 'orderby' => 'term_order' ),
                'rewrite'           => array(
                    'slug'       => empty( $timeline_permalinks['category_base'] ) ? __( 'timeline-category', 'swift-framework-plugin' ) : __( $timeline_permalinks['category_base']  , 'swift-framework-plugin' ),
                    'with_front' => false
                ),
                'query_var'         => true
            );

            register_taxonomy( 'timeline-category', 'timeline', $args );
        }
        add_action( 'init', 'sf_timeline_category_register' );
    }

    /* NEWS POST TYPE
    ================================================== */
    if ( !function_exists('sf_timeline_register') ) {
        function sf_timeline_register() {

            $timeline_permalinks = get_option( 'sf_timeline_permalinks' );
            $timeline_permalink  = empty( $timeline_permalinks['timeline_base'] ) ? __( 'timeline', 'swift-framework-plugin' ) : __( $timeline_permalinks['timeline_base']  , 'swift-framework-plugin' );

            $labels = array(
                'name'               => __( 'Timeline Points', 'swift-framework-plugin' ),
                'singular_name'      => __( 'Timeline Point', 'swift-framework-plugin' ),
                'add_new'            => __( 'Add New', 'swift-framework-plugin' ),
                'add_new_item'       => __( 'Add New Timeline Point', 'swift-framework-plugin' ),
                'edit_item'          => __( 'Edit Timeline Point', 'swift-framework-plugin' ),
                'new_item'           => __( 'New Timeline Point', 'swift-framework-plugin' ),
                'view_item'          => __( 'View Timeline Point', 'swift-framework-plugin' ),
                'search_items'       => __( 'Search Timeline Point', 'swift-framework-plugin' ),
                'not_found'          => __( 'No timeline milestones have been added yet', 'swift-framework-plugin' ),
                'not_found_in_trash' => __( 'Nothing found in Trash', 'swift-framework-plugin' ),
                'parent_item_colon'  => ''
            );

            $args = array(
                'labels'            => $labels,
                'public'            => false,
                'show_ui'           => true,
                'show_in_menu'      => true,
                'show_in_nav_menus' => true,
                'menu_icon'         => 'dashicons-clock',
                'rewrite'           => $timeline_permalink != "timeline" ? array(
                    'slug'       => untrailingslashit( $timeline_permalink ),
                    'with_front' => false,
                    'feeds'      => false
                )
                    : false,
                'supports'          => array( 'title', 'thumbnail', 'editor', 'excerpt', 'revisions' ),
                'has_archive'       => false,
                'taxonomies'        => array( 'timeline-category' )
            );

            register_post_type( 'timeline', $args );
        }
        add_action( 'init', 'sf_timeline_register' );
    }

    /* NEWS POST TYPE COLUMNS
    ================================================== */
    if ( !function_exists('sf_timeline_edit_columns') ) {
        function sf_timeline_edit_columns( $columns ) {
            $columns = array(
                "cb"            => "<input type=\"checkbox\" />",
                "title"         => __( "Timeline Point", 'swift-framework-plugin' ),
                "description"   => __( "Description", 'swift-framework-plugin' ),
                "timeline-category"  => __( "Categories", 'swift-framework-plugin' ),
                "tags"          => __( "Tags", 'swift-framework-plugin' ),
                "date"          => __( "Date", 'swift-framework-plugin' )
            );

            return $columns;
        }
        add_filter( "manage_edit-timeline_columns", "sf_timeline_edit_columns" );
    }

    /**
     * Custom Post Columns Data
     *
     * @param string $column_name
     * @param int $post_id
     * @return null
     */
    if ( !function_exists('sf_timeline_columns_data') ) {
        function sf_timeline_columns_data( $column_name, $post_id ) {
            $taxonomies = array( 'timeline-category' );
            $post_type = 'timeline';
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
        add_action( 'manage_posts_custom_column', 'sf_timeline_columns_data', 10, 2 );
    }
    /********************* ARTICLE OPTIONS REGISTERING ***********************/

?>