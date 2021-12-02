<?php

    /*
    *
    *	Swift Page Builder - Code-Snipper Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2016 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_codesnippet extends SwiftPageBuilderShortcode {

        public function content( $atts, $content = null ) {

            $title = $el_class = $width = $el_position = '';

            extract( shortcode_atts( array(
                'title'       => '',
                'icon'        => '',
                'language'	  => '',
                'el_class'    => '',
                'el_position' => '',
                'width'       => '1'
            ), $atts ) );

            wp_enqueue_script( 'prism' );

            $output = '';

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );

            $icon_output = "";

            if ( $icon ) {
                $icon_output = '<i class="' . $icon . '"></i>';
            }

            $output .= "\n\t" . '<div class="spb_codesnippet_element ' . $width . $el_class . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content">';
            if ( $icon_output != "" ) {
                $output .= ( $title != '' ) ? "\n\t\t\t" . '<div class="title-wrap"><h3 class="spb-heading spb-icon-heading"><span>' . $icon_output . '' . $title . '</span></h3></div>' : '';
            } else {
                $output .= ( $title != '' ) ? "\n\t\t\t" . $this->spb_title( $title, 'spb-text-heading' ) : '';
            }
            $content = str_replace( '”', '&quot;', $content);
            $content = str_replace( '&#8220;', '&quot;', $content);
            $content = str_replace( '&#8221;', '&quot;', $content);
            $content = spb_format_content( $content );
            $output .= "\n\t\t" . '<pre><code class="code-block language-'.$language.'">' . $content . '</code></pre>';
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            //
            $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

            return $output;
        }
    }

    SPBMap::map( 'spb_codesnippet', array(
        "name"   => __( "Code Snippet", 'swift-framework-plugin' ),
        "base"   => "spb_codesnippet",
        "class"  => "spb_codesnippet spb_tab_media",
        "icon"   => "icon-code-snippet",
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
                "value"       => __( "<p>Add your code snippet here.</p>", 'swift-framework-plugin' ),
                "description" => __( "Enter your code snippet.", 'swift-framework-plugin' )
            ),
            array(
                "type"       => "dropdown",
                "heading"    => __( "Language", 'swift-framework-plugin' ),
                "param_name" => "language",
                "value"      => array(
                	'HTML' => 'markup',
                    'CSS' => 'css',
                    'PHP' => 'php',
                    'JavaScript' => 'javascript',
                )
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
