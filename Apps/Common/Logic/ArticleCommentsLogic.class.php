<?php
namespace Common\Logic;
class ArticleCommentsLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'order' => 'aco_id DESC',
            'p' => intval($param['p']),
            'where' => array('aco_pid' => 0)
        );
        
        if ($param['aco_status']) {
            $default['where']['aco_status'] = $param['aco_status'];
        }

        if ($param['aco_pid']) {
            $default['where']['aco_pid'] = $param['aco_pid'];
        }

        $config = array_merge($default, $config);
        
        // 分页获取数据
        $lists = D('ArticleComments')->getListByPage($config);
        foreach ($lists['list'] as $key => $value) {
            // 资源名称
            if ($value['art_id']) {
                $res_info = D('Article')->getOne($value['art_id']);
                $lists['list'][$key]['art_title'] = $res_info['art_title'];
            }
            // 用户姓名
            if ($value['me_id']) {
                $member_info = D('Member')->getOne($value['me_id']);
                $lists['list'][$key]['me_nickname'] = $member_info['me_nickname'];
            }
            if ($value['aco_status']) {
                $lists['list'][$key]['aco_status'] = getStatus($value['aco_status']);
            }
        }

        // 输出数据
        return $lists;
    }

    public function getById($id) {
        // 信息
        $info = D('ArticleComments')->getOne(intval($id));
        $info['aco_created'] = date('Y-m-d', $info['aco_created']);
        // 资源
        $res_info = D('Article')->getOne($info['art_id']);
        $info['art_title'] = $res_info['art_title'];
        // 用户
        $member_info = D('Member')->getOne($info['me_id']);
        $info['me_nickname'] = $member_info['me_nickname'];

        return $info;
    }

    public function delete($config) {

        // 有子集
        $data['where']['aco_pid'] = $config['where']['aco_id'];
        $list = D('ArticleComments')->getAll($data);
        if ($list) {
            $this->error = L('CHILDREN_EXIST');
            return false;
        }
        
        // 删除
        return D('ArticleComments', 'Model')->delete($config);
    }
}