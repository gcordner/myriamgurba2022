<?php

    /*
    *
    *	Swift Page Builder - Algolia Content Feed Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
    *
    */

    if ( is_plugin_active("socialdriver-algolia/socialdriver-algolia.php") ) {

        /* BLOG ITEMS
        ================================================== */
        if ( ! function_exists( 'algolia_blog_items' ) ) {

            global $sf_query;

            function algolia_blog_items( $atts ) {

                extract( shortcode_atts( array(
                    'feed_blog_id'              => '',
                    'index_override'            => '',
                    'post_type'                 => '',
                    'author'                    => '',
                    'blog_type'                 => 'mini',
                    'gutters'                   => 'yes',
                    'columns'                   => '4',
                    'equal_heights'             => 'no',
                    'fullwidth'                 => 'no',
                    'show_image'                => 'yes',
                    'show_title'                => 'yes',
                    'show_excerpt'              => 'yes',
                    'show_details'              => 'yes',
                    'offset'                    => '0',
                    'excerpt_length'            => '20',
                    'show_read_more'            => 'yes',
                    'item_count'                => '20',
                    'pagination'                => 'no',
                    'blog_keyword'              => 'no',
                    'blog_posttype_filter'      => 'no',
                    'blog_filter'               => 'yes',
                    'blog_filter_scope'         => 'local',
                    'blog_filter_taxonomies'    => '',
                    'link_type'                 => 'page',
                    'alt_styling'               => 'no',
                    'hover_style'               => 'default',
                    'content_output'            => 'excerpt',
                    'width'                     => '1/1',
                    'paged'                     => '',
                    'taxonomies'                => '',
                    'q'                         => '',
                    'blogID'                    => '',
                    'datearchive'               => ''
                ), $atts ) );

                $blog_items_output = "";
                $no_content = '';
                $algolia_options = get_option("maven-algolia");

                global $wp_query, $sf_options, $sf_sidebar_config, $post, $coauthors_plus, $sf_query, $sf_blogID, $sf_blog_atts;

                /* BLOG QUERY SETUP
                ================================================== */

                if ( $paged == "" ) {
                    if ( isset($_GET["pagenum"]) && !empty($_GET["pagenum"]) && $_GET["pagenum"] != "" ) {
                        if ( $pagination == "none" ) {
                            $paged = 1;
                        } else {
                            $paged  = $_GET["pagenum"]; 
                        }
                    } else {
                        $paged = 1;
                    }
                    // $offset = $offset + ( $item_count * ( $paged - 1 ) );
                } else {
                    // $offset = $offset + ( $item_count * ( $paged - 1 ) );
                }

                if ( $post_type == "" ) {
                    $post_types = spb_get_post_types();

                    foreach($post_types as $key => $post_type) {
                        if ( $post_type == "" ) {
                            unset($post_types[$key]);
                        } else {
                            $post_type_object = get_post_type_object($post_type);
                            unset($post_types[$key]);
                            $post_types = array_merge( array( $post_type ), $post_types );
                        }
                    }
                    $post_type = $post_types;
                    if ( is_plugin_active("relevanssi-premium/relevanssi.php") ) {
                        $post_type[] = "attachment";
                    }
                }

                if ( isset($atts["blog_filter_taxonomies"]) && !empty($atts["blog_filter_taxonomies"]) && $atts["blog_filter_taxonomies"] != "" ) {
                    if ( !is_array($atts["blog_filter_taxonomies"]) ) {
                        $blog_filter_taxonomies = explode(",", $atts["blog_filter_taxonomies"]);
                        $blog_filter_taxonomies = array_map('trim', $blog_filter_taxonomies);
                    }
                    foreach ( $blog_filter_taxonomies as $taxonomy ) {
                        if ( is_object($atts["blog_filter_taxonomies"]) ) {
                            $atts["blog_filter_taxonomies"] = get_object_vars($atts["blog_filter_taxonomies"]);
                        }
                        if ( !is_array($atts["blog_filter_taxonomies"]) ) {
                            $atts["blog_filter_taxonomies"] = explode(",", $atts["blog_filter_taxonomies"]);
                            $atts["blog_filter_taxonomies"] = array_map('trim', $atts["blog_filter_taxonomies"]);
                        }  
                        $atts["blog_filter_taxonomies"] = array_diff( $atts["blog_filter_taxonomies"], array($taxonomy) );
                        $atts["blog_filter_taxonomies"][] = $taxonomy;
                    }
                }

                $taxonomy_values = array();
                if ( isset($taxonomies) && !empty($taxonomies) && count($taxonomies) > 0 ) {
                    foreach ( $taxonomies as $taxonomy ) {
                        if ( is_object($atts["taxonomies"]) ) {
                            $atts["taxonomies"] = get_object_vars($atts["taxonomies"]);
                        }
                        if ( !is_array($atts["taxonomies"]) ) {
                            $atts["taxonomies"] = explode(",", $atts["taxonomies"]);
                            $atts["taxonomies"] = array_map('trim', $atts["taxonomies"]);
                        }   
                        $atts["taxonomies"] = $taxonomies = array_diff( $atts["taxonomies"], array($taxonomy) );
                        $atts["taxonomies"][] = $taxonomies[] = $taxonomy;
                        $taxonomy_values[$taxonomy] = array();
                        if ( isset($atts[$taxonomy]) && !empty($atts[$taxonomy]) && $atts[$taxonomy] != "" && $atts[$taxonomy] != "All" && $atts[$taxonomy] != "all" && $atts[$taxonomy] != "0" ) {
                            $taxonomy_values[$taxonomy] = array_merge(array($atts[$taxonomy]), $taxonomy_values[$taxonomy]);
                        }
                        if ( isset($atts[preg_replace('/[-]+/', '_', $taxonomy)]) && !empty($atts[preg_replace('/[-]+/', '_', $taxonomy)]) && $atts[preg_replace('/[-]+/', '_', $taxonomy)] != "" && $atts[$taxonomy] != "All" && $atts[$taxonomy] != "all" && $atts[$taxonomy] != "0" ) {
                            $taxonomy_values[$taxonomy] = array_merge(array($atts[preg_replace('/[-]+/', '_', $taxonomy)]), $taxonomy_values[$taxonomy]);
                        }
                        if ( isset($atts["blog_filter_scope"]) && !empty($atts["blog_filter_scope"]) && $atts["blog_filter_scope"] == "global" && isset($_GET[$taxonomy]) && !empty($_GET[$taxonomy]) && $_GET[$taxonomy] != "" && $_GET[$taxonomy] != "all" && $_GET[$taxonomy] != "All" && $_GET[$taxonomy] != "0" ) {
                           $taxonomy_values[$taxonomy] = array_merge(array($_GET[$taxonomy]), $taxonomy_values[$taxonomy]);
                        }
                        $taxonomy_values[$taxonomy] = array_unique($taxonomy_values[$taxonomy]);
                        if ( is_array($atts["blog_filter_taxonomies"]) && in_array($taxonomy, $atts["blog_filter_taxonomies"]) && isset($_GET[$taxonomy]) ) {
                            $taxonomy_values[$taxonomy] = explode(",", $_GET[$taxonomy]);
                        }
                        $taxonomy_values[$taxonomy] = array_filter($taxonomy_values[$taxonomy]);
                    }
                }

                $blog_args      = array();
                if (!is_array($post_type) && strpos($post_type,', ') !== false) {
                    $post_type = explode(", ",$post_type);
                }
                if (!is_array($post_type) && strpos($post_type,',') !== false) {
                    $post_type = explode(",",$post_type);
                }
                if ( !is_array($post_type) ) {
                    $post_type = array($post_type);
                }

                /* POST TYPE FILTERING
                ================================================== */
                if ( isset($_GET["post-type"]) && !empty($_GET["post-type"]) && $_GET["post-type"] != "" && $_GET["post-type"] != "all" && $_GET["post-type"] != "All" ) {
                    $post_type = $atts["post_type"] = $_GET["post-type"];
                }

                $client = \Algolia\AlgoliaSearch\SearchClient::create($algolia_options["appId"], $algolia_options["apiKeySearch"]);
                $indexName = $algolia_options["defaultIndex"];
                if ( isset($atts["index_override"]) && !empty($atts["index_override"]) && $atts["index_override"] != "" ) {
                    $indexName = $atts["index_override"];
                }
                $index = $client->initIndex($indexName);
                $filterString = "[";

                /* WORDPRESS QUERY */
                $blog_args = array(
                    'post_type'      => $post_type,
                    'post_status'    => 'publish',
                    'paged'          => $paged,
                    'posts_per_page' => $item_count
                );
                /* ALGOLIA QUERY */
                $filterString .= '"public:true"';
                if ( isset($feed_blog_id) && !empty($feed_blog_id) && $feed_blog_id != "all" && $feed_blog_id != "" && get_blog_details($feed_blog_id) ) {
                    $filterString .= ',"siteID:' . $feed_blog_id . '"';
                }
                if ( isset($post_type) && count($post_type) > 0 ) {
                    $filterString .= ',[';
                    foreach( $post_type as $t => $type ) {
                        $filterString .= '"type:' . $type . '"';
                        if ( $t < count($post_type)-1 ) {
                            $filterString .= ',';
                        }
                    }
                    $filterString .= ']';
                }

                /* TAXONOMY SLUG MODIFICATION
                ================================================== */
                if ( isset($taxonomies) && !empty($taxonomies) && count($taxonomies) > 0 ) {
                    $blog_args = array_merge(array( 'tax_query' => array("relation" => "AND") ), $blog_args);
                    foreach ( $taxonomies as $taxonomy ) {
                        $taxonomy_values[$taxonomy] = array_unique($taxonomy_values[$taxonomy]);
                        $taxonomy_values[$taxonomy] = array_values($taxonomy_values[$taxonomy]);
                        $taxonomy_values[$taxonomy] = array_diff($taxonomy_values[$taxonomy], array("ALL", "all", "", "0"));
                        $taxonomy_array = $taxonomy_values[$taxonomy];
                        if ( is_array($taxonomy_array) && count($taxonomy_array) > 0 ) {
                            $post_types = spb_post_types_by_taxonomy( $taxonomy );
                            if ( count($post_types) <= 0 ) {
                                $post_types = spb_post_types_by_taxonomy( $taxonomy );
                            }
                            if ( count($post_types) > 0 ) {
                                foreach($taxonomy_array as $t => $taxonomy_term) {
                                    if ( strpos($taxonomy_term, ',') !== false ) {
                                        unset($taxonomy_array[$t]);
                                        if ( strtolower($taxonomy_term) != "all" ) {
                                            $taxonomy_array = array_merge($taxonomy_array, explode(",", $taxonomy_term));
                                            $taxonomy_term_array = array_map('trim', $taxonomy_term_array);
                                        }
                                    }
                                }
                                $taxonomy_array = array_diff( $taxonomy_array, array("all") );
                                $taxonomy_array = array_diff( $taxonomy_array, array("All") );
                                if ( count($taxonomy_array) > 0 ) {
                                    /* WORDPRESS QUERY */
                                    $blog_args['tax_query'] = array_merge( array(
                                            array(
                                                'taxonomy' => $taxonomy,
                                                'field'    => 'slug',
                                                'terms'    => array_unique($taxonomy_array)
                                            )
                                        ), $blog_args['tax_query']);
                                    /* ALGOLIA QUERY */
                                    $filterString .= ',[';
                                    foreach( $taxonomy_array as $t => $term ) {
                                        $filterString .= '"' . $taxonomy . ':' . get_term_by('slug', $term, $taxonomy)->name . '"';
                                        if ( $t < count($taxonomy_array)-1 ) {
                                            $filterString .= ',';
                                        }
                                    }
                                    $filterString .= ']';
                                }
                            }
                        }
                    }
                }

                if ( $blog_args["tax_query"] == array("relation" => "AND") ) {
                    unset($blog_args["tax_query"]);
                }

                /* TITLE STARTS WITH
                ================================================== */
                
                if ( isset($atts["blog_az_filtering"]) && !empty($atts["blog_az_filtering"]) && $atts["blog_az_filtering"] == "yes" && isset($_GET["alphabet"]) && !empty($_GET["alphabet"]) && $_GET["alphabet"] != "" && $_GET["alphabet"] != "All" && $_GET["alphabet"] != "all" ) {              
                    $blog_args = array_merge(array(
                        'starts_with' => $_GET["alphabet"],
                    ), $blog_args);
                    $filterString .= ',"alpha:' . $_GET["alphabet"] . '"';
                }

                /* KEYWORD SEARCH
                ================================================== */
                
                if ( isset($atts["blog_filter_scope"]) && !empty($atts["blog_filter_scope"]) && $atts["blog_filter_scope"] == "global" && isset($q) && !empty($q) && $q != "" ) {
                    if ( isset($_GET["search"]) && $_GET["search"] != "" ) {
                        $q = $atts["q"] = urldecode(stripslashes($_GET["search"]));
                    } else if ( isset($_GET["keyword"]) && $_GET["keyword"] != "" ) {
                        $q = $atts["q"] = urldecode(stripslashes($_GET["keyword"]));
                    } else if ( isset($_GET["s"]) && $_GET["s"] != "" ) {
                        $q = $atts["q"] = urldecode(stripslashes($_GET["s"]));
                    }
                }

                if ( isset($q) && !empty($q) && $q != "" ) {
                    $blog_args = array_merge(array(
                        's'      => $q
                    ), $blog_args);
                }

                $sf_query = $blog_args;
                $sf_blog_atts[$blogID] = $atts;

                $blog_args["offset"] = ( ( $blog_args["paged"] - 1 ) * $blog_args["posts_per_page"] ) + $offset;
                unset($blog_args["paged"]);

                $filterString .= "]";

                try {

                    $results = $index->search($q, ['hitsPerPage' => $item_count, 'page' => $paged - 1, 'facetFilters' => $filterString]);

                    $wp_query->max_num_pages = $results["nbHits"];

                    /* LIST CLASS CONFIG
                    ================================================== */
                    $list_class = $wrap_class = '';
                    if ( strpos($blog_type, 'masonry') !== false ) {
                        $list_class .= 'masonry-items';
                        if ( $gutters == "no" ) {
                            $list_class .= ' no-gutters';
                        } else {
                            $list_class .= ' gutters';
                        }
                        // Thumb Type
                        if ( $hover_style == "default" && function_exists( 'sf_get_thumb_type' ) ) {
                            $list_class .= ' ' . sf_get_thumb_type();
                        } else {
                            $list_class .= ' thumbnail-' . $hover_style;
                        }
                    } else if ( $blog_type == "mini" ) {
                        $list_class .= 'mini-items';
                    } else {
                        $list_class .= 'standard-items row';
                    }

                    if ( $alt_styling == "yes" ) {
                        $list_class .= ' alt-styling';
                    }

                    if ( $pagination == "infinite-scroll" ) {
                        $list_class .= ' blog-inf-scroll';
                    } else if ( $pagination == "load-more" ) {
                        $list_class .= ' blog-load-more';
                    }

                    $list_class .= ' filter-scope-' . $blog_filter_scope;
                    
                    /* BLOG ITEMS OUTPUT
                    ================================================== */
                    $blog_items_output .= '<div class="row items-row-wrap">';
                    $blog_items_output .= '<div class="container">' . sd_get_template_part( 'content/results-summary', '', true ) . "</div>";
                    $blog_items_output .= '<div class="container"><div class="blog-items-wrap columns-' . $columns . ' blog-' . $blog_type . ' ' . $wrap_class . '"><noscript><style type="text/css"> .blog-item {opacity:1 !important;} </style></noscript>';
                        
                        $blog_items_output .= '<ul class="blog-items columns-' . $columns . ' ' . $list_class . ' clearfix" data-found-posts="' . $results["nbHits"] . '" data-blog-type="' . $blog_type . '" data-atts="' . rawurlencode(json_encode($atts)) . '" data-post-type="' . implode(",", $post_type) . '" data-paged="' . $paged . '" data-template="algolia_feed" data-scope="' . $blog_filter_scope . '" data-id="' . $blogID . '"';

                        if ( isset($taxonomies) && !empty($taxonomies) && count($taxonomies) > 0 ) {
                            foreach ( $taxonomies as $taxonomy ) {
                                $blog_items_output .= ' data-' . $taxonomy . '="' . implode(",", $taxonomy_values[$taxonomy] ) . '"';
                            }
                        }

                        if ( is_array($post_type) ) {
                            $blog_items_output .= ' role="list">';
                        } else {
                            $blog_items_output .= ' role="list">';
                        }

                        $alpha = "";
                        $count = 0;

                        if ( count($results["hits"]) > 0 ) {
                            $blog_items = $results["hits"];
                            $current_blog_id = get_current_blog_id();
                            foreach($blog_items as $result) {

                                global $blog_id;
                                if ($result["objectID"] > 1000000000) {
                                    $result["objectID"] = $result["objectID"]-(1000000000*$blog_id);
                                }
                                if ( isset($result["siteID"]) && !empty($result["siteID"]) && $result["siteID"] != "" ) {
                                    switch_to_blog( $result["siteID"] );
                                }

                                $post = get_post( $result["objectID"] );
                                setup_postdata( $result["objectID"] );

                                if ( $blog_type == "mini" || $blog_type == "standard" ) {
                                    $item_class = "col-sm-12";
                                } else if ( strpos($blog_type, 'masonry') !== false || strpos($blog_type, 'directory') !== false ) {
                                    if ( $columns == "5" ) {
                                        $item_class = "col-sm-sf-5";
                                    } else if ( $columns == "4" ) {
                                        $item_class = "col-sm-3";
                                    } else if ( $columns == "3" ) {
                                        $item_class = "col-sm-4";
                                    } else if ( $columns == "2" ) {
                                        $item_class = "col-sm-6";
                                    } else if ( $columns == "1" ) {
                                        $item_class = "col-sm-12";
                                    }
                                } else {
                                    $item_class = "col-sm-12";
                                }

                                $taxonomy_name = 'category';
                                $post_type = get_post_type();
                                if ( $post_type != "post") {
                                    $taxonomy_name = $post_type . '-category';
                                }

                                $post_terms = get_the_terms( $post->ID, $taxonomy_name );
                                $term_slug  = " ";

                                if ( ! empty( $post_terms ) ) {
                                    foreach ( $post_terms as $post_term ) {
                                        if ( ! empty( $post_term ) && ! empty( $post_term->slug ) ) {
                                            $term_slug = $term_slug . $post_term->slug . ' ';
                                        }
                                    }
                                }

                                $classes = get_post_class();

                                if ( $show_image != "yes" ) {
                                    $classes = array_diff( $classes, array("has-post-thumbnail") );
                                }

                                /* BLOG ITEM OUTPUT
                                ================================================== */
                                $blog_items_output .= '<li class="blog-item blog-item-' . $blogID . ' ' . $item_class . ' ' . $term_slug . ' ' . implode( ' ', $classes ) . '" id="' . get_the_ID() . '" data-date="' . get_the_time( 'U' ) . '" role="presentation">';
                                    $blog_items_output .= algolia_get_post_item( $result, $blog_type, $show_title, $show_excerpt, $show_details, $excerpt_length, $content_output, $show_read_more, $fullwidth, $show_image, $link_type );
                                $blog_items_output .= '</li>';

                                $count++;
                                restore_current_blog();

                            }

                            switch_to_blog( $current_blog_id );

                        } else {

                            $blog_items_output .= sd_get_template_part( 'content/no-results', '', true );

                            $no_content = true;

                        }

                        wp_reset_postdata();

                        $blog_items_output .= '</ul>';

                        /* PAGINATION OUTPUT
                        ================================================== */
                        if ( $no_content != true ) {
                            if ( $pagination == "infinite-scroll" ) {

                                global $sf_include_infscroll;
                                $sf_include_infscroll = true;

                                $blog_items_output .= '<div class="pagination-wrap hidden">';
                                $blog_items_output .= algolia_pagenavi( $results );
                                $blog_items_output .= '</div>';

                            } else if ( $pagination == "load-more" ) {

                                global $sf_include_infscroll;
                                $sf_include_infscroll = true;

                                if ( $post_type != "" ) {
                                    $post_type_plural = get_post_type_object ( $post_type )->labels->name;
                                    $button_title = "Load more " . strtolower($post_type_plural);
                                } else {
                                    $button_title = "Load more content";
                                }

                                $blog_items_output .= '<a href="#" class="load-more-btn" title="' . esc_attr($button_title) . '">' . __( 'Load More', 'swiftframework' ) . '</a>';

                                $blog_items_output .= '<div class="pagination-wrap load-more hidden">';
                                $blog_items_output .= algolia_pagenavi( $results );
                                $blog_items_output .= '</div>';

                            } else if ( $pagination == "standard" ) {
                                if ( strpos($blog_type, 'masonry') !== false ) {
                                    $blog_items_output .= '<div class="pagination-wrap standard-pagination masonry-pagination">';
                                } else {
                                    $blog_items_output .= '<div class="pagination-wrap standard-pagination">';
                                }
                                $blog_items_output .= algolia_pagenavi( $results );
                                $blog_items_output .= '</div>';
                            }
                        }

                    $blog_items_output .= '</div></div></div>';

                } catch (Exception $e) {
                    
                    $blog_items_output .= "\n\t\t\t" . '<div class="no-results"><p>ERROR!</p></div>';

                }

                /* FUNCTION OUTPUT
                ================================================== */

                return $blog_items_output;

            }
        }

        /* Function that performs a Boxed Style Numbered Pagination (also called Page Navigation).
           Function is largely based on Version 2.4 of the WP-PageNavi plugin */
        if ( ! function_exists( 'algolia_pagenavi' ) ) {
            function algolia_pagenavi( $query, $before = '', $after = '' ) {

                wp_reset_query();
                global $wpdb, $paged;

                $pagenavi_options = array();
                //$pagenavi_options['pages_text'] = ('Page %CURRENT_PAGE% of %TOTAL_PAGES%:');
                $pagenavi_options['pages_text']                   = ( '' );
                $pagenavi_options['current_text']                 = '%PAGE_NUMBER%';
                $pagenavi_options['page_text']                    = '%PAGE_NUMBER%';
                $pagenavi_options['first_text']                   = __( 'First Page', 'swiftframework' );
                $pagenavi_options['last_text']                    = __( 'Last Page', 'swiftframework' );
                $pagenavi_options['next_text']                    = __( "Next <i class='ss-navigateright'></i>", "swiftframework" );
                $pagenavi_options['prev_text']                    = __( "<i class='ss-navigateleft'></i> Previous", "swiftframework" );
                $pagenavi_options['dotright_text']                = '…';
                $pagenavi_options['dotleft_text']                 = '…';
                $pagenavi_options['num_pages']                    = 5; //continuous block of page numbers
                $pagenavi_options['always_show']                  = 0;
                $pagenavi_options['num_larger_page_numbers']      = 0;
                $pagenavi_options['larger_page_numbers_multiple'] = 5;

                $output = "";

                //If NOT a single Post is being displayed
                /*http://codex.wordpress.org/Function_Reference/is_single)*/
                if ( ! is_single() ) {
                    $request = $query->request;
                    //intval - Get the integer value of a variable
                    /*http://php.net/manual/en/function.intval.php*/
                    $posts_per_page = intval( get_query_var( 'posts_per_page' ) );
                    //Retrieve variable in the WP_Query class.
                    /*http://codex.wordpress.org/Function_Reference/get_query_var*/
                    $paged = $query["page"] + 1;
                    $numposts = $query["nbHits"];
                    $max_page = $query["nbPages"];

                    //empty - Determine whether a variable is empty
                    /*http://php.net/manual/en/function.empty.php*/
                    if ( empty( $paged ) || $paged == 0 ) {
                        $paged = 1;
                    }

                    $pages_to_show         = intval( $pagenavi_options['num_pages'] );
                    $larger_page_to_show   = intval( $pagenavi_options['num_larger_page_numbers'] );
                    $larger_page_multiple  = intval( $pagenavi_options['larger_page_numbers_multiple'] );
                    $pages_to_show_minus_1 = $pages_to_show - 1;
                    $half_page_start       = floor( $pages_to_show_minus_1 / 2 );
                    //ceil - Round fractions up (http://us2.php.net/manual/en/function.ceil.php)
                    $half_page_end = ceil( $pages_to_show_minus_1 / 2 );
                    $start_page    = $paged - $half_page_start;

                    if ( $start_page <= 0 ) {
                        $start_page = 1;
                    }

                    $end_page = $paged + $half_page_end;
                    if ( ( $end_page - $start_page ) != $pages_to_show_minus_1 ) {
                        $end_page = $start_page + $pages_to_show_minus_1;
                    }
                    if ( $end_page > $max_page ) {
                        $start_page = $max_page - $pages_to_show_minus_1;
                        $end_page   = $max_page;
                    }
                    if ( $start_page <= 0 ) {
                        $start_page = 1;
                    }

                    $larger_per_page = $larger_page_to_show * $larger_page_multiple;
                    //round_num() custom function - Rounds To The Nearest Value.
                    $larger_start_page_start = ( round_num( $start_page, 10 ) + $larger_page_multiple ) - $larger_per_page;
                    $larger_start_page_end   = round_num( $start_page, 10 ) + $larger_page_multiple;
                    $larger_end_page_start   = round_num( $end_page, 10 ) + $larger_page_multiple;
                    $larger_end_page_end     = round_num( $end_page, 10 ) + ( $larger_per_page );

                    if ( $larger_start_page_end - $larger_page_multiple == $start_page ) {
                        $larger_start_page_start = $larger_start_page_start - $larger_page_multiple;
                        $larger_start_page_end   = $larger_start_page_end - $larger_page_multiple;
                    }
                    if ( $larger_start_page_start <= 0 ) {
                        $larger_start_page_start = $larger_page_multiple;
                    }
                    if ( $larger_start_page_end > $max_page ) {
                        $larger_start_page_end = $max_page;
                    }
                    if ( $larger_end_page_end > $max_page ) {
                        $larger_end_page_end = $max_page;
                    }
                    if ( $max_page > 1 || intval( $pagenavi_options['always_show'] ) == 1 ) {
                        /*http://php.net/manual/en/function.str-replace.php */
                        /*number_format_i18n(): Converts integer number to format based on locale (wp-includes/functions.php*/
                        $pages_text = str_replace( "%CURRENT_PAGE%", number_format_i18n( $paged ), $pagenavi_options['pages_text'] );
                        $pages_text = str_replace( "%TOTAL_PAGES%", number_format_i18n( $max_page ), $pages_text );
                        $output .= $before . '<ul class="pagenavi bar-styling">' . "\n";

                        if ( ! empty( $pages_text ) ) {
                            $output .= '<li><span class="pages">' . $pages_text . '</span></li>';
                        }
                        //Displays a link to the previous post which exists in chronological order from the current post.
                        /*http://codex.wordpress.org/Function_Reference/previous_post_link*/
                        if ( $paged > 1 ) {
                            $pagURL = get_pagenum_link( $paged );
                            $pagURLBase = explode("page/", explode("?", $pagURL)[0])[0];
                            if ( strpos($pagURL, '?') !== false ) {
                                $pagURLParams = explode("?", $pagURL)[1];
                                if ( strpos($pagURLParams, '&') !== false ) {
                                    $pagURLParams = explode("&", $pagURLParams);
                                } else {
                                    $pagURLParams = [$pagURLParams];
                                }
                                foreach($pagURLParams as $index => $pagURLParam) {
                                    if ( substr($pagURLParam, 0 , 5) == "#038;" ) {
                                        $pagURLParam = substr($pagURLParam, 5);
                                    }
                                    if ( strpos($pagURLParam, '=') !== false ) { } else {
                                        $pagURLParams[$index] = $pagURLParam . "=";
                                    } 
                                    if ( strpos($pagURLParam, '?') !== false || strpos($pagURLParam, 'pagenum=') !== false ) {
                                        unset($pagURLParams[$index]);
                                    }
                                    if ( substr($pagURLParam, 0 , 4) == "atts" || substr($pagURLParam, 0 , 9) == "#038;atts" ) {
                                        unset($pagURLParams[$index]);
                                    }
                                    if ( substr($pagURLParam, 0 , 12) == "permalinkURL" || substr($pagURLParam, 0 , 17) == "#038;permalinkURL" || substr($pagURLParam, 0 , 11) == "datearchive" || substr($pagURLParam, 0 , 16) == "#038;datearchive" || substr($pagURLParam, 0 , 6) == "blogID" || substr($pagURLParam, 0 , 11) == "#038;blogID" ) {
                                        unset($pagURLParams[$index]);
                                    }
                                }
                                $pagURLParams[] = "pagenum=" . $paged;
                                $pagURL = $pagURLBase . "?" . implode("&", $pagURLParams);
                            } else {
                                $pagURL = $pagURLBase . "?pagenum=" . $paged;
                            }
                            $pagURL = str_replace("pagenum=" . $paged, "pagenum=" . ($paged - 1), $pagURL);
                            $output .= '<li class="prev"><a href="' . $pagURL . '"><i class="ss-navigateleft"></i> Previous</a></li>';
                        }

                        if ( $start_page >= 2 && $pages_to_show < $max_page ) {
                            $first_page_text = str_replace( "%TOTAL_PAGES%", number_format_i18n( $max_page ), $pagenavi_options['first_text'] );
                            //esc_url(): Encodes < > & " ' (less than, greater than, ampersand, double quote, single quote).
                            /*http://codex.wordpress.org/Data_Validation*/
                            //get_pagenum_link():(wp-includes/link-template.php)-Retrieve get links for page numbers.
                            $pagURL = get_pagenum_link();
                            $pagURLBase = explode("page/", explode("?", $pagURL)[0])[0];
                            if ( strpos($pagURL, '?') !== false ) {
                                $pagURLParams = explode("?", $pagURL)[1];
                                if ( strpos($pagURLParams, '&') !== false ) {
                                    $pagURLParams = explode("&", $pagURLParams);
                                } else {
                                    $pagURLParams = [$pagURLParams];
                                }
                                foreach($pagURLParams as $index => $pagURLParam) {
                                    if ( substr($pagURLParam, 0 , 5) == "#038;" ) {
                                        $pagURLParam = substr($pagURLParam, 5);
                                    }
                                    if ( strpos($pagURLParam, '=') !== false ) { } else {
                                        $pagURLParams[$index] = $pagURLParam . "=";
                                    } 
                                    if ( strpos($pagURLParam, '?') !== false || strpos($pagURLParam, 'pagenum=') !== false ) {
                                        unset($pagURLParams[$index]);
                                    }
                                    if ( substr($pagURLParam, 0 , 4) == "atts" || substr($pagURLParam, 0 , 9) == "#038;atts" ) {
                                        unset($pagURLParams[$index]);
                                    }
                                    if ( substr($pagURLParam, 0 , 12) == "permalinkURL" || substr($pagURLParam, 0 , 17) == "#038;permalinkURL" ) {
                                        unset($pagURLParams[$index]);
                                    }
                                    if ( substr($pagURLParam, 0 , 12) == "permalinkURL" || substr($pagURLParam, 0 , 17) == "#038;permalinkURL" || substr($pagURLParam, 0 , 11) == "datearchive" || substr($pagURLParam, 0 , 16) == "#038;datearchive" || substr($pagURLParam, 0 , 6) == "blogID" || substr($pagURLParam, 0 , 11) == "#038;blogID" ) {
                                        unset($pagURLParams[$index]);
                                    }
                                }
                                $pagURLParams[] = "pagenum=" . $i;
                                $pagURL = $pagURLBase . "?" . implode("&", $pagURLParams);
                            } else {
                                $pagURL = $pagURLBase . "?pagenum=" . $i;
                            }
                            $output .= '<li><a href="' . esc_url( $pagURL ) . '" class="first" title="' . $first_page_text . '">1</a></li>';
                            if ( ! empty( $pagenavi_options['dotleft_text'] ) ) {
                                $output .= '<li><span class="expand">' . $pagenavi_options['dotleft_text'] . '</span></li>';
                            }
                        }

                        if ( $larger_page_to_show > 0 && $larger_start_page_start > 0 && $larger_start_page_end <= $max_page ) {
                            for ( $i = $larger_start_page_start; $i < $larger_start_page_end; $i += $larger_page_multiple ) {
                                $page_text = str_replace( "%PAGE_NUMBER%", number_format_i18n( $i ), $pagenavi_options['page_text'] );
                                $pagURL = get_pagenum_link( $i );
                                $pagURLBase = explode("page/", explode("?", $pagURL)[0])[0];
                                if ( strpos($pagURL, '?') !== false ) {
                                    $pagURLParams = explode("?", $pagURL)[1];
                                    if ( strpos($pagURLParams, '&') !== false ) {
                                        $pagURLParams = explode("&", $pagURLParams);
                                    } else {
                                        $pagURLParams = [$pagURLParams];
                                    }
                                    foreach($pagURLParams as $index => $pagURLParam) {
                                        if ( substr($pagURLParam, 0 , 5) == "#038;" ) {
                                            $pagURLParam = substr($pagURLParam, 5);
                                        }
                                        if ( strpos($pagURLParam, '=') !== false ) { } else {
                                            $pagURLParams[$index] = $pagURLParam . "=";
                                        } 
                                        if ( strpos($pagURLParam, '?') !== false || strpos($pagURLParam, 'pagenum=') !== false ) {
                                            unset($pagURLParams[$index]);
                                        }
                                        if ( substr($pagURLParam, 0 , 4) == "atts" || substr($pagURLParam, 0 , 9) == "#038;atts" ) {
                                            unset($pagURLParams[$index]);
                                        }
                                        if ( substr($pagURLParam, 0 , 12) == "permalinkURL" || substr($pagURLParam, 0 , 17) == "#038;permalinkURL" ) {
                                            unset($pagURLParams[$index]);
                                        }
                                        if ( substr($pagURLParam, 0 , 12) == "permalinkURL" || substr($pagURLParam, 0 , 17) == "#038;permalinkURL" || substr($pagURLParam, 0 , 11) == "datearchive" || substr($pagURLParam, 0 , 16) == "#038;datearchive" || substr($pagURLParam, 0 , 6) == "blogID" || substr($pagURLParam, 0 , 11) == "#038;blogID" ) {
                                            unset($pagURLParams[$index]);
                                        }
                                    }
                                    $pagURLParams[] = "pagenum=" . $i;
                                    $pagURL = $pagURLBase . "?" . implode("&", $pagURLParams);
                                } else {
                                    $pagURL = $pagURLBase . "?pagenum=" . $i;
                                }
                                $output .= '<li><a href="' . esc_url( $pagURL ) . '" class="single_page" title="' . $page_text . '">' . $page_text . '</a></li>';
                            }
                        }

                        for ( $i = $start_page; $i <= $end_page; $i ++ ) {
                            if ( $i == $paged ) {
                                $current_page_text = str_replace( "%PAGE_NUMBER%", number_format_i18n( $i ), $pagenavi_options['current_text'] );
                                $output .= '<li><span class="current">' . $current_page_text . '</span></li>';
                            } else {
                                $page_text = str_replace( "%PAGE_NUMBER%", number_format_i18n( $i ), $pagenavi_options['page_text'] );
                                $pagURL = get_pagenum_link( $i );
                                $pagURLBase = explode("page/", explode("?", $pagURL)[0])[0];
                                if ( strpos($pagURL, '?') !== false ) {
                                    $pagURLParams = explode("?", $pagURL)[1];
                                    if ( strpos($pagURLParams, '&') !== false ) {
                                        $pagURLParams = explode("&", $pagURLParams);
                                    } else {
                                        $pagURLParams = [$pagURLParams];
                                    }
                                    foreach($pagURLParams as $index => $pagURLParam) {
                                        if ( substr($pagURLParam, 0 , 5) == "#038;" ) {
                                            $pagURLParam = substr($pagURLParam, 5);
                                        }
                                        if ( strpos($pagURLParam, '=') !== false ) { } else {
                                            $pagURLParams[$index] = $pagURLParam . "=";
                                        } 
                                        if ( strpos($pagURLParam, '?') !== false || strpos($pagURLParam, 'pagenum=') !== false ) {
                                            unset($pagURLParams[$index]);
                                        }
                                        if ( substr($pagURLParam, 0 , 4) == "atts" || substr($pagURLParam, 0 , 9) == "#038;atts" ) {
                                            unset($pagURLParams[$index]);
                                        }
                                        if ( substr($pagURLParam, 0 , 12) == "permalinkURL" || substr($pagURLParam, 0 , 17) == "#038;permalinkURL" ) {
                                            unset($pagURLParams[$index]);
                                        }
                                        if ( substr($pagURLParam, 0 , 12) == "permalinkURL" || substr($pagURLParam, 0 , 17) == "#038;permalinkURL" || substr($pagURLParam, 0 , 11) == "datearchive" || substr($pagURLParam, 0 , 16) == "#038;datearchive" || substr($pagURLParam, 0 , 6) == "blogID" || substr($pagURLParam, 0 , 11) == "#038;blogID" ) {
                                            unset($pagURLParams[$index]);
                                        }
                                    }
                                    $pagURLParams[] = "pagenum=" . $i;
                                    $pagURL = $pagURLBase . "?" . implode("&", $pagURLParams);
                                } else {
                                    $pagURL = $pagURLBase . "?pagenum=" . $i;
                                }
                                $output .= '<li><a href="' . esc_url( $pagURL ) . '" class="single_page" title="' . $page_text . '">' . $page_text . '</a></li>';
                            }
                        }

                        if ( $end_page < $max_page ) {
                            if ( ! empty( $pagenavi_options['dotright_text'] ) ) {
                                $output .= '<li><span class="expand">' . $pagenavi_options['dotright_text'] . '</span></li>';
                            }
                            $last_page_text = str_replace( "%TOTAL_PAGES%", number_format_i18n( $max_page ), $pagenavi_options['last_text'] );
                            $pagURL = get_pagenum_link( $max_page );
                            $pagURLBase = explode("page/", explode("?", $pagURL)[0])[0];
                            if ( strpos($pagURL, '?') !== false ) {
                                $pagURLParams = explode("?", $pagURL)[1];
                                if ( strpos($pagURLParams, '&') !== false ) {
                                    $pagURLParams = explode("&", $pagURLParams);
                                } else {
                                    $pagURLParams = [$pagURLParams];
                                }
                                foreach($pagURLParams as $index => $pagURLParam) {
                                    if ( substr($pagURLParam, 0 , 5) == "#038;" ) {
                                        $pagURLParam = substr($pagURLParam, 5);
                                    }
                                    if ( strpos($pagURLParam, '=') !== false ) { } else {
                                        $pagURLParams[$index] = $pagURLParam . "=";
                                    } 
                                    if ( strpos($pagURLParam, '?') !== false || strpos($pagURLParam, 'pagenum=') !== false ) {
                                        unset($pagURLParams[$index]);
                                    }
                                    if ( substr($pagURLParam, 0 , 4) == "atts" || substr($pagURLParam, 0 , 9) == "#038;atts" ) {
                                        unset($pagURLParams[$index]);
                                    }
                                    if ( substr($pagURLParam, 0 , 12) == "permalinkURL" || substr($pagURLParam, 0 , 17) == "#038;permalinkURL" ) {
                                        unset($pagURLParams[$index]);
                                    }
                                    if ( substr($pagURLParam, 0 , 12) == "permalinkURL" || substr($pagURLParam, 0 , 17) == "#038;permalinkURL" || substr($pagURLParam, 0 , 11) == "datearchive" || substr($pagURLParam, 0 , 16) == "#038;datearchive" || substr($pagURLParam, 0 , 6) == "blogID" || substr($pagURLParam, 0 , 11) == "#038;blogID" ) {
                                        unset($pagURLParams[$index]);
                                    }
                                }
                                $pagURLParams[] = "pagenum=" . $max_page;
                                $pagURL = $pagURLBase . "?" . implode("&", $pagURLParams);
                            } else {
                                $pagURL = $pagURLBase . "?pagenum=" . $max_page;
                            }
                            $output .= '<li><a href="' . esc_url( $pagURL ) . '" class="last" title="' . $last_page_text . '">' . $max_page . '</a></li>';
                        }
                        $pagURL = get_pagenum_link( $paged );
                        $pagURLBase = explode("page/", explode("?", $pagURL)[0])[0];
                        if ( strpos($pagURL, '?') !== false ) {
                            $pagURLParams = explode("?", $pagURL)[1];
                            if ( strpos($pagURLParams, '&') !== false ) {
                                $pagURLParams = explode("&", $pagURLParams);
                            } else {
                                $pagURLParams = [$pagURLParams];
                            }
                            foreach($pagURLParams as $index => $pagURLParam) {
                                if ( substr($pagURLParam, 0 , 5) == "#038;" ) {
                                    $pagURLParam = substr($pagURLParam, 5);
                                }
                                if ( strpos($pagURLParam, '=') !== false ) { } else {
                                    $pagURLParams[$index] = $pagURLParam . "=";
                                } 
                                if ( strpos($pagURLParam, '?') !== false || strpos($pagURLParam, 'pagenum=') !== false ) {
                                    unset($pagURLParams[$index]);
                                }
                                if ( substr($pagURLParam, 0 , 4) == "atts" || substr($pagURLParam, 0 , 9) == "#038;atts" ) {
                                    unset($pagURLParams[$index]);
                                }
                                if ( substr($pagURLParam, 0 , 12) == "permalinkURL" || substr($pagURLParam, 0 , 17) == "#038;permalinkURL" ) {
                                    unset($pagURLParams[$index]);
                                }
                                if ( substr($pagURLParam, 0 , 12) == "permalinkURL" || substr($pagURLParam, 0 , 17) == "#038;permalinkURL" || substr($pagURLParam, 0 , 11) == "datearchive" || substr($pagURLParam, 0 , 16) == "#038;datearchive" || substr($pagURLParam, 0 , 6) == "blogID" || substr($pagURLParam, 0 , 11) == "#038;blogID" ) {
                                    unset($pagURLParams[$index]);
                                }
                            }
                            $pagURLParams[] = "pagenum=" . $paged;
                            $pagURL = $pagURLBase . "?" . implode("&", $pagURLParams);
                        } else {
                            $pagURL = $pagURLBase . "?pagenum=" . $paged;
                        }
                        $pagURL = str_replace("pagenum=" . $paged, "pagenum=" . ($paged + 1), $pagURL);
                        if ( $paged < $max_page ) {
                            $output .= '<li class="next"><a href="' . $pagURL . '">Next <i class="ss-navigateright"></i></a></li>';
                        }

                        if ( $larger_page_to_show > 0 && $larger_end_page_start < $max_page ) {
                            for ( $i = $larger_end_page_start; $i <= $larger_end_page_end; $i += $larger_page_multiple ) {
                                $page_text = str_replace( "%PAGE_NUMBER%", number_format_i18n( $i ), $pagenavi_options['page_text'] );
                                $pagURL = get_pagenum_link( $i );
                                $pagURLBase = explode("page/", explode("?", $pagURL)[0])[0];
                                if ( strpos($pagURL, '?') !== false ) {
                                    $pagURLParams = explode("?", $pagURL)[1];
                                    if ( strpos($pagURLParams, '&') !== false ) {
                                        $pagURLParams = explode("&", $pagURLParams);
                                    } else {
                                        $pagURLParams = [$pagURLParams];
                                    }
                                    foreach($pagURLParams as $index => $pagURLParam) {
                                        if ( substr($pagURLParam, 0 , 5) == "#038;" ) {
                                            $pagURLParam = substr($pagURLParam, 5);
                                        }
                                        if ( strpos($pagURLParam, '=') !== false ) { } else {
                                            $pagURLParams[$index] = $pagURLParam . "=";
                                        } 
                                        if ( strpos($pagURLParam, '?') !== false || strpos($pagURLParam, 'pagenum=') !== false ) {
                                            unset($pagURLParams[$index]);
                                        }
                                        if ( substr($pagURLParam, 0 , 4) == "atts" || substr($pagURLParam, 0 , 9) == "#038;atts" ) {
                                            unset($pagURLParams[$index]);
                                        }
                                        if ( substr($pagURLParam, 0 , 12) == "permalinkURL" || substr($pagURLParam, 0 , 17) == "#038;permalinkURL" ) {
                                            unset($pagURLParams[$index]);
                                        }
                                        if ( substr($pagURLParam, 0 , 12) == "permalinkURL" || substr($pagURLParam, 0 , 17) == "#038;permalinkURL" || substr($pagURLParam, 0 , 11) == "datearchive" || substr($pagURLParam, 0 , 16) == "#038;datearchive" || substr($pagURLParam, 0 , 6) == "blogID" || substr($pagURLParam, 0 , 11) == "#038;blogID" ) {
                                            unset($pagURLParams[$index]);
                                        }
                                    }
                                    $pagURLParams[] = "pagenum=" . $i;
                                    $pagURL = $pagURLBase . "?" . implode("&", $pagURLParams);
                                } else {
                                    $pagURL = $pagURLBase . "?pagenum=" . $i;
                                }
                                $output .= '<li><a href="' . esc_url( $pagURL ) . '" class="single_page" title="' . $page_text . '">' . $page_text . '</a></li>';
                            }
                        }
                        $output .= '</ul>' . $after . "\n";
                    }
                }

                return $output;
            }
        }

        global $sf_options, $sf_blog_atts, $sf_opts, $wp_registered_sidebars;

        class SwiftPageBuilderShortcode_spb_algolia_feed extends SwiftPageBuilderShortcode {

            protected function content( $atts, $content = null ) {

                $output = $title = $post_type = $author = $blog_type = $gutters = $columns = $equal_heights = $fullwidth = $show_image = $show_title = $show_excerpt = $show_details = $offset = $order_by = $order = $excerpt_length = $show_read_more = $item_count = $pagination = $blog_keyword = $blog_filter = $blog_filter_scope = $blog_filter_taxonomies = $alt_styling = $blog_filter_display = $hover_style = $content_output = $el_position = $width = $el_class = '';

                extract( shortcode_atts( array(
                    'title'                     => '',
                    'icon'                      => '',
                    'blog_post_type'            => '',
                    'author'                    => '',
                    'blog_type'                 => 'mini',
                    'gutters'                   => 'yes',
                    'columns'                   => '4',
                    'equal_heights'             => 'no',
                    'fullwidth'                 => 'no',
                    'show_image'                => 'yes',
                    'show_title'                => 'yes',
                    'show_excerpt'              => 'yes',
                    'show_details'              => 'yes',
                    'offset'                    => '0',
                    'order_by'                  => 'date',
                    'order'                     => 'DESC',
                    'excerpt_length'            => '50',
                    'show_read_more'            => 'yes',
                    'item_count'                => '20',
                    'pagination'                => 'no',
                    'blog_keyword'              => 'no',
                    'blog_sidebar'              => '',
                    'blog_author_filter'        => 'no',
                    'blog_posttype_filter'      => 'no',
                    'blog_filter_display'       => 'no',
                    'blog_filter'               => 'yes',
                    'blog_filter_scope'         => 'global',
                    'blog_az_filtering'         => 'no',
                    'multiselect_filtering'     => 'no',
                    'blog_filter_taxonomies'    => '',
                    'link_type'                 => 'link-page',
                    'alt_styling'               => 'no',
                    'hover_style'               => 'default',
                    'content_output'            => 'excerpt',
                    'el_position'               => '',
                    'width'                     => '1/1',
                    'el_class'                  => ''
                ), $atts ) );

                $atts["content_output"] = $content_output = "excerpt";
                $atts["post_type"] = $post_type = $atts["blog_post_type"];

                if ( $atts["blog_az_filtering"] == "yes" || $atts["blog_posttype_filter"] == "yes" || $atts["blog_author_filter"] == "yes" ) {
                    $blog_filter = "yes";
                }

                if ( $blog_type == "directory" ) {
                    $pagination     = $atts["pagination"]   = "none";
                    $show_image     = $atts["show_image"]   = "no";
                    $order_by       = $atts["order_by"]     = "title";
                    $order          = $atts["order"]        = "asc";
                    $show_details   = $atts["show_details"] = "no";
                    $gutters        = $atts["gutters"]      = "no";
                    $item_count     = $atts["item_count"]   = -1;
                }

                $width = spb_translateColumnWidthToSpan( $width );

                global $sf_blogID, $sf_blog_atts, $sf_opts, $blog_id;
                $sf_blogID++;
                $atts["blogID"] = $sf_blogID;
                if ( !isset($feed_blog_id) ) {
                    $feed_blog_id = $atts["feed_blog_id"] = $blog_id;
                } else {
                    $feed_blog_id = $atts["feed_blog_id"];
                }

                /* SIDEBAR CONFIG
                ================================================== */
                $sidebar_config = sf_get_post_meta( get_the_ID(), 'sf_sidebar_config', true );

                $sidebars = '';
                if ( ( $sidebar_config == "left-sidebar" ) || ( $sidebar_config == "right-sidebar" ) ) {
                    $sidebars = 'one-sidebar';
                } else if ( $sidebar_config == "both-sidebars" ) {
                    $sidebars = 'both-sidebars';
                } else {
                    $sidebars = 'no-sidebars';
                }

                /* BLOG ITEMS
                ================================================== */
                if ( isset($_GET["search"]) && $_GET["search"] != "" ) {
                    $q = $atts["q"] = stripslashes($_GET["search"]);
                } else if ( isset($_GET["keyword"]) && $_GET["keyword"] != "" ) {
                    $q = $atts["q"] = stripslashes($_GET["keyword"]);
                } else if ( isset($_GET["s"]) && $_GET["s"] != "" ) {
                    $q = $atts["q"] = stripslashes($_GET["s"]);
                } else {
                    $q = $atts["q"] = "";
                }

                /* ADDITIONAL VARIABLES
                ================================================== */
                $atts["taxonomies"] = array();
                $taxonomies = get_taxonomies(array(), "objects");
                if ( count($taxonomies) > 0 ) {
                    foreach ( $taxonomies as $taxonomy ) {
                        $post_types = spb_post_types_by_taxonomy( $taxonomy->name );
                        if ( count($post_types) > 0 && $taxonomy->name != "author" ) {
                            $atts["taxonomies"][] = $taxonomy->name;
                        }
                    }
                }

                $sf_blog_atts[$sf_blogID] = $atts;
                
                $filters = sf_post_filter( '', $post_type, $blog_filter_taxonomies, $atts );
                $items = algolia_blog_items( $atts );

                /* FINAL OUTPUT
                ================================================== */
                $title_wrap_class = "";
                if ( $blog_filter == "yes" ) {
                    $title_wrap_class .= 'has-filter ';
                }
                if ( $blog_keyword == "yes" ) {
                    $title_wrap_class .= 'has-keyword ';
                }
                if ( $fullwidth == "yes" && $sidebars == "no-sidebars" ) {
                    // $title_wrap_class .= 'container ';
                }
                $el_class = $this->getExtraClass( $el_class );

                if ( isset($equal_heights) && $equal_heights == "yes" && $blog_type == "masonry" ) {
                    $el_class .= " equal-heights";
                }

                if ( $post_type != "" ) {
                    foreach(explode(",", $post_type) as $type) {
                        $el_class .= " post-type-" . $type;
                    }
                }

                if ( $blog_filter_display != "standard" ) {
                    $el_class .= " filter-display-" . $blog_filter_display;
                }

                $icon_output = "";

                if ( $icon ) {
                    $icon_output = '<i class="' . $icon . '"></i>';
                }

                $output .= "\n\t" . '<div id="blog-items-' . $sf_blogID . '" class="spb_algolia_widget blog-wrap spb_content_element ' . $width . $el_class . '" data-blogid="' . $sf_blogID . '" data-template="template" data-post-type="' . $post_type . '">';
                $output .= "\n\t\t" . '<div class="spb-asset-content clearfix">';
                if ( $title != "" || $blog_keyword == "yes" || $blog_filter == "yes" ) {
                    $output .= "\n\t\t" . '<div class="filter-row-wrap ' . $title_wrap_class . '"><div class="row"><div class="container">';
                        if ( $icon_output != "" ) {
                            $output .= ( $title != '' ) ? "\n\t\t\t" . '<div class="title-wrap"><h3 class="spb-heading spb-icon-heading"><span>' . $icon_output . '' . $title . '</span></h3></div>' : '';
                        } else {
                            $output .= ( $title != '' ) ? "\n\t\t\t" . $this->spb_title( $title, 'spb-text-heading' ) : '';
                        }
                        if ( $blog_keyword == "yes" ) {
                            $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                            $output .= "\n\t\t" . '<form method="get" class="search-form search-widget" action="' . $protocol . $_SERVER['SERVER_NAME'] . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) . '">';
                                $output .= "\n\t\t" . '<label class="accessibility-text" for="' . $sf_blogID . '-search-feed">Search this website</label>';
                                $output .= "\n\t\t" . '<input id="' . $sf_blogID . '-search-feed" type="search" placeholder="Search…" id="content-feed-search-bar" ';
                                if ( is_search() ) {
                                    $output .= "\n\t\t" . 'name="s" class="input-large"';
                                } else {
                                    $output .= "\n\t\t" . 'name="search" class="input-large"';
                                }
                                if ( isset($atts["blog_filter_scope"]) && !empty($atts["blog_filter_scope"]) && $atts["blog_filter_scope"] == "global" && isset($_GET["search"]) && !empty($_GET["search"]) && !empty($_GET["search"]) ) {
                                    $output .= "\n\t\t" . ' value="' . stripslashes(urldecode($_GET["search"])) . '"';
                                } else if ( isset($atts["blog_filter_scope"]) && !empty($atts["blog_filter_scope"]) && $atts["blog_filter_scope"] == "global" && isset($_GET["keyword"]) && !empty($_GET["keyword"]) && !empty($_GET["keyword"]) ) {
                                    $output .= "\n\t\t" . ' value="' . stripslashes(urldecode($_GET["keyword"])) . '"';
                                } else if ( isset($atts["blog_filter_scope"]) && !empty($atts["blog_filter_scope"]) && $atts["blog_filter_scope"] == "global" && isset($_GET["s"]) && !empty($_GET["s"]) && !empty($_GET["s"]) ) {
                                    $output .= "\n\t\t" . ' value="' . stripslashes(urldecode($_GET["s"])) . '"';
                                } else {
                                    $output .= "\n\t\t" . ' value=""';
                                }
                                $output .= "\n\t\t" . ' role="searchbox" />';
                                $output .= '<a role="button" class="search-icon-position" href="#" title="Submit site search"><span class="accessibility-text">Submit site search</span><i aria-hidden="true"><img class="inject-me" data-src="' . get_stylesheet_directory_uri() . '/images/icon-search.svg" src="' . get_stylesheet_directory_uri() . '/images/icon-search.png" aria-hidden="true" /></i></a>';
                            $output .= "\n\t\t" . '</form>';
                        }
                        if ( $blog_filter == "yes" ) {
                            $output .= $filters;
                        }
                        if ( $blog_filter_display == "sidebar" && $blog_sidebar != "" ) {
                            ob_start(); 
                            dynamic_sidebar($blog_sidebar); 
                            $output .= ob_get_contents();    
                            ob_end_clean();
                        }
                    $output .= "\n\t\t" . '</div></div></div>';
                }
                $output .= "\n\t\t" . $items;
                $output .= "\n\t\t" . '</div>';
                $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

                if ( $fullwidth == "yes" ) {
                    $output = $this->startRow( $el_position, '', true ) . $output . $this->endRow( $el_position, '', true );
                } else {
                    $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );
                }

                global $sf_has_blog, $sf_include_imagesLoaded;
                $sf_include_imagesLoaded = true;
                $sf_has_blog             = true;

                return $output;

            }
        }

        $blog_types = array(
            __( 'Standard', 'swift-framework-plugin' )  => "mini",
            __( 'Masonry', 'swift-framework-plugin' )   => "masonry",
        );

        $taxonomies = get_taxonomies(array(), "objects");
        $invalid_taxonomies = array(
                                    "nav_menu",
                                    "link_category",
                                    "post_format",
                                    "swift-slider-category",
                                    "gallery-category",
                                    "elementor_library_category",
                                    "elementor_library_type",
                                    "elementor_font_type",
                                    "media_category",
                                    "post_tag"
                                    );

        /* PARAMS
        ================================================== */
        $params = array(
            array(
                "type"        => "textfield",
                "holder"      => "div",
                "heading"     => __( "Widget title", 'swift-framework-plugin' ),
                "param_name"  => "title",
                "value"       => "",
                "description" => __( "Heading text should be no more than 5 words. Leave it empty if not needed.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Title icon", 'swift-framework-plugin' ),
                "param_name"  => "icon",
                "value"       => "",
                "description" => __( "Icon to the left of the title text. This is the class name for the icon, e.g. fa-cloud", 'swift-framework-plugin' )
            ),
        );

        $blogs = get_sites();
        if ( count($blogs) > 0 ) {

            $blog_options = array( __( "All", 'swift-framework-plugin' ) => 'all' );
            foreach ($blogs as $b => $blog) {
                $blog_options[__( get_blog_details($blog->blog_id)->blogname, 'swift-framework-plugin' )] = $blog->blog_id;
            }
            $blog_options;

            $params[] = array(
                "type"        => "dropdown",
                "heading"     => __( "Site", 'swift-framework-plugin' ),
                "param_name"  => "feed_blog_id",
                "std"         => get_current_blog_id(),
                "value"       => $blog_options,
                "description" => __( "Select the site in your network to show in this feed.", 'swift-framework-plugin' )
            );
        }

        $params[] = array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __( "Index Override", 'swift-framework-plugin' ),
            "param_name"  => "index_override",
            "value"       => "",
            "description" => __( "Provide a different index to use other than the site's default.", 'swift-framework-plugin' )
        );

        $params[] = array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __( "Number of Items", 'swift-framework-plugin' ),
            "param_name"  => "item_count",
            "value"       => "20",
            "description" => __( "The number of blog items to show per page.", 'swift-framework-plugin' )
        );

        $params[] = array(
            "type"        => "select-multiple",
            "heading"     => __( "Post Type", 'swift-framework-plugin' ),
            "param_name"  => "blog_post_type",
            "std"         => "",
            "value"       => spb_get_post_types(),
            "description" => __( "Select the post type you would like to display in the feed.", 'swift-framework-plugin' )
        );

        if ( count($taxonomies) > 0 ) {
            foreach ( $taxonomies as $taxonomy ) {
                if ( !in_array ( $taxonomy->name, $invalid_taxonomies ) ) {
                    $post_types = spb_post_types_by_taxonomy( $taxonomy->name );
                    if ( count($post_types) > 0 && $taxonomy->label != "" ) {
                        $params[] = array(
                            "type"        => "dropdown",
                            "heading"     => __( $taxonomy->label, 'swift-framework-plugin' ),
                            "param_name"  => preg_replace('/[ -]+/', '_', $taxonomy->name),
                            "value"       => spb_get_category_list( $taxonomy->name ),
                            "description" => __( "Choose the " . $taxonomy->label . ".", 'swift-framework-plugin' )
                        );
                    }
                }
            }
        }

        $params[] = array(
            "type"       => "section_tab",
            "param_name" => "filter_options_tab",
            "heading"    => __( "Filters", 'swift-framework-plugin' ),
        );

        $params[] = array(
            "type"        => "dropdown",
            "heading"     => __( "Filter Scope", 'swift-framework-plugin' ),
            "param_name"  => "blog_filter_scope",
            "std"         => "global",
            "value"       => array(
                __( 'Global', 'swift-framework-plugin' ) => "global",
                __( 'Local', 'swift-framework-plugin' )  => "local"
            ),
            "description" => __( "Set the scope of the keyword and taxonomy filters. 'Local': changing filters will affact only this feed. 'Global': changing filters will affect all feeds on this page that are also set to 'global' with similar filters.", 'swift-framework-plugin' )
        );

        $params[] = array(
            "type"        => "buttonset",
            "heading"     => __( "Keyword Search Filter", 'swift-framework-plugin' ),
            "param_name"  => "blog_keyword",
            "std"         => "no",
            "value"       => array(
                __( 'Yes', 'swift-framework-plugin' ) => "yes",
                __( 'No', 'swift-framework-plugin' )  => "no"
            ),
            "description" => __( "Choose whether you would like keyword search to show above the results.", 'swift-framework-plugin' )
        );

        $params[] = array(
            "type"        => "buttonset",
            "heading"     => __( "Post Type Filter", 'swift-framework-plugin' ),
            "param_name"  => "blog_posttype_filter",
            "std"         => "no",
            "value"       => array(
                __( 'Yes', 'swift-framework-plugin' ) => "yes",
                __( 'No', 'swift-framework-plugin' )  => "no"
            ),
            "description" => __( "Choose whether you would like post type filtering to show above the results.", 'swift-framework-plugin' )
        );

        $params[] = array(
            "type"        => "buttonset",
            "heading"     => __( "A-Z Filtering", 'swift-framework-plugin' ),
            "param_name"  => "blog_az_filtering",
            "std"         => "no",
            "value"       => array(
                __( 'Yes', 'swift-framework-plugin' ) => "yes",
                __( 'No', 'swift-framework-plugin' )  => "no"
            ),
            "description" => __( "Enable A-Z filtering allowing users to filter out content that starts with a specific letter of the alphabet.", 'swift-framework-plugin' )
        );

        $params[] = array(
            "type"        => "buttonset",
            "heading"     => __( "Taxonomy Filter", 'swift-framework-plugin' ),
            "param_name"  => "blog_filter",
            "std"         => "no",
            "value"       => array(
                __( 'Yes', 'swift-framework-plugin' ) => "yes",
                __( 'No', 'swift-framework-plugin' )  => "no"
            ),
            "description" => __( "Show the blog category filter above the items.", 'swift-framework-plugin' )
        );

        $params[] = array(
            "type"        => "dropdown",
            "heading"     => __( "Filter Display Type", 'swift-framework-plugin' ),
            "param_name"  => "blog_filter_display",
            "std"         => "standard",
            "value"       => array(
                __( 'Standard', 'swift-framework-plugin' ) => "standard",
                __( 'Sidebar', 'swift-framework-plugin' )  => "sidebar"
            ),
            "description" => __( "Choose the display type for the filters.", 'swift-framework-plugin' )
        );

        $sidebars = array(
                __( 'No Widget', 'swift-framework-plugin' ) => ""
            );

        foreach($wp_registered_sidebars as $sidebar) {
            $sidebars = array_merge($sidebars,array(__( $sidebar["name"], 'swift-framework-plugin' ) => $sidebar["id"]));
        }   

        $params[] = array(
            "type"        => "dropdown",
            "heading"     => __( "Sidebar Widgets", 'swift-framework-plugin' ),
            "param_name"  => "blog_sidebar",
            "std"         => "standard",
            "value"       => $sidebars,
            "required"    => array("blog_filter_display", "=", "sidebar"),
            "description" => __( "Select a sidebar to display below filters.", 'swift-framework-plugin' )
        );

        $params[] = array(
            "type"        => "select-multiple",
            "heading"     => __( "Filters to Display", 'swift-framework-plugin' ),
            "param_name"  => "blog_filter_taxonomies",
            "std"         => "",
            "value"       => sf_get_taxonomies(),
            "required"    => array("blog_filter", "=", "yes"),
            "description" => __( "Select the taxonomies you would like to display as filters.", 'swift-framework-plugin' )
        );

        $params[] = array(
            "type"        => "buttonset",
            "heading"     => __( "Multi-Select Filtering", 'swift-framework-plugin' ),
            "param_name"  => "multiselect_filtering",
            "std"         => "no",
            "value"       => array(
                __( 'Yes', 'swift-framework-plugin' ) => "yes",
                __( 'No', 'swift-framework-plugin' )  => "no"
            ),
            "description" => __( "Enable multi-select filtering allowing users to select more than one term within a taxonomy. Selecting multiple terms in a taxonomy will display content in both terms, not just content tagged with both terms.", 'swift-framework-plugin' )
        );

        $params[] = array(
            "type"       => "section_tab",
            "param_name" => "design_options_tab",
            "heading"    => __( "Design", 'swift-framework-plugin' ),
        );

        $params[] = array(
            "type"        => "dropdown",
            "heading"     => __( "Blog Type", 'swift-framework-plugin' ),
            "param_name"  => "blog_type",
            "value"       => $blog_types,
            "std"         => $sf_options['archive_display_pagination'],
            "description" => __( "Select the display type for the feed.", 'swift-framework-plugin' )
        );

        $params[] = array(
            "type"        => "dropdown",
            "heading"     => __( "Link Type", 'swift-framework-plugin' ),
            "param_name"  => "link_type",
            "value"       => array(
                __( 'Link to Page', 'swift-framework-plugin' ) => "link-page",
                __( 'Open in Modal', 'swift-framework-plugin' )  => "open-modal"
            ),
            "description" => __( "Select if you'd like the post to link through to the page or open in a modal.", 'swift-framework-plugin' )
        );

        $params[] = array(
            "type"        => "buttonset",
            "heading"     => __( "Masonry Gutters", 'swift-framework-plugin' ),
            "param_name"  => "gutters",
            "std"         => "yes",
            "value"       => array(
                __( 'Yes', 'swift-framework-plugin' ) => "yes",
                __( 'No', 'swift-framework-plugin' )  => "no"
            ),
            "required"       => array("blog_type", "=", "masonry"),
            "description" => __( "Select if you'd like spacing between the items, or not (Masonry type only).", 'swift-framework-plugin' )
        );

        $params[] = array(
            "type"        => "buttonset",
            "heading"     => __( "Equal Heights", 'swift-framework-plugin' ),
            "param_name"  => "equal_heights",
            "std"         => "no",
            "value"       => array(
                __( 'Yes', 'swift-framework-plugin' ) => "yes",
                __( 'No', 'swift-framework-plugin' )  => "no"
            ),
            "required"    => array("blog_type", "=", "masonry"),
            "description" => __( "This will force each item in each row to be the same height.", 'swift-framework-plugin' )
        );

        $params[] = array(
            "type"        => "dropdown",
            "heading"     => __( "Columns", 'swift-framework-plugin' ),
            "param_name"  => "columns",
            "value"       => array( "5", "4", "3", "2", "1" ),
            "std"         => "4",
            "required"       => array("blog_type", "!=", "mini"),
            "description" => __( "How many blog masonry columns to display. NOTE: Only for the masonry blog type, and not when fullwidth mode is selected, as this is adaptive.", 'swift-framework-plugin' )
        );

        $params[] = array(
            "type"        => "buttonset",
            "heading"     => __( "Show Featured Image", 'swift-framework-plugin' ),
            "param_name"  => "show_image",
            "std"         => "yes",
            "value"       => array(
                __( "Yes", 'swift-framework-plugin' ) => "yes",
                __( "No", 'swift-framework-plugin' )  => "no"
            ),
            "description" => __( "Show the item featured image.", 'swift-framework-plugin' )
        );

        $params[] = array(
            "type"        => "buttonset",
            "heading"     => __( "Show Title Text", 'swift-framework-plugin' ),
            "param_name"  => "show_title",
            "std"         => "yes",
            "value"       => array(
                __( "Yes", 'swift-framework-plugin' ) => "yes",
                __( "No", 'swift-framework-plugin' )  => "no"
            ),
            "description" => __( "Show the item title text.", 'swift-framework-plugin' )
        );

        $params[] = array(
            "type"        => "buttonset",
            "heading"     => __( "Show Item Details", 'swift-framework-plugin' ),
            "param_name"  => "show_details",
            "std"         => "yes",
            "value"       => array(
                __( "Yes", 'swift-framework-plugin' ) => "yes",
                __( "No", 'swift-framework-plugin' )  => "no"
            ),
            "description" => __( "Show the item details.", 'swift-framework-plugin' )
        );

        $params[] = array(
            "type"        => "buttonset",
            "heading"     => __( "Show Item Excerpt", 'swift-framework-plugin' ),
            "param_name"  => "show_excerpt",
            "std"         => "yes",
            "value"       => array(
                __( "Yes", 'swift-framework-plugin' ) => "yes",
                __( "No", 'swift-framework-plugin' )  => "no"
            ),
            "description" => __( "Show the item excerpt text.", 'swift-framework-plugin' )
        );

        $params[] = array(
            "type"        => "textfield",
            "heading"     => __( "Excerpt Length", 'swift-framework-plugin' ),
            "param_name"  => "excerpt_length",
            "value"       => "50",
            "description" => __( "The length of the excerpt for the posts. NOTE: 60 words maximum.", 'swift-framework-plugin' )
        );

        $params[] = array(
            "type"        => "buttonset",
            "heading"     => __( "Show Read More Link", 'swift-framework-plugin' ),
            "param_name"  => "show_read_more",
            "std"         => "yes",
            "value"       => array(
                __( "Yes", 'swift-framework-plugin' ) => "yes",
                __( "No", 'swift-framework-plugin' )  => "no"
            ),
            "description" => __( "Show a read more link below the excerpt. NOTE: Not used in Bold or Masonry types.", 'swift-framework-plugin' )
        );

        $params[] = array(
            "type"        => "dropdown",
            "heading"     => __( "Pagination", 'swift-framework-plugin' ),
            "param_name"  => "pagination",
            "std"         => $sf_options['archive_display_pagination'],
            "value"       => array(
                __( "Load more (AJAX)", 'swift-framework-plugin' ) => "load-more",
                __( "Infinite scroll", 'swift-framework-plugin' )  => "infinite-scroll",
                __( "Standard", 'swift-framework-plugin' )         => "standard",
                __( "None", 'swift-framework-plugin' )             => "none"
            ),
            "description" => __( "Select how you would like this feed to paginate.", 'swift-framework-plugin' )
        );

        $params[] = array(
            "type"        => "textfield",
            "heading"     => __( "Extra Class", 'swift-framework-plugin' ),
            "param_name"  => "el_class",
            "value"       => "",
            "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin' )
        );

        /* SHORTCODE MAP
        ================================================== */
        SPBMap::map( 'spb_algolia_feed', array(
            "name"   => __( "Algolia Feed", 'swift-framework-plugin' ),
            "base"   => "spb_algolia_feed",
            "class"  => "spb_blog spb_tab_ui",
            "icon"   => "icon-blog",
            "params" => $params
        ) );

    }
