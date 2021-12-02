<?php

use Elementor\Controls_Manager;
use Elementor\Plugin;

function graphina_pro_chart_content($settings, $mainId, $type, $structure_type)
{
    $data = ['series' => [], 'category' => []];

    $dataTypeOption = $settings['iq_' . $type . '_chart_dynamic_data_option'];
    switch ($dataTypeOption) {
        case "csv":
            $data = graphina_pro_parse_csv($settings, $type, $structure_type);
            break;
        case "google-sheet" :
        case "remote-csv" :
            $data = graphina_pro_get_data_from_url($type, $settings, $dataTypeOption, $mainId, $structure_type);
            break;
        case "api":
            $data = graphina_pro_chart_get_data_from_api($type, $settings, $structure_type);
            break;
        case "sql-builder":
            $data = graphina_pro_chart_get_data_from_sql_builder($settings, $type);
            break;
    }

    $data = apply_filters('graphina_addons_render_section', $data, $type, $settings);

    if (isset($data['fail']) && $data['fail'] === 'permission') {
        $dataTypeOption = $settings['iq_' . $type . '_chart_dynamic_data_option'];
        switch ($dataTypeOption) {
            case "google-sheet" :
                $data['fail_message'] = "<pre><b>" . esc_html__('Please check file sharing permission and "Publish As" type is CSV or not. ', 'graphina-lang') . "</b><small><a target='_blank' href='https://youtu.be/Dv8s4QxZlDk'>" . esc_html__('Click for reference.', 'graphina-lang') . "</a></small></pre>";
                break;
            case "remote-csv" :
            default:
                $data['fail_message'] = "<pre><b>" . (isset($data['errorMessage']) ? $data['errorMessage'] :  esc_html__('Please check file sharing permission.', 'graphina-lang')). "</b></pre>";
                break;
        }
    }
    return $data;
}

/****************
 * @param bool $first
 * @return array|string
 */
function graphina_pro_mixed_chart_typeList($first = false, $revese = false)
{
    $charts = [
        "bar" => esc_html__('Column', 'graphina-lang'),
        "line" => esc_html__('Line', 'graphina-lang'),
        "area" => esc_html__('Area', 'graphina-lang'),
    ];
    if ($revese) {
        $charts = array_reverse($charts);
    }
    $keys = array_keys($charts);
    return $first ? (count($keys) > 0 ? $keys[0] : '') : $charts;
}

/****************
 * @return array
 */
function graphina_pro_gradient_type()
{
    return [
        "horizontal" => esc_html__('Horizontal', 'graphina-lang'),
        "vertical" => esc_html__('Vertical', 'graphina-lang'),
        "diagonal1" => esc_html__('Diagonal1', 'graphina-lang'),
        "diagonal2" => esc_html__('Diagonal2', 'graphina-lang')
    ];
}

/****************
 * @param $data
 * @param $i
 * @return mixed
 */
function graphina_pro_get_random_chart_type($data, $i)
{
    $index = $i % count($data);
    $keys = array_keys($data);
    return $keys[$index];
}

/****************
 * @param bool $first
 * @return array|string
 */
function graphina_pro_line_cap_type($first = false)
{
    $options = [
        "square" => esc_html__('Square', 'graphina-lang'),
        "butt" => esc_html__('Butt', 'graphina-lang'),
        "round" => esc_html__('Round', 'graphina-lang')
    ];
    $keys = array_keys($options);
    return $first ? (count($keys) > 0 ? $keys[0] : '') : $options;
}

/****************
 * @param bool $first
 * @return array|string
 */
function graphina_pro_plot_shape_type($first = false)
{
    $options = [
        "flat" => esc_html__('Flat', 'graphina-lang'),
        "rounded" => esc_html__('Rounded', 'graphina-lang')
    ];
    $keys = array_keys($options);
    return $first ? (count($keys) > 0 ? $keys[0] : '') : $options;
}

/***********************
 * @param object $this_ele
 * @param string $type
 * @param string[] $ele_array like ['color','stroke','drop shadow']
 * @param array $fillOptions lke ['classic', 'gradient', 'pattern']
 */
