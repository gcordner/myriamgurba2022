<?php

    /* ==================================================

    Events Post Type Functions

    ================================================== */


    /* EVENT CATEGORY
    ================================================== */
    if ( !function_exists('sf_event_category_register') ) {
        function sf_event_category_register() {

            $event_permalinks = get_option( 'sf_event_permalinks' );

            $args = array(
                "label"             => __( 'Categories', 'swift-framework-plugin' ),
                "singular_label"    => __( 'Category', 'swift-framework-plugin' ),
                'public'            => true,
                'hierarchical'      => true,
                'show_ui'           => true,
                'show_in_nav_menus' => false,
                'args'              => array( 'orderby' => 'term_order' ),
                'rewrite'           => array(
                    'slug'       => empty( $event_permalinks['category_base'] ) ? __( 'event-category', 'swift-framework-plugin' ) : __( $event_permalinks['category_base']  , 'swift-framework-plugin' ),
                    'with_front' => false
                ),
                'query_var'         => true
            );

            register_taxonomy( 'event-category', 'event', $args );
        }
        add_action( 'init', 'sf_event_category_register' );
    }


    /* EVENT POST TYPE
    ================================================== */
    if ( !function_exists('sf_event_register') ) {
        function sf_event_register() {

            $event_permalinks = get_option( 'sf_event_permalinks' );
            $event_permalink  = empty( $event_permalinks['event_base'] ) ? __( 'event', 'swift-framework-plugin' ) : __( $event_permalinks['event_base']  , 'swift-framework-plugin' );

            $labels = array(
                'name'               => __( 'Events', 'swift-framework-plugin' ),
                'singular_name'      => __( 'Event', 'swift-framework-plugin' ),
                'add_new'            => __( 'Add New', 'event member', 'swift-framework-plugin' ),
                'add_new_item'       => __( 'Add New Event', 'swift-framework-plugin' ),
                'edit_item'          => __( 'Edit Event', 'swift-framework-plugin' ),
                'new_item'           => __( 'New Event', 'swift-framework-plugin' ),
                'view_item'          => __( 'View Event', 'swift-framework-plugin' ),
                'search_items'       => __( 'Search Events', 'swift-framework-plugin' ),
                'not_found'          => __( 'No event members have been added yet', 'swift-framework-plugin' ),
                'not_found_in_trash' => __( 'Nothing found in Trash', 'swift-framework-plugin' ),
                'parent_item_colon'  => ''
            );

            $args = array(
                'labels'            => $labels,
                'public'            => true,
                'show_ui'           => true,
                'show_in_menu'      => true,
                'show_in_nav_menus' => true,
                'menu_icon'         => 'dashicons-tickets-alt',
                'rewrite'           => $event_permalink != "event" ? array(
                    'slug'       => untrailingslashit( $event_permalink ),
                    'with_front' => false,
                    'feeds'      => false
                )
                    : false,
                'supports'          => array( 'title', 'thumbnail', 'editor', 'excerpt', 'comments', 'revisions' ),
                'has_archive'       => false,
                'taxonomies'        => array( 'post_tag', 'event-category' )
            );

            register_post_type( 'event', $args );
        }
        add_action( 'init', 'sf_event_register' );
    }

    /* EVENT POST TYPE COLUMNS
    ================================================== */
    if ( !function_exists('sf_event_edit_columns') ) {
        function sf_event_edit_columns( $columns ) {
            $columns = array(
                "cb"            => "<input type=\"checkbox\" />",
                "title"         => __( "Events", 'swift-framework-plugin' ),
                "description"   => __( "Description", 'swift-framework-plugin' ),
                "event-category"=> __( "Categories", 'swift-framework-plugin' ),
                "tags"          => __( "Tags", 'swift-framework-plugin' ),
                "date"          => __( "Date", 'swift-framework-plugin' )
            );

            if ( is_plugin_active("google-analytics-dashboard-for-wp/gadwp.php") ) {
                $columns["gadwp_stats"] = __( "Analytics", 'swift-framework-plugin' );
            }

            return $columns;
        }
        add_filter( "manage_edit-event_columns", "sf_event_edit_columns" );
    }

    /**
     * Custom Post Columns Data
     *
     * @param string $column_name
     * @param int $post_id
     * @return null
     */
    if ( !function_exists('sf_event_columns_data') ) {
        function sf_event_columns_data( $column_name, $post_id ) {
            $taxonomies = array( 'event-category' );
            $post_type = 'event';
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
        add_action( 'manage_posts_custom_column', 'sf_event_columns_data', 10, 2 );
    }
    /********************* ARTICLE OPTIONS REGISTERING ***********************/

    /* EVENT POST TYPE COLUMNS
    ================================================== */
    if ( ! function_exists( 'sf_event_date_meta' ) ) {
        function sf_event_date_meta( $post ) {

            $now = strtotime("now");
            $tbd = get_field("sf_event_tbd", $post->ID);
            $tbd_text = get_field("sf_event_tbd_text", $post->ID);
            if ( $tbd_text == "" ) {
                $tbd_text = "To Be Determined";
            }
            $start_date = get_post_meta( $post->ID, "sf_event_start_date" );
            if ( isset($start_date) && count($start_date) ) {
                $start_date = $start_date[0];
            } else {
                $start_date = "";
            }
            $start_time = get_post_meta( $post->ID, "sf_event_start_time" );
            if ( isset($start_time) && count($start_time) ) {
                $start_time = $start_time[0];
            } else {
                $start_time = "";
            }
            $end_date = get_post_meta( $post->ID, "sf_event_end_date" );
            if ( isset($end_date) && count($end_date) ) {
                $end_date = $end_date[0];
            } else {
                $end_date = "";
            }
            $end_time = get_post_meta( $post->ID, "sf_event_end_time" );
            if ( isset($end_time) && count($end_time) ) {
                $end_time = $end_time[0];
            } else {
                $end_time = "";
            }
            $occurence = get_post_meta( $post->ID, "sf_repeat" );
            if ( isset($occurence) && count($occurence) ) {
                $occurence = $occurence[0];
            } else {
                $occurence = "";
            }

            if ( $end_date == "" && $start_date != "" ) {
                $end_date = $start_date;
            }

            $timezone = get_post_meta( $post->ID, "sf_event_timezone" )[0];
            if ( isset($timezone) && !empty($timezone) && $timezone != "" && $timezone != "default" ) { } else {
                $timezone = "";
            }

            $event_date = "";

            if ( $tbd ) {

                $event_date = "<span class=\"event-time-tbd\">" . $tbd_text . "</span>";

            } else if ( $start_date != "" ) {

                if ( $start_date == $end_date ) {
                    $event_date .= '<time datetime="' . date('Y-m-d', strtotime($start_date)) . '">' . date("l, F j, Y", strtotime($start_date));
                    if ( $start_time != "" ) {
                        $event_date .= " at " . date("g:i a", strtotime($start_time));
                    }
                    if ( $start_time != "" && $end_time != "" ) {
                        $event_date .= " - " . date("g:i a", strtotime($end_time));
                    }
                    if ( $start_time != "" || $end_time != "" ) {
                         $event_date .= " " . $timezone;
                    }
                    $event_date .= "</time>";
                } else if ( $start_date != $end_date ) {
                    if ( $occurence == "none" || $occurence == "" ) {
                        $event_date .= '<time datetime="' . date('Y-m-d', strtotime($start_date)) . '">' . date("l, F j, Y", strtotime($start_date));
                        if ( $start_time != "" ) {
                            $event_date .= " at " . date("g:i a", strtotime($start_time));
                        }
                        $event_date .= "</time> - " . '<time datetime="' . date('Y-m-d', strtotime($end_date)) . '">' . date("l, F j, Y", strtotime($end_date));
                        if ( $end_time != "" ) {
                            $event_date .= " at " . date("g:i a", strtotime($end_time));
                        }
                        if ( $start_time != "" || $end_time != "" ) {
                             $event_date .= " " . $timezone;
                        }
                        $event_date .= "</time>";
                    } else if ( $occurence == "daily" ) {
                        $next_occurence = strtotime($start_date);
                        for ( $t = strtotime($start_date); $t <= strtotime($end_date); $t = strtotime(date("Y-m-d", $t) . " +1 day") ) {
                            if ( $t >= $now ) {
                                $next_occurence = $t;
                                break;
                            } else {
                                $next_occurence = $t;
                            }
                        }
                        $event_date .= '<time datetime="' . date('Y-m-d', strtotime( date("Y-m-d", $next_occurence ) ) ) . '">' . date("l, F j, Y", $next_occurence);
                        if ( $start_time != "" ) {
                            $event_date .= " at " . date("g:i a", strtotime($start_time));
                        }
                        if ( $start_time != "" && $end_time != "" ) {
                            $event_date .= " - " . date("g:i a", strtotime($end_time));
                        }
                        if ( $start_time != "" || $end_time != "" ) {
                             $event_date .= " ";
                        }
                        $event_date .= "</time>";
                    } else if ( $occurence == "weekly" ) {
                        $next_occurence = strtotime($start_date);
                        for ( $t = strtotime($start_date); $t <= strtotime($end_date); $t = strtotime(date("Y-m-d", $t) . " +1 week") ) {
                            if ( $t >= $now ) {
                                $next_occurence = $t;
                                break;
                            } else {
                                $next_occurence = $t;
                            }
                        }
                        $event_date .= '<time datetime="' . date('Y-m-d', strtotime( date("Y-m-d", $next_occurence ) ) ) . '">' . date("l, F j, Y", $next_occurence);
                        if ( $start_time != "" ) {
                            $event_date .= " at " . date("g:i a", strtotime($start_time));
                        }
                        if ( $start_time != "" && $end_time != "" ) {
                            $event_date .= " - " . date("g:i a", strtotime($end_time));
                        }
                        if ( $start_time != "" || $end_time != "" ) {
                             $event_date .= " " . $timezone;
                        }
                        $event_date .= "</time>";
                    }
                }

            }

            return $event_date;

        }
    }
    
    /**
     * Register meta boxes
     *
     * @return void
     */

    function event_register_redirect_options() {
        if ( function_exists('get_field') ) {
            add_meta_box( 'redirect_options', __( 'Redirect Options', 'textdomain' ), 'event_redirect_options_callback', 'event', 'side', 'default' );
        }
    }
    add_action( 'add_meta_boxes', 'event_register_redirect_options' );

    /**
     * Meta box display callback.
     *
     * @param WP_Post $post Current post object.
     */
    function event_redirect_options_callback( $post ) {
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
    function event_save_meta_box( $post_id ) {
        // Save logic goes here. Don't forget to include nonce checks!
        if ( get_post_type($post_id) == "event" ) {
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
    add_action( 'save_post', 'event_save_meta_box' );

?>