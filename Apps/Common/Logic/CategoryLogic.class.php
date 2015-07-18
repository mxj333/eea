<?php
namespace Common\Logic;
class CategoryLogic extends Logic {

    public function tree() {

        $config['fields'] = 'ca_id,ca_pid,ca_level,ca_title';
        $config['where']['re_id'] = '';
        $config['where']['s_id'] = 0;
        $cate = $this->getList($config);
        header("Content-Type:text/xml; charset=utf-8");
        $xml  = '<?xml version="1.0" encoding="utf-8" ?>'."\n";
        $xml .= '<tree caption="栏目" id="0">'."\n";
        $xml .= html_entity_decode(toTree(tree($cate, 'ca_id', 'ca_pid', 'list'), 'ca_id', 'ca_pid', 'ca_level', 'ca_title', 'list'));
        $xml .= '</tree>';
        exit($xml);
    }

    public function getList($config = array()) {

        $default = array(
            'where' => array(),
            'fields' => '*',
            'order' => 'ca_sort ASC',
        );

        $config = array_merge($default, $config);
        return D($this->name, 'Model')->getAll($config);
    }

    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;

        $default = array(
            'is_deal_result' => true,
            'order' => 'ca_sort ASC',
            'p' => intval($param['p']),
        );

        // 查询条件
        if ($param['ca_title']) {
            $where['ca_title'] = array('like', '%' . $param['ca_title'] . '%');
        }

        if ($param['ca_status']) {
            $where['ca_status'] = $param['ca_status'];
        }

        if ($param['ca_name']) {
            $where['ca_name'] = $param['ca_name'];
        }

        if ($param['ca_is_show']) {
            $where['ca_is_show'] = $param['ca_is_show'];
        }

        if ($param['ca_id']) {
            $where['ca_id'] = array('in', $param['ca_id']);
        }

        if (isset($param['ca_level'])) {
            $where['ca_level'] = $param['ca_level'];
        }

        if (isset($param['ca_pid'])) {
            if ($param['ca_pid']) {
                $where['ca_pid'] = array('in', strval($param['ca_pid']));
            } else {
                $where['ca_pid'] = 0;
            }
        }

        if ($param['s_id']) {
            $where['s_id'] = $param['s_id'];
        } elseif ($param['re_id']) {
            $where['re_id'] = $param['re_id'];
        } else {
            $where['s_id'] = 0;
            $where['re_id'] = '';
        }

        $default['where'] = empty($where) ? 1 : $where;

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D($this->name)->getListByPage($config);

        if ($config['is_deal_result']) {
            $model = reloadCache('model');
            // 处理数据
            foreach ($lists['list'] as $key => $value) {
                if ($value['m_id']) {
                    $lists['list'][$key]['m_title'] = $model[$value['m_id']]['m_title'];
                }
                if ($value['ca_status']) {
                    $lists['list'][$key]['ca_status'] = getStatus($value['ca_status']);
                }
            }
        }

        return $lists;
    }

    public function getSubIds($pid = '0', $res = '') {

        $config['where']['ca_status'] = 1;
        $config['where']['ca_is_show'] = 1;
        $config['where']['ca_pid'] = array('IN', $pid);
        $config['fields'] = 'ca_id';
        $cate = D($this->name, 'Model')->getAll($config);
        $res .= ',' . $pid;
        if ($cate) {
            return D('Category')->getSubIds(implode(',', $cate), $res);
        } else {
            return trim($res, ',');
        }
    }

    public function delete($id) {

        // 是否有子集
        $childConfig['where']['ca_pid'] = array('IN', $id);
        $child = D('Category', 'Model')->getOne($childConfig);
        if ($child) {
            $this->error = '请先删除子集栏目';
            return false;
        }

        // 删除栏目
        $config['where']['ca_id'] = array('IN', $id);
        $res = D('Category', 'Model')->delete($config);
        if ($res !== false) {

            // 栏目下文章标记为删除
            $data['art_deleted'] = 1;
            D('Article', 'Model')->update($data, $config);
        }

        return $res;
    }

    public function insert($data) {
        $saveData = D('Category', 'Model')->create($data);
        if (!$saveData) {
            $this->error = D('Category', 'Model')->getError();
            return false;
        }

        // 检查英文名唯一性
        if ($saveData['s_id']) {
            // 学校后台
            $cateConfig['where']['s_id'] = $saveData['s_id'];
        } elseif ($saveData['re_id']) {
            // 区域后台
            $cateConfig['where']['re_id'] = $saveData['re_id'];
        } else {
            // 平台
            $cateConfig['where']['re_id'] = '';
            $cateConfig['where']['s_id'] = 0;
        }
        if ($saveData['ca_id']) {
            $cateConfig['where']['ca_id'] = array('NEQ', $saveData['ca_id']);
        }
        $cateConfig['where']['ca_name'] = $saveData['ca_name'];
        $info = D('Category', 'Model')->getOne($cateConfig);
        if ($info) {
            $this->error = '英文名已经存在';
            return false;
        }

        if ($saveData['ca_id']) {
            $result = D('Category', 'Model')->update($saveData);
        } else {
            unset($saveData['ca_id']);
            $result = D('Category', 'Model')->insert($saveData);
        }
        
        if ($result === false) {
            $this->error = D('Category', 'Model')->getError();
            return false;
        }

        return $result;
    }
}