<?php

    /*
    *
    *	Swift Page Builder - Search Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_search extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            $width = $input_class = $el_class = $output = $search_form = $el_position = '';

            extract( shortcode_atts( array(
                'el_position'       => '',
                'search_input_text' => '',
                'input_size'        => 'large',
                'width'             => '1/1',
                'twitter_username'  => '',
                'el_class'          => ''
            ), $atts ) );

            $input_class = 'input-large';

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );

            $search_form .= '<form method="get" class="search-form search-widget" action="' . get_site_url() . '">';
            $search_form .= '<label class="accessibility-text" for="s">Search this site</label>';
            $search_form .= '<input type="text" placeholder="' . $search_input_text . '" name="s" class="' . $input_class . '"';
            if ( isset($_GET["s"]) && !empty($_GET["s"]) && !empty($_GET["s"]) ) {
                $search_form .= ' value="' . urldecode($_GET["s"]) . '"';
            } else {
                $search_form .= ' value=""';
            }
            $search_form .= ' />';
            $search_form .= '<a role="button" class="search-icon-position" href="#" title="Submit site search"><span class="accessibility-text">Submit site search</span><i aria-hidden="true"><img class="inject-me" data-src="' . get_stylesheet_directory_uri() . '/images/icon-search.svg" src="' . get_stylesheet_directory_uri() . '/images/icon-search.png" aria-hidden="true" /></i></a>';
            $search_form .= '</form>';

            $output .= "\n\t" . '<div class="spb_search_widget spb_content_element ' . $width . $el_class . '">';
            $output .= "\n\t\t" . '<div class="spb-asset-content">';
            $output .= "\n\t\t" . $search_form;
            $output .= "\n\t\t" . '</div>';
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

            return $output;

        }
    }

    SPBMap::map( 'spb_search', array(
        "name"   => __( "Search", 'swift-framework-plugin' ),
        "base"   => "spb_search",
        "class"  => "spb_search spb_tab_ui",
        "icon"   => "icon-search-element",
        "params" => array(
            array(
                "type"        => "textfield",
                "heading"     => __( "Input placeholder text", 'swift-framework-plugin' ),
                "param_name"  => "search_input_text",
                "value"       => "Search",
                "description" => __( "Enter the text that appearas as default in the search input.", 'swift-framework-plugin' )
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