<?php
    if ( post_type_exists( "sponsor" ) ) {
        /* Sponsor Grid
        ================================================== */
        class SwiftPageBuilderShortcode_spb_sponsor_grid extends SwiftPageBuilderShortcode {
            public function content( $atts, $content = null ) {
                $excerpt_length = $show_excerpt = $show_logo = $columns = $image_size = $sponsor_category = $el_class = $width = $el_position = $inline_style = '';
                extract( shortcode_atts( array(
                    'sponsor_category'  => '',
                    'carousel'          => 'no',
                    'columns'           => 'inline',
                    'image_size'        => 'small',
                    'show_logo'         => 'yes',
                    'show_title'        => 'no',
                    'show_excerpt'      => 'no',
                    'excerpt_length'    => '20',
                    'el_class'          => '',
                    'el_position'       => '',
                    'width'             => '1/1'
                ), $atts ) );
                $output = '';
                $container_classes = "";
                $el_class = $this->getExtraClass( $el_class );
                $width    = spb_translateColumnWidthToSpan( $width );
                if ( $carousel == "yes" ) {
                    $output .= "\n\t" . '<div class="spb_partner_grid_element carousel-asset row ' . $width . $el_class . '">';
                } else {
                    $output .= "\n\t" . '<div class="spb_partner_grid_element row ' . $width . $el_class . '">';
                }
                if ( $carousel != "yes" ) {
                    if ( $columns == "2" || $columns == "3" || $columns == "4" || $columns == "5" ) {
                        $container_classes .= " equal-heights";
                    }
                    if ( $show_title == "no" && $show_excerpt == "no" && ( $columns == "2" || $columns == "3" || $columns == "4" || $columns == "5" ) ) {
                        $container_classes .= " vertical-center";
                    }
                }
                if ( $columns == "inline" ) {
                    $output .= "\n\t\t\t" . '<div class="col-md-12 partner-grid-container ' . trim($container_classes) . ' clearfix">';
                } else {
                    $output .= "\n\t\t\t" . '<div class="partner-grid-container ' . trim($container_classes) . ' clearfix">';
                }
                $args = array(
                            'post_type'      => 'sponsor', 
                            'post_status'    => 'publish', 
                            'posts_per_page' => -1,
                            'orderby'        => "title",
                            'order'          => "ASC",

                        );
                if ( isset($sponsor_category) && !empty($sponsor_category) && $sponsor_category != "" && strtolower($sponsor_category) != "all" ) {
                    $args["tax_query"][] = array(
                                            "taxonomy" => "sponsor-category",
                                            "field" => "slug",
                                            "terms" => $sponsor_category
                                        );
                }
                $sponsors = get_posts( $args );
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
                } else {
                    $item_class = "col-inline";
                }
                if ( $carousel == "yes" ) {
                    global $sf_carouselID;
                    if ( $sf_carouselID == "" ) {
                        $sf_carouselID = 1;
                    } else {
                        $sf_carouselID ++;
                    }
                    $output .= '<div class="title-wrap container">' . spb_carousel_arrows() . '</div>';
                    $item_class .= ' carousel-item';
                    $output .= "\n\t\t\t" . '<div class="partner-carousel carousel-wrap"><div id="carousel-' . $sf_carouselID . '" class="partner-logos carousel-items clearfix" data-columns="' . $columns . '" aria-label="carousel" data-auto="true">';
                }
                $i = 0;
                foreach ($sponsors as $sponsor) {
                    $sponsor = $sponsor->ID;
                    if ( $sponsor != "" ) {
                        setup_postdata($sponsor);
                        $sponsor_logo = get_post_meta( $sponsor, "sf_logo", true );
                        $sponsor_logo_url = wp_get_attachment_image_src( $sponsor_logo, "full", false );
                        if ( $image_size == "sponsor-large" ) {
                            $image_width = "270";
                            $image_height = "100";
                        } else if ( $image_size == "sponsor-medium" ) {
                            $image_width = "225";
                            $image_height = "75";
                        } else if ( $image_size == "sponsor-small" ) {
                            $image_width = "180";
                            $image_height = "45";
                        }
                        $sponsor_title = get_the_title($sponsor);
                        $sponsor_excerpt = get_the_excerpt($sponsor);
                        $add_classes = "image-size-" . $image_size;
                        $output .= "\n\t\t\t" . '<div itemscope data-id="id-' . $i . '" class="partner ' . $add_classes . ' ' . $item_class . '">';
                        if ( $sponsor_logo_url[0] != "" && $show_logo == "yes" ) {
                            $output .= '<div class="logo-wrap" style="max-width:' . $image_width . 'px;"><div class="logo-inner">';
                                if ( get_post_meta( $sponsor, "sf_website", true) != "" ) {
                                    $output .= "\n\t\t\t" . '<a href="' . get_post_meta( $sponsor, "sf_website", true) . '" title="Visit ' . get_the_title($sponsor) . ' Website" target="_blank" class="partner-link">';
                                } else {
                                    $output .= "\n\t\t\t" . '<div class="partner-link">';
                                }
                                $output .= "\n\t\t\t" . '<img src="' . $sponsor_logo_url[0] . '" width="' . $sponsor_logo_url[1] . '" height="' . $sponsor_logo_url[2] . '" alt="' . get_the_title($sponsor) . ' Logo" />';
                                if ( get_post_meta( $sponsor, "sf_website", true) != "" ) {
                                    $output .= "\n\t\t\t" . '</a>';
                                } else {
                                    $output .= "\n\t\t\t" . '</div>';
                                }
                            $output .= '</div></div>';
                        }
                        if ($show_title == "yes" ) {
                            $output .= "\n\t\t\t" . '<h4 class="partner-title">';
                            if ( get_post_meta( $sponsor, "sf_website", true) != "" ) {
                                $output .= "\n\t\t\t" . '<a href="' . get_post_meta( $sponsor, "sf_website", true) . '" title="Visit ' . get_the_title($sponsor) . ' Website" target="_blank" class="partner-link" tabindex="-1">';
                            }
                            $output .= "\n\t\t\t" . $sponsor_title;
                            if ( get_post_meta( $sponsor, "sf_website", true) != "" ) {
                                $output .= "\n\t\t\t" . '</a>';
                            }
                            $output .= "\n\t\t\t" . '</h4>';
                        }
                        if ($show_excerpt == "yes" ) {
                            $output .= "\n\t\t\t" . '<div class="partner-excerpt"><p>' . $sponsor_excerpt . '</p></div>';
                        }
                        $output .= "\n\t\t\t" . '</div>';
                        $i++;
                        wp_reset_postdata();
                    }
                }
                if ( $carousel == "yes" ) {
                    $output .= "\n\t" . '</div></div>';
                }
                $output .= "\n\t\t\t" . '</div>';
                $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );
                $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

                global $sf_include_isotope, $sf_include_carousel;
                $sf_include_isotope = true;

                if ( $carousel == "yes" ) {
                    $sf_include_carousel = true;
                }

                return $output;
            }
        }
        SPBMap::map( 'spb_sponsor_grid', array(
                "name"          => __( "Sponsor Grid", 'swift-framework-plugin' ),
                "base"          => "spb_sponsor_grid",
                "class"         => "spb_tab_media",
                "icon"          => "icon-portfolio",
                "wrapper_class" => "clearfix",
                "controls"      => "full",
                "params"        => array(
                    array(
                        "type"       => "section",
                        "param_name" => "filter_options",
                        "heading"    => __( "Filter Options", 'swift-framework-plugin' ),
                    ),
                    array(
                        "type"        => "dropdown",
                        "heading"     => __( "Sponsor category", 'swift-framework-plugin' ),
                        "param_name"  => "sponsor_category",
                        "value"       => spb_get_category_list( 'sponsor-category' ),
                        "description" => __( "Choose the sponsor category for the grid.", 'swift-framework-plugin' )
                    ),
                    array(
                        "type"       => "section",
                        "param_name" => "display_options",
                        "heading"    => __( "Display Options", 'swift-framework-plugin' ),
                    ),
                    array(
                        "type"        => "buttonset",
                        "heading"     => __( "Carousel", 'swift-framework-plugin' ),
                        "param_name"  => "carousel",
                        "value"       => array(
                            __( 'No', 'swift-framework-plugin' )  => "no",
                            __( 'Yes', 'swift-framework-plugin' ) => "yes"
                        ),
                        "description" => __( "Enables carousel funcitonality in the asset.", 'swift-framework-plugin' )
                    ),
                    array(
                        "type"        => "dropdown",
                        "heading"     => __( "Columns", 'swift-framework-plugin' ),
                        "param_name"  => "columns",
                        "value"       => array( "inline", "5", "4", "3", "2", "1" ),
                        "std"         => "inline",
                        "description" => __( "How many columns to display.", 'swift-framework-plugin' )
                    ),
                    array(
                        "type"        => "buttonset",
                        "heading"     => __( "Show logo", 'swift-framework-plugin' ),
                        "param_name"  => "show_logo",
                        "std"         => "yes",
                        "value"       => array(
                            __( "Yes", 'swift-framework-plugin' ) => "yes",
                            __( "No", 'swift-framework-plugin' )  => "no"
                        ),
                        "description" => __( "Show the sponsor logo.", 'swift-framework-plugin' )
                    ),
                    array(
                        "type"        => "dropdown",
                        "heading"     => __( "Image Size", 'swift-framework-plugin' ),
                        "param_name"  => "image_size",
                        "value"       => array(
                            __( "Small", 'swift-framework-plugin' )     => "sponsor-small",
                            __( "Medium", 'swift-framework-plugin' )    => "sponsor-medium",
                            __( "Large", 'swift-framework-plugin' )     => "sponsor-large",
                            __( "Full", 'swift-framework-plugin' )      => "full",
                        ),
                        "required"    => array("show_logo", "=", "yes" ),
                        "description" => __( "Select the source size for the image (NOTE: this doesn't affect it's size on the front-end, only the quality).", 'swift-framework-plugin' )
                    ),
                    array(
                        "type"        => "buttonset",
                        "heading"     => __( "Show title text", 'swift-framework-plugin' ),
                        "param_name"  => "show_title",
                        "std"         => "no",
                        "value"       => array(
                            __( "Yes", 'swift-framework-plugin' ) => "yes",
                            __( "No", 'swift-framework-plugin' )  => "no"
                        ),
                        "description" => __( "Show the item title text.", 'swift-framework-plugin' )
                    ),
                    array(
                        "type"        => "buttonset",
                        "heading"     => __( "Show item excerpt", 'swift-framework-plugin' ),
                        "param_name"  => "show_excerpt",
                        "std"         => "no",
                        "value"       => array(
                            __( "Yes", 'swift-framework-plugin' ) => "yes",
                            __( "No", 'swift-framework-plugin' )  => "no"
                        ),
                        "description" => __( "Show the item excerpt text.", 'swift-framework-plugin' )
                    ),
                    array(
                        "type"        => "textfield",
                        "heading"     => __( "Excerpt Length", 'swift-framework-plugin' ),
                        "param_name"  => "excerpt_length",
                        "value"       => "20",
                        "required"    => array("show_excerpt", "=", "yes" ),
                        "description" => __( "The length of the excerpt for the posts. NOTE: 60 words maximum.", 'swift-framework-plugin' )
                    ),  
                    array(
                        "type"        => "section",
                        "param_name"  => "btn_misc_options",
                        "heading"     => __( "Misc Options", 'swift-framework-plugin' ),
                    ),
                    array(
                        "type"        => "textfield",
                        "heading"     => __( "Extra class", 'swift-framework-plugin' ),
                        "param_name"  => "el_class",
                        "value"       => "",
                        "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin' )
                    )
                )
            )
        );
    }
?>