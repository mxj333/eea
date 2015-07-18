<?php
namespace Api\Controller;
use Think\Controller;
class StatisticsController extends OpenController {

    // 获取总数
    public function getCounts() {
        extract($_POST['args']);

        // 范围 're_id', 's_id', 'c_id'
        if ($re_id) {
            $default['where']['re_id'] = array('LIKE', strval($re_id) . '%');
        }
        if ($s_id) {
            $default['where']['s_id'] = intval($s_id);
        }
        if ($c_id) {
            $default['where']['c_id'] = intval($c_id);
        }

        // 类型 'member', 'resource', 'app', 'school'
        $type = $type ? $type : 'member,resource,app,school,article,class';
        $type_list = explode(',', $type);
        $result = array();
        // 所属平台
        $belong = in_array(intval($belong), array(1, 2, 3)) ? intval($belong) : 1;

        foreach ($type_list as $type_name) {
            // 初始化
            $config = array();

            switch ($type_name) {
                case 'member':
                    $config = (array)$default;
                    $config['where']['me_is_deleted'] = 9;
                    $total = D('Member', 'Model')->total($config);
                    break;
                case 'resource':
                    $config = (array)$default;
                    $config['where']['SUBSTRING(res_is_deleted, '.$belong.', 1)'] = 9;
                    $config['where']['SUBSTRING(res_is_eliminated, '.$belong.', 1)'] = 9;
                    $total = D('Resource', 'Model')->total($config);
                    break;
                case 'article':
                    $config = (array)$default;
                    $config['where']['art_is_deleted'] = 9;
                    $config['where']['art_status'] = array('neq', 9);
                    $total = D('Article', 'Model')->total($config);
                    break;
                case 'school':
                    $config = (array)$default;
                    $config['where']['s_is_deleted'] = 9;
                    $total = D('School', 'Model')->total($config);
                    break;
                case 'class':
                    $config = (array)$default;
                    $config['where']['c_is_deleted'] = 9;
                    $total = D('Class', 'Model')->total($config);
                    break;
                default : 
                    // app 应用  有后台控制   开启、有效期内 才对外展示
                    $config['where']['a_status'] = 1;
                    $config['where']['a_online_time'] = array('ELT', $param['a_online_time']);
                    $config['where']['a_valided'] = array('EGT', $param['a_valided']);
                    $total = D('App', 'Model')->total($config);
                    break;
            }

            $result[$type_name] = intval($total);
        }

        $this->returnData($result);
    }

    public function getMember() {
        extract($_POST['args']);

        // 范围 're_id', 's_id', 'c_id'
        if ($re_id) {
            $default['where']['re_id'] = array('LIKE', strval($re_id) . '%');
        }
        if ($s_id) {
            $default['where']['s_id'] = intval($s_id);
        }
        if ($c_id) {
            $default['where']['c_id'] = intval($c_id);
        }
        if ($starttime && $endtime) {
            $default['where']['me_created'] = array('BETWEEN', array(strtotime($starttime), strtotime($endtime)));
        } elseif ($starttime && !$endtime) {
            $default['where']['me_created'] = array('EGT', strtotime($starttime));
        } elseif (!$starttime && $endtime) {
            $default['where']['me_created'] = array('ELT', strtotime($endtime));
        } 

        // 统计方式  时间、地区、身份
        $way = in_array($way, array('time', 'region', 'type')) ? $way : 'time';
        if ($way == 'time') {
            // 年、月、日
            $way_type = $way_type ? $way_type : 'year';
            if ($way_type == 'month') {
                $default['fields']['FROM_UNIXTIME(me_created,"%Y-%m")'] = 'x_name';
            } elseif ($way_type == 'day') {
                $default['fields']['FROM_UNIXTIME(me_created,"%Y-%m-%d")'] = 'x_name';
            } else {
                $default['fields']['FROM_UNIXTIME(me_created,"%Y")'] = 'x_name';
            }
        } elseif ($way == 'type') {
            $default['fields']['me_type'] = 'x_name';
        } else {
            // 地区统计
            // 待定
        }
        $default['group'] = 'x_name';

        // 统计类别
        $result_type = $result_type ? $result_type : 'me_id';
        $default['fields']['count('.$result_type.')'] = 'num';
        
        $result = D('Member', 'Model')->getAll($default);
        $this->returnData($result);
    }

