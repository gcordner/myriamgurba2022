<?php

    /*
    *
    *   Swift Page Builder - Team Shortcode
    *   ------------------------------------------------
    *   Swift Framework
    *   Copyright Swift Ideas 2015 - http://www.swiftideas.com
    *
    */

    if ( post_type_exists( "team" ) ) {

        function sd_team_member_item($postID, $display_type, $profile_link, $custom_image_height = "", $count = 1, $item_class = "") {

            $item = "";

            if ( $postID != 0 && get_post($postID) ) {

                global $post;
                $post = get_post($postID);
                setup_postdata( $post );

                $image_width  = 360;
                $image_height = 540;

                if ( $custom_image_height != "" ) {
                    $image_height = $custom_image_height;
                }

                $member_name     = get_the_title( $postID );
                $member_position = sf_get_post_meta( $postID, 'sf_team_member_position', true );
                $custom_excerpt  = sf_get_post_meta( $postID, 'sf_custom_excerpt', true );
                $pb_active       = get_post_meta( $postID, '_spb_js_status', true);
                $member_link     = get_permalink( $postID );
                $member_bio      = sf_excerpt( 20, $postID );
                
                $member_email       = sf_get_post_meta( $postID, 'sf_team_member_email', true );
                $member_phone       = sf_get_post_meta( $postID, 'sf_team_member_phone_number', true );
                $member_twitter     = sf_get_post_meta( $postID, 'sf_team_member_twitter', true );
                $member_facebook    = sf_get_post_meta( $postID, 'sf_team_member_facebook', true );
                $member_linkedin    = sf_get_post_meta( $postID, 'sf_team_member_linkedin', true );
                $member_google_plus = sf_get_post_meta( $postID, 'sf_team_member_google_plus', true );
                $member_skype       = sf_get_post_meta( $postID, 'sf_team_member_skype', true );
                $member_instagram   = sf_get_post_meta( $postID, 'sf_team_member_instagram', true );
                $member_dribbble    = sf_get_post_meta( $postID, 'sf_team_member_dribbble', true );
                $view_profile_text  = __( "View Profile", 'swift-framework-plugin' );
                $member_image       = get_post_thumbnail_id( $postID );
                

                $item = '<div itemscope data-id="id-' . $count . '" class="clearfix team-member display-type-' . $display_type . ' ' . $item_class . '">';

                $img_url = wp_get_attachment_url( $member_image, 'team-portrait' );
                $image   = sf_aq_resize( $img_url, $image_width, $image_height, true, false );

                $item .= '<figure class="animated-overlay"';
                if ( $custom_image_height != "" ) {
                    $item .= ' style="max-height:'.$custom_image_height.'px;"';
                }
                $item .= '>';

                if ( $profile_link == "yes" || ( $profile_link == "profile-modal" && get_post_field('post_content', $post_id) != "" ) ) {
                    $item .= '<a href="' . $member_link . '" data-postid="' . $postID . '"';
                    if ( $profile_link == "profile-modal" ) {
                        $item .= ' class="sf-menu-item-modal"';
                    }
                    $item .= ' aria-hidden="true"></a>';
                } else if ( $profile_link == "yes" || $display_type == "gallery" ) {
                    $item .= '<a href="' . $member_link . '" data-postid="' . $postID . '"';
                    if ( $profile_link == "profile-modal" ) {
                        $item .= ' class="sf-menu-item-modal"';
                    }
                    $item .= ' aria-hidden="true"></a>';
                }

                if ( $image ) {
                    $item .= '<img itemprop="image" src="' . $image[0] . '" width="' . $image[1] . '" height="' . $image[2] . '" alt="' . $member_name . '" />';
                } else {
                    $item .= '<img itemprop="image" src="' . get_template_directory_uri() . '/images/default-team-member.png" width="240" height="240" alt="' . $member_name . '" />';
                }

                $item .= '<figcaption class="team-' . $display_type . '">';
                $item .= '<div class="thumb-info">';

                if ( $display_type == "gallery" ) {
                    $item .= '<a href="' . $member_link . '" data-postid="' . $postID . '"';
                    if ( $profile_link == "profile-modal" ) {
                        $item .= ' class="sf-menu-item-modal"';
                    }
                    $item .= ' aria-hidden="true"></a>';
                    if ( $profile_link == "yes" || ( $profile_link == "profile-modal" && get_post_field('post_content', $post_id) != "" ) ) {
                        $item .= '<h4 class="team-member-name"><a href="' . $member_link . '" data-postid="' . $postID . '"';
                        if ( $profile_link == "profile-modal" ) {
                            $item .= ' class="sf-menu-item-modal"';
                        }
                        $item .= '>' . $member_name . '</a></h4>';
                    } else {
                        $item .= '<h4 class="team-member-name">' . $member_name . '</h4>';
                    }
                    $item .= '<h5 class="team-member-position">' . $member_position . '</h5>';
                }

                if ( $display_type != "gallery" && ( $profile_link == "yes" || ( $profile_link == "profile-modal" && get_post_field('post_content', $post_id) != "" ) ) ) {
                    $item .= $view_profile_text;
                }

                $item .= '</div>';
                $item .= '</figcaption>';

                $item .= '</figure>';

                if ( $display_type != "gallery" ) {
                    if ( $profile_link == "yes" ) {
                        $item .= '<h4 class="team-member-name"><a href="' . get_permalink() . '">' . $member_name . '</a></h4>';
                    } else {
                        $item .= '<h4 class="team-member-name">' . $member_name . '</h4>';
                    }
                    $item .= '<h5 class="team-member-position">' . $member_position . '</h5>';
                }

                if ( $display_type == "standard" ) {
                    if ( $profile_link == "yes" ) {
                        $item .= '<div class="team-member-bio">' . $member_bio . '<a href="' . get_permalink() . '" class="read-more">' . $view_profile_text . ' <i aria-hidden="true"><img class="inject-me" data-src="' . get_template_directory_uri() . '/images/icon-arrow.svg" src="' . get_template_directory_uri() . '/images/icon-arrow.png" /></i></a></div>';
                    } else {
                        $item .= '<div class="team-member-bio">' . $member_bio . '</div>';
                        $item .= '<ul class="member-contact">';
                        if ( $member_email ) {
                            $item .= '<li>'.$contact_icon.'<span itemscope="email"><a href="mailto:' . $member_email . '">' . $member_email . '</a></span></li>';
                        }
                        if ( $member_phone ) {
                            $item .= '<li>'.$phone_icon.'<span itemscope="telephone">' . $member_phone . '</span></li>';
                        }
                        $item .= '</ul>';
                    }
                }

                $item .= '</div>';

                wp_reset_postdata( $post );

            }

            return $item;

        }

        class SwiftPageBuilderShortcode_spb_team extends SwiftPageBuilderShortcode {

            protected function content( $atts, $content = null ) {

                $title = $width = $el_class = $output = $filter = $social_icon_type = $items = $el_position = '';

                extract( shortcode_atts( array(
                    'title'        => '',
                    'item_columns' => '3',
                    'display_type' => 'standard',
                    'carousel'     => 'no',
                    "item_count"   => '12',
                    "custom_image_height" => '',
                    "category"     => '',
                    'pagination'   => '',
                    'profile_link' => 'yes',
                    'fullwidth'    => 'no',
                    'gutters'      => 'yes',
                    'el_position'  => '',
                    'width'        => '1/1',
                    'el_class'     => ''
                ), $atts ) );

                // CATEGORY SLUG MODIFICATION
                if ( $category == "All" ) {
                    $category = "all";
                }
                if ( $category == "all" ) {
                    $category = '';
                }
                $category_slug = str_replace( '_', '-', $category );

                $contact_icon       = apply_filters( 'sf_mail_icon', '<i class="ss-mail"></i>' );
                $phone_icon         = apply_filters( 'sf_phone_icon', '<i class="ss-phone"></i>' );

                /* SIDEBAR CONFIG
                ================================================== */
                global $sf_sidebar_config;

                $sidebars = '';
                if ( ( $sf_sidebar_config == "left-sidebar" ) || ( $sf_sidebar_config == "right-sidebar" ) ) {
                    $sidebars = 'one-sidebar';
                } else if ( $sf_sidebar_config == "both-sidebars" ) {
                    $sidebars = 'both-sidebars';
                } else {
                    $sidebars = 'no-sidebars';
                }


                global $post, $wp_query;

                $paged        = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
                $team_args    = array(
                    'post_type'           => 'team',
                    'post_status'         => 'publish',
                    'paged'               => $paged,
                    'team-category'       => $category_slug,
                    'posts_per_page'      => $item_count,
                    'ignore_sticky_posts' => 1,
                    'order'               => "ASC",
                    'orderby'             => "title"
                );
                $team_members = new WP_Query( $team_args );

                $count        = 0;

                if ( $item_columns == "1" ) {
                    $item_class = 'col-sm-12';
                } else if ( $item_columns == "2" ) {
                    $item_class   = 'col-sm-6';
                } else if ( $item_columns == "3" ) {
                    $item_class   = 'col-sm-4';
                } else if ( $item_columns == "5" ) {
                    $item_class   = 'col-sm-sf-5';
                } else {
                    $item_class = 'col-sm-3';
                }

                if ( $custom_image_height != "" ) {
                    $image_height = $custom_image_height;
                }

                $list_class = 'display-type-' . $display_type;

                if ( $gutters == "no" ) {
                    $list_class .= ' no-gutters';
                }

                if ( $carousel == "yes" ) {
                    global $sf_carouselID;
                    if ( $sf_carouselID == "" ) {
                        $sf_carouselID = 1;
                    } else {
                        $sf_carouselID ++;
                    }
                    $item_class = 'carousel-item';
                    $items .= '<div class="team-carousel carousel-wrap"><div id="carousel-' . $sf_carouselID . '" class="team-members carousel-items ' . $list_class . ' clearfix" data-columns="' . $item_columns . '" aria-label="carousel" data-auto="false">';
                } else {
                    $items .= '<div class="team-members ' . $list_class . ' row clearfix">';
                }

                while ( $team_members->have_posts() ) : $team_members->the_post();

                    $items .= sd_team_member_item($post->ID, $display_type, $profile_link, $custom_image_height, $count, $item_class);
                    $count ++;

                endwhile;

                wp_reset_query();
                wp_reset_postdata();

                if ( $carousel == "yes" ) {
                    $items .= '</div></div>';
                } else {
                    $items .= '</div>';
                }

                // PAGINATION
                if ( $pagination == "yes" && $carousel == "no" ) {
                    $items .= '<div class="pagination-wrap">';
                    $items .= pagenavi( $team_members );
                    $items .= '</div>';
                }

                $el_class = $this->getExtraClass( $el_class );
                $width    = spb_translateColumnWidthToSpan( $width );

                $output .= "\n\t" . '<div class="team_list spb_content_element ' . $width . $el_class . '">';
                $output .= "\n\t\t" . '<div class="spb-asset-content">';
                if ( $fullwidth == "yes" && $sidebars == "no-sidebars" ) {
                    $output .= "\n\t\t" . '<div class="title-wrap container">';
                    if ( $title != '' ) {
                        $output .= '<h3 class="spb-heading"><span>' . $title . '</span></h3>';
                    }
                    if ( $carousel == "yes" ) {
                        $output .= spb_carousel_arrows();
                    }
                    $output .= '</div>';
                } else {
                    $output .= "\n\t\t" . '<div class="title-wrap clearfix">';
                    if ( $title != '' ) {
                        $output .= '<h3 class="spb-heading"><span>' . $title . '</span></h3>';
                    }
                    if ( $carousel == "yes" ) {
                        $output .= spb_carousel_arrows();
                    }
                    $output .= '</div>';
                }
                $output .= "\n\t\t" . $items;
                $output .= "\n\t\t" . '</div>';
                $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

                if ( $fullwidth == "yes" && $sidebars == "no-sidebars" ) {
                    $output = $this->startRow( $el_position, '', true ) . $output . $this->endRow( $el_position, '', true );
                } else {
                    $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );
                }

                global $sf_include_isotope, $sf_has_team, $sf_include_carousel;
                $sf_include_isotope = true;
                $sf_has_team        = true;

                if ( $carousel == "yes" ) {
                    $sf_include_carousel = true;
                }

                return $output;
            }
        }

        SPBMap::map( 'spb_team', array(
            "name"   => __( "Team List", 'swift-framework-plugin' ),
            "base"   => "spb_team",
            "class"  => "team spb_tab_media",
            "icon"   => "icon-blog",
            "params" => array(
                array(
                    "type"        => "textfield",
                    "holder"      => "div",
                    "heading"     => __( "Widget title", 'swift-framework-plugin' ),
                    "param_name"  => "title",
                    "value"       => "",
                    "description" => __( "Heading text should be no more than 5 words. Leave it empty if not needed.", 'swift-framework-plugin' )
                ),
                array(
                    "type"       => "section",
                    "param_name" => "ib_display_options",
                    "heading"    => __( "Display Options", 'swift-framework-plugin' ),
                ),
                array(
                    "type"        => "dropdown",
                    "heading"     => __( "Display Type", 'swift-framework-plugin' ),
                    "param_name"  => "display_type",
                    "value"       => array(
                        __( 'Standard', 'swift-framework-plugin' )              => "standard",
                        __( 'Standard (No Excerpt)', 'swift-framework-plugin' ) => "standard-alt",
                        __( 'Gallery', 'swift-framework-plugin' )               => "gallery"
                    ),
                    "description" => __( "Choose the display type for the team members.", 'swift-framework-plugin' )
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
                    "param_name"  => "item_columns",
                    "value"       => array(
                        __( '2', 'swift-framework-plugin' ) => "2",
                        __( '3', 'swift-framework-plugin' ) => "3",
                        __( '4', 'swift-framework-plugin' ) => "4",
                        __( '5', 'swift-framework-plugin' ) => "5"
                    ),
                    "description" => __( "Choose the amount of columns you would like for the team asset.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "textfield",
                    "class"       => "",
                    "heading"     => __( "Custom Image Height", 'swift-framework-plugin' ),
                    "param_name"  => "custom_image_height",
                    "value"       => "",
                    "description" => __( "Enter a value here if you would like to override the image height of the team member images. Numerical value (no px).", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "dropdown",
                    "heading"     => __( "Profile Link", 'swift-framework-plugin' ),
                    "param_name"  => "profile_link",
                    "value"       => array(
                        __( 'Link to Page', 'swift-framework-plugin' ) => "yes",
                        __( 'Link to Modal', 'swift-framework-plugin' )  => "profile-modal",
                        __( 'No', 'swift-framework-plugin' )  => "no"
                    ),
                    "description" => __( "Select if you'd like the team members to link through to the profile page.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "buttonset",
                    "heading"     => __( "Full Width", 'swift-framework-plugin' ),
                    "param_name"  => "fullwidth",
                    "value"       => array(
                        __( 'No', 'swift-framework-plugin' )  => "no",
                        __( 'Yes', 'swift-framework-plugin' ) => "yes"
                    ),
                    "description" => __( "Select if you'd like the asset to be full width (edge to edge). NOTE: only possible on pages without sidebars.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "buttonset",
                    "heading"     => __( "Gutters", 'swift-framework-plugin' ),
                    "param_name"  => "gutters",
                    "value"       => array(
                        __( 'Yes', 'swift-framework-plugin' ) => "yes",
                        __( 'No', 'swift-framework-plugin' )  => "no"
                    ),
                    "description" => __( "Select if you'd like spacing between the items, or not.", 'swift-framework-plugin' )
                ),
                array(
                    "type"       => "section",
                    "param_name" => "ib_filter_options",
                    "heading"    => __( "Filter Options", 'swift-framework-plugin' ),
                ),
                array(
                    "type"        => "select-multiple",
                    "heading"     => __( "Team category", 'swift-framework-plugin' ),
                    "param_name"  => "category",
                    "value"       => sf_get_category_list( 'team-category' ),
                    "description" => __( "Choose the category for the team items.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "textfield",
                    "class"       => "",
                    "heading"     => __( "Number of items", 'swift-framework-plugin' ),
                    "param_name"  => "item_count",
                    "value"       => "12",
                    "description" => __( "The number of team members to show per page.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "textfield",
                    "heading"     => __( "Extra class", 'swift-framework-plugin' ),
                    "param_name"  => "el_class",
                    "value"       => "",
                    "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin' )
                )
            )
        ) );


        /* Team Member Tile
        ================================================== */

        class SwiftPageBuilderShortcode_spb_team_member extends SwiftPageBuilderShortcode {
            public function content( $atts, $content = null ) {
                $title = $el_class = $width = $team_member = $display_type = $profile_link = $el_position = $inline_style = '';
                extract( shortcode_atts( array(
                    'team_member'       => '',
                    'profile_link'      => 'yes',
                    'display_type'      => 'standard',
                    'width'             => '1/1',
                    'el_position'       => '',
                    'el_class'          => ''
                ), $atts ) );

                $el_class = $this->getExtraClass( $el_class );
                $width    = spb_translateColumnWidthToSpan( $width );

                $output = '';

                $output .= "\n\t" . '<div class="spb_team_member ' . $width . $el_class . '" data-id="'.$team_member.'">';
                $output .= "\n\t\t" . '<div class="spb-asset-content">';
                $output .= "\n\t\t" . sd_team_member_item($team_member, $display_type, $profile_link, "", 1, "");
                $output .= "\n\t\t" . '</div>';
                $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

                $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

                return $output;
            }
        }

        SPBMap::map( 'spb_team_member', array(
                "name"          => __( "Team Member", 'swift-framework-plugin' ),
                "base"          => "spb_team_member",
                "class"         => "spb_team_member spb_tab_media",
                "icon"          => "icon-team",
                "wrapper_class" => "clearfix",
                "params"        => array(
                    array(
                        "type"        => "dropdown",
                        "heading"     => __( "Team Member", 'swift-framework-plugin' ),
                        "param_name"  => "team_member",
                        "value"       => sf_get_post_type( 'team' ),
                        "description" => __( "Choose the team member.", 'swift-framework-plugin' )
                    ),
                    array(
                        "type"       => "section",
                        "param_name" => "ib_display_options",
                        "heading"    => __( "Display Options", 'swift-framework-plugin' ),
                    ),
                    array(
                        "type"        => "dropdown",
                        "heading"     => __( "Display Type", 'swift-framework-plugin' ),
                        "param_name"  => "display_type",
                        "value"       => array(
                            __( 'Standard', 'swift-framework-plugin' )              => "standard",
                            __( 'Standard (No Excerpt)', 'swift-framework-plugin' ) => "standard-alt"
                        ),
                        "description" => __( "Choose the display type for the team members.", 'swift-framework-plugin' )
                    ),
                    array(
                        "type"        => "dropdown",
                        "heading"     => __( "Profile Link", 'swift-framework-plugin' ),
                        "param_name"  => "profile_link",
                        "value"       => array(
                            __( 'Link to Page', 'swift-framework-plugin' ) => "yes",
                            __( 'Link to Modal', 'swift-framework-plugin' )  => "profile-modal",
                            __( 'No', 'swift-framework-plugin' )  => "no"
                        ),
                        "description" => __( "Select if you'd like the team members to link through to the profile page.", 'swift-framework-plugin' )
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
