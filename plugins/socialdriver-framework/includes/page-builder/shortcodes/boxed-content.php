<?php

    /*
    *
    *   Swift Page Builder - Client Custom Shortcode
    *   ------------------------------------------------
    *   Swift Framework
    *   Copyright Swift Ideas 2015 - http://www.swiftideas.com
    *
    */

    if ( class_exists('SwiftPageBuilderShortcode') ) {

        /* BOXED CONTENT ASSET
        ================================================== */

        class SwiftPageBuilderShortcode_spb_boxed_content extends SwiftPageBuilderShortcode {

            public function content( $atts, $content = null ) {

                $title = $type = $box_link = $box_link_target = $image = $bg_style = $inline_style = $custom_bg_colour = $custom_text_colour = $el_class = $width = $el_position = $box_link = '';

                extract( shortcode_atts( array(
                    'title'              => '',
                    'box_link_target'    => '_self',
                    'image'              => '',
                    'el_class'           => '',
                    'el_position'        => '',
                    'width'              => '1/2',
                    'box_link'      => '',
                ), $atts ) );

                $output = '';

                $el_class = $this->getExtraClass( $el_class );
                $width    = spb_translateColumnWidthToSpan( $width );

                $image_width  = 300;
                $image_height = 200;
                $img_url            = wp_get_attachment_url( $image, 'full' );
                $image              = sf_aq_resize( $img_url, $image_width, $image_height, true, false );

                if ( $image ) {
                    $type = 'has-image';
                    $style_image = ' style="background-image: url(' . $img_url . ');"';
                }

                $output .= "\n\t" . '<div class="spb_content_element spb_box_content ' . $width . $el_class . '">';
                if($box_link){
                    if($box_link_target == "_blank"){
                        $output .= "\n\t" . '<a href="' . $box_link .'" class="boxed-content-link" target="_blank"></a>';
                    } else {
                        $output .= "\n\t" . '<a href="' . $box_link .'" class="boxed-content-link"></a>';
                    }
                }
                $output .= "\n\t\t"   . '<div class="spb-bg-color-wrap ' . $type . '"' . $style_image . '>';
                $output .= "\n\t\t"   . '<div class="spb-asset-content">';
                if ( $title != "" ) {
                    $output .= "\n\t\t\t" . '<div class="box-content-meta"><h4>' . $title .'</h4></div>';
                }
                $output .= "\n\t\t\t" . '<div class="box-content-wrap">' . do_shortcode( $content ) . '</div>';
                $output .= "\n\t\t"   . '</div>';
                $output .= "\n\t\t"   . '</div>';
                $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

                $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

                global $sf_include_imagesLoaded;
                $sf_include_imagesLoaded = true;

                return $output;
            }
        }

        $target_arr = array(
            __( "Same window", 'swift-framework-plugin' ) => "_self",
            __( "New window", 'swift-framework-plugin' )  => "_blank"
        );

        SPBMap::map( 'spb_boxed_content', array(
            "name"          => __( "Boxed Content", 'swift-framework-plugin' ),
            "base"          => "spb_boxed_content",
            "class"         => "spb_tab_media",
            "icon"          => "icon-boxed-content",
            "wrapper_class" => "clearfix",
            "controls"      => "full",
            "params"        => array(
                array(
                    "type"        => "textfield",
                    "holder"      => "div",
                    "heading"     => __( "Title", 'swift-framework-plugin' ),
                    "param_name"  => "title",
                    "value"       => "",
                    "description" => __( "Insert the title of the resource here", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "textarea_html",
                    "holder"      => "div",
                    "class"       => "",
                    "heading"     => __( "Description", 'swift-framework-plugin' ),
                    "param_name"  => "content",
                    "value"       => __( "<p>This is a boxed content block. Click the edit button to edit this text.</p>", 'swift-framework-plugin' ),
                    "description" => __( "Enter your content.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "attach_image",
                    "heading"     => __( "Image", 'swift-framework-plugin' ),
                    "param_name"  => "image",
                    "value"       => "",
                    "description" => "Optional. Please provide and image with a minimum height of 200px."
                ),
                array(
                    "type"        => "textfield",
                    "heading"     => __( "Link", 'swift-framework-plugin' ),
                    "param_name"  => "box_link",
                    "value"       => "",
                    "description" => __( "Enter the resource link here, be sure to include http://.", 'swift-framework-plugin' )
                ),
                array(
                    "type"       => "dropdown",
                    "heading"    => __( "Link Target", 'swift-framework-plugin' ),
                    "param_name" => "box_link_target",
                    "value"      => $target_arr,
                    "description" => __( "Select if the link should open in the same window or a new tab.", 'swift-framework-plugin' )
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

    }
    