    public function getResource() {
        extract($_POST['args']);
        
        // 所属平台
        $belong = in_array(intval($belong), array(1, 2, 3)) ? intval($belong) : 1;

        $default['where']['SUBSTRING(res_is_deleted, '.$belong.', 1)'] = 9; // 未删
        $default['where']['SUBSTRING(res_is_eliminated, '.$belong.', 1)'] = 9; // 未淘汰

        // 范围 're_id', 's_id', 'c_id'
        if ($re_id) {
            $default['where']['re_id'] = array('LIKE', strval($re_id) . '%');
        }
        if ($s_id) {
            $default['where']['s_id'] = intval($s_id);
        }
        if ($c_id) {
            $default['where']['c_id'] = intval($c_id);
        }

        // 统计方式  时间、地区
        $way = in_array($way, array('time', 'region', 'subject')) ? $way : 'time';
        if ($way == 'time') {
            // 年、月、日
            // 待定
        } elseif ($way == 'subject') {
            $default['fields']['res_subject'] = 'x_name';
        } else {
            // 地区统计
            $level = count(explode('-', $re_id)); // 第几级
            $level = $level < 1 ? 1 : ($level > 4 ? 4 : $level);
            if ($level == 4) {
                $default['fields']['re_title'] = 'x_name';
            } else {
                $default['fields']['substring_index(re_title, "-", ' . ($level+1) . ')'] = 'x_name';
            }
        }
        $default['group'] = 'x_name';

        // 统计类别
        $result_type = $result_type ? $result_type : 'res_id';
        $default['fields']['count('.$result_type.')'] = 'num';

        $default['order'] = 'num desc';

        $result = D('Resource', 'Model')->getAll($default);
        $this->returnData($result);
    }

    public function getApp() {
        extract($_POST['args']);

        // 范围 're_id', 's_id', 'c_id'

        // 统计方式
        $way = in_array($way, array('goodRate', 'number')) ? $way : 'goodRate';
        if ($way == 'number') {
            // 好评
            $default['table'][C('DB_PREFIX').'app_score_log'] = 'asl';
            $default['join'][] = 'LEFT JOIN ' . C('DB_PREFIX') . 'app a ON asl.a_id = a.a_id';
            $default['where']['asl.asl_score'] = array('GT', 3);
            $default['fields']['a.ac_id'] = 'ac_id';
            $default['fields']['count(asl.asl_id)'] = 'num';
            $default['group'] = 'ac_id';
            $goodNum = D('AppScoreLog', 'Model')->getAll($default);
            // 中评
            $default['where']['asl.asl_score'] = array('EQ', 3);
            $generalNum = D('AppScoreLog', 'Model')->getAll($default);

            // 所有应用
            $app_cate_list = reloadCache('appCategory');

            $result = array();
            foreach ($app_cate_list as $ac_id => $ac_title) {
                $result[$ac_id] = array(
                    'good' => array('ac_title' => $ac_title, 'num' => intval($goodNum[$ac_id])),
                    'general' => array('ac_title' => $ac_title, 'num' => intval($generalNum[$ac_id])),
                );
            }
            $this->returnData($result);
        } else {
            // 好评
            $default['where']['asl_score'] = array('GT', 3);
            $goodNum = D('AppScoreLog', 'Model')->total($default);

            // 总评
            $totalNum = D('AppScoreLog', 'Model')->total();

            // 好评率
            $rate = ceil($goodNum * 100 / $totalNum);

            $this->returnData($rate);
        }
    }

    public function getArticle() {
        extract($_POST['args']);

        // 范围 're_id', 's_id', 'c_id'
        if ($re_id) {
            $default['where']['re_id'] = array('LIKE', strval($re_id) . '%');
        }
        if ($s_id) {
            $default['where']['s_id'] = intval($s_id);
        }
        if ($c_id) {
            $default['where']['c_id'] = intval($c_id);
        }

        // 统计方式
        $way = in_array($way, array('ca_id')) ? $way : 'ca_id';
        if ($way == 'ca_id') {
            $default['fields']['ca_id'] = 'x_name';
        }
        $default['group'] = 'x_name';

        // 统计类别
        $result_type = $result_type ? $result_type : 'art_id';
        $default['fields']['count('.$result_type.')'] = 'num';

        $default['order'] = 'num desc';

        $result = D('Article', 'Model')->getAll($default);

        $this->returnData($result);
    }

    public function getClass() {
        extract($_POST['args']);

        // 范围 're_id', 's_id', 'c_id'
        if ($re_id) {
            $default['where']['re_id'] = array('LIKE', strval($re_id) . '%');
        }
        if ($s_id) {
            $default['where']['s_id'] = intval($s_id);
        }
        if ($c_id) {
            $default['where']['c_id'] = intval($c_id);
        }

        // 统计方式
        $way = in_array($way, array('grade')) ? $way : 'grade';
        if ($way == 'grade') {
            $default['fields']['c_grade'] = 'x_name';
        }
        $default['group'] = 'x_name';

        // 统计类别
        $result_type = $result_type ? $result_type : 'c_id';
        $default['fields']['count('.$result_type.')'] = 'num';

        $default['order'] = 'num desc';

        $result = D('Class', 'Model')->getAll($default);

        $this->returnData($result);
    }
}