<?php

    /*
    *
    *	Swift Page Builder - Accordian Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_single_tab extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {
            
            $title = $el_class = $open = null;

            extract( shortcode_atts( array(
                'title' => __( "Section", 'swift-framework-plugin' ),
                'accordion_id'  => '',
                'icon'  => ''
            ), $atts ) );

            $output = '';
            
            global $accordion_id_number;

            $accordion_id_number++;

            $width = spb_translateColumnWidthToSpan( $width );

            $icon_output = "";

            if ( $icon ) {
                $icon_output = '<i class="' . $icon . '"></i>';
            }

            $accordion_id = '';
            
            if( $accordion_id == '' ){
                $accordion_id = preg_replace( '/\s+/', '-', sanitize_title($title) );
            }
            $accordion_id = $accordion_id . "-" . $accordion_id_number;

            $output .= "\n\t\t\t" . '<div id="' . $accordion_id . '" class="spb_accordion_section group">';
            $output .= "\n\t\t\t\t" . '<h4><a href="#'. $accordion_id .'"><span>' . $icon_output . '' . $title . '</span></a></h4>';
            //$output .= "\n\t\t\t\t" . '<div><div class="row-fluid">';
            $output .= "\n\t\t\t\t" . '<div class="row-fluid">';
            $output .= "\n\t\t\t\t" . spb_format_content( $content );
            $output .= "\n\t\t\t\t" . '</div>';
            //$output .= "\n\t\t\t\t" . '</div></div>';
            $output .= "\n\t\t\t" . '</div> ' . $this->endBlockComment( '.spb_accordion_section' ) . "\n";

            return $output;
        }

        public function contentAdmin( $atts, $content = null ) {
            $title    = '';
            $icon     = '';
            $accordion_id = '';
            
            $defaults = array( 'title' => __( 'Section', 'swift-framework-plugin' ), 'icon' => '', 'accordion_id' => '' );
            extract( shortcode_atts( $defaults, $atts ) );

           /* return '<div class="group">
            <h3 data-title-icon="' . $icon . '" accordion_id="' . $accordion_id . '"><a class="title-text" href="#">' . $title . '</a><a class="delete_tab"><span class="icon-delete"></span></a><a class="edit_tab"><span class="icon-edit"></span></a></h3>
            <div>
                <div class="row-fluid spb_column_container not-column-inherit">
                       ' . do_shortcode( $content ) . SwiftPageBuilder::getInstance()->getLayout()->getContainerHelper() . '
                    <div class="tabs_expanded_helper">
                        <a href="#" class="add_element"><span class="icon-add"></span>' . __( "Add Element", 'swift-framework-plugin' ) .'</a>
                        <a href="#" class="add_section"><span class="icon-add-tab"></span>' . __( "Add Section", 'swift-framework-plugin' ) .'</a>
                    </div>
          
                </div>
            </div>';*/


            return '<div class="group">
            
            <h3 data-title-icon="' . $icon . '" accordion_id="' . $accordion_id . '"><a class="title-text" href="#">' . $title . '</a><a class="delete_tab"><span class="icon-delete"></span></a><a class="edit_tab"><span class="icon-edit"></span></a></h3>
            <div>
                <div class="row-fluid spb_column_container spb_sortable_container not-column-inherit">
                       ' . do_shortcode( $content ) . '

                    
                    <div class="tabs_expanded_helper">
                      <a href="#" class="add_element"><span class="icon-add"></span>' . __( "Add Element", 'swift-framework-plugin' ) .'</a>
                      <a href="#" class="add_section"><span class="icon-add-tab"></span>' . __( "Add Section", 'swift-framework-plugin' ) .'</a>
                    </div>
                    
                    <div class="container-helper">
                         <a href="#" class="add-element-to-column btn-floating waves-effect waves-light"><span class="icon-add"></span></a>
                    </div>

                </div>
            </div>
        </div>';
        }
    }

    class SwiftPageBuilderShortcode_spb_tabs extends SwiftPageBuilderShortcode {

        public function __construct( $settings ) {
            parent::__construct( $settings );
            SwiftPageBuilder::getInstance()->addShortCode( array( 'base' => 'spb_single_tab' ) );
        }

        protected function content( $atts, $content = null ) {
            $widget_title = $type = $active_section = $interval = $width = $el_position = $el_class = '';
            //
            extract( shortcode_atts( array(
                'widget_title'   => '',
                'interval'       => 0,
                'active_section' => 0,
                'display_type'   => "top",
                'width'          => '1/1',
                'el_position'    => '',
                'el_class'       => ''
            ), $atts ) );
            $output = '';

            if ( $active_section == "" ) {
                $active_section = 0;
            }
            if ( $display_type == "" ) {
                $display_type = "top";
            }

            // Enqueue
            wp_enqueue_script( 'jquery-ui' );

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );

            $el_class .= " display-" . $display_type;

            $output .= "\n\t" . '<div class="spb_accordion spb_tabs spb_content_element ' . $width . $el_class . ' not-column-inherit" data-active="' . $active_section . '">'; //data-interval="'.$interval.'"
            $output .= "\n\t\t" . '<div class="spb_wrapper spb-asset-content spb_accordion_wrapper ">';
            $output .= ( $widget_title != '' ) ? "\n\t\t\t" . $this->spb_title( $widget_title, 'spb_accordion_heading' ) : '';
            $output .= "\n\t\t\t" . spb_format_content( $content );
            $output .= "\n\t\t" . '</div> ' . $this->endBlockComment( '.spb_wrapper' );
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );


            $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

            return $output;
        }

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
            //$elem = str_ireplace('%spb_element_content%', $iner, $elem);
            $tmp = '';
            if ( isset( $this->settings["custom_markup"] ) && $this->settings["custom_markup"] != '' ) {
                if ( $content != '' ) {
                    $custom_markup = str_ireplace( "%content%", $tmp . $content, $this->settings["custom_markup"] );
                } else if ( $content == '' && isset( $this->settings["default_content"] ) && $this->settings["default_content"] != '' ) {
                    $custom_markup = str_ireplace( "%content%", $this->settings["default_content"], $this->settings["custom_markup"] );
                }
                //$output .= do_shortcode($this->settings["custom_markup"]);
                $iner .= do_shortcode( $custom_markup );
            }
            $elem   = str_ireplace( '%spb_element_content%', $iner, $elem );
            $output = $elem;

            return $output;
        }
    }

    SPBMap::map( 'spb_tabs', array(
        "name"            => __( "Tabs", 'swift-framework-plugin' ),
        "base"            => "spb_tabs",
        "controls"        => "full",
        "class"           => "spb_tabs spb_tab_ui",
        "icon"            => "icon-tabs",
        "params"          => array(
            array(
                "type"        => "textfield",
                "heading"     => __( "Widget title", 'swift-framework-plugin' ),
                "param_name"  => "widget_title",
                "value"       => "",
                "description" => __( "What text use as widget title. Leave blank if no title is needed.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Title icon", 'swift-framework-plugin' ),
                "param_name"  => "icon",
                "value"       => "",
                "description" => __( "Icon to the left of the title text. This is the class name for the icon, e.g. fa-cloud", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Active Section", 'swift-framework-plugin' ),
                "param_name"  => "active_section",
                "value"       => "0",
                "description" => __( "You can set the section that is active here by entering the number of the section here. NOTE: The first section would be 0, second would be 1, and so on. By default, the first section will be open.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Display Type", 'swift-framework-plugin' ),
                "param_name"  => "display_type",
                "value"       => array(
                        __( 'Top Horizontal', 'swift-framework-plugin' )  => "top",
                        __( 'Vertical Sidebar', 'swift-framework-plugin' )  => "sidebar",
                    ),
                "std"         => "top",
                "description" => __( "Select the display type for the feed.", 'swift-framework-plugin' )
            ),
            array(
                "type"       => "section_tab",
                "param_name" => "design_options_tab",
                "heading"    => __( "Design", 'swift-framework-plugin' ),
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Extra class", 'swift-framework-plugin' ),
                "param_name"  => "el_class",
                "value"       => "",
                "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin' )
            )
        ),
        "custom_markup"   => '
            
        <div class="spb_accordion_holder clearfix">
            %content%
        </div>',
        'default_content' => '
        <div class="group">
            <h3><a class="title-text" href="#section-1"  id="section-1">' . __( 'Section 1', 'swift-framework-plugin' ) . '</a><a class="delete_tab"><span class="icon-delete"></span></a><a class="edit_tab"><span class="icon-edit"></span></a></h3>
            <div>
                <div class="row-fluid spb_column_container spb_sortable_container not-column-inherit">

                    [spb_text_block width="1/1"] ' . __( 'This is a text block. Click the edit button to change this text.', 'swift-framework-plugin' ) . ' [/spb_text_block]
                    
                    <div class="tabs_expanded_helper">
                      <a href="#" class="add_element"><span class="icon-add"></span>' . __( "Add Element", 'swift-framework-plugin' ) .'</a>
                      <a href="#" class="add_section"><span class="icon-add-tab"></span>' . __( "Add Section", 'swift-framework-plugin' ) .'</a>
                    </div>
                    
                    <div class="container-helper">
                         <a href="#" class="add-element-to-column btn-floating waves-effect waves-light"><span class="icon-add"></span></a>
                    </div>

                </div>
            </div>
        </div>
        <div class="group">
            <h3><a class="title-text" href="#section-2" id="section-2">' . __( 'Section 2', 'swift-framework-plugin' ) . '</a><a class="delete_tab"><span class="icon-delete"></span></a><a class="edit_tab"><span class="icon-edit"></span></a></h3>
            <div>
                <div class="row-fluid spb_column_container spb_sortable_container not-column-inherit">
                    
                    [spb_text_block width="1/1"] ' . __( 'This is a text block. Click the edit button to change this text.', 'swift-framework-plugin' ) . ' [/spb_text_block]
                    
                    <div class="tabs_expanded_helper">
                      <a href="#" class="add_element"><span class="icon-add"></span>' . __( "Add Element", 'swift-framework-plugin' ) .'</a>
                      <a href="#" class="add_section"><span class="icon-add-tab"></span>' . __( "Add Section", 'swift-framework-plugin' ) .'</a>
                    </div>
                    
                    <div class="container-helper">
                         <a href="#" class="add-element-to-column btn-floating waves-effect waves-light"><span class="icon-add"></span></a>
                    </div>

                </div>
            </div>
        </div>',
        "js_callback"     => array(
            "init"      => "spbAccordionInitCallBack",
            "shortcode" => "spbAccordionGenerateShortcodeCallBack"
        )
    ) );
