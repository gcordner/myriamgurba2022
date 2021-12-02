<?php

    /*
    *
    *	Swift Page Builder - Social Sharing Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
    *
    */

    if ( class_exists('SwiftPageBuilderShortcode') ) {

        /* SOCIAL SHARING ASSET
        ================================================== */

        class SwiftPageBuilderShortcode_spb_socialsharing extends SwiftPageBuilderShortcode {

            protected function content( $atts, $content = null ) {
                $sharing = $type = $enable_email = $enable_print = $enable_facebook = $enable_twitter = $enable_linkedin = $enable_print = $share_url = $width = $el_class = $text = $tweet_text = '';
                extract( shortcode_atts( array(
                    'tweet_text'        => '',
                    'share_url'         => '',
                    'enable_email'      => 'yes',
                    'enable_facebook'   => 'yes',
                    'enable_twitter'    => 'yes',
                    'enable_linkedin'   => 'yes',
                    'enable_print'      => 'yes',
                    'width'             => '1/1',
                    'el_class'          => '',
                    'el_position'       => '',
                    'align_type'        => '',
                ), $atts ) );

                global $post;
                global $sf_options;

                $title = get_the_title( $post );
                $button_count = 0;
                $type = "standard";
                $post_excerpt = get_the_excerpt($post->ID);

                $link = $share_url;
                if ( $link == "" ) {
                    $link = get_the_permalink($post->ID);
                }

                if ( isset($sf_options["twitter_username"]) && !empty($sf_options["twitter_username"]) && $sf_options["twitter_username"] != "" ) {
                    $twitter_handle = $sf_options["twitter_username"];
                } else {
                    $twitter_handle = "";
                }

                $width = spb_translateColumnWidthToSpan( $width );

                if ( $enable_email == "yes" || $enable_email == 1 ) {
                    $button_count++;
                    $sharing .= '<a class="share-icon share-icon--email" href="mailto:?body=' . rawurlencode($tweet_text . ' ' . $link) . '" target="_self" aria-label="email this page"><i class="fa fa-envelope"></i></a>';
                }
                if ( $enable_facebook == "yes" || $enable_facebook == 1 ) {
                    $button_count++;
                    $sharing .= '<a class="share-icon share-icon--facebook" href="https://www.facebook.com/sharer.php?u=' . rawurlencode($link) . '" target="_blank" aria-label="share this page on Facebook"><i class="fa fa-facebook"></i></a>';
                }
                if ( $enable_twitter == "yes" || $enable_twitter == 1 ) {
                    $button_count++;
                    if ( $twitter_handle != "" ) {
                        $tweet_text = "@" . $twitter_handle . " " . $tweet_text;
                    }
                    $sharing .= '<a class="share-icon share-icon--twitter" href="https://twitter.com/intent/tweet?url=' . rawurlencode($link) . '&text=' . rawurlencode($tweet_text) . '" target="_blank" aria-label="share this page on Twitter"><i class="fa fa-twitter"></i></a>';
                }
                if ( $enable_linkedin == "yes" || $enable_linkedin == 1 ) {
                    $button_count++;
                    $sharing .= '<a class="share-icon share-icon--linkedin" href="https://www.linkedin.com/shareArticle?url=' . rawurlencode($link) . '&title=' . rawurlencode($title) . '&summary=' . rawurlencode($tweet_text) . '" target="_blank" aria-label="share this page on Linkedin"><i class="fa fa-linkedin"></i></a>';
                }
                if ( $enable_print == "yes" || $enable_print == 1 ) {
                    $button_count++;
                    $sharing .= '<a class="share-icon share-icon--print" href="#" onclick="window.print();return false;" aria-label="print this page" role="button"><i class="fa fa-print" aria-hidden="true"></i></a>';
                }

                $output = '';
                $output .= '<div class="socialsharing-wrap ' . $width . '">';
                    $output .= '<div class="spb_socialsharing ' . $type . ' spb_content_element ' . $el_class . ' ' . $align_type . '">';
                        $output .= '<div class="social socialsharing-button-wrap">';
                          $output .= '<div class="share-label">';
                            $output .= '<span>Share</span>';
                          $output .= '</div>';
                            $output .= $sharing;
                        $output .= '</div>';
                    $output .= '</div>' . $this->endBlockComment( 'divider' ) . "\n";
                $output .= '</div>';

                $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );
                
                return $output;
            }
        }

        SPBMap::map( 'spb_socialsharing', array(
            "name"        => __( "Social Sharing", 'swift-framework-plugin' ),
            "base"        => "spb_socialsharing",
            "class"       => "spb_socialsharing",
            'icon'        => 'icon-third-party',
            "controls"    => '',
            "params"      => array(
                array(
                    "type"       => "section",
                    "param_name" => "socialsharing_options",
                    "heading"    => __( "Sharing Options", 'swift-framework-plugin' ),
                ),
                array(
                    "type"        => "textfield",
                    "heading"     => __( "URL to Share", 'swift-framework-plugin' ),
                    "param_name"  => "share_url",
                    "value"       => "",
                    "description" => __( "This is the main URL you would like to share. Leave blank if you would like to share the current page.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "textfield",
                    "heading"     => __( "Email Content", 'swift-framework-plugin' ),
                    "param_name"  => "tweet_text",
                    "value"       => "",
                    "description" => __( "This is the content that will display in the email, before the link.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "dropdown",
                    "heading"     => __( "Align Type", 'swift-framework-plugin' ),
                    "param_name"  => "align_type",
                    "value"       => array(
                        __( 'Left', 'swift-framework-plugin' )      => "align-left",
                        __( 'Center', 'swift-framework-plugin' )    => "align-center",
                        __( 'Right', 'swift-framework-plugin' )     => "align-right"
                    ),
                    "description" => __( "Choose how to align the group.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "buttonset",
                    "heading"     => __( "Email", 'swift-framework-plugin' ),
                    "param_name"  => "enable_email",
                    "value"       => array(
                        __( 'Yes', 'swift-framework-plugin' ) => "yes",
                        __( 'No', 'swift-framework-plugin' )  => "no"
                    ),
                    "description" => __( "Would you like email sharing to display on this page?", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "buttonset",
                    "heading"     => __( "Facebook", 'swift-framework-plugin' ),
                    "param_name"  => "enable_facebook",
                    "value"       => array(
                        __( 'Yes', 'swift-framework-plugin' ) => "yes",
                        __( 'No', 'swift-framework-plugin' )  => "no"
                    ),
                    "description" => __( "Would you like facebook sharing to display on this page?", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "buttonset",
                    "heading"     => __( "Twitter", 'swift-framework-plugin' ),
                    "param_name"  => "enable_twitter",
                    "value"       => array(
                        __( 'Yes', 'swift-framework-plugin' ) => "yes",
                        __( 'No', 'swift-framework-plugin' )  => "no"
                    ),
                    "description" => __( "Would you like tweeting to display on this page?", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "buttonset",
                    "heading"     => __( "Linkedin", 'swift-framework-plugin' ),
                    "param_name"  => "enable_linkedin",
                    "value"       => array(
                        __( 'Yes', 'swift-framework-plugin' ) => "yes",
                        __( 'No', 'swift-framework-plugin' )  => "no"
                    ),
                    "description" => __( "Would you like linkedin sharing to display on this page?", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "buttonset",
                    "heading"     => __( "Print", 'swift-framework-plugin' ),
                    "param_name"  => "enable_print",
                    "value"       => array(
                        __( 'Yes', 'swift-framework-plugin' ) => "yes",
                        __( 'No', 'swift-framework-plugin' )  => "no"
                    ),
                    "description" => __( "Would you like the option to print to be displayed on this page?", 'swift-framework-plugin' )
                ),
                array(
                    "type"       => "section",
                    "param_name" => "advanced_options",
                    "heading"    => __( "Advanced Options", 'swift-framework-plugin' ),
                ),
                array(
                    "type"        => "textfield",
                    "heading"     => __( "Extra class", 'swift-framework-plugin' ),
                    "param_name"  => "el_class",
                    "value"       => "",
                    "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'swift-framework-plugin' )
                )
            ),
            "js_callback" => array( "init" => "spbTextSeparatorInitCallBack" )
        ) );

    }

?>