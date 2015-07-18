<?php
namespace Common\Logic;
class StatisticsLogic extends Logic {
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
        
        $filename = $config['x_title'] . '-' . $config['y_title'] . $config['title'] . '统计表.xls';

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

    // 地区选择  如果选择的是自己所属地区的下属地区  则为 下属地区id   否则  为自身所属地区id
    public function dealRegion($re_id) {

        if (!$string) {
            return session('re_id');
        }

        // -1-2  过滤 第一个 -
        if ($re_id[0] == '-') {
            $re_id = substr($re_id, 1);
        }
        // 1-2-0  过滤 -0
        if (substr($re_id, -2) == '-0') {
            $re_id = substr($re_id, 0, -2);
        }

        // 比较  确定是否为下属区域
        $me_re_arr = explode('-', session('re_id'));
        $re_arr = explode('-', $re_id);
        $status = true;
        foreach ($me_re_arr as $key => $val) {
            if ($re_arr[$key] != $val) {
                $status = false;
                break;
            }
        }

        if ($status) {
            return $re_id;
        }
        return session('re_id');
    }
}