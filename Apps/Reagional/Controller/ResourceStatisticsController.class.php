<?php
namespace Reagional\Controller;
class ResourceStatisticsController extends ReagionalController {

    // check_x 地区/时间/来源 统计
    public function index() {

        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . '.js'));
        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/statistics.css'));

        // 数据列表
        $param = (array)$_REQUEST;
        $param['re_id'] = session('re_id');
        $res = $this->apiReturnDeal(getApi($param, 'Resource', 'statistics'));
        // 时间
        $this->assign('res_starttime', I('res_starttime'));
        $this->assign('res_endtime', I('res_endtime'));
        // 资源商
        $this->assign('suppliers', reloadCache('resourceSuppliers'));
        $this->assign('rsu_id', I('rsu_id'));
        // 知识点
        $this->assign('kp_title', I('kp_title'));
        $this->assign('kp_id', I('kp_id'));
        // 课文目录
        $this->assign('d_title', I('d_title'));
        $this->assign('d_id', I('d_id'));
        $tag = reloadCache('tag');
        // 版本
        $this->assign('ver_id', I('ver_id'));
        $this->assign('version', $tag[6]);
        // 学制
        $this->assign('st_id', I('st_id'));
        $this->assign('school_type', $tag[4]);
        // 年级
        $this->assign('grade_id', I('grade_id'));
        $this->assign('grade', $tag[7]);
        // 学期
        $this->assign('sem_id', I('sem_id'));
        $this->assign('semester', $tag[8]);
        // 学科
        $this->assign('sub_id', I('sub_id'));
        $this->assign('subject', $tag[5]);
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
            $config['x_title'] = '来源';
        } elseif ($x_type == 3) {
            $config['x_title'] = '地区';
        } elseif ($x_type == 4) {
            $config['x_title'] = '学科';
        } elseif ($x_type == 5) {
            $config['x_title'] = '学制';
        } elseif ($x_type == 6) {
            $config['x_title'] = '版本';
        } elseif ($x_type == 7) {
            $config['x_title'] = '知识点';
        } elseif ($x_type == 8) {
            $config['x_title'] = '课文目录';
        } elseif ($x_type == 9) {
            $config['x_title'] = '格式类型';
        } elseif ($x_type == 10) {
            $config['x_title'] = '年级';
        } elseif ($x_type == 11) {
            $config['x_title'] = '学期';
        } else {
            $config['x_title'] = '使用类型';
        }
        
        $y_type = I('check_y', 0, 'intval');
        if ($y_type == 1) {
            $config['y_title'] = '下载量';
        } elseif ($y_type == 2) {
            $config['y_title'] = '访问量';
        } else {
            $config['y_title'] = '总个数';
        }

        $param = (array)$_REQUEST;
        $param['re_id'] = session('re_id');
        $res = $this->apiReturnDeal(getApi($param, 'Resource', 'statistics'));

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
            $config['x_title'] = '来源';
        } elseif ($x_type == 3) {
            $config['x_title'] = '地区';
        } elseif ($x_type == 4) {
            $config['x_title'] = '学科';
        } elseif ($x_type == 5) {
            $config['x_title'] = '学制';
        } elseif ($x_type == 6) {
            $config['x_title'] = '版本';
        } elseif ($x_type == 7) {
            $config['x_title'] = '知识点';
        } elseif ($x_type == 8) {
            $config['x_title'] = '课文目录';
        } elseif ($x_type == 9) {
            $config['x_title'] = '格式类型';
        } elseif ($x_type == 10) {
            $config['x_title'] = '年级';
        } elseif ($x_type == 11) {
            $config['x_title'] = '学期';
        } else {
            $config['x_title'] = '使用类型';
        }

        $y_type = I('check_y', 0, 'intval');
        if ($y_type == 1) {
            $config['y_title'] = '下载量';
        } elseif ($y_type == 2) {
            $config['y_title'] = '访问量';
        } else {
            $config['y_title'] = '总个数';
        }

        $param = (array)$_REQUEST;
        $param['re_id'] = session('re_id');
        $res = $this->apiReturnDeal(getApi($param, 'Resource', 'statistics'));

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

    public function getKnowledge() {
        $sub_id = I('request.sub_id', 0, 'intval');
        $kp_pid = I('id', 0, 'intval');
        if ($kp_pid) {
            $subConfig['kp_pid'] = $kp_pid;
        } elseif ($sub_id) {
            $subConfig['kp_subject'] = $sub_id;
            $subConfig['kp_pid'] = 0;
        } else {
            $subConfig = array();
        }
        
        if ($subConfig) {
            $subConfig['fields'] = 'kp_id,kp_title';
            $subConfig['is_page'] = false;
            $res = $this->apiReturnDeal(getApi($subConfig, 'KnowledgePoints', 'lists'));
            $return = $res['list'];
        } else {
            $return = array();
        }

        $this->ajaxReturn($return);
    }

    public function getDirectory() {
        $ver_id = I('request.ver_id', 0, 'intval');
        $st_id = I('request.st_id', 0, 'intval');
        $grade_id = I('request.grade_id', 0, 'intval');
        $sem_id = I('request.sem_id', 0, 'intval');
        $sub_id = I('request.sub_id', 0, 'intval');
        $d_pid = I('id', 0, 'intval');
        if ($d_pid) {
            $subConfig['d_pid'] = I('id', 0, 'intval');
        } elseif ($ver_id && $st_id && $grade_id && $sem_id && $sub_id) {
            $subConfig['d_version'] = $ver_id;
            $subConfig['d_school_type'] = $st_id;
            $subConfig['d_grade'] = $grade_id;
            $subConfig['d_semester'] = $sem_id;
            $subConfig['d_subject'] = $sub_id;
            $subConfig['d_level'] = 5;
        } else {
            $subConfig = array();
        }

        if ($subConfig) {
            $subConfig['re_id'] = '';
            $subConfig['fields'] = 'd_id,d_title';
            $subConfig['is_page'] = false;
            $res = $this->apiReturnDeal(getApi($subConfig, 'Directory', 'lists'));
            $return = $res['list'];
        } else {
            $return = array();
        }
        
        $this->ajaxReturn($return);
    }
}