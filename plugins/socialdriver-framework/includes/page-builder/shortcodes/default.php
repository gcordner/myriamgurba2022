<?php

    /*
    *
    *   Swift Page Builder - Default Shortcodes
    *   ------------------------------------------------
    *   Swift Framework
    *   Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    global $sf_opts;

    /* TEXT BLOCK ASSET
    ================================================== */

    class SwiftPageBuilderShortcode_spb_text_block extends SwiftPageBuilderShortcode {

        public function content( $atts, $content = null ) {

            $title = $el_class = $width = $el_position = $inline_style = $form_content = $custom_css = $bk_image_global = '';

            extract( shortcode_atts( array(
                'title'              => '',
                'icon'               => '',
                'padding_vertical'   => '0',
                'padding_horizontal' => '0',
                'animation'          => '',
                'animation_delay'    => '',
                'el_class'           => '',
                'el_position'        => '',
                'form_content'       => '',
                'width'              => '1/2',
                'custom_css'         => '',
                'bk_image_global'    => '',
            ), $atts ) );

            if ( $form_content != '' ){
                $content = html_entity_decode($form_content);
            }  
           
            $output = '';

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );

            $el_class .= ' spb_text_column';

            if( $custom_css != "" ){
                
                $pos = strpos( $custom_css, 'margin-bottom' );
                if ($pos !== false) { 
                    //$el_class .= ' mt0 mb0';        
                 }
            

                $inline_style .= $custom_css;
                $img_url = wp_get_attachment_image_src( $bk_image_global, 'full' );

                if( isset( $img_url ) && $img_url[0] != "" ) {
                    $inline_style .= 'background-image: url(' . $img_url[0] . ');';
                }
            }else{

                if ( $padding_vertical != "" ) {
                    $inline_style .= 'padding-top:' . $padding_vertical . '%;padding-bottom:' . $padding_vertical . '%;';
                }
                if ( $padding_horizontal != "" ) {
                    $inline_style .= 'padding-left:' . $padding_horizontal . '%;padding-right:' . $padding_horizontal . '%;';
                }
            }

            $icon_output = "";

            if ( $icon ) {
                $icon_output = '<i class="' . $icon . '"></i>';
            }

            if ( $animation != "" && $animation != "none" ) {
                $output .= "\n\t" . '<div class="spb_content_element sf-animation ' . $width . $el_class . '" data-animation="' . $animation . '" data-delay="' . $animation_delay . '">';
            } else {
                $output .= "\n\t" . '<div class="spb_content_element ' . $width . $el_class . '">';
            }

            $output .= "\n\t\t" . '<div class="spb-asset-content" style="' . $inline_style . '">';
            if ( $icon_output != "" ) {
                $output .= ( $title != '' ) ? "\n\t\t\t" . '<div class="title-wrap"><h3 class="spb-heading spb-icon-heading"><span>' . $icon_output . '' . $title . '</span></h3></div>' : '';
            } else {
                $output .= ( $title != '' ) ? "\n\t\t\t" . $this->spb_title( $title, 'spb-text-heading' ) : '';
            }
            $output .= "\n\t\t\t" . do_shortcode( $content );
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position, '', false ) . $output . $this->endRow( $el_position, '', false );

            return $output;
        }
    }
    
   
   SPBMap::map( 'spb_text_block', array(
            "name"          => __( "Text Block", 'swift-framework-plugin' ),
            "base"          => "spb_text_block",
            "class"         => "spb_tab_media",
            "icon"          => "icon-text-block",
            "wrapper_class" => "clearfix",
            "controls"      => "full",
            "params"        => array(
                array(
                    "type"        => "textfield",
                    "holder"      => "div",
                    "heading"     => __( "Widget title", 'swift-framework-plugin' ),
                    "param_name"  => "title",
                    "value"       => "",
                    "description" => __( "Heading text. Leave it empty if not needed.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "textfield",
                    "heading"     => __( "Title icon", 'swift-framework-plugin' ),
                    "param_name"  => "icon",
                    "value"       => "",
                    "description" => __( "Icon to the left of the title text. This is the class name for the icon, e.g. fa-cloud", 'swift-framework-plugin' )
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
                    "type"       => "section_tab",
                    "param_name" => "animation_options_tab",
                    "heading"    => __( "Animation", 'swift-framework-plugin' ),
                ),
                array(
                    "type"        => "dropdown",
                    "heading"     => __( "Intro Animation", 'swift-framework-plugin' ),
                    "param_name"  => "animation",
                    "value"       => spb_animations_list(),
                    "description" => __( "Select an intro animation for the text block that will show it when it appears within the viewport.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "textfield",
                    "heading"     => __( "Animation Delay", 'swift-framework-plugin' ),
                    "param_name"  => "animation_delay",
                    "value"       => "0",
                    "description" => __( "If you wish to add a delay to the animation, then you can set it here (ms).", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "textfield",
                    "heading"     => __( "Data Form Content", 'swift-framework-plugin' ),
                    "param_name"  => "form_content",
                    "value"       => "",
                    "description" => __( "This is a hidden field that is used to save the content when using forms inside the content.", 'swift-framework-plugin' )
                )
            )
        )
    );

    /* BLANK SPACER ASSET
    ================================================== */

    class SwiftPageBuilderShortcode_spb_blank_spacer extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {
            $height = $height_tablet_land = $height_tablet_port = $height_mobile = $el_class = '';
            extract( shortcode_atts( array(
                'spacer_scope'          => 'global',
                'height'                => '',
                'height_tablet_land'    => '',
                'height_tablet_port'    => '',
                'height_mobile'         => '',
                'responsive_vis'        => '',
                'el_position'           => '',
                'el_class'              => '',
                'width'                 => '1/2',
            ), $atts ) );

            global $sf_opts;

            $responsive_vis = str_replace( "_", " ", $responsive_vis );
            $width          = spb_translateColumnWidthToSpan( $width );
            $el_class       = $this->getExtraClass( $el_class ) . ' ' . $responsive_vis;

            if ( $atts["spacer_scope"] == "global" ) {
                if ( $sf_opts["spacer_desktop"] ) {
                    $height = $sf_opts["spacer_desktop"];
                } else {
                    $height = 50;
                }
                if ( $sf_opts["spacer_tablet_land"] ) {
                    $height_tablet_land = $sf_opts["spacer_tablet_land"];
                } else {
                    $height_tablet_land = 40;
                }
                if ( $sf_opts["spacer_tablet_port"] ) {
                    $height_tablet_port = $sf_opts["spacer_tablet_port"];
                } else {
                    $height_tablet_port = 30;
                }
                if ( $sf_opts["spacer_mobile"] ) {
                    $height_mobile = $sf_opts["spacer_mobile"];
                } else {
                    $height_mobile = 20;
                }
            } else {
                if ( !isset($atts["height_tablet_land"]) || $atts["height_tablet_land"] == "" ) {
                    $height_tablet_land = $atts["height_tablet_land"] = $atts["height"];
                } else {
                    $height_tablet_land = $atts["height_tablet_land"];
                }
                if ( !isset($atts["height_tablet_port"]) || $atts["height_tablet_port"] == "" ) {
                    $height_tablet_port = $atts["height_tablet_port"] = $atts["height"];
                } else {
                    $height_tablet_port = $atts["height_tablet_port"];
                }
                if ( !isset($atts["height_mobile"]) || $atts["height_mobile"] == "" ) {
                    $height_mobile = $atts["height_mobile"] = $atts["height"];
                } else {
                    $height_mobile = $atts["height_mobile"];
                }
            }

            $output = '';
            if (strpos($height, 'px') == false) {
                $height = $height."px";
            }
            if (strpos($height_tablet_land, 'px') == false) {
                $height_tablet_land = $height_tablet_land."px";
            }
            if (strpos($height_tablet_port, 'px') == false) {
                $height_tablet_port = $height_tablet_port."px";
            }
            if (strpos($height_mobile, 'px') == false) {
                $height_mobile = $height_mobile."px";
            }
            $output .= '<div class="blank_spacer hidden-md hidden-sm hidden-xs ' . $width . ' ' . $el_class . '" style="height:' . $height . ';"></div>';
            $output .= '<div class="blank_spacer hidden-lg hidden-sm hidden-xs ' . $width . ' ' . $el_class . '" style="height:' . $height_tablet_land . ';"></div>';
            $output .= '<div class="blank_spacer hidden-lg hidden-md hidden-xs ' . $width . ' ' . $el_class . '" style="height:' . $height_tablet_port . ';"></div>';
            $output .= '<div class="blank_spacer hidden-lg hidden-md hidden-sm ' . $width . ' ' . $el_class . '" style="height:' . $height_mobile . ';"></div>';
            $output .= "\n\t" . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

            return $output;
        }
    }

    if ( $sf_opts["spacer_desktop"] ) {
        $desktop_spacer = $sf_opts["spacer_desktop"];
    } else {
        $desktop_spacer = 70;
    }

    if ( $sf_opts["spacer_tablet_land"] ) {
        $tablet_land_spacer = $sf_opts["spacer_tablet_land"];
    } else {
        $tablet_land_spacer = 50;
    }

    if ( $sf_opts["spacer_tablet_port"] ) {
        $tablet_port_spacer = $sf_opts["spacer_tablet_port"];
    } else {
        $tablet_port_spacer = 30;
    }

    if ( $sf_opts["spacer_mobile"] ) {
        $mobile_spacer = $sf_opts["spacer_mobile"];
    } else {
        $mobile_spacer = 30;
    }

    SPBMap::map( 'spb_blank_spacer', array(
        "name"   => __( "Blank Spacer", 'swift-framework-plugin' ),
        "base"   => "spb_blank_spacer",
        "class"  => "spb_blank_spacer spb_tab_layout",
        'icon'   => 'icon-blank-spacer',
        "params" => array(
            array(
                "type"        => "dropdown",
                "heading"     => __( "Spacer Scope", 'swift-framework-plugin' ),
                "param_name"  => "spacer_scope",
                "std"         => "global",
                "value"       => array(
                    __( 'Global', 'swift-framework-plugin' ) => "global",
                    __( 'Local', 'swift-framework-plugin' )  => "local"
                ),
                "description" => __( "Set the scope of the spacer. 'Local': allows you to override the global spacing options with your own for this particular spacer. 'Global': pulls from the site's general settings for desktop, tablet, and mobile.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "uislider",
                "heading"     => __( "Height on Desktop", 'swift-framework-plugin' ),
                "param_name"  => "height",
                "value"       => $desktop_spacer,
                "step"        => "1",
                "min"         => "0",
                "max"         => "300",
                "required"    => array("spacer_scope", "!=", "global"),
                "description" => __( "The height of the spacer on desktop. (px).", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "uislider",
                "heading"     => __( "Tablet Landscape", 'swift-framework-plugin' ),
                "param_name"  => "height_tablet_land",
                "value"       => $tablet_land_spacer,
                "step"        => "1",
                "min"         => "0",
                "max"         => "300",
                "required"    => array("spacer_scope", "!=", "global"),
                "description" => __( "The height of the spacer on landscape tablet. (px).", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "uislider",
                "heading"     => __( "Tablet Portrait", 'swift-framework-plugin' ),
                "param_name"  => "height_tablet_port",
                "value"       => $tablet_port_spacer,
                "step"        => "1",
                "min"         => "0",
                "max"         => "300",
                "required"    => array("spacer_scope", "!=", "global"),
                "description" => __( "The height of the spacer on portrait tablet. (px).", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "uislider",
                "heading"     => __( "Height on Mobile", 'swift-framework-plugin' ),
                "param_name"  => "height_mobile",
                "value"       => $mobile_spacer,
                "step"        => "1",
                "min"         => "0",
                "max"         => "300",
                "required"    => array("spacer_scope", "!=", "global"),
                "description" => __( "The height of the spacer on mobile. (px).", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Responsive Visiblity", 'swift-framework-plugin' ),
                "param_name"  => "responsive_vis",
                "holder"      => 'indicator',
                "value"       => spb_responsive_vis_list(),
                "description" => __( "Set the responsive visiblity for the row, if you would only like it to display on certain display sizes.", 'swift-framework-plugin' )
            )
        )
    ) );

