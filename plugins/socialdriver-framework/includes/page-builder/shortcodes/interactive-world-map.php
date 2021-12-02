<?php

    /*
    *
    *	Swift Page Builder - Interactive World Map Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
    *
    */

    if ( function_exists("is_plugin_active") && is_plugin_active("interactive-world-maps/map.php") ) {

        class SwiftPageBuilderShortcode_spb_interactive_map extends SwiftPageBuilderShortcode {

            public function content( $atts, $content = null ) {

                $interactive_map = $table_id = $el_class = $width = $el_position = $inline_style = '';

                extract( shortcode_atts( array(
                    'interactive_map'         => '',
                    'el_class'                => '',
                    'el_position'             => '',
                    'width'                   => '1/2'
                ), $atts ) );

                $output = '';

                global $wpdb;
                $map = $wpdb->get_results( "SELECT id FROM " . $wpdb->prefix . "i_world_map WHERE id = " . $interactive_map . " LIMIT 1", OBJECT );

                if ( isset($map) && $map && $map != "" && count($map) > 0 ) {

                    $el_class = $this->getExtraClass( $el_class );
                    $width    = spb_translateColumnWidthToSpan( $width );

                    $el_class .= ' spb_interative_map';

                    $output .= "\n\t" . '<div class="spb_content_element ' . $width . $el_class . '">';
                    $output .= "\n\t\t" . '<div class="spb-asset-content">';
                    $output .= "\n\t\t\t" . do_shortcode("[show-map id='".($map[0]->id)."']");
                    $output .= "\n\t\t" . '</div>';
                    $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );
                    
                    $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

                }

                return $output;
            }
        }

        $interactive_map = array();

        global $wpdb;
        $maps = $wpdb->get_results( "SELECT id, name FROM " . $wpdb->prefix . "i_world_map", OBJECT );

        if( isset($maps) && count($maps) > 0 ){
            foreach($maps as $map){
                $interactive_map = array_merge($interactive_map, array(
                        __( $map->name, 'swift-framework-plugin' ) => $map->id
                    ));
            }
        }

        SPBMap::map( 'spb_interactive_map', array(
                "name"          => __( "Interactive Map", 'swift-framework-plugin' ),
                "base"          => "spb_interactive_map",
                "class"         => "spb_tab_data",
                "icon"          => "icon-directory-map",
                "wrapper_class" => "clearfix",
                "controls"      => "full",
                "params"        => array(
                    array(
                        "type"        => "dropdown",
                        "heading"     => __( "Map", 'swift-framework-plugin' ),
                        "param_name"  => "interactive_map",
                        "value"       => $interactive_map,
                        "description" => __( "Select the map you would like to display.", 'swift-framework-plugin' )
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