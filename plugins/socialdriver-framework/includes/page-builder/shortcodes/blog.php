<?php

    /*
    *
    *	Swift Page Builder - Blog Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
    *
    */

    global $sf_options, $sf_blog_atts, $sf_opts, $wp_registered_sidebars;

    class SwiftPageBuilderShortcode_spb_content_feed extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            $output = $title = $post_type = $author = $blog_type = $gutters = $columns = $equal_heights = $fullwidth = $show_image = $show_title = $show_excerpt = $show_details = $offset = $order_by = $order = $excerpt_length = $show_read_more = $item_count = $exclude_categories = $pagination = $social_integration = $twitter_username = $instagram_id = $instagram_token = $blog_keyword = $blog_filter = $blog_filter_scope = $blog_filter_taxonomies = $alt_styling = $blog_filter_display = $hover_style = $content_output = $el_position = $width = $el_class = $filter_loveit = '';

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
                'exclude_categories'        => '',
                'pagination'                => 'no',
                'social_integration'        => 'no',
                'twitter_username'          => '',
                'instagram_id'              => '',
                'instagram_token'           => '',
                'blog_keyword'              => 'no',
                'blog_sidebar'              => '',
                'blog_author_filter'        => 'no',
                'blog_posttype_filter'      => 'no',
                'blog_filter_display'       => 'no',
                'blog_filter'               => 'yes',
                'sort_filter'               => 'no',
                'blog_filter_sort'          => '', 
                'blog_filter_scope'         => 'global',
                'blog_az_filtering'         => 'no',
                'multiselect_filtering'     => 'no',
                'blog_filter_taxonomies'    => '', 
                'link_type'                 => 'link-page',
                'alt_styling'               => 'no',
                'hover_style'               => 'default',
                'content_output'            => 'excerpt',
                'filter_private'            => '',
                'filter_loveit'             => '',
                'el_position'               => '',
                'width'                     => '1/1',
                'el_class'                  => ''
            ), $atts ) );

            $atts["content_output"] = $content_output = "excerpt";
            $atts["post_type"] = $post_type = $atts["blog_post_type"];

            if ( $atts["blog_az_filtering"] == "yes" || $atts["blog_posttype_filter"] == "yes" || $atts["blog_author_filter"] == "yes" || $atts["blog_filter_sort"] == "yes" ) {
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

            global $sf_blogID, $sf_blog_atts, $sf_opts;
            $sf_blogID++;
            $atts["blogID"] = $sf_blogID;

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
            $items = sf_blog_items( $atts );

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

            $output .= "\n\t" . '<div id="blog-items-' . $sf_blogID . '" class="spb_blog_widget blog-wrap spb_content_element ' . $width . $el_class . '" data-blogid="' . $sf_blogID . '" data-template="template" data-post-type="' . $post_type . '">';
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
        __( 'Directory', 'swift-framework-plugin' ) => "directory",
        __( 'Timeline', 'swift-framework-plugin' )  => "timeline",
    );

    if ( $sf_opts['cpt-disable']["timeline"] != 1 ) {
        $blog_types = array_merge($blog_types, array( __( 'Timeline', 'swift-framework-plugin' )  => "timeline"));
    }

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

    $params[] = array(
        "type"        => "textfield",
        "class"       => "",
        "heading"     => __( "Number of Items", 'swift-framework-plugin' ),
        "param_name"  => "item_count",
        "value"       => "20",
        "required"       => array("blog_type", "!=", "directory"),
        "description" => __( "The number of blog items to show per page.", 'swift-framework-plugin' )
    );

    $params[] = array(
        "type"        => "textfield",
        "heading"     => __( "Posts Offset", 'swift-framework-plugin' ),
        "param_name"  => "offset",
        "value"       => "0",
        "description" => __( "The offset for the start of the posts that are displayed, e.g. enter 5 here to start from the 5th post.", 'swift-framework-plugin' )
    );

    $params[] = array(
        "type"        => "select-multiple",
        "heading"     => __( "Post Type", 'swift-framework-plugin' ),
        "param_name"  => "blog_post_type",
        "std"         => "",
        "value"       => spb_get_post_types(),
        "description" => __( "Select the post type you would like to display in the feed.", 'swift-framework-plugin' )
    );

    $params[] = array(
        "type"        => "select-multiple",
        "heading"     => __( "Authors", 'swift-framework-plugin' ),
        "param_name"  => "author",
        "std"         => "",
        "value"       => array_merge( array( __( "All", "swift-framework-admin" ) => "all" ), spb_get_authors() ),
        "description" => __( "Select the author you would like to display in the feed.", 'swift-framework-plugin' )
    );

    if ( count($taxonomies) > 0 ) {
        foreach ( $taxonomies as $taxonomy ) {
            if ( !in_array ( $taxonomy->name, $invalid_taxonomies ) ) {
                $post_types = spb_post_types_by_taxonomy( $taxonomy->name );
                if ( count($post_types) > 0 && $taxonomy->label != "" ) {
                    if ( $taxonomy->name == "news-category" ) {
                        $taxonomy->label = "News Categories";
                    } else if ( $taxonomy->name == "team-category" ) {
                        $taxonomy->label = "Team Categories";
                    } else if ( $taxonomy->name == "sponsor-category" ) {
                        $taxonomy->label = "Sponsor Categories";
                    } else if ( $taxonomy->name == "event-category" ) {
                        $taxonomy->label = "Event Categories";
                    } else if ( $taxonomy->name == "job-category" ) {
                        $taxonomy->label = "Job Categories";
                    } else if ( $taxonomy->name == "testimonials-category" ) {
                        $taxonomy->label = "Testimonial Categories";
                    } else if ( $taxonomy->name == "category" ) {
                        $taxonomy->label = "Categories";
                    }
                    $params[] = array(
                        "type"        => "select-multiple",
                        "heading"     => __( $taxonomy->label, 'swift-framework-plugin' ),
                        "param_name"  => preg_replace('/[ -]+/', '_', $taxonomy->name),
                        "value"       => array_merge( array( __( "All", "swift-framework-admin" ) => "all" ), spb_get_category_list( $taxonomy->name ) ),
                        "description" => __( "Choose the " . $taxonomy->label . ".", 'swift-framework-plugin' )
                    );
                }
            }
        }
    }

    $order = array(
            __( 'Date', 'swift-framework-plugin' )  => "date",
            __( 'ID', 'swift-framework-plugin' )  => "ID",
            __( 'Title', 'swift-framework-plugin' )  => "title",
            __( 'Random', 'swift-framework-plugin' )  => "rand",
            __( 'Menu Order', 'swift-framework-plugin' )  => "menu_order",
            __( 'None', 'swift-framework-plugin' ) => "none"
        );

    $params[] = array(
        "type"        => "dropdown",
        "heading"     => __( "Order By", 'swift-framework-plugin' ),
        "param_name"  => "order_by",
        "std"         => "date",
        "value"       => $order,
        "required"       => array("blog_type", "!=", "directory"),
        "description" => __( "Select how you'd like the items to be ordered.", 'swift-framework-plugin' )
    );

    $params[] = array(
        "type"        => "dropdown",
        "heading"     => __( "Order", 'swift-framework-plugin' ),
        "param_name"  => "order",
        "std"         => "DESC",
        "value"       => array(
            __( "Descending", 'swift-framework-plugin' ) => "DESC",
            __( "Ascending", 'swift-framework-plugin' )  => "ASC"
        ),
        "required"       => array("blog_type", "!=", "directory"),
        "description" => __( "Select if you'd like the items to be ordered in ascending or descending order.", 'swift-framework-plugin' )
    );

    if ( is_plugin_active("members/members.php") ) {
        $params[] = array(
            "type"        => "buttonset",
            "heading"     => __("Show only private content", 'swift-framework-plugin'),
            "param_name"  => "filter_private",
            "std"         => "no",
            "value"       => array(
                __('Yes', 'swift-framework-plugin') => "yes",
                __('No', 'swift-framework-plugin')  => "no"
            ),
            "description" => __("Choose whether you would like filter for only private content.", 'swift-framework-plugin')
        );
    }

    if ($sf_options['enable_loveit'] == 1) {
        $params[] = array(
            "type"        => "buttonset",
            "heading"     => __("Show only saved content", 'swift-framework-plugin'),
            "param_name"  => "filter_loveit",
            "std"         => "no",
            "value"       => array(
                __('Yes', 'swift-framework-plugin') => "yes",
                __('No', 'swift-framework-plugin')  => "no"
            ),
            "description" => __("Choose whether you would like filter for only content saved to account dashboard.", 'swift-framework-plugin')
        );
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
        "heading"     => __( "Author Filter", 'swift-framework-plugin' ),
        "param_name"  => "blog_author_filter",
        "std"         => "no",
        "value"       => array(
            __( 'Yes', 'swift-framework-plugin' ) => "yes",
            __( 'No', 'swift-framework-plugin' )  => "no"
        ),
        "description" => __( "Choose whether you would like author filtering to show above the results.", 'swift-framework-plugin' )
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
        "type"        => "buttonset",
        "heading"     => __( "Sort Filter", 'swift-framework-plugin' ),
        "param_name"  => "sort_filter",
        "std"         => "no",
        "value"       => array(
            __( 'Yes', 'swift-framework-plugin' ) => "yes",
            __( 'No', 'swift-framework-plugin' )  => "no"
        ),
        "description" => __( "Show the absility for users to sort by relevance, date, or alphabetically.", 'swift-framework-plugin' )
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
        "type"        => "select-multiple",
        "heading"     => __( "Sort Options to Display", 'swift-framework-plugin' ),
        "param_name"  => "blog_filter_sort",
        "std"         => "",
        "value"       => array(
            __( 'relevance', 'swift-framework-plugin' ) => "Relevance",
            __( 'recent', 'swift-framework-plugin' )  => "Recent by Publish Date",
            __( 'oldest', 'swift-framework-plugin' )  => "Oldest by Publish Date",
            __( 'a-z', 'swift-framework-plugin' )  => "Alphabetically (A-Z)",
            __( 'z-a', 'swift-framework-plugin' )  => "Reverse Alphabetically (Z-A)"
        ),
        "required"    => array("sort_filter", "=", "yes"),
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
        "required"    => array("blog_type", "!=", "directory"),
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
        "required"       => array("blog_type", "!=", "directory"),
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
        "required"       => array("blog_type", "!=", "directory"),
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
    SPBMap::map( 'spb_content_feed', array(
        "name"   => __( "Content Feed", 'swift-framework-plugin' ),
        "base"   => "spb_content_feed",
        "class"  => "spb_blog spb_tab_ui",
        "icon"   => "icon-blog",
        "params" => $params
    ) );
