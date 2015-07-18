<?php
namespace Home\Controller;
use Think\Controller;
class TestController extends HomeController {
    public function index() {
        echo '123123';
        exit;
    }

    public function memberLogin() {
        
        $length = I('max', 0, 'intval');
        for ($start = 0; $start < $length; $start ++) {
            $me_id = rand(1, 334);
            $time = rand(2013, 2015) . '-' . rand(1, 12) . '-' . rand(1, 28);
            $data = array(
                'me_id' => $me_id,
                'mll_ip' => ip2long('127.0.0.1'),
                'mll_created' => strtotime($time),
            );
            
            $res = D('MemberLoginLog', 'Model')->insert($data);
            echo $start;
            dump($res);
            echo '============<br/>';
        }
    }

    public function resource() {
        // 资源导数据
        $resourceList = D('ResourceFile')->getAll();
        foreach ($resourceList as $resourceInfo) {
            // 资源信息
            $config = array();
            $config['where']['rf_id'] = $resourceInfo['rf_id'];
            $config['fields'] = 'res_id';
            $res_ids = D('Resource')->getAll($config);
            if ($res_ids) {
                $resData['rt_id'] = $resourceInfo['rt_id'];
                $resData['res_transform_status'] = $resourceInfo['rf_transform_status'];
                $resConfig['where']['res_id'] = array('IN', $res_ids);
                $rows = D('Resource')->update($resData, $resConfig);
                if ($rows === false) {
                    echo '==========';
                    echo '<br/>';
                    echo $resourceInfo['rf_id'] . ' => ' . $res_ids;
                    echo '<br/>';
                }
            }
        }
    }

    public function region() {
        // 地区 增加 re_ids
        /*$config['where']['re_level'] = 3;
        $region = D('Region', 'Model')->getAll($config);
        foreach ($region as $re_info) {
            echo $re_info['re_title'];
            echo '<br/>';
            $reConfig['where']['re_id'] = $re_info['re_pid'];
            $reConfig['fields'] = 're_titles';
            $re_pids = D('Region', 'Model')->getOne($reConfig);
            $re_ids = $re_pids . '-' . $re_info['re_title'];
            echo $re_ids;
            echo '<br/>';

            if ($re_ids) {
                $upConfig['where']['re_id'] = $re_info['re_id'];
                $upData['re_titles'] = $re_ids;
                $res = D('Region', 'Model')->update($upData, $upConfig);
                if ($res === false) {
                    echo 'error:'.$re_info['re_title'];
                    echo '<br/>';
                }
            }
        }*/

        // 地区 增加 re_children
        /*$region = D('Region', 'Model')->getAll();
        foreach ($region as $re_info) {
            echo $re_info['re_id'];
            echo "<br/>";
            $ids = '';
            $ids = $this->getSubIds($re_info['re_id']);
            $ids_arr = explode(',', $ids);
            if ($ids_arr[0] == $re_info['re_id']) {
                unset($ids_arr[0]);
            }
            $ids = implode(',', $ids_arr);
            echo $ids;
            echo '<br/>';

            if ($ids) {
                $upConfig['where']['re_id'] = $re_info['re_id'];
                $upData['re_children'] = $ids;
                $res = D('Region', 'Model')->update($upData, $upConfig);
                if ($res === false) {
                    echo 'error:'.$re_info['re_id'];
                    echo '<br/>';
                }
            }
        }*/
    }

    public function regionChildren() {
        /*$config['where']['re_level'] = 3;
        $region = D('Region', 'Model')->getAll($config);
        foreach ($region as $re_info) {
            echo $re_info['re_id'];
            echo '<br/>';
            $re_arr = explode(',', $re_info['re_children']);
            if ($re_arr) {
                $all_ids = array();
                foreach ($re_arr as $arr_id) {
                    $reConfig['where']['re_id'] = $arr_id;
                    $reConfig['fields'] = 're_ids';
                    $re_ids = D('Region', 'Model')->getOne($reConfig);
                    if ($re_ids) {
                        $all_ids[] = $re_ids;
                    } else {
                        echo 'error:'.$arr_id.' no re_ids';
                        echo '<br/>';
                    }
                }

                $all_ids_string = implode(',', $all_ids);
                echo $all_ids_string;
                echo '<br/>';

                if ($all_ids_string) {
                    $upConfig['where']['re_id'] = $re_info['re_id'];
                    $upData['re_ids_children'] = $all_ids_string;
                    $res = D('Region', 'Model')->update($upData, $upConfig);
                    if ($res === false) {
                        echo 'error:'.$re_info['re_title'];
                        echo '<br/>';
                    }
                }
            }
            
        }*/
    }

