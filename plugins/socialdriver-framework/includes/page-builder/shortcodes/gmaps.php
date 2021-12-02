<?php

    /*
    *
    *   Swift Page Builder - Google Maps Shortcodes
    *   ------------------------------------------------
    *   Swift Framework
    *   Copyright Swift Ideas 2015 - http://www.swiftideas.com
    *
    */


    /* GOOGLE MAPS ASSET
    ================================================== */

    class SwiftPageBuilderShortcode_spb_gmaps extends SwiftPageBuilderShortcode {

        public function contentAdmin( $atts, $content = null ) {
            $width                = $custom_markup = '';
            $shortcode_attributes = array( 'width' => '1/1' );
            foreach ( $this->settings['params'] as $param ) {
                if ( $param['param_name'] != 'content' ) {

                    if ( is_string( $param['value'] ) ) {
                        $shortcode_attributes[ $param['param_name'] ] = __( $param['value'], 'swift-framework-plugin' );
                    } else {
                        $shortcode_attributes[ $param['param_name'] ] = $param['value'];
                    }
                } else if ( $param['param_name'] == 'content' && $content == null ) {
                    $content = __( $param['value'], 'swift-framework-plugin' );
                }
            }
            extract( shortcode_atts(
                $shortcode_attributes
                , $atts ) );

            $output = '';

            $tmp = '';

            $output .= do_shortcode( $content );
            $elem = $this->getElementHolder( $width );

            $iner = '';
            foreach ( $this->settings['params'] as $param ) {
                $custom_markup = '';
                $param_value   = isset( ${$param['param_name']} ) ? ${$param['param_name']} : null;

                if ( is_array( $param_value ) ) {
                    // Get first element from the array
                    reset( $param_value );
                    $first_key   = key( $param_value );
                    $param_value = $param_value[ $first_key ];
                }
                $iner .= $this->singleParamHtmlHolder( $param, $param_value );
            }


            if ( isset( $this->settings["custom_markup"] ) && $this->settings["custom_markup"] != '' ) {
                if ( $content != '' ) {
                    $custom_markup = str_ireplace( "%content%", $tmp . $content, $this->settings["custom_markup"] );
                } else if ( $content == '' && isset( $this->settings["default_content"] ) && $this->settings["default_content"] != '' ) {
                    $custom_markup = str_ireplace( "%content%", $this->settings["default_content"], $this->settings["custom_markup"] );
                }

                $iner .= do_shortcode( $custom_markup );
            }
            $elem   = str_ireplace( '%spb_element_content%', $iner, $elem );
            $output = $elem;

            return $output;
        }

        public function content( $atts, $content = null ) {

            $address = $size = $zoom = $color = $saturation = $map_center_latitude = $map_center_longitude = $pin_image = $type = $el_position = $width = $el_class = '';
            extract( shortcode_atts( array(
                'size'                 => 200,
                'zoom'                 => 14,
                'map_center_latitude'  => '',
                'map_center_longitude' => '',
                'map_controls'         => '',
                'advanced_styling'     => '',
                'style_array'          => '',
                'color'                => '',
                'saturation'           => '',
                'type'                 => 'm',
                'fullscreen'           => 'no',
                'el_position'          => '',
                'width'                => '1/1',
                'el_class'             => ''
            ), $atts ) );
            $output = '';

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );
            $size     = str_replace( array( 'px', ' ' ), array( '', '' ), $size );
            if ( $fullscreen == "yes" && $width == "col-sm-12" ) {
                $fullscreen = true;
            } else {
                $fullscreen = false;
            }


            if ( $fullscreen ) {
                $output .= "\n\t" . '<div class="spb_gmaps_widget fullscreen-map spb_content_element ' . $width . $el_class . '">';
            } else {
                $output .= "\n\t" . '<div class="spb_gmaps_widget spb_content_element ' . $width . $el_class . '">';
            }
            $output .= "\n\t\t" . '<div class="spb-asset-content">';
            $output .= '<div class="spb_map_wrapper">';

            if ( $advanced_styling == "yes" ) {
                $output .= '<div class="map-styles-array">';
                $output .= rawurldecode( base64_decode( strip_tags( $style_array ) ) );
                $output .= '</div>';
            }

            $output .= '<div class="map-canvas" style="width:100%;height:' . $size . 'px;" data-center-lat="' . $map_center_latitude . '" data-center-lng="' . $map_center_longitude . '" data-zoom="' . $zoom . '" data-controls="'.$map_controls.'" data-maptype="' . $type . '" data-mapcolor="' . $color . '" data-mapsaturation="' . $saturation . '"></div>';

            $output .= "\n\t\t\t" . do_shortcode( $content );
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            if ( $fullscreen ) {
                $output = $this->startRow( $el_position, '', true ) . $output . $this->endRow( $el_position, '', true );
            } else {
                $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );
            }
            global $sf_include_maps;
            $sf_include_maps = true;

            return $output;
        }

    }

    /* PARAMS
    ================================================== */
    $params = array(
        array(
            "type"        => "textfield",
            "heading"     => __( "Map Height", 'swift-framework-plugin' ),
            "param_name"  => "size",
            "value"       => "300",
            "description" => __( 'Enter map height in pixels. Example: 300.', 'swift-framework-plugin' )
        ),
        array(
            "type"        => "dropdown",
            "heading"     => __( "Map Type", 'swift-framework-plugin' ),
            "param_name"  => "type",
            "value"       => array(
                __( "Map", 'swift-framework-plugin' )       => "roadmap",
                __( "Satellite", 'swift-framework-plugin' ) => "satellite",
                __( "Hybrid", 'swift-framework-plugin' )    => "hybrid",
                __( "Terrain", 'swift-framework-plugin' )   => "terrain"
            ),
            "description" => __( "Select map display type. NOTE, if you set a color below, then only the standard Map type will show.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "textfield",
            "heading"     => __( "Map Center Latitude Coordinate", 'swift-framework-plugin' ),
            "param_name"  => "map_center_latitude",
            "value"       => "",
            "description" => __( "Enter the Latitude coordinate of the center of the map.", 'swift-framework-plugin' )
        ),
        array(
            "type"        => "textfield",
            "heading"     => __( "Map Center Longitude Coordinate", 'swift-framework-plugin' ),
            "param_name"  => "map_center_longitude",
            "value"       => "",
            "description" => __( "Enter the Longitude coordinate of the center of the map.", 'swift-framework-plugin' )
        ),
        array(
            "type"       => "dropdown",
            "heading"    => __( "Map Zoom", 'swift-framework-plugin' ),
            "param_name" => "zoom",
            "value"      => array(
                __( "14 - Default", 'swift-framework-plugin' ) => 14,
                1,
                2,
                3,
                4,
                5,
                6,
                7,
                8,
                9,
                10,
                11,
                12,
                13,
                15,
                16,
                17,
                18,
                19,
                20
            )
        )
    );

    if ( sf_theme_supports( 'advanced-map-styles' ) ) {
        $params[] = array(
                        "type"        => "buttonset",
                        "heading"     => __( "Show Controls", 'swift-framework-plugin' ),
                        "param_name"  => "map_controls",
                        "value"       => array(
                            __( "Yes", 'swift-framework-plugin' ) => "yes",
                            __( "No", 'swift-framework-plugin' )        => "no",
                        ),
                        "description" => __( "Set whether you would like to show the default Google Maps controls UI.", 'swift-framework-plugin' )
                    );
        $params[] = array(
                        "type"        => "buttonset",
                        "heading"     => __( "Advanced Styling", 'swift-framework-plugin' ),
                        "param_name"  => "advanced_styling",
                        "value"       => array(
                            __( "No", 'swift-framework-plugin' )        => "no",
                            __( "Yes", 'swift-framework-plugin' ) => "yes",
                        ),
                        "description" => __( "Set whether you would like to use the advanced map styling option.", 'swift-framework-plugin' )
                    );
        $params[] = array(
                        "type"        => "textarea_encoded",
                        "heading"     => __( "Google Map Style Array", 'swift-framework-plugin' ),
                        "param_name"  => "style_array",
                        "value"       => "",
                        "required"       => array("advanced_styling", "=", "yes"),
                        "description" => __( "Enter the style array for the google map here. You can find examples of these <a href='https://snazzymaps.com' target='_blank'>here</a>.", 'swift-framework-plugin' )
                    );
    }

    $params[] = array(
                    "type"        => "colorpicker",
                    "heading"     => __( "Map Color", 'swift-framework-plugin' ),
                    "param_name"  => "color",
                    "value"       => "",
                    "required"    => array("advanced_styling", "=", "no"),
                    "description" => __( 'If you would like, you can enter a hex color here to style the map by changing the hue.', 'swift-framework-plugin' )
                );
    $params[] = array(
                    "type"        => "dropdown",
                    "heading"     => __( "Map Saturation", 'swift-framework-plugin' ),
                    "param_name"  => "saturation",
                    "value"       => array(
                        __( "Color", 'swift-framework-plugin' )        => "color",
                        __( "Mono (Light)", 'swift-framework-plugin' ) => "mono-light",
                        __( "Mono (Dark)", 'swift-framework-plugin' )  => "mono-dark"
                    ),
                    "required"    => array("advanced_styling", "=", "no"),
                    "description" => __( "Set whether you would like the map to be in color or mono (black/white).", 'swift-framework-plugin' )
                );
    $params[] = array(
                    "type"        => "buttonset",
                    "heading"     => __( "Fullscreen Display", 'swift-framework-plugin' ),
                    "param_name"  => "fullscreen",
                    "value"       => array(
                        __( "No", 'swift-framework-plugin' )  => "no",
                        __( "Yes", 'swift-framework-plugin' ) => "yes"
                    ),
                    "description" => __( "If yes, the map will be displayed from screen edge to edge.", 'swift-framework-plugin' )
                );
    $params[] = array(
                    "type"        => "textfield",
                    "heading"     => __( "Extra class", 'swift-framework-plugin' ),
                    "param_name"  => "el_class",
                    "value"       => "",
                    "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin' )
                );


    /* SHORTCODE MAP
    ================================================== */
    SPBMap::map( 'spb_gmaps', array(
        "name"            => __( "Google Map", 'swift-framework-plugin' ),
        "base"            => "spb_gmaps",
        "controls"        => "full",
        "class"           => "spb_gmaps spb_tab_data",
        "icon"            => "icon-map",
        //"wrapper_class" => "clearfix",
        "params"          => $params,
        "custom_markup"   => '
            <div class="tab_controls">
                <button class="add_tab">' . __( "Add New Pin", 'swift-framework-plugin' ) . '</button>
            </div>

            <div class="spb_tabs_holder">
                %content%
            </div>',
            'default_content' => '

                    [spb_map_pin pin_title="' . __( "First Pin", 'swift-framework-plugin' ) . '" width="1/1"]' . __( 'This is a map pin. Click the edit button to change it.', 'swift-framework-plugin' ) . '[/spb_map_pin]',
        "js_callback"     => array( "init" => "spbTabsInitCallBack" )
        )
    );


    /* MAP PIN ASSET
    ================================================== */

    class SwiftPageBuilderShortcode_spb_map_pin extends SwiftPageBuilderShortcode {


        public function contentAdmin( $atts, $content ) {
            $width                = $custom_markup = '';
            $shortcode_attributes = array( 'width' => '1/1' );
            foreach ( $this->settings['params'] as $param ) {
                if ( $param['param_name'] != 'content' ) {
                    if ( is_string( $param['value'] ) ) {
                        $shortcode_attributes[ $param['param_name'] ] = __( $param['value'], 'swift-framework-plugin' );
                    } else {
                        $shortcode_attributes[ $param['param_name'] ] = $param['value'];
                    }
                } else if ( $param['param_name'] == 'content' && $content == null ) {
                    $content = __( $param['value'], 'swift-framework-plugin' );
                }
            }
            extract( shortcode_atts(
                $shortcode_attributes
                , $atts ) );

            $output = '';

            $elem = $this->getElementHolder( $width );

            $iner = '';
            foreach ( $this->settings['params'] as $param ) {
                $param_value = isset( ${$param['param_name']} ) ? ${$param['param_name']} : null;

                if ( is_array( $param_value ) ) {
                    // Get first element from the array
                    reset( $param_value );
                    $first_key   = key( $param_value );
                    $param_value = $param_value[ $first_key ];
                }
                $iner .= $this->singleParamHtmlHolder( $param, $param_value );
            }

            $tmp = '';
            if ( isset( $this->settings["custom_markup"] ) && $this->settings["custom_markup"] != '' ) {
                if ( $content != '' ) {
                    $custom_markup = "";
                } else if ( $content == '' && isset( $this->settings["default_content"] ) && $this->settings["default_content"] != '' ) {

                    $custom_markup = "";
                }

                $iner .= do_shortcode( $custom_markup );
            }
            $elem   = str_ireplace( '%spb_element_content%', $iner, $elem );
            $output = $elem;
            $output = '<div class="row-fluid spb_column_container map_pin_wrapper not-column-inherit not-sortable">' . $output . '</div>';

            return $output;
        }


        protected function content( $atts, $content = null ) {

            $pin_title = $el_class = $address = $width = $el_position = $pin_image = $pin_link = $pin_latitude = $pin_longitude = $inline_style = '';

            extract( shortcode_atts( array(

                'pin_title'       => '',
                'icon'            => '',
                'el_class'        => '',
                'address'         => '',
                'pin_image'       => '',
                'pin_link'        => '',
                'pin_latitude'    => '',
                'pin_longitude'   => '',
                'el_position'     => '',
                'width'           => '1/1',
                'pin_id'          => ''
            ), $atts ) );

            $output = '';


            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );
            $el_class .= ' spb_map_pin';
            $img_url = wp_get_attachment_image_src( $pin_image, 'full' );

            $output = '<div class="pin_location" data-title="' . $pin_title . '" data-pinlink="' . $pin_link . '" data-pinimage="' . $img_url[0] . '"  data-address="' . $address . '"  data-content="' . strip_tags( $content ) . '" data-lat="' . $pin_latitude . '" data-lng="' . $pin_longitude . '" ></div>';

            return $output;
        }
    }

    SPBMap::map( 'spb_map_pin', array(
            "name"     => __( "Map Pin", 'swift-framework-plugin' ),
            "base"     => "spb_map_pin",
            "class"    => "",
            "icon"     => "spb-icon-map-pin",
            "controls" => "delete_edit",
            "params"   => array(
                array(
                    "type"        => "textfield",
                    "holder"      => "div",
                    "heading"     => __( "Title", 'swift-framework-plugin' ),
                    "param_name"  => "pin_title",
                    "value"       => "",
                    "description" => __( "Heading text. Leave it empty if not needed.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "textfield",
                    "holder"      => "div",
                    "heading"     => __( "Address", 'swift-framework-plugin' ),
                    "param_name"  => "address",
                    "value"       => __( 'Click the edit button to change the map pin details.', 'swift-framework-plugin' ),
                    "description" => __( 'Enter the address that you would like to show on the map here, i.e. "Cupertino".', 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "textfield",
                    "heading"     => __( "Latitude Coordinate", 'swift-framework-plugin' ),
                    "param_name"  => "pin_latitude",
                    "value"       => "",
                    "description" => __( "Enter the Latitude coordinate of the location marker.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "textfield",
                    "heading"     => __( "Longitude Coordinate", 'swift-framework-plugin' ),
                    "param_name"  => "pin_longitude",
                    "value"       => "",
                    "description" => __( "Enter the Longitude coordinate of the location marker.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "attach_image",
                    "heading"     => __( "Custom Map Pin", 'swift-framework-plugin' ),
                    "param_name"  => "pin_image",
                    "value"       => "",
                    "description" => "Choose an image to use as the custom pin for the address on the map. Upload your custom map pin, the image size must be 150px x 75px."
                ),
                array(
                    "type"        => "textfield",
                    "heading"     => __( "Pin Link", 'swift-framework-plugin' ),
                    "param_name"  => "pin_link",
                    "value"       => "",
                    "description" => __( "Enter the Link url of the location marker.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "textarea_html",
                    "holder"      => "div",
                    "class"       => "hide-shortcode",
                    "heading"     => __( "Text", 'swift-framework-plugin' ),
                    "param_name"  => "content",
                    "value"       => __( "Click the edit button to change the map pin detail text.", 'swift-framework-plugin' ),
                    "description" => __( "Enter your content.", 'swift-framework-plugin' )
                )
            )
        )
    );
