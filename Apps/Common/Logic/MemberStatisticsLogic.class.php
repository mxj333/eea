<?php
namespace Common\Logic;
class MemberStatisticsLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;

        if (intval($param['check_y']) != 1) {
            // 登录次数统计  需要连表
            $me_prev = 'm.';
            $log_prev = 'mll.';
            $default['table'][C('DB_PREFIX').'member'] = substr($me_prev, 0, -1);
            $default['join'][] = 'RIGHT JOIN ' . C('DB_PREFIX') . 'member_login_log ' . substr($log_prev, 0, -1) . ' ON ' . $me_prev . 'me_id = ' . $log_prev . 'me_id';
        }

        if ($param['re_id']) {
            $level = count(explode('-', $param['re_id']));
            $re_id = $this->dealValueDelimiter($param['re_id']);
            $regConfig['where']['re_ids'] = $re_id;
            $regConfig['fields'] = 're_ids_children';
            $region = D('Region', 'Model')->getOne($regConfig);
            $region = $region ? $region . ',' . $re_id : $re_id;
            $default['where'][$me_prev . 're_id'] = array('IN', $region);
        }
        if ($param['s_id']) {
            $default['where'][$me_prev . 's_id'] = $param['s_id'];
        }

        $starttime = strtotime($param['starttime']);
        $endtime = strtotime($param['endtime']);
        if ($starttime && $endtime) {
            $default['where'][$log_prev . 'mll_created'] = array('BETWEEN', array($starttime, $endtime));
        } elseif (!$starttime && $endtime) {
            $default['where'][$log_prev . 'mll_created'] = array('ELT', $endtime);
        } elseif ($starttime && !$endtime) {
            $default['where'][$log_prev . 'mll_created'] = array('EGT', $starttime);
        }

        // 统计 x 轴
        if ($param['check_x'] == 1) {
            // 时间
            if (intval($param['check_y']) == 1) {
                $check_x_field = 'me_created';
                $check_x_prev = $me_prev;
                $check_x_table = 'Member';
            } else {
                $check_x_field = 'mll_created';
                $check_x_prev = $log_prev;
                $check_x_table = 'MemberLoginLog';
            }
            if (!$starttime) {
                $resConfig['order'] = $check_x_field . ' ASC';
                $resConfig['fields'] = $check_x_field;
                $starttime = D($check_x_table)->getOne($resConfig);
            }
            if (!$endtime) {
                $resConfig['order'] = $check_x_field . ' DESC';
                $resConfig['fields'] = $check_x_field;
                $endtime = D($check_x_table)->getOne($resConfig);
            }
            // 计算时间差
            $type_name = $param['check_time_type'] ? $param['check_time_type'] : $this->checkTimeType($starttime, $endtime);
            if ($type_name == 'year') {
                $default['fields']['FROM_UNIXTIME(' . $check_x_prev . $check_x_field . ',"%Y")'] = 'x_name';
            } elseif ($type_name == 'month') {
                $default['fields']['FROM_UNIXTIME(' . $check_x_prev . $check_x_field . ',"%Y-%m")'] = 'x_name';
            } else {
                $default['fields']['FROM_UNIXTIME(' . $check_x_prev . $check_x_field . ',"%Y-%m-%d")'] = 'x_name';
            }
        } elseif ($param['check_x'] == 2) {
            // 身份
            $default['fields'][$me_prev . 'me_type'] = 'x_name';
        } elseif ($param['check_x'] == 3) {
            // 学校
            $default['fields'][$me_prev . 's_id'] = 'x_name';
        } elseif ($param['check_x'] == 4) {
            // 班级
            $default['fields'][$me_prev . 'c_id'] = 'x_name';
        } else {
            $level = $level < 1 ? 1 : ($level > 4 ? 4 : $level);
            $default['fields'][$me_prev . 're_id'] = 're_id';
            if ($level == 4) {
                $default['fields'][$me_prev . 're_title'] = 'x_name';
            } else {
                $default['fields']['substring_index(' . $me_prev . 're_title, "-", ' . ($level+1) . ')'] = 'x_name';
            }
        }
        $default['group'] = 'x_name';

        // 统计Y轴字段
        $check_y = intval($param['check_y']);
        if ($check_y == 1) {
            $default['fields']['count(' . $me_prev . 'me_id)'] = 'num';
        } else {
            $default['fields']['sum(' . $me_prev . 'me_login_count)'] = 'num';
        }
        
        $config = array_merge($default, $config);

        $lists = D('Member')->getAll($config);

        // 结果处理
        $result = array();
        if ($param['check_x'] == 1) {
            $lists = $this->dealDatetime($starttime, $endtime, $type_name, $lists);
        } elseif ($param['check_x'] == 2) {
            $me_type = explode(',', C('MEMBER_TYPE'));
            $me_type[0] = '其他';
            foreach ($lists as $key => $values) {
                $result[] = array(
                    'x_name' => $me_type[$key],
                    'num' => $values,
                );
                $lists = $result;
            }
        } elseif ($param['check_x'] == 3) {
            foreach ($lists as $key => $values) {
                $s_title = D('School', 'Model')->getOne(array('fields' => 's_title', 'where' => array('s_id' => $key)));
                $result[] = array(
                    'x_name' => strval($s_title),
                    'num' => $values,
                );
                $lists = $result;
            }
        } elseif ($param['check_x'] == 4) {
            foreach ($lists as $key => $values) {
                $c_title = D('Class', 'Model')->getOne(array('fields' => 'c_title', 'where' => array('c_id' => $key)));
                $result[] = array(
                    'x_name' => strval($c_title),
                    'num' => $values,
                );
                $lists = $result;
            }
        }
        
        // 输出数据
        return $lists;
    }

    // 自动验证时间统计类型
    public function checkTimeType($starttime, $endtime) {
        $year_diff = date('Y', $endtime) - date('Y', $starttime); // 年差
        $month_diff = date('m', $endtime) - date('m', $starttime); // 月差

        // 天数
        $diff = ceil(($endtime - $starttime)/86400);
        
        if ($diff > 365 && $year_diff > 0) {
            $type_name = 'year';
        } elseif ($diff > 28 && abs($month_diff) > 0) {
            $type_name = 'month';
        } else {
            $type_name = 'day';
        }
        
        // 返回统计类型
        return $type_name;
    }

    // 时间处理
    // type_name 确定统计类型  年、月、日
    // 结果补全日期
    public function dealDatetime($starttime, $endtime, $type_name, $result = array()) {
        $month = range(1,12); // 月份
        $day = range(1,31); // 日
        $year_s = date('Y', $starttime); // 开始年
        $month_s = date('m', $starttime); // 开始月
        $day_s = date('d', $starttime); // 开始日
        $year_diff = date('Y', $endtime) - $year_s; // 年差
        $month_diff = date('m', $endtime) - $month_s; // 月差
        $day_diff = date('d', $endtime) - $day_s; // 日差

        $res = array();
        if ($type_name == 'year') {
            // 年
            for ($start = 0; $start < $year_diff+1; $start++) {
                $start_year = $year_s + $start;
                $res[] = array(
                    'x_name' => strval($start_year),
                    'num' => $result[$start_year] ? $result[$start_year] : 0,
                );
            }
        } elseif ($type_name == 'month') {
            // 月
            for ($start = 0; $start < $year_diff*12+$month_diff+1; $start++) {
                $start_month = $month[($month_s + $start -1)%12];
                $start_year = $year_s + floor(($month_s + $start -1)/12);
                $start_month = $start_month < 10 ? '0' . $start_month : $start_month;
                $res[] = array(
                    'x_name' => $start_year . '-' . $start_month,
                    'num' => $result[$start_year . '-' . $start_month] ? $result[$start_year . '-' . $start_month] : 0,
                );
            }
        } else {
            // 日
            if (in_array($month_s, array('01', '03', '05', '07', '08', '10', '12'))) {
                $max_diff = 31;
            } elseif ($month_s == '02') {
                $max_diff = is_leap_years($year_s) ? 29 : 28;
            } else {
                $max_diff = 30;
            }

            for ($start = 0; $start < $month_diff*$max_diff+$day_diff+1; $start++) {
                $start_day = $day[($day_s + $start - 1)%$max_diff];
                $start_month = $month[(floor(($day_s + $start-1)/$max_diff) + $month_s - 1)%12];
                $start_year = $year_s + floor((floor(($day_s + $start - 1)/$max_diff) + $month_s - 1)/12);
                $start_month = $start_month < 10 ? '0' . $start_month : $start_month;
                $start_day = $start_day < 10 ? '0' . $start_day : $start_day;
                $res[] = array(
                    'x_name' => $start_year . '-' . $start_month . '-' . $start_day,
                    'num' => $result[$start_year . '-' . $start_month . '-' . $start_day] ? $result[$start_year . '-' . $start_month . '-' . $start_day] : 0,
                );
            }
        }

        return $res;
    }

    // chart 属性定义
    public function getChartData($config = array()) {
        $default = array(
            'type' => 'bar',
            'x_title' => 'X轴',
            'y_title' => 'Y轴',
            'x_values' => array(),
            'y_values' => array(),
            'colours' => array(),
        );
        
        $config = array_merge($default, $config);
        $data = array();

        /* X轴标题（X轴下方） */
        $data['x_legend'] = array(
            'text' => $config['x_title'], /* 标题文本 */
            'style' => '{font-size: 12px; color:#736AFF;}', /* CSS样式 */
        );
        /* Y轴标题（Y轴左方） */
        $data['y_legend'] = array(
            'text' => $config['y_title'], /* 标题文本 */
            'style' => '{font-size: 12px; color:#2F55FF;}', /* CSS样式 */
        );

        /* X轴 */
        $data['x_axis'] = array(
            'stroke' => 2, /* X轴的粗细 */
            'tick-height' => 15, /* X轴刻度的长度 */
            'colour' => '#df0fd0', /* 颜色 */
            'grid-colour' => '#00ff00', /* 网格线的颜色 */
            'offset' => 1, /* (0/1), 是否根据数据图形和标签的宽度进行延展 */
            'labels' => array(
                'rotate' => 'vertical', /* 垂直方向显示标签 */
                'size' => 13, /* 字体大小 */
                'align' => 'center', /* 旋转的标签居中对齐，默认是较高的一端对其到刻度上 */
                'labels' => $config['x_values'],
            )
        );

        /* Y轴 */
        $max = max($config['y_values']);
        $steps = $max > 10 ? ceil($max/10) : 1;
        $max = $max > 10 ? $max : 10;
        $data['y_axis'] = array(
            'colour' => '#d000d0',
            'grid-colour' => '#00ff00',
            'steps' => $steps,
            'max' => ($steps*10),
        );

        if ($config['type'] == 'bar') {
            /* 数据元素 */
            $data['elements'] = array(
                array(
                    'type' => 'bar', /* 关于柱图类型参考“bar-all-onlick.json” （从官网下载ofc2完整包的话可以找到这个文件）*/
                    'alpha' => 1, //透明度
                    'colour' => '#9933CC',
                    //'text' => 'Page views',
                    'font-size' => 10,
                    'on-show' => array(
                        'type' => 'grow-up', /* 展现样式 */
                        'cascade' => 1, /* 弹出方式, 此外还有 drop 和 pop */  
                        'delay' => 0.5, /* 延迟时间 */
                    ),
                    'values' => $config['y_values']
                )
            );
        }
        
        if ($config['type'] == 'line') {
            /* 数据元素 */
            $data['elements'] = array(
                array(
                    'type' => 'line', /* 关于柱图类型参考“bar-all-onlick.json” （从官网下载ofc2完整包的话可以找到这个文件）*/
                    'colour' => '#9933CC',
                    'width' => 2,
                    //'text' => 'Page views',
                    'font-size' => 10,
                    'dot-size' => 6,
                    'values' => $config['y_values']
                )
            );
        }

        /* 鼠标提示信息 */
        $data['tooltip'] = array(
            //'shadow' => 1, /* 提示框影子 */
            'mouse' => 2, /* 1 - 滑动样式，2 - 非滑动样式，折线图不支持*/
            //'stroke' => 5, /* 边框粗细 */
            'rounded' => 12, /* 边角圆滑程度 */
            //'colour' => '#00d000', /* 边框颜色 */
            //'background' => '#d0d0ff', /* 背景颜色 */
            //'title' => '{font-size: 14px; color: #905050;}', /* 标题样式 */
            //'body' => '{font-size: 10px; font-weight: bold; color: #9090ff;}', /* 本体样式 */
        );

        return $data;
    }

    // chart 饼状图 属性定义
    public function getChartPieData($config = array()) {
        $data['title']['text'] = '';
        $data['title']['style'] = '{font-size: 30px;}';
        /* 数据元素 */
        $data['elements'] = array(
            array(
                'type' => 'pie', // 饼状图
                'colours' => $config['colours'],
                'alpha' => 0.6,
                'border' => 2,
                'start-angle' => 35,
                'values' => $config['values']
            )
        );

        return $data;
    }

    public function getChartWidth($count) {
        $width = 450; // 总宽度
        $bar_width = 45; // 单个 bar 加 间距
        $number = 10; // 450 最大 10 个 bar
        if ($count > 10) {
            return ($count - 10) * $bar_width + $width;
        }

        return $width;
    }

    public function export($config) {

        //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
        import("Org.Util.PHPExcel");
		import("Org.Util.PHPExcel.Writer.Excel5");
		import("Org.Util.PHPExcel.IOFactory.php");
        //创建Excel对象，注意，不能少了\
        $objPHPExcel = new \PHPExcel();
        
        $filename = $config['x_title'] . '-' . $config['y_title'] . '资源统计表.xls';

        $objPHPExcel->getProperties()->setTitle($filename);
        $objPHPExcel->setActiveSheetIndex(0);
        
        // 表头
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $config['x_title']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', $config['y_title']);

        // 内容
        $maxWidth = 12;
        foreach ($config['x_values'] as $key => $val) {
            $cellLength = strlen($val);
            if ($maxWidth < $cellLength) {
                $maxWidth = $cellLength;
            }
            $objPHPExcel->getActiveSheet(0)->setCellValue('A' . ($key+2), $val);
            $objPHPExcel->getActiveSheet(0)->setCellValue('B' . ($key+2), $config['y_values'][$key]);
        }

        // 宽度
        $objPHPExcel->getActiveSheet(0)->getColumnDimension('A')->setWidth($maxWidth);
        $objPHPExcel->getActiveSheet(0)->getColumnDimension('B')->setWidth(12);

        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //文件通过浏览器下载
    }
}