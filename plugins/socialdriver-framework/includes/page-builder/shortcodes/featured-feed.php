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

    class SwiftPageBuilderShortcode_spb_featured_feed extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            $output = $title = $post_type = $author = $gutters = $fullwidth = $show_image = $show_title = $show_excerpt = $show_details = $offset = $order_by = $order = $excerpt_length = $show_read_more = $item_count = $link_type = $el_position = $width = $el_class = '';

            extract( shortcode_atts( array(
                'title'                     => '',
                'icon'                      => '',
                'blog_post_type'            => '',
                'author'                    => '',
                'gutters'                   => 'yes',
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
                'item_count'                => '4',
                'link_type'                 => 'link-page',
                'el_position'               => '',
                'width'                     => '1/1',
                'el_class'                  => '',
                'columns'                   => ''
            ), $atts ) );

            $atts["content_output"] = $content_output = "excerpt";
            $atts["post_type"] = $post_type = $atts["blog_post_type"];
            if ($atts["columns"] == "") {
                $atts["columns"] = $columns = 3;
            }
            $atts["blog_filter_display"] = $blog_filter_display = "no";
            $atts["pagination"] = $pagination = "none";
            $atts["blog_type"] = $blog_type = "masonry-featured-feed";
            $atts["equal_heights"] = $equal_heights = "yes";

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
            
            $items = sf_blog_items( $atts );

            /* FINAL OUTPUT
            ================================================== */
            $title_wrap_class = "";
            $el_class = $this->getExtraClass( $el_class );

            if ( isset($equal_heights) && $equal_heights == "yes" ) {
                $el_class .= " equal-heights";
            }

            if ( $post_type != "" ) {
                foreach(explode(",", $post_type) as $type) {
                    $el_class .= " post-type-" . $type;
                }
            }

            $icon_output = "";

            if ( $icon ) {
                $icon_output = '<i class="' . $icon . '"></i>';
            }

            $output .= "\n\t" . '<div id="blog-items-' . $sf_blogID . '" class="spb_blog_widget spb_featured_feed blog-wrap spb_content_element ' . $width . $el_class . '" data-blogid="' . $sf_blogID . '" data-template="template" data-post-type="' . $post_type . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content clearfix">';
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
        "value"       => "4",
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
        "description" => __( "Select if you'd like the items to be ordered in ascending or descending order.", 'swift-framework-plugin' )
    );

    $params[] = array(
        "type"       => "section_tab",
        "param_name" => "design_options_tab",
        "heading"    => __( "Design", 'swift-framework-plugin' ),
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
        "description" => __( "Select if you'd like spacing between the items, or not (Masonry type only).", 'swift-framework-plugin' )
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
        "type"        => "textfield",
        "heading"     => __( "Extra Class", 'swift-framework-plugin' ),
        "param_name"  => "el_class",
        "value"       => "",
        "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin' )
    );

    /* SHORTCODE MAP
    ================================================== */
    SPBMap::map( 'spb_featured_feed', array(
        "name"   => __( "Featured Feed", 'swift-framework-plugin' ),
        "base"   => "spb_featured_feed",
        "class"  => "spb_blog spb_tab_ui",
        "icon"   => "icon-blog",
        "params" => $params
    ) );
