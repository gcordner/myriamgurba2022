<?php

    /* ==================================================

    Resource Post Type Functions

    ================================================== */

    /* RESOURCE CATEGORY
    ================================================== */
    if ( !function_exists('sf_resource_category_register') ) {
        function sf_resource_category_register() {

            $resource_permalinks = get_option( 'sf_resource_permalinks' );

            $args = array(
                "label"             => __( 'Resource Types', 'swift-framework-plugin' ),
                "singular_label"    => __( 'Resource Type', 'swift-framework-plugin' ),
                'public'            => true,
                'hierarchical'      => true,
                'show_ui'           => true,
                'show_in_nav_menus' => false,
                'args'              => array( 'orderby' => 'term_order' ),
                'rewrite'           => array(
                    'slug'       => empty( $resource_permalinks['category_base'] ) ? __( 'resource-category', 'swift-framework-plugin' ) : __( $resource_permalinks['category_base']  , 'swift-framework-plugin' ),
                    'with_front' => false
                ),
                'query_var'         => true
            );

            register_taxonomy( 'resource-category', 'resource', $args );
        }
        add_action( 'init', 'sf_resource_category_register' );
    }

    /* RESOURCE POST TYPE
    ================================================== */
    if ( !function_exists('sf_resource_register') ) {
        function sf_resource_register() {

            $resource_permalinks = get_option( 'sf_resource_permalinks' );
            $resource_permalink  = empty( $resource_permalinks['resource_base'] ) ? __( 'resource', 'swift-framework-plugin' ) : __( $resource_permalinks['resource_base']  , 'swift-framework-plugin' );

            $labels = array(
                'name'               => __( 'Resources', 'swift-framework-plugin' ),
                'singular_name'      => __( 'Resources', 'swift-framework-plugin' ),
                'add_new'            => __( 'Add New', 'swift-framework-plugin' ),
                'add_new_item'       => __( 'Add New Resource', 'swift-framework-plugin' ),
                'edit_item'          => __( 'Edit Resource', 'swift-framework-plugin' ),
                'new_item'           => __( 'New Resource', 'swift-framework-plugin' ),
                'view_item'          => __( 'View Resource', 'swift-framework-plugin' ),
                'search_items'       => __( 'Search Resources', 'swift-framework-plugin' ),
                'not_found'          => __( 'No resources have been added yet', 'swift-framework-plugin' ),
                'not_found_in_trash' => __( 'Nothing found in Trash', 'swift-framework-plugin' ),
                'parent_item_colon'  => ''
            );

            $args = array(
                'labels'            => $labels,
                'public'            => true,
                'show_ui'           => true,
                'show_in_menu'      => true,
                'show_in_nav_menus' => true,
                'show_admin_column' => true,
                'menu_icon'         => 'dashicons-chart-bar',
                'rewrite'           => $resource_permalink != "resource" ? array(
                    'slug'       => untrailingslashit( $resource_permalink ),
                    'with_front' => false,
                    'feeds'      => false
                )
                    : false,
                'supports'          => array( 'title', 'thumbnail', 'editor', 'excerpt', 'comments', 'revisions' ),
                'has_archive'       => false,
                'taxonomies'        => array( 'post_tag', 'resource-category' )
            );

            register_post_type( 'resource', $args );
        }
        add_action( 'init', 'sf_resource_register' );
    }

    /* RESOURCES POST TYPE COLUMNS
    ================================================== */
    if ( !function_exists('sf_resource_edit_columns') ) {
        function sf_resource_edit_columns( $columns ) {
            $columns = array(
                "cb"                => "<input type=\"checkbox\" />",
                "title"             => __( "Resources", 'swift-framework-plugin' ),
                "description"       => __( "Description", 'swift-framework-plugin' ),
                "resource-category" => __( "Types", 'swift-framework-plugin' ),
                "tags"              => __( "Tags", 'swift-framework-plugin' ),
                "date"              => __( "Date", 'swift-framework-plugin' )
            );

            if ( is_plugin_active("google-analytics-dashboard-for-wp/gadwp.php") ) {
                $columns["gadwp_stats"] = __( "Analytics", 'swift-framework-plugin' );
            }

            return $columns;
        }
        add_filter( "manage_edit-resource_columns", "sf_resource_edit_columns" );
    }
    /**
     * Custom Post Columns Data
     *
     * @param string $column_name
     * @param int $post_id
     * @return null
     */
    if ( !function_exists('sf_resource_columns_data') ) {
        function sf_resource_columns_data( $column_name, $post_id ) {
            $taxonomies = array( 'resource-category' );
            $post_type = 'resource';
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
        add_action( 'manage_posts_custom_column', 'sf_resource_columns_data', 10, 2 );
    }
    /********************* ARTICLE OPTIONS REGISTERING ***********************/
    
    /**
     * Register meta boxes
     *
     * @return void
     */

    function resource_register_redirect_options() {
        if ( function_exists('get_field') ) {
            add_meta_box( 'redirect_options', __( 'Redirect Options', 'textdomain' ), 'resource_redirect_options_callback', 'resource', 'side', 'default' );
        }
    }
    add_action( 'add_meta_boxes', 'resource_register_redirect_options' );

    /**
     * Meta box display callback.
     *
     * @param WP_Post $post Current post object.
     */
    function resource_redirect_options_callback( $post ) {
        global $post;

        // Noncename needed to verify where the data originated
        echo '<input type="hidden" name="eventmeta_noncename" id="eventmeta_noncename" value="' . 
        wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
        
        // Get the location data if its already been entered
        $link = get_post_meta($post->ID, 'external_link', true);
        if ( $link == "" ) {
            $link = get_post_meta($post->ID, 'link', true);
        }
        
        // Echo out the field
        echo '<div id="acf-link" class="acf-field field field_type-text field_key-link" data-field_name="external_link" data-field_key="external_link" data-field_type="text"><div class="acf-label"><label for="acf-field-link">External Link</label><p class="description">This will redirect to the link you provide. Leave blank if you would like to host this content internally.</p></div><input type="url" name="external_link" value="' . $link  . '" class="widefat" /></div>';
    }

    /**
     * Save meta box content.
     *
     * @param int $post_id Post ID
     */
    function resource_save_meta_box( $post_id ) {
        // Save logic goes here. Don't forget to include nonce checks!
        if ( get_post_type($post_id) == "resource" ) {
            if ( array_key_exists('external_link', $_POST ) ) {
                $redirect_link = $_POST['external_link'];
                if ( !empty($redirect_link) ) {
                    if ( strpos($redirect_link, 'http://') <= 0 && strpos($redirect_link, 'https://') <= 0 ) {
                        $redirect_link = 'http://' . $redirect_link;
                    }
                    $redirect_link = str_replace("http://http://", "http://", $redirect_link);
                    $redirect_link = str_replace("http://https://", "https://", $redirect_link);
                    $redirect_link = esc_url($redirect_link);
                    update_post_meta( $post_id, 'external_link', $redirect_link );
                } else {
                    update_post_meta( $post_id, 'external_link', "" );
                    update_post_meta( $post_id, 'link', "" );
                }
            }
        }
    }
    add_action( 'save_post', 'resource_save_meta_box' );


?>