    public function getSubIds($pid = '0', $res = '') {

        $config['where']['re_pid'] = array('IN', $pid);
        $config['fields'] = 're_id';
        $cate = D('Region', 'Model')->getAll($config);
        $res .= ',' . $pid;
        if ($cate) {
            return $this->getSubIds(implode(',', $cate), $res);
        } else {
            return trim($res, ',');
        }
    }

    public function article() {
        echo 123;exit;
        // 导入资讯
        $cateConfig['where']['re_id'] = array('NEQ', '');
        $cateConfig['fields'] = 'ca_id,ca_title,re_id,re_title,s_id';
        $article = D('Category', 'Model')->getAll($cateConfig);
        $cateConfig1['where']['s_id'] = array('NEQ', 0);
        $cateConfig1['fields'] = 'ca_id,ca_title,re_id,re_title,s_id';
        $article1 = D('Category', 'Model')->getAll($cateConfig1);
        foreach ($article as $article_info) {

            echo $article_info['re_title'];
            echo "\n";
            // 文章
            $config['fields'] = 'art_id';
            $config['where']['ca_id'] = $article_info['ca_id'];
            $art_ids = D('Article', 'Model')->getAll($config);
            
            $data['re_id'] = $article_info['re_id'];
            $data['re_title'] = $article_info['re_title'];
            $where['where']['art_id'] = array('IN', $art_ids);
            if (D('Article', 'Model')->update($data, $where) === false) {
                echo 'Error:' . $article_info['ca_id'] . ' => ' . $article_info['ca_title'];
            } else {
                echo 'Success:' . $article_info['ca_id'] . ' => ' . $article_info['ca_title'];
            }

            echo "\n";
            echo ' count:'. count($art_ids) . '  detail:'. implode(',', $art_ids);
            echo "\n";
        }

        echo "\n===============================\n\n";

        foreach ($article1 as $article_info1) {

            echo $article_info1['s_id'];
            echo "\n";
            // 文章
            $config1['fields'] = 'art_id';
            $config1['where']['ca_id'] = $article_info1['ca_id'];
            $art_ids1 = D('Article', 'Model')->getAll($config1);
            
            $data1['s_id'] = $article_info1['s_id'];
            $where1['where']['art_id'] = array('IN', $art_ids1);
            if (D('Article', 'Model')->update($data1, $where1) === false) {
                echo 'Error:' . $article_info1['ca_id'] . ' => ' . $article_info1['ca_title'];
            } else {
                echo 'Success:' . $article_info1['ca_id'] . ' => ' . $article_info1['ca_title'];
            }

            echo "\n";
            echo ' count:'. count($art_ids1) . '  detail:'. implode(',', $art_ids1);
            echo "\n";
        }
    }

    public function update() {
        // dkt_directory

        // dkt_resource_directory_relation

        // dkt_resource

        // 第一步  改resource表 字段
        // 1. res_version 字段删除
        // 2.增加 res_version  res_school_type  res_grade  res_semester  res_subject

        // 第二步  relation  表 增加标识字段 status  查询资源对应的目录将 上面几个属性加到资源表
        $config = array();
        $config['where']['status'] = 0;
        $count = D('ResourceDirectoryRelation', 'Model')->total($config);
        echo "Total: $count \n";
        $config['limit'] = 1000;
        $config['fields'] = 'res_id,d_id';
        $relation = D('ResourceDirectoryRelation', 'Model')->getAll($config);
        if (!$relation) {
            echo "Run over \n";
            exit;
        }
        
        foreach ($relation as $res_id => $d_id) {
            // 获取目录相关信息
            $dConfig['where']['d_id'] = $d_id;
            $directory = D('Directory', 'Model')->getOne($dConfig);
            $resData['res_version'] = intval($directory['d_version']);
            $resData['res_school_type'] = intval($directory['d_school_type']);
            $resData['res_grade'] = intval($directory['d_grade']);
            $resData['res_semester'] = intval($directory['d_semester']);
            $resData['res_subject'] = intval($directory['d_subject']);

            $resConfig['where']['res_id'] = $res_id;
            $res = D('Resource', 'Model')->update($resData, $resConfig);
            if ($res === false) {
                echo "Error: d_id => $d_id \n res_id => $res_id \n";
            }

            // 状态更改为 已执行过
            $relConfig['where']['d_id'] = $d_id;
            $relConfig['where']['res_id'] = $res_id;
            $relData['status'] = 1;
            D('ResourceDirectoryRelation', 'Model')->update($relData, $relConfig);
        }

        sleep(rand(1));

        $this->index();
    }
}