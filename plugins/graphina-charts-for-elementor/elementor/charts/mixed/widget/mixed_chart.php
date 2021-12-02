<?php

namespace Elementor;

if (!defined('ABSPATH')) exit;

/**
 * Elementor Blog widget.
 *
 * Elementor widget that displays an eye-catching headlines.
 *
 * @since 1.2.5
 */
class Mixed_chart extends Widget_Base
{

    /**
     * Get widget name.
     *
     * Retrieve heading widget name.
     *
     * @return string Widget name.
     * @since 1.2.5
     * @access public
     *
     */

    public function get_name()
    {
        return 'mixed_chart';
    }

    /**
     * Get widget Title.
     *
     * Retrieve heading widget Title.
     *
     * @return string Widget Title.
     * @since 1.2.5
     * @access public
     *
     */

    public function get_title()
    {
        return 'Mixed';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the heading widget belongs to.
     *
     * Used to determine where to display the widget in the editor.
     *
     * @return array Widget categories.
     * @since 1.2.5
     * @access public
     *
     */


    public function get_categories()
    {
        return ['iq-graphina-charts'];
    }


    /**
     * Get widget icon.
     *
     * Retrieve heading widget icon.
     *
     * @return string Widget icon.
     * @since 1.2.5
     * @access public
     *
     */

    public function get_icon()
    {
        return 'fas fa-water';
    }

    public function get_chart_type()
    {
        return 'mixed';
    }

    protected function _register_controls()
    {
        $type = $this->get_chart_type();

        graphina_basic_setting($this, $type);

        graphina_chart_data_option_setting($this, $type, 3);

        $this->start_controls_section(
            'iq_' . $type . '_section_2',
            [
                'label' => esc_html__('Chart Setting', 'graphina-lang')
            ]
        );

        graphina_common_chart_setting($this, $type, false, false, false, false, false);

        graphina_tooltip($this, $type);

        graphina_animation($this, $type);

        graphina_dropshadow($this, $type, false);

        $plotOptionTypeCondition = [];
        for ($loop = 0; $loop < graphina_default_setting('max_series_value'); $loop++) {
            $plotOptionTypeCondition[] = ['name' => 'iq_' . $type . '_chart_type_3_' . $loop, 'operator' => '===', 'value' => 'bar'];
        }

        $this->add_control(
            'iq_' . $type . '_chart_hr_plot_option_setting',
            [
                'type' => Controls_Manager::DIVIDER,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => $plotOptionTypeCondition,
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_plot_options_setting_title',
            [
                'label' => esc_html__('Plot Option Settings ( Column )', 'graphina-lang'),
                'type' => Controls_Manager::HEADING,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => $plotOptionTypeCondition,
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_plot_start_shape',
            [
                'label' => esc_html__('Start Shape', 'graphina-lang'),
                'type' => Controls_Manager::SELECT,
                'default' => graphina_pro_plot_shape_type(true),
                'options' => graphina_pro_plot_shape_type(),
                'conditions' => [
                    'relation' => 'or',
                    'terms' => $plotOptionTypeCondition,
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_plot_end_shape',
            [
                'label' => esc_html__('End Shape', 'graphina-lang'),
                'type' => Controls_Manager::SELECT,
                'default' => graphina_pro_plot_shape_type(true),
                'options' => graphina_pro_plot_shape_type(),
                'conditions' => [
                    'relation' => 'or',
                    'terms' => $plotOptionTypeCondition,
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_plot_datalabel_position_show',
            [
                'label' => esc_html__('Position', 'graphina-lang'),
                'type' => Controls_Manager::SELECT,
                'default' => graphina_position_type("vertical", true),
                'options' => graphina_position_type("vertical"),
                'conditions' => [
                    'relation' => 'or',
                    'terms' => $plotOptionTypeCondition,
                ]
            ]
        );

        $fillStyleTypeCondition = [];
        for ($loop = 0; $loop < graphina_default_setting('max_series_value'); $loop++) {
            $fillStyleTypeCondition[] = ['name' => 'iq_' . $type . '_chart_fill_style_type_' . $loop, 'operator' => '===', 'value' => 'gradient'];
        }

        $this->add_control(
            'hr_2_02',
            [
                'type' => Controls_Manager::DIVIDER,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => $fillStyleTypeCondition,
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_fill_setting_title',
            [
                'label' => esc_html__('Fill Setting', 'graphina-lang'),
                'type' => Controls_Manager::HEADING,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => $fillStyleTypeCondition,
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_can_chart_fill_inverse_color',
            [
                'label' => esc_html__('Inverse Color', 'graphina-lang'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'graphina-lang'),
                'label_off' => esc_html__('No', 'graphina-lang'),
                'default' => false,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => $fillStyleTypeCondition,
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_fill_gradient_type',
            [
                'label' => esc_html__('Gradient Type', 'graphina-lang'),
                'type' => Controls_Manager::SELECT,
                'default' => 'horizontal',
                'options' => graphina_pro_gradient_type(),
                'conditions' => [
                    'relation' => 'or',
                    'terms' => $fillStyleTypeCondition,
                ]
            ]
        );

        $this->add_control(
            'hr_2_03',
            [
                'type' => Controls_Manager::DIVIDER
            ]
        );

        $this->add_control(
            'iq_' . $type . '_stroke_setting_title',
            [
                'label' => esc_html__('Stroke Setting', 'graphina-lang'),
                'type' => Controls_Manager::HEADING
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_stroke_line_cap',
            [
                'label' => esc_html__('Line Cap', 'graphina-lang'),
                'type' => Controls_Manager::SELECT,
                'default' => graphina_pro_line_cap_type(true),
                'options' => graphina_pro_line_cap_type()
            ]
        );

        $this->add_control(
            'hr_2_01',
            [
                'type' => Controls_Manager::DIVIDER,
            ]
        );

        $this->add_control(
            'iq_' . $type . '_category_title',
            [
                'label' => esc_html__('Categories', 'graphina-lang'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $repeater = new Repeater();
        $repeater->add_control(
            'iq_' . $type . '_chart_category',
            [
                'label' => esc_html__('Category Value', 'graphina-lang'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => esc_html__('Add Value', 'graphina-lang'),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        /** Chart value list. */
        $this->add_control(
            'iq_' . $type . '_category_list',
            [
                'label' => esc_html__('Values', 'graphina-lang'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    ['iq_' . $type . '_chart_category' => 'Jan'],
                    ['iq_' . $type . '_chart_category' => 'Feb'],
                    ['iq_' . $type . '_chart_category' => 'Mar'],
                    ['iq_' . $type . '_chart_category' => 'Apr'],
                    ['iq_' . $type . '_chart_category' => 'May'],
                    ['iq_' . $type . '_chart_category' => 'Jun'],
                ],
                'condition' => ['iq_' . $type . '_chart_data_option' => 'manual'],
                'title_field' => '{{{ iq_' . $type . '_chart_category }}}',
            ]
        );

        $this->end_controls_section();

        graphina_chart_label_setting($this, $type);

        graphina_advance_x_axis_setting($this, $type);

        $this->start_controls_section(
            'iq_' . $type . '_section_6',
            [
                'label' => esc_html__('Y-Axis Setting', 'graphina-lang')
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_show_multiple_yaxis',
            [
                'label' => esc_html__('Show Multiple', 'graphina-lang'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'graphina-lang'),
                'label_off' => esc_html__('No', 'graphina-lang'),
                'default' => false
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_yaxis_line_show',
            [
                'label' => esc_html__('Line', 'graphina-lang'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Hide', 'graphina-lang'),
                'label_off' => esc_html__('Show', 'graphina-lang'),
                'default' => 'yes'
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_yaxis_datalabel_tick_amount',
            [
                'label' => esc_html__('Tick Amount', 'graphina-lang'),
                'type' => Controls_Manager::NUMBER,
                'default' => 0,
                'max' => 30,
                'min' => 0
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_yaxis_datalabel_decimals_in_float',
            [
                'label' => esc_html__('Decimals In Float', 'graphina-lang'),
                'type' => Controls_Manager::NUMBER,
                'default' => 2,
                'max' => 6,
                'min' => 0,
                'description' => esc_html__('If you enabled "Labels Prefix/Postfix", this won’t have any effect.', 'graphina-lang')
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_yaxis_tooltip_show',
            [
                'label' => esc_html__('Tooltip', 'graphina-lang'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Hide', 'graphina-lang'),
                'label_off' => esc_html__('Show', 'graphina-lang'),
                'default' => ''
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_yaxis_crosshairs_show',
            [
                'label' => esc_html__('Pointer Line', 'graphina-lang'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Hide', 'graphina-lang'),
                'label_off' => esc_html__('Show', 'graphina-lang'),
                'default' => ''
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_yaxis_datalabel_show',
            [
                'label' => esc_html__('Labels', 'graphina-lang'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Hide', 'graphina-lang'),
                'label_off' => esc_html__('Show', 'graphina-lang'),
                'default' => 'yes',
                'condition' => [
                    'iq_' . $type . '_chart_show_multiple_yaxis!' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_yaxis_datalabel_position',
            [
                'label' => esc_html__('Position', 'graphina-lang'),
                'type' => Controls_Manager::CHOOSE,
                'default' => graphina_position_type('horizontal_boolean', true),
                'options' => graphina_position_type('horizontal_boolean'),
                'condition' => [
                    'iq_' . $type . '_chart_yaxis_datalabel_show' => 'yes',
                    'iq_' . $type . '_chart_show_multiple_yaxis!' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_yaxis_datalabel_offset_x',
            [
                'label' => esc_html__('Offset-X', 'graphina-lang'),
                'type' => Controls_Manager::NUMBER,
                'default' => 0,
                'condition' => [
                    'iq_' . $type . '_chart_yaxis_datalabel_show' => 'yes',
                    'iq_' . $type . '_chart_show_multiple_yaxis!' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_yaxis_datalabel_offset_y',
            [
                'label' => esc_html__('Offset-Y', 'graphina-lang'),
                'type' => Controls_Manager::NUMBER,
                'default' => 0,
                'condition' => [
                    'iq_' . $type . '_chart_yaxis_datalabel_show' => 'yes',
                    'iq_' . $type . '_chart_show_multiple_yaxis!' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_yaxis_datalabel_rotate',
            [
                'label' => esc_html__('Rotate', 'graphina-lang'),
                'type' => Controls_Manager::NUMBER,
                'default' => 0,
                'max' => 360,
                'min' => -360,
                'condition' => [
                    'iq_' . $type . '_chart_yaxis_datalabel_show' => 'yes',
                    'iq_' . $type . '_chart_show_multiple_yaxis!' => 'yes'
                ]
            ]
        );

        $this->end_controls_section();

        graphina_pro_mixed_series_setting($this, $type, ['tooltip', 'color', 'stroke', 'drop-shadow'], ['classic', 'gradient', 'pattern']);

        for ($i = 0; $i < graphina_default_setting('max_series_value'); $i++) {
            $this->start_controls_section(
                'iq_' . $type . '_section_4_' . $i,
                [
                    'label' => esc_html__('Element ' . ($i + 1), 'graphina-lang'),
                    'condition' => [
                        'iq_' . $type . '_chart_data_series_count' => range(1 + $i, graphina_default_setting('max_series_value')),
                        'iq_' . $type . '_chart_data_option' => 'manual'
                    ],
                ]
            );

            $this->add_control(
                'iq_' . $type . '_chart_title_4_' . $i,
                [
                    'label' => esc_html__('Title', 'graphina-lang'),
                    'type' => Controls_Manager::TEXT,
                    'placeholder' => esc_html__('Add Tile', 'graphina-lang'),
                    'default' => 'Element ' . ($i + 1),
                    'dynamic' => [
                        'active' => true,
                    ],

                ]
            );

            $this->add_control(
                'hr_4_05_' . $i,
                [
                    'type' => Controls_Manager::DIVIDER,
                ]
            );

            $this->add_control(
                'iq_' . $type . '_chart_element_values_4_' . $i,
                [
                    'label' => esc_html__('Value Setting', 'graphina-lang'),
                    'type' => Controls_Manager::HEADING,
                ]
            );

            $this->add_control(
                'iq_' . $type . '_can_chart_negative_values_4_' . $i,
                [
                    'label' => esc_html__('Default Negative', 'graphina-lang'),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__('Yes', 'graphina-lang'),
                    'label_off' => esc_html__('No', 'graphina-lang'),
                    'default' => false
                ]
            );

            $repeater = new Repeater();

            $repeater->add_control(
                'iq_' . $type . '_chart_value_4_' . $i,
                [
                    'label' => esc_html__('Series Value', 'graphina-lang'),
                    'type' => Controls_Manager::NUMBER,
                    'placeholder' => esc_html__('Add Value', 'graphina-lang'),
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );

            /** Chart value list. */
            $this->add_control(
                'iq_' . $type . '_value_list_4_1_' . $i,
                [
                    'label' => esc_html__('Values List', 'graphina-lang'),
                    'type' => Controls_Manager::REPEATER,
                    'fields' => $repeater->get_controls(),
                    'default' => [
                        ['iq_' . $type . '_chart_value_4_' . $i => rand(10, 200)],
                        ['iq_' . $type . '_chart_value_4_' . $i => rand(10, 200)],
                        ['iq_' . $type . '_chart_value_4_' . $i => rand(10, 200)],
                        ['iq_' . $type . '_chart_value_4_' . $i => rand(10, 200)],
                        ['iq_' . $type . '_chart_value_4_' . $i => rand(10, 200)],
                        ['iq_' . $type . '_chart_value_4_' . $i => rand(10, 200)]
                    ],
                    'condition' => [
                        'iq_' . $type . '_can_chart_negative_values_4_' . $i . '!' => 'yes'
                    ],
                    'title_field' => '{{{ iq_' . $type . '_chart_value_4_' . $i . ' }}}',
                ]
            );

            $this->add_control(
                'iq_' . $type . '_value_list_4_2_' . $i,
                [
                    'label' => esc_html__('Values', 'graphina-lang'),
                    'type' => Controls_Manager::REPEATER,
                    'fields' => $repeater->get_controls(),
                    'default' => [
                        ['iq_' . $type . '_chart_value_4_' . $i => rand(-200, 200)],
                        ['iq_' . $type . '_chart_value_4_' . $i => rand(-200, 200)],
                        ['iq_' . $type . '_chart_value_4_' . $i => rand(-200, 200)],
                        ['iq_' . $type . '_chart_value_4_' . $i => rand(-200, 200)],
                        ['iq_' . $type . '_chart_value_4_' . $i => rand(-200, 200)],
                        ['iq_' . $type . '_chart_value_4_' . $i => rand(-200, 200)]
                    ],
                    'condition' => [
                        'iq_' . $type . '_can_chart_negative_values_4_' . $i => 'yes'
                    ],
                    'title_field' => '{{{ iq_' . $type . '_chart_value_4_' . $i . ' }}}',
                ]
            );

            $this->end_controls_section();
        }

        graphina_style_section($this, $type);

        graphina_card_style($this, $type);

        graphina_chart_style($this, $type);

        graphina_pro_password_style_section($this, $type);

    }

    protected function render()
    {
        $type = $this->get_chart_type();
        $defaultChartType = "line";
        $settings = $this->get_settings_for_display();
        $mainId = $this->get_id();
        $chartTypes = [];
        $fillType = [];
        $fillOpacity = [];
        $color1 = [];
        $color2 = [];
        $fill_pattern = [];
        $opacityFrom = [];
        $opacityTo = [];
        $yaxis = [];
        $dropShadowEnabledOn = [];
        $tooltipSeries = [];
        $dropShadowColor = [];
        $strokeCurves = [];
        $strokeWidths = [];
        $strokeDashArray = [];
        $yaxisYesCount = 0;
        $xLabelPrefix = $xLabelPostfix = '';
        $dataLabelEnabledOn = [];
        $data = ['series' => [], 'category' => []];
        $markerSize = [];
        $markerStrokeColor = [];
        $markerStokeWidth = [];
        $markerShape = [];

        $exportFileName = (
            !empty($settings['iq_' . $type . '_can_chart_show_toolbar']) && $settings['iq_' . $type . '_can_chart_show_toolbar'] === 'yes'
            && !empty($settings['iq_' . $type . '_export_filename'])
        ) ? $settings['iq_' . $type . '_export_filename'] : $mainId;

        $defaultYaxis = [
            "style" => [
                "colors" => strval($settings['iq_' . $type . '_chart_font_color']),
                "fontSize" => $settings['iq_' . $type . '_chart_font_size']['size'] . $settings['iq_' . $type . '_chart_font_size']['unit'],
                "fontFamily" => $settings['iq_' . $type . '_chart_font_family'],
                "fontWeight" => $settings['iq_' . $type . '_chart_font_weight']
            ]
        ];

        if ($settings['iq_' . $type . '_chart_xaxis_label_show'] === 'yes') {
            $xLabelPrefix = $settings['iq_' . $type . '_chart_xaxis_label_prefix'];
            $xLabelPostfix = $settings['iq_' . $type . '_chart_xaxis_label_postfix'];
        }

        for ($i = 0; $i < $settings['iq_' . $type . '_chart_data_series_count']; $i++) {
            $chartTypes[] = $settings['iq_' . $type . '_chart_type_3_' . $i];
            $fillType[] = ($settings['iq_' . $type . '_chart_type_3_' . $i] === 'line' && $settings['iq_' . $type . '_chart_fill_style_type_' . $i] === 'pattern') ? 'classic' : $settings['iq_' . $type . '_chart_fill_style_type_' . $i];
            $fillOpacity[] = ($settings['iq_' . $type . '_chart_type_3_' . $i] === 'line' && $settings['iq_' . $type . '_chart_fill_style_type_' . $i] === 'pattern') ? 1 : $settings['iq_' . $type . '_chart_fill_opacity_' . $i];
            $color1[] = strval($settings['iq_' . $type . '_chart_gradient_3_1_' . $i]);
            $color2[] = strval(isset($settings['iq_' . $type . '_chart_gradient_3_2_' . $i]) ? $settings['iq_' . $type . '_chart_gradient_3_2_' . $i] : "#ffffff");
            $fill_pattern[] = $settings['iq_' . $type . '_chart_pattern_3_' . $i];
            $opacityFrom[] = isset($settings['iq_' . $type . '_chart_fill_opacityFrom_' . $i]) ? $settings['iq_' . $type . '_chart_fill_opacityFrom_' . $i] : 1;
            $opacityTo[] = isset($settings['iq_' . $type . '_chart_fill_opacityTo_' . $i]) ? $settings['iq_' . $type . '_chart_fill_opacityTo_' . $i] : 1;
            $strokeCurves[] = $settings['iq_' . $type . '_chart_stroke_curve_3_' . $i];
            $strokeWidths[] = $settings['iq_' . $type . '_chart_stroke_width_3_' . $i];
            $strokeDashArray[] = $settings['iq_' . $type . '_chart_stroke_dash_3_' . $i];
            $markerSize[] = $settings['iq_' . $type . '_chart_marker_size_'.$i];
            $markerStrokeColor[] = strval($settings[ 'iq_' . $type . '_chart_marker_stroke_color_'.$i]);
            $markerStokeWidth[] = $settings[ 'iq_' . $type . '_chart_marker_stroke_width_'.$i];
            $markerShape[] = strval($settings['iq_' . $type . '_chart_chart_marker_stroke_shape_'.$i]);
            if ($settings['iq_' . $type . '_chart_drop_shadow_enabled_3_' . $i] === "yes") {
                $dropShadowColor[] = strval($settings['iq_' . $type . '_chart_drop_shadow_color_3_' . $i]);
                $dropShadowEnabledOn[] = $i;
            } else {
                $dropShadowColor[] = strval('#FFFFFF00');
            }
            if ($settings['iq_' . $type . '_chart_datalabel_show_3_' . $i] === 'yes') {
                $dataLabelEnabledOn[] = $i;
            }
            if ($settings['iq_' . $type . '_chart_show_multiple_yaxis'] === 'yes') {
                $yaxis[] = [
                    "show" => $settings['iq_' . $type . '_chart_yaxis_show_3_' . $i] === 'yes',
                    "opposite" => $settings['iq_' . $type . '_chart_yaxis_opposite_3_' . $i] === 'yes',
                    "tickAmount" => $settings['iq_' . $type . '_chart_yaxis_datalabel_tick_amount'],
                    "decimalsInFloat" => $settings['iq_' . $type . '_chart_yaxis_datalabel_decimals_in_float'],
                    "title" => [
                        "text" => $settings['iq_' . $type . '_chart_title_4_' . $i]
                    ],
                    "labels" => $defaultYaxis,
                    "tooltip" => [
                        "enabled" => !empty($settings['iq_' . $type . '_chart_yaxis_tooltip_show']) && $settings['iq_' . $type . '_chart_yaxis_tooltip_show'] === 'yes'
                    ],
                    "crosshairs" => [
                        "show" => !empty($settings['iq_' . $type . '_chart_yaxis_crosshairs_show']) && $settings['iq_' . $type . '_chart_yaxis_crosshairs_show'] === 'yes'
                    ]
                ];
                if ($settings['iq_' . $type . '_chart_yaxis_show_3_' . $i] === 'yes') {
                    $yaxisYesCount++;
                }
            }
            if (!empty($settings['iq_' . $type . '_chart_tooltip_shared']) && $settings['iq_' . $type . '_chart_tooltip_shared'] === "yes") {
                if (!empty($settings['iq_' . $type . '_chart_tooltip_enabled_on_1_' . $i]) && $settings['iq_' . $type . '_chart_tooltip_enabled_on_1_' . $i] === "yes") {
                    $tooltipSeries[] = $i;
                }
            } else {
                $tooltipSeries[] = $i;
            }
        }
        $dataTypeOption = $settings['iq_' . $type . '_chart_data_option'] === 'manual' ? 'manual' : $settings['iq_' . $type . '_chart_dynamic_data_option'];
        switch ($dataTypeOption) {
            case "manual" :
                $categoryList = $settings['iq_' . $type . '_category_list'];
                foreach ($categoryList as $v) {
                    $data['category'][] =(string)graphina_get_dynamic_tag_data($v,'iq_' . $type . '_chart_category');
                }
                for ($i = 0; $i < $settings['iq_' . $type . '_chart_data_series_count']; $i++) {
                    $valueList = $settings['iq_' . $type . '_value_list_4_' . ($settings['iq_' . $type . '_can_chart_negative_values_4_' . $i] === 'yes' ? 2 : 1) . '_' . $i];
                    $value = [];
                    foreach ($valueList as $v) {
                        $value[] = (float)graphina_get_dynamic_tag_data($v,'iq_' . $type . '_chart_value_4_' . $i);
                    }
                    $data['series'][] = [
                        'name' => (string)graphina_get_dynamic_tag_data($settings,'iq_' . $type . '_chart_title_4_' . $i),
                        'data' => $value,
                    ];
                }
                break;
            case "csv":
                $data = graphina_pro_parse_csv($settings, $type, 'area');
                $settings['iq_' . $type . '_chart_legend_show'] = "yes";
                break;
            case "remote-csv" :
            case "google-sheet" :
                $data = graphina_pro_get_data_from_url($type, $settings, $dataTypeOption, $mainId, 'area');
                $settings['iq_' . $type . '_chart_legend_show'] = "yes";
                break;
            case "api":
                $data = graphina_pro_chart_get_data_from_api($type, $settings, 'area');
                $settings['iq_' . $type . '_chart_legend_show'] = "yes";
                break;
        }
        if($settings['iq_' . $type . '_chart_data_option'] === 'firebase') {
            $data = apply_filters('graphina_addons_render_section', $data, $type, $settings);
        }
        if (isset($data['fail']) && $data['fail'] === 'permission') {
            switch ($dataTypeOption) {
                case "google-sheet" :
                    echo "<pre><b>" . esc_html__('Please check file sharing permission and "Publish As" type is CSV or not. ', 'graphina-lang') . "</b><small><a target='_blank' href='https://youtu.be/Dv8s4QxZlDk'>" . esc_html__('Click for reference.', 'graphina-lang') . "</a></small></pre>";
                    return;
                    break;
                case "remote-csv" :
                default:
                    echo "<pre><b>" . (isset($data['errorMessage']) ? $data['errorMessage'] :  esc_html__('Please check file sharing permission.', 'graphina-lang')). "</b></pre>";
                    return;
                    break;
            }
        }

        $categoryLength = count($data['category']);
        foreach ($data['series'] as $index => $series) {
            if (count($series['data']) >= $categoryLength) {
                $data['series'][$index]['data'] = array_slice($series['data'], 0, $categoryLength);
            } else {
                $data['series'][$index]['data'] = array_pad($series['data'], $categoryLength, 0);
            }
        }

        if ($settings['iq_' . $type . '_chart_show_multiple_yaxis'] !== 'yes' || $yaxisYesCount === 0) {
            $yaxis = array_merge(
                [
                    "labels" => array_merge(
                        $defaultYaxis,
                        [
                            "show" => $settings['iq_' . $type . '_chart_yaxis_datalabel_show'] === 'yes' || $settings['iq_' . $type . '_chart_show_multiple_yaxis'] === 'yes',
                            "rotate" => $settings['iq_' . $type . '_chart_yaxis_datalabel_rotate'],
                            "offsetX" => $settings['iq_' . $type . '_chart_yaxis_datalabel_offset_x'],
                            "offsetY" => $settings['iq_' . $type . '_chart_yaxis_datalabel_offset_y'],
                        ]
                    )
                ],
                [
                    "opposite" => $settings['iq_' . $type . '_chart_yaxis_datalabel_position'],
                    "tickAmount" => $settings['iq_' . $type . '_chart_yaxis_datalabel_tick_amount'],
                    "decimalsInFloat" => $settings['iq_' . $type . '_chart_yaxis_datalabel_decimals_in_float'],
                    "tooltip" => [
                        "enabled" => !empty($settings['iq_' . $type . '_chart_yaxis_tooltip_show']) && $settings['iq_' . $type . '_chart_yaxis_tooltip_show'] === 'yes'
                    ],
                    "crosshairs" => [
                        "show" => !empty($settings['iq_' . $type . '_chart_yaxis_crosshairs_show']) && $settings['iq_' . $type . '_chart_yaxis_crosshairs_show'] === 'yes'
                    ]
                ]
            );
        }

        if (count($chartTypes) > 0 && $defaultChartType === $chartTypes[0] && $chartTypes[0] === "line") {
            $defaultChartType = "area";
        }

        $new_data = ['chart_type' => [], 'fill_type' => [], 'fill_opacity' => [], 'opacity_from' => [], 'opacity_to' => [], 'drop_shadow_color' => [], 'drop_shadow_enabled_on' => [], 'data_label_enabled_on' => [], 'stroke_curves' => [], 'stroke_widths' => [], 'stroke_dash_array' => [], 'color1' => [], 'color2' => [], 'fill_pattern' => []];
        $desiredLength = count($data['series']);
        while (count($new_data['chart_type']) < $desiredLength) {
            $new_data['chart_type'] = array_merge($new_data['chart_type'], $chartTypes);
            $new_data['fill_type'] = array_merge($new_data['fill_type'], $fillType);
            $new_data['fill_opacity'] = array_merge($new_data['fill_opacity'], $fillOpacity);
            $new_data['opacity_from'] = array_merge($new_data['opacity_from'], $opacityFrom);
            $new_data['opacity_to'] = array_merge($new_data['opacity_to'], $opacityTo);
            $new_data['stroke_curves'] = array_merge($new_data['stroke_curves'], $strokeCurves);
            $new_data['stroke_widths'] = array_merge($new_data['stroke_widths'], $strokeWidths);
            $new_data['stroke_dash_array'] = array_merge($new_data['stroke_dash_array'], $strokeDashArray);
            $new_data['color1'] = array_merge($new_data['color1'], $color1);
            $new_data['color2'] = array_merge($new_data['color2'], $color2);
            $new_data['fill_pattern'] = array_merge($new_data['fill_pattern'], $fill_pattern);
        }

        $chartTypes = array_slice($new_data['chart_type'], 0, $desiredLength);
        foreach ($data['series'] as $index => $info) {
            $data['series'][$index]['type'] = $chartTypes[$index];
        }

        $markerSize =  implode('_,_', $markerSize);
        $markerStrokeColor =  implode('_,_', $markerStrokeColor);
        $markerStokeWidth =  implode('_,_', $markerStokeWidth);
        $markerShape =  implode('_,_', $markerShape);
        $fillType = implode('_,_', array_slice($new_data['fill_type'], 0, $desiredLength));
        $fillOpacity = implode('_,_', array_slice($new_data['fill_opacity'], 0, $desiredLength));
        $opacityFrom = implode('_,_', array_slice($new_data['opacity_from'], 0, $desiredLength));
        $opacityTo = implode('_,_', array_slice($new_data['opacity_to'], 0, $desiredLength));
        $dropShadowColor = implode('_,_', $dropShadowColor);
        $dropShadowEnabledOn = implode('_,_', $dropShadowEnabledOn);
        $dataLabelEnabledOn = implode('_,_', $dataLabelEnabledOn);
        $strokeCurves = implode('_,_', array_slice($new_data['stroke_curves'], 0, $desiredLength));
        $strokeWidths = implode('_,_', array_slice($new_data['stroke_widths'], 0, $desiredLength));
        $strokeDashArray = implode('_,_', array_slice($new_data['stroke_dash_array'], 0, $desiredLength));
        $color1 = implode('_,_', array_slice($new_data['color1'], 0, $desiredLength));
        $color2 = implode('_,_', array_slice($new_data['color2'], 0, $desiredLength));
        $fill_pattern = implode('_,_', array_slice($new_data['fill_pattern'], 0, $desiredLength));
        $tooltipSeries = implode(',', $tooltipSeries);

        $category = implode('_,_', $data['category']);
        $chartDataJson = json_encode($data['series']);
        $yaxisJson = json_encode($yaxis);

        require GRAPHINA_PRO_ROOT . '/elementor/charts/mixed/render/mixed_chart.php';
        if( isRestrictedAccess('mixed',$this->get_id(),$settings,false) === false)
        {
        ?>
        <script>

            var myElement = document.querySelector(".mixed-chart-<?php esc_attr_e($mainId); ?>");

            if (typeof isInit === 'undefined') {
                var isInit = {};
            }
            isInit['<?php esc_attr_e($mainId); ?>'] = false;

            var mixedOptions = {
                series: <?php echo $chartDataJson ?>,
                chart: {
                    type: '<?php echo $defaultChartType;?>',
                    height: parseInt('<?php echo $settings['iq_' . $type . '_chart_height'] ?>'),
                    toolbar: {
                        show: '<?php echo $settings['iq_' . $type . '_can_chart_show_toolbar'] === 'yes'; ?>',
                        export:{
                            csv:{
                                filename:"<?php echo $exportFileName; ?>"
                            },
                            svg:{
                                filename:"<?php echo $exportFileName; ?>"
                            },
                            png:{
                                filename:"<?php echo $exportFileName; ?>"
                            }
                        }
                    },
                    animations: {
                        enabled: '<?php echo($settings['iq_' . $type . '_chart_animation'] === "yes") ?>',
                        speed: '<?php echo $settings['iq_' . $type . '_chart_animation_speed'] ?>',
                        delay: '<?php echo $settings['iq_' . $type . '_chart_animation_delay'] ?>'
                    },
                    dropShadow: {
                        enabled: '<?php echo($settings['iq_' . $type . '_is_chart_dropshadow'] === "yes") ?>',
                        enabledOnSeries: '<?php echo $dropShadowEnabledOn; ?>'.split('_,_').map(v => parseInt(v)),
                        top: parseInt('<?php echo $settings['iq_' . $type . '_is_chart_dropshadow_top'] ?>'),
                        left: parseInt('<?php echo $settings['iq_' . $type . '_is_chart_dropshadow_left'] ?>'),
                        blur: parseInt('<?php echo $settings['iq_' . $type . '_is_chart_dropshadow_blur'] ?>'),
                        color: '<?php echo $dropShadowColor; ?>'.split('_,_'),
                        opacity: parseFloat('<?php echo $settings['iq_' . $type . '_is_chart_dropshadow_opacity'] ?>')
                    }
                },
                labels: '<?php echo $category; ?>'.split('_,_'),
                colors: '<?php echo $color1; ?>'.split('_,_'),
                xaxis: {
                    position: '<?php esc_html_e($settings['iq_' . $type . '_chart_xaxis_datalabel_position']) ?>',
                    tickAmount: parseInt("<?php esc_html_e($settings['iq_' . $type . '_chart_xaxis_datalabel_tick_amount']); ?>"),
                    tickPlacement: "<?php esc_html_e($settings['iq_' . $type . '_chart_xaxis_datalabel_tick_placement']) ?>",
                    labels: {
                        show: '<?php echo $settings['iq_' . $type . '_chart_xaxis_datalabel_show'] === 'yes'; ?>',
                        rotateAlways: '<?php echo $settings['iq_' . $type . '_chart_xaxis_datalabel_auto_rotate'] ?>',
                        rotate: '<?php echo $settings['iq_' . $type . '_chart_xaxis_datalabel_rotate'] ?>',
                        offsetX: parseInt('<?php echo $settings['iq_' . $type . '_chart_xaxis_datalabel_offset_x'] ?>'),
                        offsetY: parseInt('<?php echo $settings['iq_' . $type . '_chart_xaxis_datalabel_offset_y'] ?>'),
                        trim: true,
                        style: {
                            colors: '<?php echo strval($settings['iq_' . $type . '_chart_font_color']) ?>',
                            fontSize: '<?php echo $settings['iq_' . $type . '_chart_font_size']['size'] . $settings['iq_' . $type . '_chart_font_size']['unit'] ?>',
                            fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family'] ?>',
                            fontWeight: '<?php echo $settings['iq_' . $type . '_chart_font_weight'] ?>'
                        },
                        formatter: function (val, index) {
                            return '<?php esc_html_e($xLabelPrefix) ?>' + val + '<?php esc_html_e($xLabelPostfix) ?>';
                        }
                    },
                    tooltip: {
                        enabled: "<?php echo !empty($settings['iq_' . $type . '_chart_xaxis_tooltip_show']) && $settings['iq_' . $type . '_chart_xaxis_tooltip_show'] === 'yes';?>"
                    },
                    crosshairs: {
                        show: "<?php echo !empty($settings['iq_' . $type . '_chart_xaxis_crosshairs_show']) && $settings['iq_' . $type . '_chart_xaxis_crosshairs_show'] === 'yes';?>"
                    }
                },
                yaxis: <?php echo $yaxisJson ?>,
                grid: {
                    yaxis: {
                        lines: {
                            show: '<?php echo $settings['iq_' . $type . '_chart_yaxis_line_show']; ?>'
                        }
                    }
                },
                plotOptions: {
                    bar: {
                        startingShape: '<?php echo $settings['iq_' . $type . '_chart_plot_start_shape'] ?>',
                        endingShape: '<?php echo $settings['iq_' . $type . '_chart_plot_end_shape'] ?>',
                        dataLabels: {
                            position: '<?php echo $settings['iq_' . $type . '_chart_plot_datalabel_position_show'] ?>',
                        }
                    },
                },
                stroke: {
                    curve: '<?php echo $strokeCurves; ?>'.split('_,_'),
                    lineCap: ['<?php echo $settings['iq_' . $type . '_chart_stroke_line_cap']; ?>'],
                    colors: '<?php echo $color1; ?>'.split('_,_'),
                    width: '<?php echo $strokeWidths; ?>'.split('_,_').map(v => parseInt(v)),
                    dashArray: '<?php echo $strokeDashArray; ?>'.split('_,_').map(v => parseInt(v)),
                },
                fill: {
                    type: '<?php echo $fillType; ?>'.split('_,_'),
                    opacity: '<?php echo $fillOpacity; ?>'.split('_,_').map(v => parseFloat(v)),
                    colors: '<?php echo $color1; ?>'.split('_,_'),
                    gradient: {
                        inverseColors: '<?php echo $settings['iq_' . $type . '_can_chart_fill_inverse_color'] === "yes"; ?>',
                        gradientToColors: '<?php echo $color2; ?>'.split('_,_'),
                        type: '<?php echo $settings['iq_' . $type . '_chart_fill_gradient_type'] ?>',
                        opacityFrom: '<?php echo $opacityFrom; ?>'.split('_,_').map(v => parseFloat(v)),
                        opacityTo: '<?php echo $opacityTo; ?>'.split('_,_').map(v => parseFloat(v))
                    },
                    pattern: {
                        style: '<?php echo $fill_pattern; ?>'.split('_,_'),
                        width: 6,
                        height: 6,
                        strokeWidth: 2
                    }
                },
                legend: {
                    showForSingleSeries:true,
                    show: '<?php echo $settings['iq_' . $type . '_chart_legend_show'] ?>',
                    position: '<?php esc_html_e($settings['iq_' . $type . '_chart_legend_position']) ?>',
                    horizontalAlign: '<?php esc_html_e($settings['iq_' . $type . '_chart_legend_horizontal_align']) ?>',
                    fontSize: '<?php echo $settings['iq_' . $type . '_chart_font_size']['size'] . $settings['iq_' . $type . '_chart_font_size']['unit'] ?>',
                    fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family'] ?>',
                    fontWeight: '<?php echo $settings['iq_' . $type . '_chart_font_weight'] ?>',
                    labels: {
                        colors: '<?php echo strval($settings['iq_' . $type . '_chart_font_color']) ?>'
                    }
                },
                tooltip: {
                    enabled: '<?php echo $settings['iq_' . $type . '_chart_tooltip'] ?>',
                    theme: '<?php echo $settings['iq_' . $type . '_chart_tooltip_theme'] ?>',
                    shared: '<?php echo !empty($settings['iq_' . $type . '_chart_tooltip_shared']) ? $settings['iq_' . $type . '_chart_tooltip_shared'] : ''; ?>' === "yes",
                    enabledOnSeries: [<?php esc_html_e($tooltipSeries); ?>],
                    style: {
                        fontSize: '<?php echo $settings['iq_' . $type . '_chart_font_size']['size'] . $settings['iq_' . $type . '_chart_font_size']['unit'] ?>',
                        fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family'] ?>'
                    }
                },
                noData: {
                    text: '<?php echo $settings['iq_' . $type . '_chart_no_data_text'] ?>',
                    align: 'center',
                    verticalAlign: 'middle',
                    style: {
                        fontSize: '<?php echo $settings['iq_' . $type . '_chart_font_size']['size'] . $settings['iq_' . $type . '_chart_font_size']['unit'] ?>',
                        fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family'] ?>',
                        color: '<?php echo strval($settings['iq_' . $type . '_chart_font_color']) ?>'
                    }
                },
                dataLabels: {
                    enabled: '<?php echo $settings['iq_' . $type . '_chart_datalabel_show'] ?>',
                    enabledOnSeries: '<?php echo $dataLabelEnabledOn; ?>'.split('_,_').map(v => parseInt(v)),
                    style: {
                        colors: '<?php echo $color1; ?>'.split('_,_'),
                    }
                }
            };

            mixedOptions['markers'] ={
                size: '<?php echo $markerSize; ?>'.split('_,_'),
                strokeColors: '<?php echo $markerStrokeColor; ?>'.split('_,_'),
                strokeWidth: '<?php echo $markerStokeWidth; ?>'.split('_,_'),
                shape: '<?php echo $markerShape; ?>'.split('_,_'),
                showNullDataPoints: true,
                hover: {
                    size: 3,
                    sizeOffset: 1
                }
            }

            initNowGraphina(
                myElement,
                {
                    ele: document.querySelector(".mixed-chart-<?php esc_attr_e($mainId); ?>"),
                    options: mixedOptions
                },
                '<?php esc_attr_e($mainId); ?>'
            );

        </script>
        <?php
        }
    }

}

Plugin::instance()->widgets_manager->register_widget_type(new Mixed_chart());