<?php
namespace Reagional\Controller;
class MemberStatisticsController extends ReagionalController {

    // check_x 地区/时间/来源 统计
    public function index() {

        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . '.js'));
        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/statistics.css'));

        // 数据列表
        $param = (array)$_REQUEST;
        $param['re_id'] = D('Statistics')->dealRegion(I('re_id'));
        $res = $this->apiReturnDeal(getApi($param, 'Member', 'statistics'));
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
        $this->assign('width', D('Statistics')->getChartWidth(count($res)));
        $this->display();
    }

    public function getChartData() {
        
        $x_type = I('check_x', 0, 'intval');
        if ($x_type == 1) {
            $config['x_title'] = '时间';
        } elseif ($x_type == 2) {
            $config['x_title'] = '身份';
        } elseif ($x_type == 3) {
            $config['x_title'] = '学校';
        } else {
            $config['x_title'] = '地区';
        }
        
        $y_type = I('check_y', 0, 'intval');
        if ($y_type == 1) {
            $config['y_title'] = '人数';
        } else {
            $config['y_title'] = '登录次数';
        }

        $param = (array)$_REQUEST;
        $param['re_id'] = D('Statistics')->dealRegion(I('re_id'));
        $res = $this->apiReturnDeal(getApi($param, 'Member', 'statistics'));

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
        } elseif ($x_type == 2) {
            $config['x_title'] = '身份';
        } elseif ($x_type == 3) {
            $config['x_title'] = '学校';
        } else {
            $config['x_title'] = '地区';
        }
        
        $y_type = I('check_y', 0, 'intval');
        if ($y_type == 1) {
            $config['y_title'] = '人数';
        } else {
            $config['y_title'] = '登录次数';
        }

        $config['title'] = '用户';

        $param = (array)$_REQUEST;
        $param['re_id'] = D('Statistics')->dealRegion(I('re_id'));
        $res = $this->apiReturnDeal(getApi($param, 'Member', 'statistics'));

        $y_values = array();
        $x_values = array();
        foreach ($res as $info) {
            $x_values[] = $info['x_name'];
            $y_values[] = intval($info['num']);
        }

        $config['x_values'] = $x_values;
        $config['y_values'] = $y_values;
        
        D('Statistics')->export($config);
    }

    // 获取地区
    public function getRegion() {
        $region = loadCache('region');
        $this->ajaxReturn($region[I('id', 0, 'intval')]);
    }
}