function graphina_pro_mixed_series_setting($this_ele, $type = 'chart_id', $ele_array = [], $fillOptions = [])
{
    $colors = graphina_colors('color');
    $gradientColor = graphina_colors('gradientColor');
    $this_ele->start_controls_section(
        'iq_' . $type . '_section_11',
        [
            'label' => esc_html__('Elements Setting', 'graphina-lang'),
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_chart_marker_setting_pro_divider',
        [
            'type' => Controls_Manager::DIVIDER,

        ]
    );

    for ($i = 0; $i < graphina_default_setting('max_series_value'); $i++) {
        $condition = [
            'iq_' . $type . '_chart_data_series_count' => range(1 + $i, graphina_default_setting('max_series_value'))
        ];

        if ($i !== 0) {
            $this_ele->add_control(
                'iq_' . $type . '_chart_hr_series_element_setting_1_' . $i,
                [
                    'type' => Controls_Manager::DIVIDER,
                    'condition' => $condition
                ]
            );
            $this_ele->add_control(
                'iq_' . $type . '_chart_hr_series_element_setting_2_' . $i,
                [
                    'type' => Controls_Manager::DIVIDER,
                    'condition' => $condition
                ]
            );
        }

        $this_ele->add_control(
            'iq_' . $type . '_chart_series_element_setting_title_' . $i,
            [
                'label' => esc_html__('Element ' . ($i + 1), 'graphina-lang'),
                'type' => Controls_Manager::HEADING,
                'condition' => $condition
            ]
        );

        $this_ele->add_control(
            'iq_' . $type . '_chart_type_3_' . $i,
            [
                'label' => esc_html__('Type', 'graphina-lang'),
                'type' => Controls_Manager::SELECT,
                'default' => graphina_pro_get_random_chart_type(graphina_pro_mixed_chart_typeList(), $i),
                'options' => graphina_pro_mixed_chart_typeList(),
                'condition' => $condition
            ]
        );

        $this_ele->add_control(
            'iq_' . $type . '_chart_datalabel_show_3_' . $i,
            [
                'label' => esc_html__('Show Data Labels', 'graphina-lang'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Hide', 'graphina-lang'),
                'label_off' => esc_html__('Show', 'graphina-lang'),
                'default' => 'yes',
                'condition' => array_merge(['iq_' . $type . '_chart_datalabel_show' => 'yes'], $condition)
            ]
        );

        $this_ele->add_control(
            'hr_4_01_' . $i,
            [
                'type' => Controls_Manager::DIVIDER,
                'condition' => array_merge(['iq_' . $type . '_chart_show_multiple_yaxis' => 'yes'], $condition)
            ]
        );

        $this_ele->add_control(
            'iq_' . $type . '_chart_yaxis_setting_title_3_' . $i,
            [
                'label' => esc_html__('Y-Axis Setting', 'graphina-lang'),
                'type' => Controls_Manager::HEADING,
                'condition' => array_merge(['iq_' . $type . '_chart_show_multiple_yaxis' => 'yes'], $condition)
            ]
        );

        $this_ele->add_control(
            'iq_' . $type . '_chart_yaxis_show_3_' . $i,
            [
                'label' => esc_html__('Show Axis With Title', 'graphina-lang'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Hide', 'graphina-lang'),
                'label_off' => esc_html__('Show', 'graphina-lang'),
                'default' => '',
                'condition' => array_merge(['iq_' . $type . '_chart_show_multiple_yaxis' => 'yes'], $condition)
            ]
        );


        $this_ele->add_control(
            'iq_' . $type . '_chart_yaxis_opposite_3_' . $i,
            [
                'label' => esc_html__('Position', 'graphina-lang'),
                'type' => Controls_Manager::CHOOSE,
                'default' => graphina_position_type('horizontal_boolean', true),
                'options' => graphina_position_type('horizontal_boolean'),
                'condition' => array_merge(['iq_' . $type . '_chart_show_multiple_yaxis' => 'yes', 'iq_' . $type . '_chart_yaxis_show_4_' . $i => 'yes'], $condition)
            ]
        );

        if (in_array('color', $ele_array)) {

            $this_ele->add_control(
                'iq_' . $type . '_chart_hr_fill_setting_3_' . $i,
                [
                    'type' => Controls_Manager::DIVIDER,
                    'condition' => $condition
                ]
            );

            graphina_fill_style_setting($this_ele, $type, $fillOptions, true, $i, $condition, true);

            $this_ele->add_control(
                'iq_' . $type . '_chart_gradient_3_1_' . $i,
                [
                    'label' => esc_html__('Color', 'graphina-lang'),
                    'type' => Controls_Manager::COLOR,
                    'default' => $colors[$i],
                    'condition' => $condition
                ]
            );
            $this_ele->add_control(
                'iq_' . $type . '_chart_gradient_3_2_' . $i,
                [
                    'label' => esc_html__('Second Color', 'graphina-lang'),
                    'type' => Controls_Manager::COLOR,
                    'default' => $gradientColor[$i],
                    'condition' => array_merge(['iq_' . $type . '_chart_fill_style_type_' . $i => 'gradient'], $condition)
                ]
            );

            $this_ele->add_control(
                'iq_' . $type . '_chart_pattern_3_' . $i,
                [
                    'label' => esc_html__('Fill Pattern', 'graphina-lang'),
                    'type' => Controls_Manager::SELECT,
                    'default' => graphina_get_fill_patterns(true),
                    'options' => graphina_get_fill_patterns(),
                    'condition' => array_merge([
                        'iq_' . $type . '_chart_type_3_' . $i . '!' => 'line',
                        'iq_' . $type . '_chart_fill_style_type_' . $i => 'pattern',
                        'iq_' . $type . '_chart_data_series_count' => range(1 + $i, graphina_default_setting('max_series_value'))
                    ], $condition)
                ]
            );

            if (function_exists('graphina_marker_setting')) {
                graphina_marker_setting($this_ele, $type, $i);
            }

            graphina_gradient_setting($this_ele, $type, false, true, $i, $condition);
        }

        if (in_array('stroke', $ele_array)) {

            $this_ele->add_control(
                'hr_4_03_' . $i,
                [
                    'type' => Controls_Manager::DIVIDER,
                    'condition' => $condition
                ]
            );

            $this_ele->add_control(
                'iq_' . $type . '_chart_stroke_setting_title_3_' . $i,
                [
                    'label' => esc_html__('Stroke Setting', 'graphina-lang'),
                    'type' => Controls_Manager::HEADING,
                    'condition' => $condition
                ]
            );

            $this_ele->add_control(
                'iq_' . $type . '_chart_stroke_curve_3_' . $i,
                [
                    'label' => esc_html__('Curve', 'graphina-lang'),
                    'type' => Controls_Manager::SELECT,
                    'default' => graphina_stroke_curve_type(true),
                    'options' => graphina_stroke_curve_type(),
                    'condition' => array_merge(['iq_' . $type . '_chart_type_3_' . $i => ['line', 'area']], $condition)
                ]
            );

            $this_ele->add_control(
                'iq_' . $type . '_chart_stroke_dash_3_' . $i,
                [
                    'label' => 'Dash',
                    'type' => Controls_Manager::NUMBER,
                    'default' => 0,
                    'min' => 0,
                    'max' => 100,
                    'condition' => [
                        'iq_' . $type . '_chart_data_series_count' => range(1 + $i, graphina_default_setting('max_series_value'))
                    ]
                ]
            );

            $this_ele->add_control(
                'iq_' . $type . '_chart_stroke_width_3_' . $i,
                [
                    'label' => 'Stroke Width',
                    'type' => Controls_Manager::NUMBER,
                    'default' => 5,
                    'min' => 1,
                    'max' => 20,
                    'condition' => [
                        'iq_' . $type . '_chart_data_series_count' => range(1 + $i, graphina_default_setting('max_series_value'))
                    ]
                ]
            );
        }

        if (in_array('drop-shadow', $ele_array)) {
            $this_ele->add_control(
                'hr_4_04_' . $i,
                [
                    'type' => Controls_Manager::DIVIDER,
                    'condition' => $condition
                ]
            );

            $this_ele->add_control(
                'iq_' . $type . '_drop_shadow_setting_title_3_' . $i,
                [
                    'label' => esc_html__('Drop Shadow Setting', 'graphina-lang'),
                    'type' => Controls_Manager::HEADING,
                    'condition' => $condition
                ]
            );

            $this_ele->add_control(
                'iq_' . $type . '_chart_drop_shadow_enabled_3_' . $i,
                [
                    'label' => esc_html__('Enabled', 'graphina-lang'),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__('True', 'graphina-lang'),
                    'label_off' => esc_html__('False', 'graphina-lang'),
                    'default' => '',
                    'condition' => $condition
                ]
            );

            $this_ele->add_control(
                'iq_' . $type . '_chart_drop_shadow_color_3_' . $i,
                [
                    'label' => esc_html__('Color', 'graphina-lang'),
                    'type' => Controls_Manager::COLOR,
                    'default' => '#FFFFFF00',
                    'condition' => array_merge(['iq_' . $type . '_chart_drop_shadow_enabled_3_' . $i => 'yes'], $condition)
                ]
            );
        }
        if (in_array('tooltip', $ele_array)) {
            $condition = array_merge($condition, ['iq_' . $type . '_chart_tooltip' => 'yes', 'iq_' . $type . '_chart_tooltip_shared' => 'yes']);

            $this_ele->add_control(
                'hr_4_06_' . $i,
                [
                    'type' => Controls_Manager::DIVIDER,
                    'condition' => $condition
                ]
            );

            $this_ele->add_control(
                'iq_' . $type . '_tooltip_setting_title_3_' . $i,
                [
                    'label' => esc_html__('Tooltip Setting', 'graphina-lang'),
                    'type' => Controls_Manager::HEADING,
                    'condition' => $condition
                ]
            );

            $this_ele->add_control(
                'iq_' . $type . '_chart_tooltip_enabled_on_1_' . $i,
                [
                    'label' => esc_html__('Enabled', 'graphina-lang'),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__('Yes', 'graphina-lang'),
                    'label_off' => esc_html__('No', 'graphina-lang'),
                    'default' => 'yes',
                    'condition' => $condition
                ]
            );
        }
    }
    $this_ele->end_controls_section();
}

/*********************
 * @param $settings
 * @param string $type
 * @param string $areaType
 * @return array[]
 */
function graphina_pro_parse_csv($settings, $type = 'chart_id', $areaType = "area")
{
    $data = [];
    $category = [];
    $total = 0;

    $response = wp_remote_get(
        $settings['iq_' . $type . '_chart_upload_csv']['url'],
        [
            'sslverify' => false,
        ]
    );

    if ('' == $settings['iq_' . $type . '_chart_upload_csv']['url'] || is_wp_error($response) || 200 != $response['response']['code'] || '.csv' !== substr($settings['iq_' . $type . '_chart_upload_csv']['url'], -4)) {
        return ['series' => $data, 'category' => $category, 'total' => $total];
    }

    $file = $settings['iq_' . $type . '_chart_upload_csv']['url'];
    try {
        // Attempt to change permissions if not readable.
//    if (!is_readable($file)) {
//        chmod($file, 0744);
//    }

        // Check if file is writable, then open it in 'read only' mode.
//    if (is_readable($file)) {

        $_file = fopen($file, 'r');

        if (!$_file) {
            return ['series' => $data, 'category' => $category, 'total' => $total];
        }
        $lineNumber = 1;

        switch ($areaType) {
            case "mixed":
            case "area" :
                while (($raw_string = fgets($_file)) !== false) {
                    $row = str_getcsv($raw_string);
                    if ($lineNumber === 1) {
                        $category = filter_var_array($row, FILTER_SANITIZE_STRING);
                        unset($category[0]);
                    } else {
                        $file_data = [
                            'name' => $row[0],
                            'data' => []
                        ];
                        unset($row[0]);
                        $file_data['data'] = array_values(array_map(function ($d) {
                            return (float)$d;
                        }, $row));
                        $data[] = $file_data;
                    }
                    $lineNumber++;
                }
                break;
            case "bubble" :
                while (($raw_string = fgets($_file)) !== false) {
                    $row = str_getcsv($raw_string);
                    if ($lineNumber === 1) {
                        $category = filter_var_array($row, FILTER_SANITIZE_STRING);;
                        unset($category[0]);
                    } else {
                        $file_data = [
                            'name' => $row[0],
                            'data' => []
                        ];
                        unset($row[0]);
                        $row = array_chunk($row, 3);
                        $file_data['data'] = array_values(array_map(function ($d) {
                            return [
                                'x' => isset($d[0]) ? (float)$d[0] : 0,
                                'y' => isset($d[1]) ? (float)$d[1] : 0,
                                'z' => isset($d[2]) ? (float)$d[2] : 0
                            ];
                        }, $row));
                        $data[] = $file_data;
                    }
                    $lineNumber++;
                }
                break;
            case "nested_column" :
                while (($raw_string = fgets($_file)) !== false) {
                    $row = str_getcsv($raw_string);
                    if ($lineNumber !== 1) {
                        $file_data = [
                            'x' => $row[0],
                            'quarters' => []
                        ];
                        unset($row[0]);
                        $row = array_chunk($row, 2);
                        $file_data['quarters'] = array_values(array_map(function ($d) {
                            return [
                                'x' => isset($d[0]) ? $d[0] : '',
                                'y' => isset($d[1]) ? (float)$d[1] : 0
                            ];
                        }, $row));
                        $data[] = $file_data;
                    }
                    $lineNumber++;
                }
                break;
            case "candle" :
                while (($raw_string = fgets($_file)) !== false) {
                    $row = str_getcsv($raw_string);
                    if ($lineNumber !== 1) {
                        $file_data = [
                            'name' => $row[0],
                            'data' => []
                        ];
                        unset($row[0]);
                        $row = array_chunk($row, 5);
                        $file_data['data'] = array_values(array_map(function ($d) {
                            return [
                                'x' => isset($d[0]) ? strtotime(strval($d[0])) * 1000 : 0,
                                'y' => [
                                    isset($d[1]) ? (float)$d[1] : 0,
                                    isset($d[2]) ? (float)$d[2] : 0,
                                    isset($d[3]) ? (float)$d[3] : 0,
                                    isset($d[4]) ? (float)$d[4] : 0
                                ]
                            ];
                        }, $row));
                        $data[] = $file_data;
                    }
                    $lineNumber++;
                }
                break;
            case "timeline" :
                while (($raw_string = fgets($_file)) !== false) {
                    $row = str_getcsv($raw_string);
                    if ($lineNumber === 1) {
                        $category = filter_var_array($row, FILTER_SANITIZE_STRING);
                        unset($category[0]);
                        $category = array_filter($category, function ($v) {
                            return isset($v) && $v !== '';
                        });
                    } else if ($lineNumber > 2) {
                        $file_data = [
                            'name' => $row[0],
                            'data' => []
                        ];
                        unset($row[0]);
                        $row = array_chunk($row, 2);
                        $file_data['data'] = array_values(array_map(function ($d, $c) {
                            return [
                                'x' => isset($c) ? $c : 0,
                                'y' => [
                                    isset($d[0]) ? strtotime($d[0]) * 1000 : 0,
                                    isset($d[1]) ? strtotime($d[1]) * 1000 : 0
                                ]
                            ];
                        }, $row, $category));
                        $data[] = $file_data;
                    }
                    $lineNumber++;
                }
                break;
            case "circle" :
                while (($raw_string = fgets($_file)) !== false) {
                    $row = str_getcsv($raw_string);
                    if ($lineNumber === 1) {
                        $category = filter_var_array($row, FILTER_SANITIZE_STRING);
                    } else {
                        $data = array_values(array_map(function ($d) {
                            return (float)$d;
                        }, $row));
                        $total = array_sum($data);
                    }
                    $lineNumber++;
                }
                break;
        }
        fclose($_file);
//    }
        return ['series' => $data, 'category' => array_values($category), 'total' => $total];
    } catch (Exception $e) {
        return ['series' => [], 'category' => [], 'total' => 0];
    }
}

/******************
 * @param $ele_type
 * @param $settings
 * @param $type
 * @param $mainId
 * @param string $from_type
 * @return array[]
 *******************/
function graphina_pro_get_data_from_url($ele_type, $settings, $type, $mainId, $from_type = 'area')
{
    $data = ['series' => [], 'category' => [], 'total' => 0];
    $import_from = $type === 'remote-csv' ? 'iq_' . $ele_type . '_chart_import_from_url' : 'iq_' . $ele_type . '_chart_import_from_google_sheet';
    $val = graphina_get_dynamic_tag_data($settings, $import_from);
    $val = htmlspecialchars_decode($val);
    if ($val !== '') {
        if (Plugin::$instance->editor->is_edit_mode() && $settings['iq_' . $ele_type . '_can_use_cache_development'] === "yes") {
            $data = get_transient('iq_' . $ele_type . '_' . $mainId);
            if (false === $data || count($data['series']) === 0) {
                $data = graphina_pro_get_data_from_remote_csv($val, $from_type);
                set_transient('iq_' . $ele_type . '_' . $mainId, $data, HOUR_IN_SECONDS);
            }
        } else {
            $data = graphina_pro_get_data_from_remote_csv($val, $from_type);
        }
    }
    return $data;
}

/*********************
 * @param string $url
 * @param string $areaType
 * @return array[]
 */
function graphina_pro_get_data_from_remote_csv($url = '', $areaType = 'area')
{
    $result = [];
    $category = [];
    $total = 0;
    if ($url === '') {
        return ["series" => $result, "category" => $category, 'total' => $total];
    }
    $file = file_get_contents($url);
    if (strpos($file, '<!DOCTYPE html>') !== false || strpos($file, '<html>') !== false || strpos($file, '</html>') !== false) {
        return ["series" => $result, "category" => $category, 'total' => $total, 'fail' => 'permission'];
    }
    $file = str_replace("\r\n", "\n", $file);
    $arr = explode("\n", $file);
    switch ($areaType) {
        case "area" :
            foreach ($arr as $i => $a) {
                if (!empty($a)) {
                    if ($i !== 0) {
                        $v = str_getcsv($a);
                        $name = $v[0];
                        unset($v[0]);
                        $v = array_map(function ($d) {
                            return (float)$d;
                        }, $v);
                        $result[] = [
                            "name" => filter_var($name, FILTER_SANITIZE_STRING),
                            "data" => array_values($v)
                        ];
                    } else {
                        $category = filter_var_array(str_getcsv($a), FILTER_SANITIZE_STRING);
                        unset($category[0]);
                    }
                }
            }
            break;
        case "bubble" :
            foreach ($arr as $i => $a) {
                if (!empty($a)) {
                    $v = str_getcsv($a);
                    if ($i === 0) {
                        $category = filter_var_array($v, FILTER_SANITIZE_STRING);
                        unset($category[0]);
                    } else {
                        $file_data = [
                            'name' => filter_var($v[0], FILTER_SANITIZE_STRING),
                            'data' => []
                        ];
                        unset($v[0]);
                        $v = array_chunk($v, 3);
                        $file_data['data'] = array_values(array_map(function ($d) {
                            return [
                                'x' => isset($d[0]) ? (float)$d[0] : 0,
                                'y' => isset($d[1]) ? (float)$d[1] : 0,
                                'z' => isset($d[2]) ? (float)$d[2] : 0
                            ];
                        }, $v));
                        $result[] = $file_data;
                    }
                }
            }
            break;
        case "nested_column" :
            foreach ($arr as $i => $a) {
                if (!empty($a)) {
                    $v = str_getcsv($a);
                    if ($i !== 0) {
                        $file_data = [
                            'x' => filter_var($v[0], FILTER_SANITIZE_STRING),
                            'quarters' => []
                        ];
                        unset($v[0]);
                        $v = array_chunk($v, 2);
                        $file_data['quarters'] = array_values(array_map(function ($d) {
                            return [
                                'x' => isset($d[0]) ? filter_var($d[0], FILTER_SANITIZE_STRING) : '',
                                'y' => isset($d[1]) ? (float)$d[1] : 0
                            ];
                        }, $v));
                        $result[] = $file_data;
                    }
                }
            }
            break;
        case "candle":
            foreach ($arr as $i => $a) {
                if (!empty($a)) {
                    $v = str_getcsv($a);
                    if ($i !== 0) {
                        $file_data = [
                            'name' => filter_var($v[0], FILTER_SANITIZE_STRING),
                            'data' => []
                        ];
                        unset($v[0]);
                        $v = array_chunk($v, 5);
                        $file_data['data'] = array_values(array_map(function ($d) {
                            return [
                                'x' => isset($d[0]) ? strtotime(strval($d[0])) * 1000 : 0,
                                'y' => [
                                    isset($d[1]) ? (float)$d[1] : 0,
                                    isset($d[2]) ? (float)$d[2] : 0,
                                    isset($d[3]) ? (float)$d[3] : 0,
                                    isset($d[4]) ? (float)$d[4] : 0
                                ]
                            ];
                        }, $v));
                        $result[] = $file_data;
                    }
                }
            }
            break;
        case "timeline" :
            foreach ($arr as $i => $a) {
                if (!empty($a)) {
                    $v = str_getcsv($a);
                    if ($i === 0) {
                        $category = filter_var_array($v, FILTER_SANITIZE_STRING);;
                        unset($category[0]);
                        $category = array_filter($category, function ($v) {
                            return isset($v) && $v !== '';
                        });
                    } else if ($i > 1) {
                        $file_data = [
                            'name' => filter_var($v[0], FILTER_SANITIZE_STRING),
                            'data' => []
                        ];
                        unset($v[0]);
                        $v = array_chunk($v, 2);
                        $file_data['data'] = array_values(array_map(function ($d, $c) {
                            return [
                                'x' => isset($c) ? $c : 0,
                                'y' => [
                                    isset($d[0]) ? strtotime($d[0]) * 1000 : 0,
                                    isset($d[1]) ? strtotime($d[1]) * 1000 : 0
                                ]
                            ];
                        }, $v, $category));
                        $result[] = $file_data;
                    }
                }
            }
            break;
        case "circle" :
            foreach ($arr as $i => $a) {
                if (!empty($a)) {
                    $v = str_getcsv($a);
                    if ($i === 0) {
                        $category = filter_var_array($v, FILTER_SANITIZE_STRING);;
                    } else {
                        $result = array_values(array_map(function ($d) {
                            return (float)$d;
                        }, $v));
                        $total = array_sum($result);
                    }
                }
            }
            break;
    }
    return ["series" => $result, "category" => array_values($category), 'total' => $total];
}

/*********************
 * @param string $api_url
 * @param string $type
 * @return array
 */
function graphina_pro_chart_get_data_from_api($mainType, $settings, $type = '')
{
    $api_url = $settings['iq_' . $mainType . '_chart_import_from_api'];
    $result = ['series' => [], 'category' => [], 'total' => 0];
    if ($api_url === '') {
        return $result;
    }

    $args = [];
    if(isset($settings['iq_'.$mainType.'_authrization_token']) 
        && $settings['iq_'.$mainType.'_authrization_token'] == 'yes') {
        $args['headers'] = [];
        $args['headers'][$settings['iq_'.$mainType.'_header_key']] = $settings['iq_'.$mainType.'_header_token'];
    }
    $response = wp_remote_get($api_url, $args );
    
    if (is_array($response) && !is_wp_error($response)) {
        $res_body = $response['body']; // use the content
        $res_body = json_decode($res_body, true);
        if (gettype($res_body['data']) === 'array') {
            switch ($type) {
                case 'area' :
                case 'circle':
                    $result['series'] = $res_body['data'];
                    $result['category'] = $res_body['category'];
                    $result['total'] = array_sum($res_body['data']);
                    break;
                case 'bubble':
                case 'nested_column':
                case 'candle':
                $result['series'] = $res_body['data'];
                break;
                case 'timeline':
                $result['series'] = array_map(function($v){
                    $v['data'] = array_map(function($v1){
                        $v1['y'] = array_map(function($v2){
                            if(gettype($v2) === 'string'){
                                $v2 = strtotime($v2) * 1000;
                            }
                            return $v2;
                        },$v1['y']);
                        return $v1;
                    },$v['data']);
                    return $v;
                },$res_body['data']);
                    break;
            }
        }
    }
    return $result;
}

/*********************
 * @param array $settings
 * @param string $type
 * @return array
 */
function graphina_pro_chart_get_data_from_sql_builder($settings, $type = '')
{

    global $wpdb;

    try {

        $result = ['series' => [], 'category' => [], 'total' => 0];
        $sql_custom_query = strip_tags(trim($settings['iq_' . $type . '_chart_sql_builder']));
        if ($sql_custom_query === null) return [];
        $sql_custom_query = stripslashes($sql_custom_query);
        $result_data = $wpdb->get_results($sql_custom_query);

        if (count($result_data)) {

            $fields = [];
            $values = [];
            
            // dynamic column name
            $x_columns = $settings['iq_' . $type . '_chart_sql_builder_x_columns'];
            $y_columns = $settings['iq_' . $type . '_chart_sql_builder_y_columns'];

            if(!is_array($y_columns)) {
                $y_columns = [$y_columns] ;
            }

            foreach ($result_data[0] as $key => $value) {
                $fields[] = $key;
            }

            $category = [];

            $series = ['series' => [], 'category' => ['col 1', 'col 2', 'col 3', 'col 4', 'col 5'], 'total' => 0, 'db_column' => $fields, 'sql_fail' => ''];

            if ($x_columns !== '' && $x_columns !== null) {
                foreach ($result_data as $key => $value) {
                    $category[] = $value->$x_columns;
                }
            }

            if (count($category) > 0) {
                $series['category'] = $category;
            }

            if (count($y_columns) > 0) {

                foreach ($y_columns as $key => $column) {

                    foreach ($result_data as $key => $value) {
                        // value casting to int
                        if (in_array($type, ['pie' , 'donut' , 'radial' , 'polar'])) {
                            $values[$column][] = (int)$value->$column;
                        } else {
                            $values[$column][] = $value->$column;
                        }
                    }

                    $temp_element = [
                        'name' => $column,
                        'data' => $values[$column]
                    ];
                    
                    if (in_array($type, ['pie' , 'donut' , 'radial' , 'polar'])) {
                        $series['series'] = $values[$column];
                    } else {
                        $series['series'][] = $temp_element;
                    }
                }

            }

            $series['x_axis_value'] = $x_columns ;

            if (!graphina_is_preview_mode()) {
                if (empty($result_data) || count($result_data) === 0) {
                    $series['sql_fail'] = esc_attr__('No data found, Please check your sql statement.', 'graphina-lang');
                }
            }

            return $series;

        } else {
            wp_send_json(['status' => false, 'data' => ['series' => [], 'category' => [], 'total' => 0, 'sql_fail' => esc_attr__('No data found, Please check your sql statement.', 'graphina-lang')]]);
        }

    } catch (Exception $exception) {
        wp_send_json(['status' => false, 'data' => ['series' => [], 'category' => [], 'total' => 0, 'sql_fail' => esc_attr__('No data found, Please check your sql statement.', 'graphina-lang')]]);
    }

}

/**********************
 * @param string $this_ele
 * @param string $type
 */
function graphina_pro_get_dynamic_options($this_ele = '', $type = '')
{

    $this_ele->add_control(
        'iq_' . $type . '_chart_sql_builder',
        [
            'label' => esc_html__('SQL Raw Query', 'graphina-lang'),
            'type' => Controls_Manager::TEXTAREA,
            'dynamic' => ['active' => true],
            'placeholder' => esc_html__('SQL Builder', 'graphina-lang'),
            'description' => esc_html__('Fetch data from customize/raw query builder', 'graphina-lang'),
            'label_block' => true,
            'default' => '',
            'condition' => [
                'iq_' . $type . '_chart_data_option' => ['dynamic'],
                'iq_' . $type . '_chart_dynamic_data_option' => 'sql-builder',
            ]
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_chart_sql_builder_x_columns',
        [
            'label' => esc_html__('X-Axis Columns', 'graphina-lang'),
            'type' => Controls_Manager::SELECT2,
            'default' => '',
            'options' => ['not_found' => 'Not found'],
            'condition' => [
                'iq_' . $type . '_chart_data_option' => ['dynamic'],
                'iq_' . $type . '_chart_dynamic_data_option' => 'sql-builder',
            ]
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_chart_sql_builder_y_columns',
        [
            'label' => esc_html__('Y-Axis Columns', 'graphina-lang'),
            'type' => Controls_Manager::SELECT2,
            'default' => '',
            'options' => ['not_found' => 'Not found'],
            'multiple' =>  !in_array($type, ['pie' , 'donut' , 'radial' , 'polar']),
            'condition' => [
                'iq_' . $type . '_chart_data_option' => ['dynamic'],
                'iq_' . $type . '_chart_dynamic_data_option' => 'sql-builder',
            ]
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_chart_upload_csv',
        [
            'label' => esc_html__('Upload CSV', 'graphina-lang'),
            'type' => Controls_Manager::MEDIA,
            'dynamic' => ['active' => true],
            'media_type' => 'text/csv',
            'default' => [
                'id' => '',
                'url' => '',
            ],
            'condition' => [
                'iq_' . $type . '_chart_data_option' => ['dynamic'],
                'iq_' . $type . '_chart_dynamic_data_option' => 'csv'
            ]
        ]);


    $this_ele->add_control(
        'iq_' . $type . '_chart_import_from_url',
        [
            'label' => esc_html__('URL', 'graphina-lang'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => ['active' => true],
            'placeholder' => esc_html__('Remote File URL', 'graphina-lang'),
            'description' => esc_html__('This URL is used to fetch CSV from remote server', 'graphina-lang'),
            'label_block' => true,
            'default' => '',
            'condition' => [
                'iq_' . $type . '_chart_data_option' => ['dynamic'],
                'iq_' . $type . '_chart_dynamic_data_option' => 'remote-csv'
            ],
            'dynamic' => [
                'active' => true,
            ],
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_chart_import_from_google_sheet',
        [
            'label' => esc_html__('Enter Google Sheet Published URL', 'graphina-lang'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => ['active' => true],
            'placeholder' => esc_html__('Google Sheet Published URL', 'graphina-lang'),
            'label_block' => true,
            'default' => '',
            'condition' => [
                'iq_' . $type . '_chart_data_option' => ['dynamic'],
                'iq_' . $type . '_chart_dynamic_data_option' => 'google-sheet'
            ],
            'dynamic' => [
                'active' => true,
            ],
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_chart_import_from_api',
        [
            'label' => esc_html__('URL', 'graphina-lang'),
            'type' => Controls_Manager::TEXT,
            'placeholder' => esc_html__('URL', 'graphina-lang'),
            'label_block' => true,
            'default' => '',
            'condition' => [
                'iq_' . $type . '_chart_data_option' => ['dynamic'],
                'iq_' . $type . '_chart_dynamic_data_option' => 'api'
            ],
            'dynamic' => [
                'active' => true,
            ],
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_can_use_cache_development',
        [
            'label' => esc_html__('Use Cache For Development', 'graphina-lang'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('No', 'graphina-lang'),
            'label_off' => esc_html__('Yes', 'graphina-lang'),
            'description' => esc_html__("This feature is used to cache the CSV file for 1 hour only in editor mode. It will not generate cache for live site or preview", 'graphina-lang'),
            'default' => false,
            'condition' => [
                'iq_' . $type . '_chart_dynamic_data_option' => ['remote-csv', 'google-sheet']
            ]
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_authrization_token',
        [
            'label' => esc_html__('Enable Header Options', 'graphina-lang'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'graphina-lang'),
            'label_off' => esc_html__('No', 'graphina-lang'),
            'default' => false,
            'condition' => [
                'iq_' . $type . '_chart_dynamic_data_option' => 'api'
            ]
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_header_key',
        [
            'label' => esc_html__('Header Key', 'graphina-lang'),
            'type' => Controls_Manager::TEXT,
            'condition' => [
                'iq_' . $type . '_authrization_token' => 'yes',
                'iq_' . $type . '_chart_dynamic_data_option' => 'api'
            ],
            'dynamic' => [
                'active' => true,
            ],
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_header_token',
        [
            'label' => esc_html__('Header Token', 'graphina-lang'),
            'type' => Controls_Manager::TEXT,
            'condition' => [
                'iq_' . $type . '_authrization_token' => 'yes',
                'iq_' . $type . '_chart_dynamic_data_option' => 'api'
            ],
            'dynamic' => [
                'active' => true,
            ],
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_element_download_csv_sample_doc',
        [
            'label' => '<div class="elementor-control-field-description" style="display: block;">Click
                                        <a style="text-decoration: underline; font-style: italic" href="' . GRAPHINA_PRO_URL . '/elementor/sample-doc/' . $type . '-chart-sample.csv" download>here</a>
                                        to download sample CSV file.                                            
                                    </div>',
            'type' => Controls_Manager::RAW_HTML,
            'condition' => [
                'iq_' . $type . '_chart_dynamic_data_option' => ['csv', 'remote-csv']
            ]
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_element_download_google_sheet',
        [
            'label' => '<div class="elementor-control-field-description" style="display: block;">Click
                                        <a style="text-decoration: underline; font-style: italic" target="_blank" href="' . graphina_pro_get_sheet($type) . '">here</a>
                                        to view the sample format.                                           
                                    </div>',
            'type' => Controls_Manager::RAW_HTML,
            'condition' => [
                'iq_' . $type . '_chart_dynamic_data_option' => 'google-sheet'
            ]
        ]
    );

    $this_ele->add_control(
        'iq_' . $type . '_element_download_sample_json',
        [
            'label' => '<div class="elementor-control-field-description" style="display: block;">Click
                                        <a style="text-decoration: underline; font-style: italic" href="' . GRAPHINA_PRO_URL . '/elementor/sample-json/' . $type . '-chart-sample.json" download>here</a>
                                        to download sample JSON file.                                            
                                    </div>',
            'type' => Controls_Manager::RAW_HTML,
            'condition' => [
                'iq_' . $type . '_chart_dynamic_data_option' => 'api'
            ]
        ]
    );

}

/**********************
 * @param string $type
 * @return string
 */
function graphina_pro_get_sheet($type = 'area')
{

    $sheet = 'https://docs.google.com/spreadsheets/d/1zCIRmobXye0BSgUnY4vMG4Sn6LEp6We_4RjJdPS4CYg/edit?usp=sharing';

    switch ($type) {

        case 'donut':
        case 'pie':
        case 'polar':
        case 'radial':
            $sheet = 'https://docs.google.com/spreadsheets/d/1v2v1W61vZahN2qhbCL2Z79CEnzPOwJLyQqswIOzjxmU/edit?usp=sharing';
            break;
        case 'bubble':
            $sheet = 'https://docs.google.com/spreadsheets/d/1Wqv3095LVzkKG_1uwNWWiSovyWwnC0EdqVgIkm746Yo/edit?usp=sharing';
            break;
        case 'timeline':
            $sheet = 'https://docs.google.com/spreadsheets/d/1trOwuavFpWMXEG-53pjRLAA_fhgNqOvBWfuQ6S6JNDM/edit?usp=sharing';
            break;
        case 'nested_column':
            $sheet = 'https://docs.google.com/spreadsheets/d/1drqaZ3CbRseRXJekNHBSnW6v-S91opSfh0QhBRZbjJ8/edit?usp=sharing';
            break;
        case 'graphina-counter';
            $sheet = 'https://docs.google.com/spreadsheets/d/1ZEtWaHVocV3O2G2CO1iHK38vKcAe1sJjEDj_WUdINIg/edit?usp=sharing';
            break;
        case 'advance-datatable';
            $sheet = 'https://docs.google.com/spreadsheets/d/1NPZwZXIoG0Cgl8mtnvV8U6MqjuJ8_VFplNm3qnj_Wyo/edit?usp=sharing';
            break;
    }

    return $sheet;
}

function graphina_get_data_for_key($settings, $key, $type)
{
    $val = graphina_get_dynamic_tag_data($settings, $key);
    if (empty($val)) {
        $val = $settings[$val];
    }
    switch ($type) {
        case "array_string":
            $val = array_map(function ($val) {
                return (string)$val;
            }, $val);
            break;
        case "array_float":
            $val = array_map(function ($val) {
                return (float)$val;
            }, $val);
            break;
        case "int":
            $val = (int)$val;
            break;
        case "float":
            $val = (float)$val;
            break;
        default :
            $val = (string)$val;
            break;
    }
    return $val;
}

/*******************************
 * @param $settings
 * @param $type
 * @param $mainId
 * @return array[]
 */
function graphina_pro_get_chart_responsive_data($settings, $type, $mainId = null)
{

    $responsive = [];
    $responsive_element = ['tablet', 'mobile'];
    $current_data = [];
    $default_data = [
        'dataLabels_enabled' => graphina_get_data_for_key($settings, 'iq_' . $type . '_chart_datalabel_show', 'string')
    ];
    $options = [];

    foreach ($responsive_element as $ele) {
        $current_data = [
            'dataLabels_enabled' => graphina_get_data_for_key($settings, 'iq_' . $type . '_chart_datalabel_show_'.$ele, 'string')
        ];
        $options[$ele] = [
            "options" => [
                "chart" => [
                    'height' => graphina_get_data_for_key($settings, 'iq_' . $type . '_chart_height_'.$ele, 'float')
                ],
                "dataLabels" => [
                    "enabled" => key_exists('iq_' . $type . '_chart_datalabel_show_'.$ele,$settings) ? $current_data['dataLabels_enabled'] === 'yes' : $default_data['dataLabels_enabled'] === 'yes'
                ]
            ]
        ];
    }


    /***********************************
     *        Responsive 1024
     ***********************************/

    $responsive['1024'] = array_merge([
        "breakpoint" => 1024
    ],$options['tablet']);

    /***********************************
     *        Responsive 767
     ***********************************/

    $responsive['767'] = array_merge([
        "breakpoint" => 767
    ],$options['mobile']);

    return array_values($responsive);
}