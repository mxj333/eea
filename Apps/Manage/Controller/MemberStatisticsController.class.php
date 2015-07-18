<?php
namespace Manage\Controller;
class MemberStatisticsController extends ManageController {

    // check_x 地区/时间/来源 统计
    public function index() {

        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . '.js'));
        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/statistics.css'));

        // 数据列表
        $res = D('MemberStatistics')->lists();
        // 地区
        $this->assign('re_title', I('re_title'));
        $this->assign('re_id', I('re_id'));
        // 时间
        $this->assign('starttime', I('starttime'));
        $this->assign('endtime', I('endtime'));
        // 统计字段
        $this->assign('check_x', I('check_x'));
        // 时间类型
        $this->assign('check_time_type', I('check_time_type'));
        // 统计字段
        $this->assign('check_y', I('check_y'));
        $this->assign('width', D('MemberStatistics')->getChartWidth(count($res)));
        $this->display();
    }

    public function getChartData() {
        
        $x_type = I('check_x', 0, 'intval');
        if ($x_type == 1) {
            $config['x_title'] = '时间';
        } else {
            $config['x_title'] = '地区';
        }
        
        $y_type = I('check_y', 0, 'intval');
        if ($y_type == 1) {
            $config['y_title'] = '登录人数';
        } elseif ($y_type == 2) {
            $config['y_title'] = '在线人数';
        } else {
            $config['y_title'] = '登录次数';
        }

        $res = D('MemberStatistics')->lists();
        $y_values = array();
        $x_values = array();
        foreach ($res as $info) {
            $x_values[] = $info['x_name'];
            $y_values[] = intval($info['num']);
        }

        $config['x_values'] = $x_values;
        $config['y_values'] = $y_values;
        
        $data = D('MemberStatistics')->getChartData($config);

        echo json_encode($data);
    }

    public function export() {
        
        $x_type = I('check_x', 0, 'intval');
        if ($x_type == 1) {
            $config['x_title'] = '时间';
        } else {
            $config['x_title'] = '地区';
        }

        $y_type = I('check_y', 0, 'intval');
        if ($y_type == 1) {
            $config['y_title'] = '登录人数';
        } elseif ($y_type == 2) {
            $config['y_title'] = '在线人数';
        } else {
            $config['y_title'] = '登录次数';
        }

        $res = D('MemberStatistics')->lists();
        $y_values = array();
        $x_values = array();
        foreach ($res as $info) {
            $x_values[] = $info['x_name'];
            $y_values[] = intval($info['num']);
        }

        $config['x_values'] = $x_values;
        $config['y_values'] = $y_values;
        
        D('MemberStatistics')->export($config);
    }

    public function onlineNumber() {

        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/memberstatisticsPie.js'));
        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/statistics.css'));

        $this->display();
    }

    public function onlineNumberChart() {

        $info = D('MemberOnlineLog')->onlineNumber();
        $config['colours'] = array('#d01f3c', '#356aa0');
        $config['values'] = array(
            array('value' => $info['login_num'], 'label' => '登录人数'),
            array('value' => $info['logout_num'], 'label' => '游客人数'),
        );

        $data = D('MemberStatistics')->getChartPieData($config);

        echo json_encode($data);
    }

    // 获取地区
    public function getRegion() {
        $region = loadCache('region');
        $this->ajaxReturn($region[I('id', 0, 'intval')]);
    }
}