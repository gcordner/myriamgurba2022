<?php

    /*
    *
    *	Swift Page Builder - Media Shortcodes
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */


    /* VIDEO ASSET
    ================================================== */

    class SwiftPageBuilderShortcode_spb_video extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {
            $title = $link = $size = $el_position = $full_width = $width = $el_class = '';
            extract( shortcode_atts( array(
                'title'       => '',
                'link'        => '',
                'size'        => '1280x720',
                'remove_related' => 'no',
                'autoplay'    => 'no',
                'el_position' => '',
                'width'       => '1/1',
                'full_width'  => 'no',
                'el_class'    => ''
            ), $atts ) );
            $output = '';

            if ( $link == '' ) {
                return null;
            }
            $video_h  = '';

            /* FULL WIDTH CONFIG
            ================================================== */
            if ( $full_width == "yes" && $width == '1/1' ) {
                $fullwidth = true;
            } else {
                $fullwidth = false;
            }

            $fullwidth = apply_filters('spb_video_fw_override', $fullwidth);

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );
            $size     = str_replace( array( 'px', ' ' ), array( '', '' ), $size );
            $size     = explode( "x", $size );
            $video_w  = $size[0];
            if ( count( $size ) > 1 ) {
                $video_h = $size[1];
            }
            $extra_params = '';

            if ( $remove_related == "yes" ) {
                $extra_params .= '&rel=0';
            }
            if ( $autoplay == "yes" ) {
                $extra_params .= '&amp;autoplay=1';
            }

            $embed = spb_video_embed( $link, $video_w, $video_h, $extra_params );

            if ( $fullwidth ) {
                $output .= "\n\t" . '<div class="spb_video_widget full-width spb-full-width-element spb_content_element ' . $width . $el_class . '">';
            } else {
                $output .= "\n\t" . '<div class="spb_video_widget spb_content_element ' . $width . $el_class . '">';
            }
            $output .= "\n\t\t" . '<div class="spb-asset-content">';
            $output .= ( $title != '' ) ? "\n\t\t\t" . $this->spb_title( $title, '' ) : '';
            $output .= apply_filters( 'spb_video_before_embed', '', $atts );
            $output .= $embed;
            $output .= apply_filters( 'spb_video_after_embed', '', $atts );
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position, '', $fullwidth ) . $output . $this->endRow( $el_position, '', $fullwidth );

            return $output;
        }
    }

    SPBMap::map( 'spb_video', array(
        "name"   => __( "Video Player", 'swift-framework-plugin' ),
        "base"   => "spb_video",
        "class"  => " spb_tab_media ",
        "icon"   => "icon-video",
        "params" => array(
            array(
                "type"        => "textfield",
                "heading"     => __( "Widget title", 'swift-framework-plugin' ),
                "param_name"  => "title",
                "value"       => "",
                "description" => __( "Heading text. Leave it empty if not needed.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Video link", 'swift-framework-plugin' ),
                "param_name"  => "link",
                "value"       => "",
                "description" => __( 'Enter the full, non-shortened, link to the video. YouTube or Vimeo.', 'swift-framework-plugin' ),
                //"link"        =>  '<a class="spb_field_link" href="http://codex.wordpress.org/Embeds#Okay.2C_So_What_Sites_Can_I_Embed_From.3F" target="_blank">'. __('More about supported formats click this link', 'swift-framework-plugin' ) . '</a>'
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Video size", 'swift-framework-plugin' ),
                "param_name"  => "size",
                "value"       => "",
                "description" => __( 'Enter video size in pixels. Example: 200x100 (Width x Height). NOTE: This does not affect the size of the video which is shown on the page, this is purely used for aspect ration purposes.', 'swift-framework-plugin' )
            ),
            array(
                "type"        => "buttonset",
                "heading"     => __( "Remove related (YT)", 'swift-framework-plugin' ),
                "param_name"  => "remove_related",
                "value"       => array(
                    __( 'Yes', 'swift-framework-plugin' ) => "yes",
                    __( 'No', 'swift-framework-plugin' )  => "no"
                ),
                "buttonset_on"  => "yes",
                "description" => __( "Select this if you would like to remove the related videos shown at the end of the video.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "buttonset",
                "heading"     => __( "Autoplay", 'swift-framework-plugin' ),
                "param_name"  => "autoplay",
                "value"       => array(
                    __( 'Yes', 'swift-framework-plugin' ) => "yes",
                    __( 'No', 'swift-framework-plugin' )  => "no"
                ),
                "buttonset_on"  => "yes",
                "std"         => 'no',
                "description" => __( "Select this if you would like the video to autoplay.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "buttonset",
                "heading"     => __( "Full width", 'swift-framework-plugin' ),
                "param_name"  => "full_width",
                "value"       => array(
                    __( 'Yes', 'swift-framework-plugin' ) => "yes",
                    __( 'No', 'swift-framework-plugin' )  => "no"
                ),
                "buttonset_on"  => "yes",
                "std"         => 'no',
                "description" => __( "Select this if you want the video to be the full width of the page container (leave the above size blank).", 'swift-framework-plugin' )
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


        /* SINGLE IMAGE ASSET
    ================================================== */

    class SwiftPageBuilderShortcode_spb_image extends SwiftPageBuilderShortcode {

        public function content( $atts, $content = null ) {

            $el_class = $width = $image_size = $animation = $frame = $lightbox = $hover_style = $image_link = $link_target = $caption_title = $caption_subtitle = $caption_title_initial = $caption_subtitle_initial = $fullwidth = $el_position = $el_class = $image = '';

            extract( shortcode_atts( array(
                'width'                     => '1/1',
                'image'                     => $image,
                'image_size'                => '',
                'image_width'               => '',
                'intro_animation'           => 'none',
                'animation_delay'           => '200',
                'lightbox'                  => '',
                'image_link'                => '',
                'image_link_title'          => '',
                'link_target'               => '',
                'hover_style'               => 'default',
                'caption_title'             => '',
                'caption_subtitle'          => '',
                'caption_title_initial'     => '',
                'caption_subtitle_initial'  => '',
                'caption_pos'               => 'hover',
                'fullwidth'                 => 'no',
                'el_position'               => '',
                'el_class'                  => ''
            ), $atts ) );

            $atts["frame"] = $frame = "noframe";
            $atts["fullwidth"] = $fullwidth = "no";
            $image_link_title = $atts["image_link_title"];

            $link_icon = apply_filters( 'sf_link_icon' , '<i class="ss-link"></i>' );
            $view_icon = apply_filters( 'sf_view_icon' , '<i class="ss-view"></i>' );

            if ( $image_size == "" ) {
                $image_size = "full";
            }
            $detect = new Mobile_Detect;
            if ( $detect->isMobile() && $image_width > wp_get_attachment_image_src( $image, "preview-card" )[1] ) {
                $image_width = wp_get_attachment_image_src( $image, "preview-card" )[1];
            }

            $output = '';
            if ( $fullwidth == "yes" && $width == "1/1" ) {
                $fullwidth = true;
            } else {
                $fullwidth = false;
            }
            $img      = spb_getImageBySize( array(
                'attach_id'  => preg_replace( '/[^\d]/', '', $image ),
                'thumb_size' => $image_size
            ) );
            $img_url = wp_get_attachment_image_src( $image, $image_size );
            $img_srcset = wp_get_attachment_image_srcset( $image, $image_size );
            $img_alt = get_post_meta( $image, '_wp_attachment_image_alt', true );
            if ( $image_width != "" && $img_url[1] > $image_width ) {
                $thumb_width = $image_width;
                $thumb_height = round( ( $img_url[2] * $thumb_width ) / $img_url[1] );
                $img["p_img_large"] = sf_aq_resize( $img_url[0], $thumb_width, $thumb_height, true, false );
                $img["thumbnail"] = '<img src="' . $img["p_img_large"][0] . '" width="' . $thumb_width . '" height="' . $thumb_height . '" alt="' . $img_alt . '" srcset="' . $img_srcset . '" />';
            }

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );
            // $content =  !empty($image) ? '<img src="'..'" alt="">' : '';
            $content = '';
            if ( $image_width != "" ) {
                $image_width = 'style="width:' . $image_width . 'px;margin:0 auto;"';
            }

            // Thumb Type
            if ( $hover_style == "default" && function_exists( 'sf_get_thumb_type' ) ) {
                $el_class .= ' ' . sf_get_thumb_type();
            } else {
                $el_class .= ' thumbnail-' . $hover_style;
            }

            if ( $intro_animation != "none" ) {
                $output .= "\n\t" . '<div class="spb_content_element spb_image sf-animation ' . $frame . ' ' . $width . $el_class . '" data-animation="' . $intro_animation . '" data-delay="' . $animation_delay . '">';
            } else {
                $output .= "\n\t" . '<div class="spb_content_element spb_image ' . $frame . ' ' . $width . $el_class . '">';
            }
            $output .= "\n\t\t" . '<div class="spb-asset-content">';
            if ( $caption_pos == "hover" && ( $caption_title != "" || $caption_subtitle != "" ) ) {
                $output .= '<figure class="animated-overlay overlay-style caption-hover clearfix" ' . $image_width . '>';
            } else if ( $lightbox == "yes" || $image_link != "" ) {
                $output .= '<figure class="animated-overlay overlay-alt clearfix" ' . $image_width . '>';
            } else {
                $output .= '<figure class="clearfix" ' . $image_width . '>';
            }
            if ( $image_link != "" ) {
                $output .= $img['thumbnail'];
                $output .= '<div class="figcaption-wrap"></div>';
                if ( $caption_pos == "hover" && ( $caption_title_initial != "" || $caption_subtitle_initial != "" ) ) {
                    $output .= '<figcaption class="initial">';
                    $output .= '<div class="thumb-info">';
                    if ( $caption_title_initial != "" ) {
                        $output .= '<h4> ' . $caption_title_initial . '</h4>';
                    }
                    if ( $caption_subtitle_initial != "" ) {
                        $output .= '<h5>' . $caption_subtitle_initial . '</h5>';
                    }
                    $output .= '</div>';
                    $output .= '</figcaption>';
                }
                if ( $caption_pos == "hover" && ( $caption_title != "" || $caption_subtitle != "" ) ) {
                    $output .= '<figcaption class="hover">';
                    $output .= '<div class="thumb-info">';

                    if ( $caption_title != "" ) {
                        $output .= '<h4>' . $caption_title . '</h4>';
                    }
                    if ( $caption_subtitle != "" ) {
                        $output .= '<h5>' . $caption_subtitle . '</h5>';
                    }
                } else {
                    $output .= '<figcaption>';
                    $output .= '<div class="thumb-info thumb-info-alt">';
                    $output .= $link_icon;
                }
                $output .= '</div></figcaption>';
                if ( $image_link_title != "" ) {
                    $output .= "\n\t\t" . '<a class="img-link" href="' . $image_link . '" target="' . $link_target . '" title="' . esc_attr($image_link_title) . '"><span class="accessibility-text">' . $image_link_title . '</span></a>';
                } else {
                    $output .= "\n\t\t" . '<a class="img-link" href="' . $image_link . '" target="' . $link_target . '" aria-hidden="true" tabindex="-1"></a>';
                }
            } else if ( $lightbox == "yes" ) {
                $output .= $img['thumbnail'];
                $output .= '<div class="figcaption-wrap"></div>';
                $output .= '<figcaption>';
                if ( $caption_pos == "hover" ) {
                    if ( $caption_title != "" || $caption_subtitle != "" ) {
                        $output .= '<div class="thumb-info">';
                        if ( $caption_title != "" ) {
                            $output .= '<h4>' . $caption_title . '</h4>';
                        }
                        if ( $caption_subtitle != "" ) {
                            $output .= '<h5>' . $caption_subtitle . '</h5>';
                        }
                    } else {
                        $output .= '<div class="thumb-info thumb-info-alt">';
                        $output .= $view_icon;
                    }
                } else {
                    $output .= '<div class="thumb-info thumb-info-alt">';
                    $output .= $view_icon;
                }
                $output .= '</div></figcaption>';
                if ( $img_url[0] != "" ) {
                    if ( $image_link_title != "" ) {
                        $output .= '<a class="lightbox" href="' . $img_url[0] . '" data-rel="ilightbox[' . $image . '-' . rand( 0, 1000 ) . ']" data-caption="' . $caption_title . "\n" . $caption_subtitle . '" title="' . esc_attr($image_link_title) . '"><span class="accessibility-text">' . $image_link_title . '</span></a>';
                    } else {
                        $output .= '<a class="lightbox" href="' . $img_url[0] . '" data-rel="ilightbox[' . $image . '-' . rand( 0, 1000 ) . ']" data-caption="' . $caption_title . "\n" . $caption_subtitle . '" aria-hidden="true" tabindex="-1"></a>';
                    }
                }
            } else {
                $output .= "\n\t\t" . $img['thumbnail'];
                $output .= '<div class="figcaption-wrap"></div>';
                if ( $caption_pos == "hover" && ( $caption_title_initial != "" || $caption_subtitle_initial != "" ) ) {
                    $output .= '<figcaption class="initial">';
                    $output .= '<div class="thumb-info"><div class="initial-content">';
                    if ( $caption_title_initial != "" ) {
                        $output .= '<h4>' . $caption_title_initial . '</h4>';
                    }
                    if ( $caption_subtitle_initial != "" ) {
                        $output .= '<h5>' . $caption_subtitle_initial . '</h5>';
                    }
                    $output .= '</div></div>';
                    $output .= '</figcaption>';
                }
                if ( $caption_pos == "hover" && ( $caption_title != "" || $caption_subtitle != "" ) ) {
                    $output .= '<figcaption class="hover">';
                    $output .= '<div class="thumb-info">';
                    if ( $caption_title != "" ) {
                        $output .= '<h4>' . $caption_title . '</h4>';
                    }
                    if ( $caption_subtitle != "" ) {
                        $output .= '<h5>' . $caption_subtitle . '</h5>';
                    }
                    $output .= '</div>';
                    $output .= '</figcaption>';
                }
            }
            $output .= '</figure>';
            if ( $caption_pos == "below" && ( $caption_title != "" || $caption_subtitle != "" ) ) {
                $output .= '<div class="image-caption">';
                if ( $caption_title != "" ) {
                    $output .= '<h4>' . $caption_title . '</h4>';
                }
                if ( $caption_subtitle != "" ) {
                    $output .= '<h5>' . $caption_subtitle . '</h5>';
                }
                $output .= '</div>';
            }
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            if ( $fullwidth == "yes" ) {
                $output = $this->startRow( $el_position, '', true ) . $output . $this->endRow( $el_position, '', true );
            } else {
                $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );
            }

            return $output;
        }

        public function singleParamHtmlHolder( $param, $value ) {
            $output = '';
            // Compatibility fixes
            $old_names = array(
                'yellow_message',
                'blue_message',
                'green_message',
                'button_green',
                'button_grey',
                'button_yellow',
                'button_blue',
                'button_red',
                'button_orange'
            );
            $new_names = array(
                'alert-block',
                'alert-info',
                'alert-success',
                'btn-success',
                'btn',
                'btn-info',
                'btn-primary',
                'btn-danger',
                'btn-warning'
            );
            $value     = str_ireplace( $old_names, $new_names, $value );

            $param_name = isset( $param['param_name'] ) ? $param['param_name'] : '';
            $type       = isset( $param['type'] ) ? $param['type'] : '';
            $class      = isset( $param['class'] ) ? $param['class'] : '';

            if ( isset( $param['holder'] ) == false || $param['holder'] == 'hidden' ) {
                $output .= '<input type="hidden" class="spb_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" value="' . $value . '" />';
                if ( ( $param['type'] ) == 'attach_image' ) {
                    $img = spb_getImageBySize( array(
                        'attach_id'  => (int) preg_replace( '/[^\d]/', '', $value ),
                        'thumb_size' => 'thumbnail'
                    ) );
                    $output .= ( $img ? $img['thumbnail'] : '<img width="150" height="150" src="' . SwiftPageBuilder::getInstance()->assetURL( 'img/blank_f7.gif' ) . '" class="attachment-thumbnail" alt="" title="" />' ) . '<a href="#" class="column_edit_trigger' . ( $img && ! empty( $img['p_img_large'][0] ) ? ' image-exists' : '' ) . '"><i class="spb-icon-single-image"></i>' . __( 'No image yet! Click here to select it now.', 'swift-framework-plugin' ) . '</a>';
                }
            } else {
                $output .= '<' . $param['holder'] . ' class="spb_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '">' . $value . '</' . $param['holder'] . '>';
            }

            return $output;
        }
    }

    SPBMap::map( 'spb_image', array(
        "name"   => __( "Image", 'swift-framework-plugin' ),
        "base"   => "spb_image",
        "class"  => "spb_image_widget spb_tab_media",
        "icon"   => "icon-image",
        "params" => array(
            array(
                "type"        => "attach_image",
                "heading"     => __( "Image", 'swift-framework-plugin' ),
                "param_name"  => "image",
                "value"       => "",
                "description" => ""
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Image Size", 'swift-framework-plugin' ),
                "param_name"  => "image_size",
                "value"       => spb_get_image_sizes(),
                "description" => __( "Select the source size for the image (NOTE: this doesn't affect it's size on the front-end, only the quality).", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Image width", 'swift-framework-plugin' ),
                "param_name"  => "image_width",
                "value"       => "",
                "description" => __( "If you would like to override the width that the image is displayed at, then please provide the value here (no px). NOTE: The image can only be max 100% of it's container, this is generally for use if you would like to make the image smaller, and it will be centralised.", 'swift-framework-plugin' )
            ),
            array(
                "type"       => "section",
                "param_name" => "tb_link_options",
                "heading"    => __( "Link Options", 'swift-framework-plugin' ),
            ),
            array(
                "type"        => "buttonset",
                "heading"     => __( "Enable lightbox link", 'swift-framework-plugin' ),
                "param_name"  => "lightbox",
                "value"       => array(
                    __( "No", 'swift-framework-plugin' )  => "no",
                    __( "Yes", 'swift-framework-plugin' ) => "yes"
                ),
                "description" => __( "Select if you want the image to open in a lightbox on click", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Add link to image", 'swift-framework-plugin' ),
                "param_name"  => "image_link",
                "value"       => "",
                "description" => __( "If you would like the image to link to a URL, then enter it here. NOTE: this will override the lightbox functionality if you have enabled it.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Add title for link", 'swift-framework-plugin' ),
                "param_name"  => "image_link_title",
                "value"       => "",
                "description" => __( "This is used for accessibility purposes and tells the user a bit more about where they will be going when they click the link.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Link opens in new window?", 'swift-framework-plugin' ),
                "param_name"  => "link_target",
                "value"       => array(
                    __( "Self", 'swift-framework-plugin' )       => "_self",
                    __( "New Window", 'swift-framework-plugin' ) => "_blank"
                ),
                "description" => __( "Select if you want the link to open in a new window", 'swift-framework-plugin' )
            ),
            array(
                "type"       => "section",
                "param_name" => "tb_caption_options",
                "heading"    => __( "Caption Options", 'swift-framework-plugin' ),
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Caption Position", 'swift-framework-plugin' ),
                "param_name"  => "caption_pos",
                "value"       => array(
                    __( "Hover", 'swift-framework-plugin' ) => "hover",
                    __( "Below", 'swift-framework-plugin' ) => "below"
                ),
                "description" => __( "Choose if you would like the caption to appear on the hover, or below the image. If you leave both of the caption fields below blank then no caption will be shown.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Caption Title", 'swift-framework-plugin' ),
                "param_name"  => "caption_title",
                "value"       => "",
                "description" => __( "If you would like a caption title to be shown on hover or below the image, add it here. This will be rendered as a Heading 2.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Caption Subtitle", 'swift-framework-plugin' ),
                "param_name"  => "caption_subtitle",
                "value"       => "",
                "description" => __( "If you would like a caption subtitle to be shown on hover or below the image, add it here. This will be rendered as a Heading 4.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Initial Caption Title", 'swift-framework-plugin' ),
                "param_name"  => "caption_title_initial",
                "value"       => "",
                "required"    => array("caption_pos", "=", "hover"),
                "description" => __( "If you would like an caption title to be shown on the image initially, add it here. Otherwise leave blank. This will be rendered as a Heading 2.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Initial Caption Subtitle", 'swift-framework-plugin' ),
                "param_name"  => "caption_subtitle_initial",
                "value"       => "",
                "required"    => array("caption_pos", "=", "hover"),
                "description" => __( "If you would like a caption subtitle to be shown for on the image initially, add it here. Otherwise leave blank. This will be rendered as a Heading 4.", 'swift-framework-plugin' )
            ),
            array(
                "type"       => "section",
                "param_name" => "tb_animation_options",
                "heading"    => __( "Animation Options", 'swift-framework-plugin' ),
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Intro Animation", 'swift-framework-plugin' ),
                "param_name"  => "intro_animation",
                "value"       => spb_animations_list(),
                "description" => __( "Select an intro animation for the image that will show it when it appears within the viewport.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Animation Delay", 'swift-framework-plugin' ),
                "param_name"  => "animation_delay",
                "value"       => "200",
                "description" => __( "If you wish to add a delay to the animation, then you can set it here (default 200) (ms).", 'swift-framework-plugin' )
            ),
            array(
                "type"       => "section",
                "param_name" => "btn_misc_options",
                "heading"    => __( "Misc Options", 'swift-framework-plugin' ),
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
 