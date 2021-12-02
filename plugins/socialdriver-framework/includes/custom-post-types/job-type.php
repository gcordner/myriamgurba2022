<?php

    /* ==================================================

    Job Post Type Functions

    ================================================== */

    /* Job CATEGORY
    ================================================== */
    if ( !function_exists('sf_job_category_register') ) {
        function sf_job_category_register() {

            $job_permalinks = get_option( 'sf_job_permalinks' );

            $args = array(
                "label"             => __( 'Categories', 'swift-framework-plugin' ),
                "singular_label"    => __( 'Category', 'swift-framework-plugin' ),
                'public'            => true,
                'hierarchical'      => true,
                'show_ui'           => true,
                'show_in_nav_menus' => false,
                'args'              => array( 'orderby' => 'term_order' ),
                'rewrite'           => array(
                    'slug'       => empty( $job_permalinks['category_base'] ) ? __( 'job-category', 'swift-framework-plugin' ) : __( $job_permalinks['category_base']  , 'swift-framework-plugin' ),
                    'with_front' => false
                ),
                'query_var'         => true
            );

            register_taxonomy( 'job-category', 'job', $args );
        }
        add_action( 'init', 'sf_job_category_register' );
    }

    /* NEWS POST TYPE
    ================================================== */
    if ( !function_exists('sf_job_register') ) {
        function sf_job_register() {

            $job_permalinks = get_option( 'sf_job_permalinks' );
            $job_permalink  = empty( $job_permalinks['job_base'] ) ? __( 'job', 'swift-framework-plugin' ) : __( $job_permalinks['job_base']  , 'swift-framework-plugin' );

            $labels = array(
                'name'               => __( 'Jobs', 'swift-framework-plugin' ),
                'singular_name'      => __( 'Job', 'swift-framework-plugin' ),
                'add_new'            => __( 'Add New', 'swift-framework-plugin' ),
                'add_new_item'       => __( 'Add New Job', 'swift-framework-plugin' ),
                'edit_item'          => __( 'Edit Job', 'swift-framework-plugin' ),
                'new_item'           => __( 'New Job', 'swift-framework-plugin' ),
                'view_item'          => __( 'View Job', 'swift-framework-plugin' ),
                'search_items'       => __( 'Search Job', 'swift-framework-plugin' ),
                'not_found'          => __( 'No jobs have been added yet', 'swift-framework-plugin' ),
                'not_found_in_trash' => __( 'Nothing found in Trash', 'swift-framework-plugin' ),
                'parent_item_colon'  => ''
            );

            $args = array(
                'labels'            => $labels,
                'public'            => true,
                'show_ui'           => true,
                'show_in_menu'      => true,
                'show_in_nav_menus' => true,
                'menu_icon'         => 'dashicons-businessman',
                'rewrite'           => $job_permalink != "job" ? array(
                    'slug'       => untrailingslashit( $job_permalink ),
                    'with_front' => false,
                    'feeds'      => false
                )
                    : false,
                'supports'          => array( 'title', 'thumbnail', 'editor', 'excerpt', 'comments', 'revisions' ),
                'has_archive'       => false,
                'taxonomies'        => array( 'post_tag', 'job-category' )
            );

            register_post_type( 'job', $args );
        }
        add_action( 'init', 'sf_job_register' );
    }

    /* NEWS POST TYPE COLUMNS
    ================================================== */
    if ( !function_exists('sf_job_edit_columns') ) {
        function sf_job_edit_columns( $columns ) {
            $columns = array(
                "cb"            => "<input type=\"checkbox\" />",
                "title"         => __( "Job", 'swift-framework-plugin' ),
                "description"   => __( "Description", 'swift-framework-plugin' ),
                "job-category"  => __( "Categories", 'swift-framework-plugin' ),
                "tags"          => __( "Tags", 'swift-framework-plugin' ),
                "date"          => __( "Date", 'swift-framework-plugin' )
            );

            if ( is_plugin_active("google-analytics-dashboard-for-wp/gadwp.php") ) {
                $columns["gadwp_stats"] = __( "Analytics", 'swift-framework-plugin' );
            }

            return $columns;
        }
        add_filter( "manage_edit-job_columns", "sf_job_edit_columns" );
    }

    /**
     * Custom Post Columns Data
     *
     * @param string $column_name
     * @param int $post_id
     * @return null
     */
    if ( !function_exists('sf_job_columns_data') ) {
        function sf_job_columns_data( $column_name, $post_id ) {
            $taxonomies = array( 'job-category' );
            $post_type = 'job';
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
        add_action( 'manage_posts_custom_column', 'sf_job_columns_data', 10, 2 );
    }
    /********************* ARTICLE OPTIONS REGISTERING ***********************/

?>