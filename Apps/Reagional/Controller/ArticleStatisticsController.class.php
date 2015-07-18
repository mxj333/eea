<?php
namespace Reagional\Controller;
class ArticleStatisticsController extends ReagionalController {

    // 地区统计
    public function index() {

        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . '.js'));
        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/statistics.css'));

        // 数据列表
        $param = (array)$_REQUEST;
        $param['re_id'] = session('re_id');
        $res = $this->apiReturnDeal(getApi($param, 'Article', 'statistics'));
        
        // 类型
        $this->assign('m_id', I('m_id'));
        $this->assign('model', reloadCache('model'));
        // 发布者
        $this->assign('me_nickname', I('me_nickname', '', 'strval'));
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
            $config['x_title'] = '地区';
        } elseif ($x_type == 3) {
            $config['x_title'] = '发布者';
        } else {
            $config['x_title'] = '类型';
        }
        
        $y_type = I('check_y', 0, 'intval');
        if ($y_type == 1) {
            $config['y_title'] = '访问量';
        } else {
            $config['y_title'] = '总个数';
        }

        $param = (array)$_REQUEST;
        $param['re_id'] = session('re_id');
        $res = $this->apiReturnDeal(getApi($param, 'Article', 'statistics'));

        $y_values = array();
        $x_values = array();
        foreach ($res as $info) {
            $x_values[] = $info['x_name'];
            $y_values[] = intval($info['num']);
        }

        $config['x_values'] = $x_values;
        $config['y_values'] = $y_values;
        
        $data = D('Statistics')->getChartData($config);

        echo json_encode($data);
    }

    public function export() {
        
        $x_type = I('check_x', 0, 'intval');
        if ($x_type == 1) {
            $config['x_title'] = '时间';
        } elseif ($x_type == 2) {
            $config['x_title'] = '地区';
        } elseif ($x_type == 3) {
            $config['x_title'] = '发布者';
        } else {
            $config['x_title'] = '类型';
        }
        
        $y_type = I('check_y', 0, 'intval');
        if ($y_type == 1) {
            $config['y_title'] = '访问量';
        } else {
            $config['y_title'] = '总个数';
        }

        $config['title'] = '资讯';

        $param = (array)$_REQUEST;
        $param['re_id'] = session('re_id');
        $res = $this->apiReturnDeal(getApi($param, 'Article', 'statistics'));

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
}