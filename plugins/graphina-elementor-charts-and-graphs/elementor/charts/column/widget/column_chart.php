<?php

namespace Elementor;
if (!defined('ABSPATH')) exit;

/**
 * Elementor Blog widget.
 *
 * Elementor widget that displays an eye-catching headlines.
 *
 * @since 1.5.7
 */
class Column_chart extends Widget_Base
{

    /**
     * Get widget name.
     *
     * Retrieve heading widget name.
     *
     * @return string Widget name.
     * @since 1.5.7
     * @access public
     *
     */

    public function get_name()
    {
        return 'column_chart';
    }

    /**
     * Get widget Title.
     *
     * Retrieve heading widget Title.
     *
     * @return string Widget Title.
     * @since 1.5.7
     * @access public
     *
     */

    public function get_title()
    {
        return 'Column';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the heading widget belongs to.
     *
     * Used to determine where to display the widget in the editor.
     *
     * @return array Widget categories.
     * @since 1.5.7
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
     * @since 1.5.7
     * @access public
     *
     */

    public function get_icon()
    {
        return 'fas fa-chart-bar';
    }

    public function get_chart_type()
    {
        return 'column';
    }

    protected function _register_controls()
    {
        $type = $this->get_chart_type();

        graphina_basic_setting($this, $type);

        graphina_chart_data_option_setting($this, $type, 0, true);

        $this->start_controls_section(
            'iq_' . $type . '_section_2',
            [
                'label' => esc_html__('Chart Setting', 'graphina-lang'),
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'iq_' . $type . '_chart_is_pro',
                                    'operator' => '==',
                                    'value' => 'false'
                                ],
                                [
                                    'name' => 'iq_' . $type . '_chart_data_option',
                                    'operator' => '==',
                                    'value' => 'manual'
                                ]
                            ]
                        ],
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'iq_' . $type . '_chart_is_pro',
                                    'operator' => '==',
                                    'value' => 'true'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        graphina_common_chart_setting($this, $type, false, true, true);

        graphina_tooltip($this, $type);

        $this->add_control(
            'iq_' . $type . '_is_chart_stroke_width',
            [
                'label' => esc_html__('Column Width', 'graphina-lang'),
                'type' => Controls_Manager::NUMBER,
                'default' => 50,
                'min' => 1,
                'max' => 100,
                'step' => 10
            ]
        );

        $this->add_responsive_control(
            'iq_' . $type . '_is_chart_horizontal',
            [
                'label' => esc_html__('Horizontal', 'graphina-lang'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'graphina-lang'),
                'label_off' => esc_html__('No', 'graphina-lang'),
                'desktop_default' => false,
                'tablet_default' => false,
                'mobile_default' => false,
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_stacked',
            [
                'label' => esc_html__('Stacked Columns', 'graphina-lang'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'graphina-lang'),
                'label_off' => esc_html__('No', 'graphina-lang'),
                'default' => false,
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_stack_type',
            [
                'label' => esc_html__('Stack Type', 'graphina-lang'),
                'type' => Controls_Manager::SELECT,
                'default' => 'normal',
                'options' => [
                    'normal' => esc_html__('Normal', 'graphina-lang'),
                    '100%' => esc_html__('Percentage', 'graphina-lang'),
                ],
                'condition' => [
                    'iq_' . $type . '_chart_stacked' => 'yes',
                ]
            ]
        );

        graphina_dropshadow($this, $type);

        graphina_animation($this, $type);

        $this->add_control(
            'iq_' . $type . '_chart_hr_plot_setting',
            [
                'type' => Controls_Manager::DIVIDER,
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_plot_setting_title',
            [
                'label' => esc_html__('Plot Settings', 'graphina-lang'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_plot_start_shape',
            [
                'label' => esc_html__('Starting Shape', 'graphina-lang'),
                'type' => Controls_Manager::SELECT,
                'default' => 'flat',
                'options' => [
                    'flat' => esc_html__('Flat', 'graphina-lang'),
                    'rounded' => esc_html__('Rounded', 'graphina-lang'),
                ],
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_plot_end_shape',
            [
                'label' => esc_html__('Ending Shape', 'graphina-lang'),
                'type' => Controls_Manager::SELECT,
                'default' => 'flat',
                'options' => [
                    'flat' => esc_html__('Flat', 'graphina-lang'),
                    'rounded' => esc_html__('Rounded', 'graphina-lang'),
                ],
            ]
        );

        $this->add_control(
            'iq_' . $type . '_chart_hr_category_listing',
            [
                'type' => Controls_Manager::DIVIDER,
                'condition' => [
                    'iq_' . $type . '_chart_data_option' => 'manual'
                ],
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
                'label' => esc_html__('Categories', 'graphina-lang'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    ['iq_' . $type . '_chart_category' => 'Jan'],
                    ['iq_' . $type . '_chart_category' => 'Feb'],
                    ['iq_' . $type . '_chart_category' => 'Mar'],
                    ['iq_' . $type . '_chart_category' => 'Apr'],
                    ['iq_' . $type . '_chart_category' => 'May'],
                    ['iq_' . $type . '_chart_category' => 'Jun'],
                    ['iq_' . $type . '_chart_category' => 'July'],
                    ['iq_' . $type . '_chart_category' => 'Aug'],
                ],
            ]
        );

        $this->end_controls_section();

        graphina_chart_label_setting($this, $type);

        graphina_advance_x_axis_setting($this, $type);

        graphina_advance_y_axis_setting($this, $type);

        graphina_series_setting($this, $type, ['tooltip', 'color'], true, ['classic', 'gradient', 'pattern'], true, true);

        for ($i = 0; $i < 10; $i++) {
            $this->start_controls_section(
                'iq_' . $type . '_section_4_' . $i,
                [
                    'label' => esc_html__('Element ' . ($i + 1), 'graphina-lang'),
                    'condition' => [
                        'iq_' . $type . '_chart_data_series_count' => range(1 + $i, 10),
                        'iq_' . $type . '_chart_data_option' => 'manual'
                    ],
                    'conditions' => [
                        'relation' => 'or',
                        'terms' => [
                            [
                                'relation' => 'and',
                                'terms' => [
                                    [
                                        'name' => 'iq_' . $type . '_chart_is_pro',
                                        'operator' => '==',
                                        'value' => 'false'
                                    ],
                                    [
                                        'name' => 'iq_' . $type . '_chart_data_option',
                                        'operator' => '==',
                                        'value' => 'manual'
                                    ]
                                ]
                            ],
                            [
                                'relation' => 'and',
                                'terms' => [
                                    [
                                        'name' => 'iq_' . $type . '_chart_is_pro',
                                        'operator' => '==',
                                        'value' => 'true'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            );

            $this->add_control(
                'iq_' . $type . '_chart_title_4_' . $i,
                [
                    'label' => esc_html__('Element Title', 'graphina-lang'),
                    'type' => Controls_Manager::TEXT,
                    'placeholder' => esc_html__('Add Tile', 'graphina-lang'),
                    'default' => 'Element ' . ($i + 1),
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );

            $repeater = new Repeater();

            $repeater->add_control(
                'iq_' . $type . '_chart_value_4_' . $i,
                [
                    'label' => esc_html__('Chart Value', 'graphina-lang'),
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
                    'label' => esc_html__('Chart value list', 'graphina-lang'),
                    'type' => Controls_Manager::REPEATER,
                    'fields' => $repeater->get_controls(),
                    'default' => [
                        ['iq_' . $type . '_chart_value_4_' . $i => rand(100, 200)],
                        ['iq_' . $type . '_chart_value_4_' . $i => rand(100, 200)],
                        ['iq_' . $type . '_chart_value_4_' . $i => rand(100, 200)],
                        ['iq_' . $type . '_chart_value_4_' . $i => rand(100, 200)],
                        ['iq_' . $type . '_chart_value_4_' . $i => rand(100, 200)],
                        ['iq_' . $type . '_chart_value_4_' . $i => rand(100, 200)],
                        ['iq_' . $type . '_chart_value_4_' . $i => rand(100, 200)],
                        ['iq_' . $type . '_chart_value_4_' . $i => rand(100, 200)],
                    ],
                    'condition' => [
                        'iq_' . $type . '_can_chart_negative_values!' => 'yes'
                    ],
                    'title_field' => '{{{ iq_' . $type . '_chart_value_4_' . $i . ' }}}',
                ]
            );
            /** Chart value list. */

            /** Chart value negative list. */
            $this->add_control(
                'iq_' . $type . '_value_list_4_2_' . $i,
                [
                    'label' => esc_html__('Chart value list', 'graphina-lang'),
                    'type' => Controls_Manager::REPEATER,
                    'fields' => $repeater->get_controls(),
                    'default' => [
                        ['iq_' . $type . '_chart_value_4_' . $i => rand(-200, 200)],
                        ['iq_' . $type . '_chart_value_4_' . $i => rand(-200, 200)],
                        ['iq_' . $type . '_chart_value_4_' . $i => rand(-200, 200)],
                        ['iq_' . $type . '_chart_value_4_' . $i => rand(-200, 200)],
                        ['iq_' . $type . '_chart_value_4_' . $i => rand(-200, 200)],
                        ['iq_' . $type . '_chart_value_4_' . $i => rand(-200, 200)],
                        ['iq_' . $type . '_chart_value_4_' . $i => rand(-200, 200)],
                        ['iq_' . $type . '_chart_value_4_' . $i => rand(-200, 200)],
                    ],
                    'condition' => [
                        'iq_' . $type . '_can_chart_negative_values' => 'yes'
                    ],
                    'title_field' => '{{{ iq_' . $type . '_chart_value_4_' . $i . ' }}}',
                ]
            );
            /** Chart value negative list. */

            $this->end_controls_section();

        }

        graphina_style_section($this, $type);

        graphina_card_style($this, $type);

        graphina_chart_style($this, $type);

        if (function_exists('graphina_pro_password_style_section')) {
            graphina_pro_password_style_section($this, $type);
        }

    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $mainId = $this->get_id();
        $type = $this->get_chart_type();
        $gradient = [];
        $second_gradient = [];
        $fill_pattern = [];
        $datalables_offset_y = 0;
        $datalables_offset_x = 0;
        $dropshadowSeries = [];
        $tooltipSeries = [];
        $data = ['series' => [], 'category' => []];
        $dataLabelPrefix = $dataLabelPostfix = $yLabelPrefix = $yLabelPostfix = $xLabelPrefix = $xLabelPostfix = '';
        $callAjax = false;
        $loadingText = esc_html__((isset($settings['iq_' . $type . '_chart_no_data_text']) ? $settings['iq_' . $type . '_chart_no_data_text'] : ''), 'graphina-lang');

        $exportFileName = (
            !empty($settings['iq_' . $type . '_can_chart_show_toolbar']) && $settings['iq_' . $type . '_can_chart_show_toolbar'] === 'yes'
            && !empty($settings['iq_' . $type . '_export_filename'])
        ) ? $settings['iq_' . $type . '_export_filename'] : $mainId;

        if ($settings['iq_' . $type . '_chart_datalabel_show'] === 'yes') {
            $dataLabelPrefix = $settings['iq_' . $type . '_chart_datalabel_prefix'];
            $dataLabelPostfix = $settings['iq_' . $type . '_chart_datalabel_postfix'];
        }

        if ($settings['iq_' . $type . '_chart_xaxis_label_show'] === 'yes') {
            $xLabelPrefix = $settings['iq_' . $type . '_chart_xaxis_label_prefix'];
            $xLabelPostfix = $settings['iq_' . $type . '_chart_xaxis_label_postfix'];
        }

        if ($settings['iq_' . $type . '_chart_yaxis_label_show'] === 'yes') {
            $yLabelPrefix = $settings['iq_' . $type . '_chart_yaxis_label_prefix'];
            $yLabelPostfix = $settings['iq_' . $type . '_chart_yaxis_label_postfix'];
        }

        $seriesCount = isset($settings['iq_' . $type . '_chart_data_series_count']) ? $settings['iq_' . $type . '_chart_data_series_count'] : 0;
        for ($i = 0; $i < $seriesCount; $i++) {
            $dropShadowSeries[] = $i;
            if (!empty($settings['iq_' . $type . '_chart_tooltip_enabled_on_1_' . $i]) && $settings['iq_' . $type . '_chart_tooltip_enabled_on_1_' . $i] === "yes") {
                $tooltipSeries[] = $i;
            }
            $gradient[] = strval($settings['iq_' . $type . '_chart_gradient_1_' . $i]);
            if (strval($settings['iq_' . $type . '_chart_gradient_2_' . $i]) === '') {
                $second_gradient[] = strval($settings['iq_' . $type . '_chart_gradient_1_' . $i]);
            } else {
                $second_gradient[] = strval($settings['iq_' . $type . '_chart_gradient_2_' . $i]);
            }
            if ($settings['iq_' . $type . '_chart_bg_pattern_' . $i] !== '') {
                $fill_pattern[] = $settings['iq_' . $type . '_chart_bg_pattern_' . $i];
            } else {
                $fill_pattern[] = 'verticalLines';
            }
        }

        $categoryList = $settings['iq_' . $type . '_category_list'];

        if (isGraphinaPro() && $settings['iq_' . $type . '_chart_data_option'] !== 'manual') {
            $new_settings = graphina_setting_sort($settings);
            $callAjax = true;
            $gradient = $second_gradient = ['#ffffff'];
            $loadingText = esc_html__('Loading...', 'graphina-lang');
        } else {
            $new_settings = [];
            if (gettype($categoryList) === "NULL") {
                $categoryList = [];
            }
            foreach ($categoryList as $v) {
                $data["category"][] = (string)graphina_get_dynamic_tag_data($v, 'iq_' . $type . '_chart_category');
            }
            for ($i = 0; $i < $seriesCount; $i++) {
                $valueList = $settings['iq_' . $type . '_value_list_4_' . ($settings['iq_' . $type . '_can_chart_negative_values'] === 'yes' ? 2 : 1) . '_' . $i];
                $value = [];
                if (gettype($valueList) === "NULL") {
                    $valueList = [];
                }
                foreach ($valueList as $v) {
                    $value[] = (float)graphina_get_dynamic_tag_data($v, 'iq_' . $type . '_chart_value_4_' . $i);
                }
                $data['series'][] = [
                    'name' => (string)graphina_get_dynamic_tag_data($settings, 'iq_' . $type . '_chart_title_4_' . $i),
                    'data' => $value,
                    'color' => strval($settings['iq_' . $type . '_chart_gradient_1_' . $i])
                ];
            }
            if ($settings['iq_' . $type . '_chart_data_option'] !== 'manual') {
                $data = ['series' => [], 'category' => []];
            }
            $gradient_new = $second_gradient_new = $fill_pattern_new = [];
            $desiredLength = count($data['series']);
            while (count($gradient_new) < $desiredLength) {
                $gradient_new = array_merge($gradient_new, $gradient);
                $second_gradient_new = array_merge($second_gradient_new, $second_gradient);
                $fill_pattern_new = array_merge($fill_pattern_new, $fill_pattern);
            }
            $gradient = array_slice($gradient_new, 0, $desiredLength);
            $second_gradient = array_slice($second_gradient_new, 0, $desiredLength);
            $fill_pattern = array_slice($fill_pattern_new, 0, $desiredLength);
        }

        $gradient = implode('_,_', $gradient);
        $second_gradient = implode('_,_', $second_gradient);
        $fill_pattern = implode('_,_', $fill_pattern);
        $dropshadowSeries = implode(',', $dropshadowSeries);
        $tooltipSeries = implode(',', $tooltipSeries);
        $category = implode('_,_', $data['category']);
        $chartDataJson = json_encode($data['series']);

        if ($settings['iq_' . $type . '_chart_datalabel_position_show'] == "top" && $settings['iq_' . $type . '_is_chart_horizontal'] == "yes") {
            $datalables_offset_x = 20;
        } elseif ($settings['iq_' . $type . '_chart_datalabel_position_show'] == "top" && $settings['iq_' . $type . '_is_chart_horizontal'] != "yes") {
            $datalables_offset_y = -20;
        }
        require GRAPHINA_ROOT . '/elementor/charts/column/render/column_chart.php';
        if (isRestrictedAccess('column', $this->get_id(), $settings, false) === false) {
            ?>
            <script>
                var myElement = document.querySelector(".column-chart-<?php esc_attr_e($mainId); ?>");

                if (typeof isInit === 'undefined') {
                    var isInit = {};
                }
                isInit['<?php esc_attr_e($mainId); ?>'] = false;

                var columnOptions = {
                    series: <?php echo $chartDataJson ?>,
                    chart: {
                        background: '<?php echo strval($settings['iq_' . $type . '_chart_background_color1']); ?>',
                        height: parseInt('<?php echo $settings['iq_' . $type . '_chart_height']; ?>'),
                        type: 'bar',
                        stacked: '<?php echo $settings['iq_' . $type . '_chart_stacked']; ?>',
                        stackType: '<?php echo $settings['iq_' . $type . '_chart_stack_type']; ?>',
                        animations: {
                            enabled: '<?php echo($settings['iq_' . $type . '_chart_animation'] === "yes"); ?>',
                            speed: '<?php echo $settings['iq_' . $type . '_chart_animation_speed']; ?>',
                            //delay: '<?php //echo $settings['iq_' . $type . '_chart_animation_delay'] ?>//'
                        },
                        toolbar: {
                            show: '<?php echo $settings['iq_' . $type . '_can_chart_show_toolbar']; ?>',
                            export: {
                                csv: {
                                    filename: "<?php echo $exportFileName; ?>"
                                },
                                svg: {
                                    filename: "<?php echo $exportFileName; ?>"
                                },
                                png: {
                                    filename: "<?php echo $exportFileName; ?>"
                                }
                            }
                        },
                        dropShadow: {
                            enabled: '<?php echo($settings['iq_' . $type . '_is_chart_dropshadow'] === "yes") ?>',
                            enabledOnSeries: [<?php esc_html_e($dropshadowSeries); ?>],
                            top: parseInt('<?php echo $settings['iq_' . $type . '_is_chart_dropshadow_top'] ?>'),
                            left: parseInt('<?php echo $settings['iq_' . $type . '_is_chart_dropshadow_left'] ?>'),
                            blur: parseInt('<?php echo $settings['iq_' . $type . '_is_chart_dropshadow_blur'] ?>'),
                            color: '<?php echo strval(isset($settings['iq_' . $type . '_is_chart_dropshadow_color']) ? $settings['iq_' . $type . '_is_chart_dropshadow_color'] : ''); ?>',
                            opacity: parseFloat('<?php echo $settings['iq_' . $type . '_is_chart_dropshadow_opacity'] ?>')
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: '<?php echo $settings['iq_' . $type . '_is_chart_horizontal'] ?>',
                            columnWidth: '<?php echo $settings['iq_' . $type . '_is_chart_stroke_width'] ?>% ',
                            startingShape: '<?php echo $settings['iq_' . $type . '_chart_plot_start_shape'] ?>',
                            endingShape: '<?php echo $settings['iq_' . $type . '_chart_plot_end_shape'] ?>',
                            dataLabels: {
                                position: '<?php echo $settings['iq_' . $type . '_chart_datalabel_position_show'] ?>',
                            }
                        },
                    },
                    noData: {
                        text: '<?php echo $loadingText; ?>',
                        align: 'center',
                        verticalAlign: 'middle',
                        style: {
                            fontSize: '<?php echo $settings['iq_' . $type . '_chart_font_size']['size'] . $settings['iq_' . $type . '_chart_font_size']['unit'] ?>',
                            fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family'] ?>',
                            color: '<?php echo strval($settings['iq_' . $type . '_chart_font_color']) ?>'
                        }
                    },
                    dataLabels: {
                        enabled: '<?php echo $settings['iq_' . $type . '_chart_datalabel_show'] === "yes"; ?>',
                        offsetY: parseFloat('<?php echo $datalables_offset_y ?>'),
                        offsetX: parseFloat('<?php echo $datalables_offset_x ?>'),
                        style: {
                            fontSize: '<?php echo $settings['iq_' . $type . '_chart_font_size']['size'] . $settings['iq_' . $type . '_chart_font_size']['unit']; ?>',
                            fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family']; ?>',
                            fontWeight: '<?php echo $settings['iq_' . $type . '_chart_font_weight']; ?>',
                            colors: ['<?php echo $settings['iq_' . $type . '_chart_datalabel_background_show'] === "yes" ? strval($settings['iq_' . $type . '_chart_datalabel_font_color_1']) : strval($settings['iq_' . $type . '_chart_datalabel_font_color']); ?>']
                        },
                        background: {
                            enabled: '<?php echo $settings['iq_' . $type . '_chart_datalabel_background_show'] === "yes"; ?>',
                            borderRadius:parseInt('<?php echo !empty($settings['iq_' . $type . '_chart_datalabel_border_radius']) ? $settings['iq_' . $type . '_chart_datalabel_border_radius'] : 0 ?>'),
                            foreColor: ['<?php echo strval($settings['iq_' . $type . '_chart_datalabel_background_color']); ?>'],
                            borderWidth: parseInt('<?php echo $settings['iq_' . $type . '_chart_datalabel_border_width']; ?>') || 0,
                            borderColor: '<?php echo strval($settings['iq_' . $type . '_chart_datalabel_border_color']); ?>'
                        },
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    grid: {
                        borderColor: '<?php echo !empty($settings['iq_' . $type . '_chart_yaxis_line_grid_color'])  ? strval($settings['iq_' . $type . '_chart_yaxis_line_grid_color']) : '#90A4AE'; ?>',
                        yaxis: {
                            lines: {
                                show: '<?php echo $settings['iq_' . $type . '_chart_yaxis_line_show'] ?>'
                            }
                        }
                    },
                    xaxis: {
                        categories: '<?php echo $category; ?>'.split('_,_'),
                        position: '<?php esc_html_e($settings['iq_' . $type . '_chart_xaxis_datalabel_position']) ?>',
                        tickAmount: parseInt("<?php esc_html_e($settings['iq_' . $type . '_chart_xaxis_datalabel_tick_amount']); ?>"),
                        tickPlacement: "<?php esc_html_e($settings['iq_' . $type . '_chart_xaxis_datalabel_tick_placement']) ?>",
                        labels: {
                            show: '<?php echo $settings['iq_' . $type . '_chart_xaxis_datalabel_show'] ?>',
                            rotateAlways: '<?php echo $settings['iq_' . $type . '_chart_xaxis_datalabel_auto_rotate'] ?>',
                            rotate: parseInt('<?php echo $settings['iq_' . $type . '_chart_xaxis_datalabel_rotate'] ?>') || 0,
                            offsetX: parseInt('<?php echo $settings['iq_' . $type . '_chart_xaxis_datalabel_offset_x'] ?>')|| 0,
                            offsetY: parseInt('<?php echo $settings['iq_' . $type . '_chart_xaxis_datalabel_offset_y'] ?>') || 0,
                            trim: true,
                            style: {
                                colors: '<?php echo strval($settings['iq_' . $type . '_chart_font_color']) ?>',
                                fontSize: '<?php echo $settings['iq_' . $type . '_chart_font_size']['size'] . $settings['iq_' . $type . '_chart_font_size']['unit'] ?>',
                                fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family'] ?>',
                                fontWeight: '<?php echo $settings['iq_' . $type . '_chart_font_weight'] ?>'
                            },
                            formatter: function (val) {
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
                    yaxis: {
                        opposite: '<?php esc_html_e($settings['iq_' . $type . '_chart_yaxis_datalabel_position']) ?>',
                        decimalsInFloat: parseInt("<?php esc_html_e($settings['iq_' . $type . '_chart_yaxis_datalabel_decimals_in_float']); ?>"),
                        labels: {
                            show: '<?php echo $settings['iq_' . $type . '_chart_yaxis_datalabel_show'] ?>',
                            rotate: parseInt('<?php echo $settings['iq_' . $type . '_chart_yaxis_datalabel_rotate'] ?>') || 0,
                            offsetX: parseInt('<?php echo $settings['iq_' . $type . '_chart_yaxis_datalabel_offset_x'] ?>') || 0,
                            offsetY: parseInt('<?php echo $settings['iq_' . $type . '_chart_yaxis_datalabel_offset_y'] ?>') || 0,
                            style: {
                                colors: '<?php echo strval($settings['iq_' . $type . '_chart_font_color']) ?>',
                                fontSize: '<?php echo $settings['iq_' . $type . '_chart_font_size']['size'] . $settings['iq_' . $type . '_chart_font_size']['unit'] ?>',
                                fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family'] ?>',
                                fontWeight: '<?php echo $settings['iq_' . $type . '_chart_font_weight'] ?>'
                            }
                        },
                        tooltip: {
                            enabled: "<?php echo !empty($settings['iq_' . $type . '_chart_yaxis_tooltip_show']) && $settings['iq_' . $type . '_chart_yaxis_tooltip_show'] === 'yes';?>"
                        },
                        crosshairs: {
                            show: "<?php echo !empty($settings['iq_' . $type . '_chart_yaxis_crosshairs_show']) && $settings['iq_' . $type . '_chart_yaxis_crosshairs_show'] === 'yes';?>"
                        }
                    },
                    colors: '<?php echo $gradient; ?>'.split('_,_'),
                    fill: {
                        type: '<?php echo $settings['iq_' . $type . '_chart_fill_style_type'] ?>',
                        opacity: parseFloat('<?php echo $settings['iq_' . $type . '_chart_fill_opacity'] ?>'),
                        colors: '<?php echo $gradient; ?>'.split('_,_'),
                        gradient: {
                            gradientToColors: '<?php echo $second_gradient; ?>'.split('_,_'),
                            type: '<?php echo $settings['iq_' . $type . '_chart_gradient_type'] ?>',
                            inverseColors: '<?php echo $settings['iq_' . $type . '_chart_gradient_inversecolor'] ?>',
                            opacityFrom: parseFloat('<?php echo $settings['iq_' . $type . '_chart_gradient_opacityFrom'] ?>'),
                            opacityTo: parseFloat('<?php echo $settings['iq_' . $type . '_chart_gradient_opacityTo'] ?>')
                        },
                        pattern: {
                            style: '<?php echo $fill_pattern ?>'.split('_,_'),
                            width: 6,
                            height: 6,
                            strokeWidth: 2
                        }
                    },
                    legend: {
                        showForSingleSeries:true,
                        show: '<?php echo $settings['iq_' . $type . '_chart_legend_show'] ?>',
                        position: '<?php echo !empty($settings['iq_' . $type . '_chart_legend_position']) ? esc_html_e($settings['iq_' . $type . '_chart_legend_position']) : 'bottom' ; ?>',
                        horizontalAlign: '<?php !empty($settings['iq_' . $type . '_chart_legend_horizontal_align']) ? esc_html_e($settings['iq_' . $type . '_chart_legend_horizontal_align']) : 'center' ; ?>',
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
                        style: {
                            fontSize: '<?php echo $settings['iq_' . $type . '_chart_font_size']['size'] . $settings['iq_' . $type . '_chart_font_size']['unit'] ?>',
                            fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family'] ?>'
                        }
                    },
                    responsive: [{
                        breakpoint: 1024,
                        options: {
                            chart: {
                                height: parseInt('<?php echo !empty($settings['iq_' . $type . '_chart_height_tablet']) ? $settings['iq_' . $type . '_chart_height_tablet'] : $settings['iq_' . $type . '_chart_height'] ; ?>')
                            },
                            plotOptions: {
                                bar: {
                                    horizontal: '<?php echo !empty($settings['iq_' . $type . '_is_chart_horizontal_tablet'])  && $settings['iq_' . $type . '_is_chart_horizontal_tablet'] === "yes" ; ?>'
                                }
                            }
                        }
                    },
                        {
                            breakpoint: 674,
                            options: {
                                chart: {
                                    height: parseInt('<?php echo !empty($settings['iq_' . $type . '_chart_height_mobile']) ? $settings['iq_' . $type . '_chart_height_mobile'] : $settings['iq_' . $type . '_chart_height'] ;  ?>')
                                },
                                plotOptions: {
                                    bar: {
                                        horizontal: '<?php echo !empty($settings['iq_' . $type . '_is_chart_horizontal_mobile'])  && $settings['iq_' . $type . '_is_chart_horizontal_mobile'] === "yes" ; ?>'
                                    }
                                }
                            }
                        }
                    ]
                };

                if ("<?php esc_html_e($settings['iq_' . $type . '_chart_yaxis_label_show']); ?>" === "yes") {
                    columnOptions.yaxis.labels.formatter = function (val) {
                        if("<?php esc_html_e($settings['iq_' . $type . '_chart_yaxis_format_number']); ?>" === "yes"){
                            val = val.toLocaleString()
                        }
                        else if("<?php !empty($settings['iq_' . $type . '_chart_yaxis_label_pointer']) && esc_html_e($settings['iq_' . $type . '_chart_yaxis_label_pointer']); ?>" === 'yes' 
                        &&  typeof graphinaAbbrNum  !== "undefined"){      
                            val = graphinaAbbrNum(val ,  parseInt("<?php esc_html_e($settings['iq_' . $type . '_chart_yaxis_label_pointer_number']); ?>") || 0 );
                        }
                        return '<?php esc_html_e($yLabelPrefix); ?>' + val + '<?php esc_html_e($yLabelPostfix); ?>';
                    }
                }
                if("<?php esc_html_e($settings['iq_' . $type . '_chart_stack_type']); ?>" !== '100%'){
                    columnOptions.yaxis.tickAmount = parseInt("<?php esc_html_e($settings['iq_' . $type . '_chart_yaxis_datalabel_tick_amount']); ?>");
                    columnOptions.dataLabels.formatter = function (val) {
                        if("<?php !empty($settings['iq_' . $type . '_chart_number_format_commas']) &&  esc_html_e($settings['iq_' . $type . '_chart_number_format_commas']); ?>" === "yes"){
                            val = val.toLocaleString()
                        }
                        else if("<?php !empty($settings['iq_' . $type . '_chart_yaxis_label_pointer']) && esc_html_e($settings['iq_' . $type . '_chart_yaxis_label_pointer']); ?>" === 'yes' 
                            &&  typeof graphinaAbbrNum  !== "undefined"){      
                                val = graphinaAbbrNum(val ,  parseInt("<?php esc_html_e($settings['iq_' . $type . '_chart_yaxis_label_pointer_number']); ?>") || 0 );
                            }
                        return '<?php esc_html_e($dataLabelPrefix) ?>' + val + '<?php esc_html_e($dataLabelPostfix) ?>';
                    };
                }
                if ("<?php echo !empty($settings['iq_' . $type . '_chart_tooltip_shared']) ? $settings['iq_' . $type . '_chart_tooltip_shared'] : '';?>" === "yes") {
                    columnOptions.tooltip['enabledOnSeries'] = [<?php esc_html_e($tooltipSeries); ?>];
                }
                if ("<?php esc_html_e($settings['iq_' . $type . '_chart_yaxis_0_indicator_show']); ?>" === "yes") {
                    columnOptions['annotations'] = {
                        yaxis: [
                            {
                                y: 0,
                                strokeDashArray: parseInt("<?php echo !empty($settings['iq_' . $type . '_chart_yaxis_0_indicator_stroke_dash']) ? $settings['iq_' . $type . '_chart_yaxis_0_indicator_stroke_dash'] : 0; ?>"),
                                borderColor: '<?php echo !empty($settings['iq_' . $type . '_chart_yaxis_0_indicator_stroke_color']) ? strval($settings['iq_' . $type . '_chart_yaxis_0_indicator_stroke_color']) : "#000000"; ?>'
                            }
                        ]
                    };
                }

                if("<?php echo $settings['iq_' . $type . '_chart_xaxis_title_enable'] == 'yes' ;?>"){
                    let style ={
                        color:'<?php echo strval($settings['iq_' . $type . '_chart_font_color']); ?>',
                        fontSize: '<?php echo $settings['iq_' . $type . '_chart_font_size']['size'] . $settings['iq_' . $type . '_chart_font_size']['unit']; ?>',
                        fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family']; ?>',
                        fontWeight: '<?php echo $settings['iq_' . $type . '_chart_font_weight']; ?>',
                    }
                    let title = '<?php echo strval($settings['iq_' . $type . '_chart_xaxis_title']); ?>';

                    if(typeof axisTitle !== "undefined"){
                        axisTitle(columnOptions, 'xaxis' ,title, style );
                    }
                }

                if("<?php echo $settings['iq_' . $type . '_chart_yaxis_title_enable'] == 'yes' ; ?>"){
                    let style ={
                        color:'<?php echo strval($settings['iq_' . $type . '_card_yaxis_title_font_color']); ?>',
                        fontSize: '<?php echo $settings['iq_' . $type . '_chart_font_size']['size'] . $settings['iq_' . $type . '_chart_font_size']['unit']; ?>',
                        fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family']; ?>',
                        fontWeight: '<?php echo $settings['iq_' . $type . '_chart_font_weight']; ?>',
                    }
                    let title = '<?php echo strval($settings['iq_' . $type . '_chart_yaxis_title']); ?>';
                    if(typeof axisTitle !== "undefined"){
                        axisTitle(columnOptions, 'yaxis' ,title, style );
                    }
                }

                if("<?php echo !empty($settings['iq_' . $type . '_chart_opposite_yaxis_title_enable']) && $settings['iq_' . $type . '_chart_opposite_yaxis_title_enable'] == 'yes' ;  ?>"){
                    let style = {
                        color:'<?php echo strval($settings['iq_' . $type . '_card_opposite_yaxis_title_font_color']); ?>',
                        fontSize: '<?php echo $settings['iq_' . $type . '_chart_font_size']['size'] . $settings['iq_' . $type . '_chart_font_size']['unit']; ?>',
                        fontFamily: '<?php echo $settings['iq_' . $type . '_chart_font_family']; ?>',
                        fontWeight: '<?php echo $settings['iq_' . $type . '_chart_font_weight']; ?>',
                    }
                    columnOptions['yaxis'] = [columnOptions.yaxis]
                    columnOptions.yaxis.push({
                        opposite: '<?php echo $settings['iq_'.$type.'_chart_yaxis_datalabel_position'] === 'yes' ? false : true ; ?>',
                        labels: {
                            show: '<?php echo $settings['iq_' . $type . '_chart_opposite_yaxis_label_show'] === 'yes'; ?>',
                            formatter: function (val) {
                                if("<?php esc_html_e($settings['iq_' . $type . '_chart_opposite_yaxis_format_number']); ?>" === "yes"){
                                    val = val.toLocaleString()
                                }
                                return '<?php echo $settings['iq_' .$type . '_chart_opposite_yaxis_label_prefix'] ;?>'  + val + '<?php echo $settings['iq_' .$type . '_chart_opposite_yaxis_label_postfix'] ;?>'
                            },
                            style
                        },
                        tickAmount: parseInt('<?php echo $settings['iq_' . $type . '_chart_opposite_yaxis_tick_amount']; ?>'),
                        title: {
                            text: '<?php echo $settings['iq_' .$type . '_chart_opposite_yaxis_title'] ;?>',
                            style
                        }
                    })
                }

                if (typeof initNowGraphina !== "undefined") {
                    initNowGraphina(
                        myElement,
                        {
                            ele: document.querySelector(".column-chart-<?php esc_attr_e($mainId); ?>"),
                            options: columnOptions,
                            series: [{name: '', data: []}],
                            animation: true
                        },
                        '<?php esc_attr_e($mainId); ?>'
                    );
                }
                if (window.ajaxIntervalGraphina_<?php echo $mainId; ?> !== undefined) {
                    clearInterval(window.ajaxIntervalGraphina_<?php echo $mainId; ?>)
                }
            </script>
            <?php
        }
        if (isGraphinaPro() && $settings['iq_' . $type . '_chart_data_option'] !== 'manual') {
            graphina_ajax_reload($callAjax, $new_settings, $type, $mainId);
        }
    }
}

Plugin::instance()->widgets_manager->register_widget_type(new Column_chart());