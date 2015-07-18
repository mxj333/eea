<?php
namespace Common\Logic;
class ResourceStatisticsLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $res_prev = 'r.';
        $default['table'][C('DB_PREFIX').'resource'] = substr($res_prev, 0, -1);
        if ($param['kp_id'] || $param['sub_id']) {
            $rkpr_prev = 'rkpr.';
            $default['join'][] = 'LEFT JOIN ' . C('DB_PREFIX') . 'resource_knowledge_points_relation ' . substr($rkpr_prev, 0, -1) . ' ON ' . $res_prev . 'res_id = ' . $rkpr_prev . 'res_id';
        }
        if ($param['d_id'] || $param['sub_id']) {
            $rdr_prev = 'rdr.';
            $default['join'][] = 'LEFT JOIN ' . C('DB_PREFIX') . 'resource_directory_relation ' . substr($rdr_prev, 0, -1) . ' ON ' . $res_prev . 'res_id = ' . $rdr_prev . 'res_id';
        }

        if ($param['re_id']) {
            $level = count(explode('-', $param['re_id']));
            $re_id = $this->dealValueDelimiter($param['re_id']);
            $regConfig['where']['re_ids'] = $re_id;
            $regConfig['fields'] = 're_ids_children';
            $region = D('Region', 'Model')->getOne($regConfig);
            $region = $region ? $region . ',' . $re_id : $re_id;
            $default['where'][$res_prev . 're_id'] = array('IN', $region);
        }

        if ($param['s_id']) {
            $default['where'][$res_prev . 's_id'] = $param['s_id'];
        }

        $starttime = strtotime($param['res_starttime']);
        $endtime = strtotime($param['res_endtime']);
        if ($starttime && $endtime) {
            $default['where'][$res_prev . 'res_created'] = array('BETWEEN', array($starttime, $endtime));
        } elseif (!$starttime && $endtime) {
            $default['where'][$res_prev . 'res_created'] = array('ELT', $endtime);
        } elseif ($starttime && !$endtime) {
            $default['where'][$res_prev . 'res_created'] = array('EGT', $starttime);
        }

        if (!is_null($param['rsu_id']) && $param['rsu_id'] !== '') {
            $default['where'][$res_prev . 'rsu_id'] = $param['rsu_id'];
        }

        if ($param['kp_id']) {
            // 知识点 末级 id
            $last_kp_id = $this->dealValueDelimiter($param['kp_id'], true);
            // 知识点子集 id  (按照知识点统计时，此条件忽略)
            if ($param['check_x'] != 7) {
                $knowledgeList = reloadCache('knowledgePoints');
                $knowledgeList = generateTree($knowledgeList, 'kp_id', 'kp_pid', '_child', $last_kp_id);
                $knowledgeIds = getChildren($knowledgeList, 'kp_id');
                $knowledgeIds[] = $last_kp_id;
                $default['where'][$rkpr_prev . 'kp_id'] = array('IN', $knowledgeIds);
            }
        }
        
        if ($param['ver_id']) {
            $default['where'][$res_prev . 'res_version'] = $param['ver_id'];
        }
        if ($param['st_id']) {
            $default['where'][$res_prev . 'res_school_type'] = $param['st_id'];
        }
        if ($param['grade_id']) {
            $default['where'][$res_prev . 'res_grade'] = $param['grade_id'];
        }
        if ($param['sem_id']) {
            $default['where'][$res_prev . 'res_semester'] = $param['sem_id'];
        }
        if ($param['sub_id']) {
            $default['where'][$res_prev . 'res_subject'] = $param['sub_id'];
        }
        if ($param['d_id']) {
            // 课文目录 末级 id
            $last_d_id = $this->dealValueDelimiter($param['d_id'], true);;
            // 课文目录 id  (按照目录统计时，此条件忽略)
            if ($param['check_x'] != 8) {
                $directoryList = reloadCache('Directory');
                $directoryList = generateTree($directoryList, 'd_id', 'd_pid', '_child', $last_d_id);
                $directoryList = getChildren($directoryList, 'd_id');
                $directoryIds[] = $last_d_id;
                $default['where'][$rdr_prev . 'd_id'] = array('IN', $directoryIds);
            }
        }

        // 统计 x 轴
        if ($param['check_x'] == 1) {
            // 时间
            if (!$starttime) {
                $resConfig['order'] = 'res_created ASC';
                $resConfig['fields'] = 'res_created';
                $starttime = D('Resource')->getOne($resConfig);
            }
            if (!$endtime) {
                $resConfig['order'] = 'res_created DESC';
                $resConfig['fields'] = 'res_created';
                $endtime = D('Resource')->getOne($resConfig);
            }
            // 计算时间差
            $type_name = $param['check_time_type'] ? $param['check_time_type'] : $this->checkTimeType($starttime, $endtime);
            if ($type_name == 'year') {
                $default['fields']['FROM_UNIXTIME(r.res_created,"%Y")'] = 'x_name';
            } elseif ($type_name == 'month') {
                $default['fields']['FROM_UNIXTIME(r.res_created,"%Y-%m")'] = 'x_name';
            } else {
                $default['fields']['FROM_UNIXTIME(r.res_created,"%Y-%m-%d")'] = 'x_name';
            }
        } elseif ($param['check_x'] == 2) {
            // 来源
            $default['fields'][$res_prev . 'rsu_id'] = 'x_name';
        } elseif ($param['check_x'] == 3) {
            $level = $level < 1 ? 1 : ($level > 4 ? 4 : $level);
            if ($level == 4) {
                $default['fields'][] = $res_prev . 're_id';
                $default['fields'][$res_prev . 're_title'] = 'x_name';
            } else {
                $default['fields']['substring_index(r.re_id, "-", ' . ($level+1) . ')'] = 're_id';
                $default['fields']['substring_index(r.re_title, "-", ' . ($level+1) . ')'] = 'x_name';
            }
        } elseif ($param['check_x'] == 4 || ($param['check_x'] == 7 && !$last_kp_id && !$param['sub_id'])) {
            // 学科
            $default['fields'][$res_prev . 'res_subject'] = 'x_name';
        } elseif ($param['check_x'] == 11) {
            // 学期
            $default['fields'][$res_prev . 'res_semester'] = 'x_name';
        } elseif ($param['check_x'] == 10) {
            // 年级
            $default['fields'][$res_prev . 'res_grade'] = 'x_name';
        } elseif ($param['check_x'] == 5) {
            // 学制
            $default['fields'][$res_prev . 'res_school_type'] = 'x_name';
        } elseif ($param['check_x'] == 6) {
            // 版本
            $default['fields'][$res_prev . 'res_version'] = 'x_name';
        } elseif ($param['check_x'] == 7) {
            // 知识点
            $kp_prev = 'kp.';
            $default['join'][] = 'LEFT JOIN ' . C('DB_PREFIX') . 'knowledge_points ' . substr($kp_prev, 0, -1) . ' ON ' . $rkpr_prev . 'kp_id = ' . $kp_prev . 'kp_id';
            if ($last_kp_id) {
                // 某知识点下级知识点
                $kpConfig['where']['kp_id'] = $last_kp_id;
                $kpConfig['fields'] = 'kp_level,kp_subject';
                $subject_info = D('KnowledgePoints', 'Model')->getOne($kpConfig);
                $default['where'][$kp_prev . 'kp_subject'] = intval($subject_info['kp_subject']);
                $default['where'][$kp_prev . 'kp_level'] = intval($subject_info['kp_level']+1);
            } else {
                // 某学科下知识点
                $default['where'][$kp_prev . 'kp_subject'] = intval($param['sub_id']);
                $default['where'][$kp_prev . 'kp_level'] = 0;
            }
            $default['fields'][$kp_prev . 'kp_id'] = 'kp_id';
            $default['fields'][$kp_prev . 'kp_title'] = 'x_name';
        } elseif ($param['check_x'] == 8) {
            // 目录
            $d_prev = 'd.';
            $default['join'][] = 'LEFT JOIN ' . C('DB_PREFIX') . 'directory ' . substr($d_prev, 0, -1) . ' ON ' . $rdr_prev . 'd_id = ' . $d_prev . 'd_id';
            if ($last_d_id) {
                // 某目录下级目录
                $dConfig['where']['d_id'] = $last_d_id;
                $dConfig['fields'] = 'd_level,d_version,d_school_type,d_grade,d_semester,d_subject';
                $subject_info = D('Directory', 'Model')->getOne($dConfig);
                $default['where'][$d_prev . 'd_version'] = intval($subject_info['d_version']);
                $default['where'][$d_prev . 'd_school_type'] = intval($subject_info['d_school_type']);
                $default['where'][$d_prev . 'd_grade'] = intval($subject_info['d_grade']);
                $default['where'][$d_prev . 'd_semester'] = intval($subject_info['d_semester']);
                $default['where'][$d_prev . 'd_subject'] = intval($subject_info['d_subject']);
                $default['where'][$d_prev . 'd_level'] = intval($subject_info['d_level']+1);
            } else {
                // 某学科下目录
                $default['where'][$d_prev . 'd_version'] = intval($param['ver_id']);
                $default['where'][$d_prev . 'd_school_type'] = intval($param['st_id']);
                $default['where'][$d_prev . 'd_grade'] = intval($param['grade_id']);
                $default['where'][$d_prev . 'd_semester'] = intval($param['sem_id']);
                $default['where'][$d_prev . 'd_subject'] = intval($param['sub_id']);
                $default['where'][$d_prev . 'd_level'] = 5;
            }
            $default['fields'][$d_prev . 'd_id'] = 'd_id';
            $default['fields'][$d_prev . 'd_title'] = 'x_name';
        } elseif ($param['check_x'] == 9) {
            // 格式类型
            $rf_prev = 'rf.';
            $default['join'][] = 'LEFT JOIN ' . C('DB_PREFIX') . 'resource_file ' . substr($rf_prev, 0, -1) . ' ON ' . $res_prev . 'rf_id = ' . $rf_prev . 'rf_id';
            $default['fields'][$rf_prev . 'rt_id'] = 'x_name';
        } else {
            // 使用类型
            $default['fields'][$res_prev . 'rc_id'] = 'x_name';
        }
        $default['group'] = 'x_name';

        // 统计Y轴字段
        $check_y = intval($param['check_y']);
        if ($check_y == 1) {
            $default['fields']['sum(' . $res_prev . 'res_downloads)'] = 'num';
        } elseif ($check_y == 2) {
            $default['fields']['sum(' . $res_prev . 'res_hits)'] = 'num';
        } else {
            $default['fields']['count(' . $res_prev . 'res_id)'] = 'num';
        }
        
        $config = array_merge($default, $config);

        $lists = D('Resource')->getAll($config);

        // 结果处理
        if ($param['check_x'] == 1) {
            $lists = $this->dealDatetime($starttime, $endtime, $type_name, $lists);
        } elseif ($param['check_x'] == 2) {
            // 资源商
            $rsu_list = reloadCache('resourceSuppliers');
            foreach ($rsu_list as $key => $value) {
                $lists[$key] = array(
                    'x_name' => strval($value),
                    'num' => intval($lists[$key]),
                );
            }
        } elseif ($param['check_x'] == 3) {
            // 地区
        } elseif ($param['check_x'] == 4 || (($param['check_x'] == 7 && !$last_kp_id) || ($param['check_x'] == 8 && !$last_d_id) && !$param['sub_id'] && $param['sem_id'])) {
            // 学科
            $tag = reloadCache('tag');
            $tag[5][0] = '其他';
            foreach ($tag[5] as $key => $value) {
                $lists[$key] = array(
                    'x_name' => strval($value),
                    'num' => intval($lists[$key]),
                );
            }
        } elseif ($param['check_x'] == 11 || ($param['check_x'] == 8 && !$param['sem_id'] && $param['grade_id'])) {
            // 学期
            $tag = reloadCache('tag');
            $tag[8][0] = '其他';
            foreach ($tag[8] as $key => $value) {
                $lists[$key] = array(
                    'x_name' => strval($value),
                    'num' => intval($lists[$key]),
                );
            }
        } elseif ($param['check_x'] == 10 || ($param['check_x'] == 8 && !$param['grade_id'] && $param['st_id'])) {
            // 年级
            $tag = reloadCache('tag');
            $tag[7][0] = '其他';
            foreach ($tag[7] as $key => $value) {
                $lists[$key] = array(
                    'x_name' => strval($value),
                    'num' => intval($lists[$key]),
                );
            }
        } elseif ($param['check_x'] == 5 || ($param['check_x'] == 8 && !$param['st_id'] && $param['ver_id'])) {
            // 学制
            $tag = reloadCache('tag');
            $tag[4][0] = '其他';
            foreach ($tag[4] as $key => $value) {
                $lists[$key] = array(
                    'x_name' => strval($value),
                    'num' => intval($lists[$key]),
                );
            }
        } elseif ($param['check_x'] == 6 || ($param['check_x'] == 8 && !$param['ver_id'])) {
            // 版本
            $tag = reloadCache('tag');
            $tag[6][0] = '其他';
            foreach ($tag[6] as $key => $value) {
                $lists[$key] = array(
                    'x_name' => strval($value),
                    'num' => intval($lists[$key]),
                );
            }
        } elseif ($param['check_x'] == 9) {
            // 格式类型
            $resType = reloadCache('resourceType');
            foreach ($resType as $key => $value) {
                $lists[$key] = array(
                    'x_name' => strval($value['rt_title']),
                    'num' => intval($lists[$key]),
                );
            }
        } elseif (in_array($param['check_x'], array(7, 8))) {
            // 目录
            // 知识点
        } else {
            // 使用类型
            $resCate = reloadCache('resourceCategory');
            foreach ($resCate as $key => $value) {
                $lists[$key] = array(
                    'x_name' => strval($value),
                    'num' => intval($lists[$key]),
                );
            }
        }
        //dump($lists);exit;
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