<?php

    /*
    *
    *	Swift Page Builder - Contact Form Shortcodes
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
    *
    */

    /* CONTACT FORM
    ================================================== */

    if ( function_exists("is_plugin_active") && is_plugin_active("contact-form-7/wp-contact-form-7.php") ) {

        class SwiftPageBuilderShortcode_spb_contact_form extends SwiftPageBuilderShortcode {

            public function content( $atts, $content = null ) {

                $contact_form = $el_class = $width = $el_position = $inline_style = '';

                extract( shortcode_atts( array(
                    'contact_form'       => '',
                    'el_class'           => '',
                    'el_position'        => '',
                    'width'              => '1/2'
                ), $atts ) );

                $output = '';

                $el_class = $this->getExtraClass( $el_class );
                $width    = spb_translateColumnWidthToSpan( $width );

                $el_class .= ' spb_contact_form';

                if( $cf7Form = get_post( $contact_form ) ){
                    $output .= "\n\t" . '<div class="spb_content_element ' . $width . $el_class . '">';
                    $output .= "\n\t\t" . '<div class="spb-asset-content">';
                    $output .= "\n\t\t\t" . do_shortcode('[contact-form-7 id="'.$cf7Form->ID.'" title="'.$cf7Form->post_title.'"]');
                    $output .= "\n\t\t" . '</div>';
                    $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );
                }

                $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

                return $output;
            }
        }

        $contact_forms = array();

        $args = array('post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1);
        if( $cf7Forms = get_posts( $args ) ){
            foreach($cf7Forms as $cf7Form){
                $contact_forms = array_merge($contact_forms, array(
                        __( $cf7Form->post_title, 'swift-framework-plugin' ) => $cf7Form->ID
                    ));
            }
        }

        SPBMap::map( 'spb_contact_form', array(
                "name"          => __( "Contact Form 7", 'swift-framework-plugin' ),
                "base"          => "spb_contact_form",
                "class"         => "spb_tab_ui",
                "icon"          => "icon-spb",
                "wrapper_class" => "clearfix",
                "controls"      => "full",
                "params"        => array(
                    array(
                        "type"        => "dropdown",
                        "heading"     => __( "Contact Form", 'swift-framework-plugin' ),
                        "param_name"  => "contact_form",
                        "value"       => $contact_forms,
                        "description" => __( "Select the contact form you would like to display.", 'swift-framework-plugin' )
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
            )
        );

    }

