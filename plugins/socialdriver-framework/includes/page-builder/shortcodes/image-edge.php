<?php

/* GET ATTACHMENT META
================================================== */
if ( ! function_exists( 'spb_get_attachment_meta' ) ) {
    function spb_get_attachment_meta( $attachment_id ) {

        $attachment = get_post( $attachment_id );

        if ( isset( $attachment ) ) {
            return array(
                'alt' => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
                'caption' => $attachment->post_excerpt,
                'description' => $attachment->post_content,
                'href' => get_permalink( $attachment->ID ),
                'src' => $attachment->guid,
                'title' => $attachment->post_title
            );
        }
    }
}

class SwiftPageBuilderShortcode_spb_image_edge extends SwiftPageBuilderShortcode {

    public function content( $atts, $content = null ) {

            $el_class = $width = $image_width = $form_content = $image_width = $padding_vertical = $image_position = $el_position = $el_class = $image = $img_url = $el_id = '';

        extract( shortcode_atts( array(
            'title'             => '',
            'width'             => '1/1',
            'padding_vertical'  => '0',
            'image'             => $image,
            'image_size'        => '',
            'image_width'       => '',
            'image_type'        => 'cover',
            'fixed_height'      => '',
            'image_position'    => 'left',
            'form_content'      => '',
            'el_position'       => '',
            'el_class'          => '',
            'el_id'    => ''
        ), $atts ) );

        $fullwidth = false;

        $inline_style = "";
        if ( $padding_vertical != "" ) {
            $inline_style .= 'padding-top:' . $padding_vertical . 'px;padding-bottom:' . $padding_vertical . 'px;';
        }

        if ( $form_content != '' ){
            $content = html_entity_decode($form_content);
        }  

        $image_type = $atts["image_type"];
        if ( $image_type == "" ) {
            $image_type = "cover";
        } 

        $output = '';

        $image_id = preg_replace( '/[^\d]/', '', $image );

        if ( $image_size == "" ) {
            $image_size = "full";
        }
        $detect = new Mobile_Detect;
        if ( $detect->isMobile() && $image_width > wp_get_attachment_image_src( $image, "preview-card" )[1] ) {
            $image_width = wp_get_attachment_image_src( $image, "preview-card" )[1];
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
            $img_url = $img["p_img_large"][0];
            $img["thumbnail"] = '<img src="' . $img["p_img_large"][0] . '" width="' . $thumb_width . '" height="' . $thumb_height . '" alt="' . $img_alt . '" srcset="' . $img_srcset . '" />';
        } else {
            $img_url = $img_url[0];
        }

        $el_class = $this->getExtraClass( $el_class, $fullwidth );
        $width    = spb_translateColumnWidthToSpan( $width );

        $output .= "\n\t" . '<div class="spb_content_element spb_image_edge pos-' . $image_position . ' type-' . $image_type . ' ' . $width . $el_class . '">';
        $output .= "\n\t\t" . '<div class="spb-asset-content"><div class="row">';

            $output .= '<div class="image-edge col-md-6 col-sm-4">';
                $output .= '<div class="background-image-holder" style="background-image: url(' . $img_url . '); opacity: 1;" role="img" title="' . $img_alt . '">';
                    $output .= $img["thumbnail"];
                $output .= '</div>';
            $output .= '</div>';
            $output .= '<div class="container content-holder">';
                $output .= '<div class="row">';
                    $output .= '<div id="' . $el_id . '" class="spb_text_column col-md-5 col-sm-7 col-xs-12" style="' . $inline_style . '">';
                        $output .= '<div class="image-edge-content">' . closetags(apply_filters("the_content", $content )) . '</div>';
                    $output .= '</div>';
                $output .= '</div>';
            $output .= '</div>';

        $output .= "\n\t\t" . '</div></div>';
        $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

        $output = $this->startRow( $el_position, $fullwidth ) . $output . $this->endRow( $el_position, $fullwidth );

        return $output;
    }
}

SPBMap::map( 'spb_image_edge', array(
    "name"   => __( "Image Edge", 'swift-framework-plugin' ),
    "base"   => "spb_image_edge",
    "class"  => "spb_image_edge_widget spb_tab_media",
    "icon"   => "icon-image-banner",
    "wrapper_class" => "clearfix",
    "controls"      => "full",
    "params" => array(
        array(
            "type"        => "attach_image",
            "heading"     => __( "Image", 'swift-framework-plugin' ),
            "param_name"  => "image",
            "value"       => "",
            "description" => ""
        ),
        array(
            "type"        => "dropdown-id",
            "heading"     => __( "Image Size", 'swift-framework-plugin' ),
            "param_name"  => "image_size",
            "value"       => spb_get_image_sizes(),
            "std"         => 'full',
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
            "type"        => "dropdown",
            "heading"     => __( "Image Position", 'swift-framework-plugin' ),
            "param_name"  => "image_position",
            "value"       => array(
                __( "Left", 'swift-framework-plugin' )  => "left",
                __( "Right", 'swift-framework-plugin' ) => "right",
            ),
            "description" => __( "Select the side where you would like the image to display.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Image Type", 'swift-framework-plugin' ),
            "param_name"  => "image_type",
            "value"       => array(
                __( "Cover", 'swift-framework-plugin' )  => "cover",
                __( "Contain", 'swift-framework-plugin' ) => "contain",
            ),
            "description" => __( "Select the side where you would like the image to display.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "textarea_html",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __( "Text", 'swift-framework-plugin' ),
            "param_name"  => "content",
            "value"       => '',
            //"value" => __("<p>This is a text block. Click the edit button to change this text.</p>", 'swift-framework-plugin'),
            "description" => __( "Enter your content.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "uislider",
            "heading"     => __( "Vertical Padding", 'swift-framework-plugin' ),
            "param_name"  => "padding_vertical",
            "value"       => "0",
            "step"        => "1",
            "min"         => "0",
            "max"         => "300",
            "description" => __( "Adjust the vertical padding for the text block (px).", 'swift-framework-plugin' )
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
        ),
        array(
            "type"        => "textfield",
            "heading"     => __( "Extra ID", 'swift-framework-plugin' ),
            "param_name"  => "el_id",
            "value"       => "",
            "description" => __( "If you wish to link to this particular content element through an anchor tag, then use this field to add a ID name.", 'swift-framework-plugin' )
          ),
        array(
            "type"        => "textfield",
            "heading"     => __( "Data Form Content", 'swift-framework-plugin' ),
            "param_name"  => "form_content",
            "value"       => "",
            "description" => __( "This is a hidden field that is used to save the content when using forms inside the content.", 'swift-framework-plugin' )
        )
    )
) );
