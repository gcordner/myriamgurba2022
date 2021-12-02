<?php

    /*
    *
    *   Swift Page Builder - Highcharts Shortcode
    *   ------------------------------------------------
    *   Swift Framework
    *   Copyright Swift Ideas 2015 - http://www.swiftideas.com
    *
    */

    if ( class_exists('SwiftPageBuilderShortcode') ) {

        class SwiftPageBuilderShortcode_spb_highcharts extends SwiftPageBuilderShortcode {

            protected function content( $atts, $content = null ) {

                $title = $subtitle = $height = $gauge_stat = $x_label = $y_label = $display_x_axis = $display_y_axis = $display_legend = $display_tooltips = $value_display = $chart_type = $data = $output = $el_position = $width = $el_class = '';

                extract( shortcode_atts( array(
                    'title'             => '',
                    'subtitle'          => '',
                    'height'            => '400',
                    'display_x_axis'    => 'yes',
                    'x_label'           => '',
                    'x_cat'             => '',
                    'display_y_axis'    => 'yes',
                    'y_label'           => '',
                    'display_legend'    => 'yes',
                    'display_tooltips'  => 'yes',
                    'value_display'     => '',
                    'chart_type'        => '',
                    'data'              => '',
                    'gauge_stat'        => '',
                    'el_position'       => '',
                    'width'             => '1/1',
                    'el_class'          => ''
                ), $atts ) );

                $el_class = $this->getExtraClass( $el_class );
                $width    = spb_translateColumnWidthToSpan( $width );

                $output = "\n\t" . '<div class="spb_highcharts spb_content_element ' . $width . $el_class . '">';
                $output .= "\n\t\t" . '<div class="spb-asset-content">';

                try {
                    $data = preg_replace('/^\s+|\n|\r|\s+$/m', '', rawurldecode( base64_decode( strip_tags( $data ) ) ));
                    $data = json_encode(json_decode($data));
                } catch (Exception $e) {

                }

                if ( $chart_type == "solidgauge" ) {
                    $data = $gauge_stat;
                    $value_display = "percentage";
                }

                $output .= "\n\t\t\t" . '<div id="highcharts-' . strtotime("NOW") . '-' . rand(1000,9999) . '" class="highcharts-container" data-title="' . $title . '" data-subtitle="' . $subtitle . '" data-xlabel="' . $x_label . '" data-ylabel="' . $y_label . '" data-valuedisplay="' . $value_display . '" data-charttype="' . $chart_type . '" data-xcategories="' . $x_cat . '" data-displayx="' . $display_x_axis . '" data-displayy="' . $display_y_axis . '" data-displaylegend="' . $display_legend . '" data-displaytooltips="' . $display_tooltips . '" data-height="' . $height . '"><span class="data hide">' . $data . '</span></div>';
                if ( $chart_type == "solidgauge" ) {
                    if ( $title != "" ) {
                        $output .= "\n\t\t\t" . '<h4 class="highcharts-title">' . $title . '</h4>';
                    }
                    if ( $subtitle != "" ) {
                        $output .= "\n\t\t\t" . '<h6 class="highcharts-subtitle">' . $subtitle . '</h6>';
                    }
                }
                $output .= "\n\t\t" . '</div>';
                $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

                $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );

                return $output;
            }
        }

        SPBMap::map( 'spb_highcharts', array(
            "name"   => __( "Highcharts", 'swift-framework-plugin' ),
            "base"   => "spb_highcharts",
            "class"  => "highcharts spb_tab_data",
            "icon"   => "icon-chart",
            "params" => array(
                array(
                    "type"        => "textfield",
                    "holder"      => "div",
                    "heading"     => __( "Widget title", 'swift-framework-plugin' ),
                    "param_name"  => "title",
                    "value"       => "",
                    "description" => __( "Heading text should be no more than 5 words. Leave it empty if not needed.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "textfield",
                    "heading"     => __( "Subtitle", 'swift-framework-plugin' ),
                    "param_name"  => "subtitle",
                    "value"       => "",
                    "description" => __( "Subheading text should be no more than 10 words. Leave it empty if not needed.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "dropdown",
                    "heading"     => __( "Chart Type", 'swift-framework-plugin' ),
                    "param_name"  => "chart_type",
                    "value"       => array(
                        __( 'Vertical Bar', 'swift-framework-plugin' )      => "column",
                        __( 'Horizontal Bar', 'swift-framework-plugin' )    => "bar",
                        __( 'Stacked Bar', 'swift-framework-plugin' )       => "stacked",
                        __( 'Line', 'swift-framework-plugin' )              => "line",
                        __( 'Pie', 'swift-framework-plugin' )               => "pie",
                        __( 'Statistic', 'swift-framework-plugin' )         => "solidgauge",
                    ),
                    "description" => __( "Choose the display type for this chart.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "uislider",
                    "heading"     => __( "Number", 'swift-framework-plugin' ),
                    "param_name"  => "gauge_stat",
                    "value"       => "",
                    "step"        => "1",
                    "min"         => "0",
                    "max"         => "100",
                    "required"    => array("chart_type", "=", "solidgauge"),
                    "description" => __( "Provide the statistic for this chart.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "textarea_encoded",
                    "heading"     => __( "Data", 'swift-framework-plugin' ),
                    "param_name"  => "data",
                    "value"       => '',
                    "required"    => array("chart_type", "!=", "solidgauge"),
                    "description" => __( "Provide the Highcharts options for the series in JSON format.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "dropdown",
                    "heading"     => __( "Value Display", 'swift-framework-plugin' ),
                    "param_name"  => "value_display",
                    "value"       => array(
                        __( 'Percentage', 'swift-framework-plugin' )        => "percentage",
                        __( 'Numeric', 'swift-framework-plugin' )           => "numeric",
                        __( 'Currency (US)', 'swift-framework-plugin' )     => "currency"
                    ),
                    "required"    => array("chart_type", "!=", "solidgauge"),
                    "description" => __( "Choose the display type for this chart.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "uislider",
                    "heading"     => __( "Height", 'swift-framework-plugin' ),
                    "param_name"  => "height",
                    "value"       => "400",
                    "step"        => "1",
                    "min"         => "0",
                    "max"         => "2000",
                    "description" => __( "Define the height of the chart area. (px).", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "section_tab",
                    "param_name"  => "ib_display_options",
                    "heading"     => __( "Display Options", 'swift-framework-plugin' ),
                ),
                array(
                    "type"        => "buttonset",
                    "heading"     => __( "Display Y-Axis", 'swift-framework-plugin' ),
                    "param_name"  => "display_y_axis",
                    "std"         => "yes",
                    "value"       => array(
                        __( 'Yes', 'swift-framework-plugin' ) => "yes",
                        __( 'No', 'swift-framework-plugin' )  => "no"
                    ),
                    "required"    => array("chart_type", "!=", "pie"),
                    "description" => __( "Select if you'd like y-axis labled and grid to display on the chart.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "textfield",
                    "class"       => "",
                    "heading"     => __( "Y-Axis Label", 'swift-framework-plugin' ),
                    "param_name"  => "y_label",
                    "value"       => "",
                    "required"    => array("display_y_axis", "!=", "no"),
                    "description" => __( "Provide an y-axis label for the chart.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "buttonset",
                    "heading"     => __( "Display X-Axis", 'swift-framework-plugin' ),
                    "param_name"  => "display_x_axis",
                    "std"         => "yes",
                    "value"       => array(
                        __( 'Yes', 'swift-framework-plugin' ) => "yes",
                        __( 'No', 'swift-framework-plugin' )  => "no"
                    ),
                    "required"    => array("chart_type", "!=", "pie"),
                    "description" => __( "Select if you'd like x-axis labled and grid to display on the chart.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "textfield",
                    "class"       => "",
                    "heading"     => __( "X-Axis Label", 'swift-framework-plugin' ),
                    "param_name"  => "x_label",
                    "value"       => "",
                    "required"    => array("display_x_axis", "!=", "no"),
                    "description" => __( "Provide an x-axis label for the chart.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "textfield",
                    "class"       => "",
                    "heading"     => __( "X-Axis Categories", 'swift-framework-plugin' ),
                    "param_name"  => "x_cat",
                    "value"       => "",
                    "required"    => array("display_x_axis", "!=", "no"),
                    "description" => __( "Override the categories on the x-axis. Provide values separated by commas.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "buttonset",
                    "heading"     => __( "Display Legend", 'swift-framework-plugin' ),
                    "param_name"  => "display_legend",
                    "std"         => "yes",
                    "value"       => array(
                        __( 'Yes', 'swift-framework-plugin' ) => "yes",
                        __( 'No', 'swift-framework-plugin' )  => "no"
                    ),
                    "description" => __( "Select if you'd like to display a legend of the x-axis categories.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "buttonset",
                    "heading"     => __( "Display Tooltips", 'swift-framework-plugin' ),
                    "param_name"  => "display_tooltips",
                    "std"         => "yes",
                    "value"       => array(
                        __( 'Yes', 'swift-framework-plugin' ) => "yes",
                        __( 'No', 'swift-framework-plugin' )  => "no"
                    ),
                    "description" => __( "Select if you'd like to display a tooltip when hovering over data.", 'swift-framework-plugin' )
                ),
                array(
                    "type"        => "section_tab",
                    "param_name"  => "advanced_options",
                    "heading"     => __( "Advanced Options", 'swift-framework-plugin' ),
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

    }
