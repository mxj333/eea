<?php
namespace Manage\Controller;
class AppStatisticsController extends ManageController {

    // type 1 地区统计
    public function index() {

        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . '.js'));
        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/statistics.css'));

        // 数据列表
        $res = D('AppStatistics')->lists();
        // 类型
        $this->assign('ac_id', I('ac_id', 0, 'intval'));
        $this->assign('ac_list', reloadCache('appCategory'));
        // 时间
        $this->assign('starttime', I('starttime'));
        $this->assign('endtime', I('endtime'));
        // 统计字段
        $this->assign('check_x', I('check_x'));
        // 时间类型
        $this->assign('check_time_type', I('check_time_type'));
        // 统计字段
        $this->assign('check_y', I('check_y'));
        $this->assign('width', D('AppStatistics')->getChartWidth(count($res)));
        $this->display();
    }

    public function getChartData() {
        
        $x_type = I('check_x', 0, 'intval');
        if ($x_type == 1) {
            $config['x_title'] = '时间';
        } elseif ($x_type == 2) {
            $config['x_title'] = '区域类型';
        } else {
            $config['x_title'] = '类型';
        }
        
        $y_type = I('check_y', 0, 'intval');
        if ($y_type == 1) {
            $config['y_title'] = '访问量';
        } else {
            $config['y_title'] = '总个数';
        }

        $res = D('AppStatistics')->lists();
        $y_values = array();
        $x_values = array();
        foreach ($res as $info) {
            $x_values[] = $info['x_name'];
            $y_values[] = intval($info['num']);
        }

        $config['x_values'] = $x_values;
        $config['y_values'] = $y_values;
        
        $data = D('AppStatistics')->getChartData($config);

        echo json_encode($data);
    }

    public function export() {
        
        $x_type = I('check_x', 0, 'intval');
        if ($x_type == 1) {
            $config['x_title'] = '时间';
        } elseif ($x_type == 2) {
            $config['x_title'] = '区域类型';
        } else {
            $config['x_title'] = '类型';
        }
        
        $y_type = I('check_y', 0, 'intval');
        if ($y_type == 1) {
            $config['y_title'] = '访问量';
        } else {
            $config['y_title'] = '总个数';
        }

        $res = D('AppStatistics')->lists();
        $y_values = array();
        $x_values = array();
        foreach ($res as $info) {
            $x_values[] = $info['x_name'];
            $y_values[] = intval($info['num']);
        }

        $config['x_values'] = $x_values;
        $config['y_values'] = $y_values;
        
        D('AppStatistics')->export($config);
    }

    // 获取地区
    public function getRegion() {
        $region = loadCache('region');
        $this->ajaxReturn($region[I('id', 0, 'intval')]);
    }
}