<?php

    /*
    *
    *	Swift Page Builder - Table Shortcodes
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
    *
    */

    /* TABLEPRESS
    ================================================== */

    if ( function_exists("is_plugin_active") && is_plugin_active("tablepress/tablepress.php") ) {

        class SwiftPageBuilderShortcode_spb_tablepress extends SwiftPageBuilderShortcode {

            public function content( $atts, $content = null ) {

                $tablepress = $table_id = $el_class = $width = $el_position = $inline_style = '';

                extract( shortcode_atts( array(
                    'tablepress'         => '',
                    'el_class'           => '',
                    'el_position'        => '',
                    'width'              => '1/2'
                ), $atts ) );

                $output = '';

                if ( $tablepress != "" && FALSE != get_post_status( $tablepress ) && get_post_status( $tablepress ) == "publish" ) {
                    $table = get_post( $tablepress );
                    $tablepress_options = json_decode(get_option('tablepress_tables'));
                    foreach($tablepress_options->table_post as $key => $value) {
                        if($value == $table->ID) {
                            $table_id = $key;
                        }
                    }

                    if ( isset($table_id) && $table_id && $table_id != "" ) {

                        $el_class = $this->getExtraClass( $el_class );
                        $width    = spb_translateColumnWidthToSpan( $width );

                        $el_class .= ' spb_tablepress';

                        $output .= "\n\t" . '<div class="spb_content_element ' . $width . $el_class . '">';
                        $output .= "\n\t\t" . '<div class="spb-asset-content">';
                        $output .= "\n\t\t\t" . do_shortcode('[table id='.$table_id.' /]');
                        $output .= "\n\t\t" . '</div>';
                        $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );
                        
                        $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

                    }
                }

                return $output;
            }
        }

        $tablepresss = array( __( "Blank", 'swift-framework-plugin' ) => "" );

        $args = array('post_type' => 'tablepress_table', 'post_status' => 'publish', 'posts_per_page' => -1);
        if( $tables = get_posts( $args ) ){
            foreach($tables as $table){
                $tablepresss = array_merge($tablepresss, array(
                        __( $table->post_title, 'swift-framework-plugin' ) => $table->ID
                    ));
            }
        }

        SPBMap::map( 'spb_tablepress', array(
                "name"          => __( "TablePress", 'swift-framework-plugin' ),
                "base"          => "spb_tablepress",
                "class"         => "spb_tab_data",
                "icon"          => "icon-icon-grid",
                "wrapper_class" => "clearfix",
                "controls"      => "full",
                "params"        => array(
                    array(
                        "type"        => "dropdown",
                        "heading"     => __( "Table", 'swift-framework-plugin' ),
                        "param_name"  => "tablepress",
                        "value"       => $tablepresss,
                        "description" => __( "Select the table you would like to display.", 'swift-framework-plugin' )
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

