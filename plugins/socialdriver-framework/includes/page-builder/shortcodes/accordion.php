<?php

    /*
    *
    *	Swift Page Builder - Accordian Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_accordion_tab extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            global $accordion_id_number;

            $accordion_id_number++;

            $title = '';
            $icon  = '';
            $accordion_id = '';

            extract( shortcode_atts( array(
                'title' => __( "Section", 'swift-framework-plugin' ),
				'accordion_id'  => '',
                'icon'  => ''
            ), $atts ) );

            if( $accordion_id == '' ){
                $accordion_id = preg_replace( '/\s+/', '-', sanitize_title($title) );
            }

            $accordion_id = $accordion_id . "-" . $accordion_id_number;

            $output = '';

            $icon_output = "";

            if ( $icon ) {
                $icon_output = '<i class="' . $icon . '"></i>';
            }

            $output .= "\n\t\t\t" . '<div class="spb_accordion_section group">';
            $output .= "\n\t\t\t\t" . '<h4 class="ui-accordion-header ui-state-default ui-accordion-icons ui-corner-all"><button id="'. $accordion_id .'" aria-controls="'. $accordion_id .'-content" aria-expanded="false"><span>' . $icon_output . '' . $title . '</span></button></h4>';
            $output .= "\n\t\t\t\t" . '<div id="'. $accordion_id .'-content" aria-labelledby="'. $accordion_id .'" role="region" class="row-fluid ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom">';
            $output .= "\n\t\t\t\t" . spb_format_content( $content );
            $output .= "\n\t\t\t\t" . '</div>';
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

    class SwiftPageBuilderShortcode_spb_accordion extends SwiftPageBuilderShortcode {

        public function __construct( $settings ) {
            parent::__construct( $settings );
            SwiftPageBuilder::getInstance()->addShortCode( array( 'base' => 'spb_accordion_tab' ) );
        }

        protected function content( $atts, $content = null ) {
            $widget_title = $type = $active_section = $interval = $width = $el_position = $el_class = '';
            //
            extract( shortcode_atts( array(
                'widget_title'   => '',
                'interval'       => 0,
                'active_section' => '',
                'width'          => '1/1',
                'el_position'    => '',
                'el_class'       => ''
            ), $atts ) );
            $output = '';

            // Enqueue
            wp_enqueue_script( 'jquery-ui' );

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );

            $output .= "\n\t" . '<div class="spb_accordion spb_content_element ' . $width . $el_class . ' not-column-inherit" data-active="' . $active_section . '">'; //data-interval="'.$interval.'"
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

    SPBMap::map( 'spb_accordion', array(
        "name"            => __( "Accordion", 'swift-framework-plugin' ),
        "base"            => "spb_accordion",
        "controls"        => "full",
        "class"           => "spb_accordion spb_tab_ui",
        "icon"            => "icon-accordion",
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
                "value"       => "",
                "description" => __( "You can set the section that is active here by entering the number of the section here. NOTE: The first section would be 0, second would be 1, and so on. Leave blank for all sections to be closed by default.", 'swift-framework-plugin' )
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


    /* TOGGLE ASSET
    ================================================== */

    class SwiftPageBuilderShortcode_spb_toggle extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            $title = $el_class = $open = null;

            extract( shortcode_atts( array(
                'title'       => __( "Click to toggle", 'swift-framework-plugin' ),
                'icon'        => '',
                'el_class'    => '',
                'open'        => 'false',
                'el_position' => '',
                'width'       => '1/1'
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

            $el_class = $this->getExtraClass( $el_class );
            $open     = ( $open == 'true' ) ? ' spb_toggle_title_active' : '';
            $el_class .= ( $open == ' spb_toggle_title_active' ) ? ' spb_toggle_open' : '';
            $output .= '<div class="toggle-wrap ' . $width . '">';
            $output .= '<h4 class="spb_toggle' . $open . '"><button id="'. $accordion_id .'" aria-controls="'. $accordion_id .'-content" aria-expanded="false"><span>' . $icon_output . '' . $title . '</span></button></h4><div class="spb_toggle_content' . $el_class . '" id="'. $accordion_id .'-content" aria-labelledby="'. $accordion_id .'" role="region">' . spb_format_content( $content ) . '</div>' . $this->endBlockComment( 'toggle' ) . "\n";
            $output .= '</div>';
            $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

            return $output;
        }
    }

    SPBMap::map( 'spb_toggle', array(
        "name"   => __( "Toggle", 'swift-framework-plugin' ),
        "base"   => "spb_toggle",
        "class"  => "spb_faq spb_tab_ui",
        "icon"   => "icon-toggle",
        "params" => array(
            array(
                "type"        => "textfield",
                "class"       => "toggle_title",
                "heading"     => __( "Widget title", 'swift-framework-plugin' ),
                "param_name"  => "title",
                "value"       => __( "Toggle title", 'swift-framework-plugin' ),
                "description" => __( "Toggle block title.", 'swift-framework-plugin' )
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
                "class"       => "toggle_content",
                "heading"     => __( "Text", 'swift-framework-plugin' ),
                "param_name"  => "content",
                "value"       => __( "<p>The toggle content goes here, click the edit button to change this text.</p>", 'swift-framework-plugin' ),
                "description" => __( "Toggle block content.", 'swift-framework-plugin' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Default state", 'swift-framework-plugin' ),
                "param_name"  => "open",
                "value"       => array(
                    __( "Closed", 'swift-framework-plugin' ) => "false",
                    __( "Open", 'swift-framework-plugin' )   => "true"
                ),
                "description" => __( "Select this if you want toggle to be open by default.", 'swift-framework-plugin' )
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
        )
    ) );
