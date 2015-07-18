<?php
namespace Manage\Controller;
class ArticleStatisticsController extends ManageController {

    // type 1 地区统计
    public function index() {

        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . '.js'));
        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/statistics.css'));

        // 数据列表
        $res = D('ArticleStatistics')->lists();
        if ($res === false) {
            $this->error(D('ArticleStatistics')->getError(), 'index');
        }
        // 地区
        $this->assign('re_title', I('re_title'));
        $this->assign('re_id', I('re_id'));
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
        $this->assign('width', D('ArticleStatistics')->getChartWidth(count($res)));
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

        $res = D('ArticleStatistics')->lists();
        $y_values = array();
        $x_values = array();
        foreach ($res as $info) {
            $x_values[] = $info['x_name'];
            $y_values[] = intval($info['num']);
        }

        $config['x_values'] = $x_values;
        $config['y_values'] = $y_values;
        
        $data = D('ArticleStatistics')->getChartData($config);

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

        $res = D('ArticleStatistics')->lists();
        $y_values = array();
        $x_values = array();
        foreach ($res as $info) {
            $x_values[] = $info['x_name'];
            $y_values[] = intval($info['num']);
        }

        $config['x_values'] = $x_values;
        $config['y_values'] = $y_values;
        
        D('ArticleStatistics')->export($config);
    }

    // 获取地区
    public function getRegion() {
        $region = loadCache('region');
        $this->ajaxReturn($region[I('id', 0, 'intval')]);
    }

    // 获取创建者
    public function searchPublisher() {
        $publisher = I('name');
        if (I('ptype') == 'user') {
            $config['where']['u_nickname'] = array('LIKE', '%' . $publisher . '%');
            $config['fields']['u_id'] = 'id';
            $config['fields']['u_nickname'] = 'nickname';
            $result = D('User')->getAll($config);
        } else {
            $config['where']['me_is_deleted'] = 9;
            $config['where']['me_nickname'] = array('LIKE', '%' . $publisher . '%');
            $config['fields']['me_id'] = 'id';
            $config['fields']['me_nickname'] = 'nickname';
            $result = D('Member')->getAll($config);
        }
        
        echo json_encode($result);
    }
}