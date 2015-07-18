<?php
namespace Common\Logic;
class ResourceTagLogic extends Logic {

    public function tree() {

        $config['where']['rta_status'] = 1;
        $config['fields'] = 'rta_id AS id,rta_pid AS pid,rta_level AS level,rta_title AS title';

        $tag = D($this->name, 'Model')->getAll($config);

        header("Content-Type:text/xml; charset=utf-8");
        $xml  = '<?xml version="1.0" encoding="utf-8" ?>'."\n";
        $xml .= '<tree caption="栏目" id="0">'."\n";
        $xml .= $this->toTree(tree($tag), 'title');
        $xml .= '</tree>';
        exit($xml);
    }

    public function toTree($list, $caption) {

        foreach ($list as $key => $val){
            $tab = str_repeat("\t", $val['level']);
            $xml .= $tab . '<level' . $val['level'] . ' id="' . $val['id'] . '" level="' . $val['level'] . '" parentId="' . $val['pid'] . '" caption="' . $val[$caption] . '"';

            if (isset($val['_child'])) {
                $xml .= '>' . $this->toTree($val['_child'], $caption) . $tab . '</level' . $val['level'] . '>';
            } else {
                $xml .= '/>';
            }
        }
        return $xml;
    }

    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;

        $default = array(
            'is_deal_result' => true,
            'order' => 'rta_id ASC',
            'p' => intval($param['p']),
        );

        // 查询条件
        if ($param['rta_title']) {
            $where['rta_title'] = array('like', '%' . $param['rta_title'] . '%');
        }

        if ($param['rta_status']) {
            $where['rta_status'] = $param['rta_status'];
        }

        if ($param['rta_id']) {
            $where['rta_id'] = array('in', $param['rta_id']);
        }

        if (isset($param['rta_pid'])) {
            if ($param['rta_pid']) {
                $where['rta_pid'] = array('in', strval($param['rta_pid']));
            } else {
                $where['rta_pid'] = 0;
            }
        }

        $default['where'] = empty($where) ? 1 : $where;

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D($this->name)->getListByPage($config);

        if ($config['is_deal_result']) {
            // 处理数据
            foreach ($lists['list'] as $key => $value) {
                if ($value['rta_status']) {
                    $lists['list'][$key]['rta_status'] = getStatus($value['rta_status']);
                }
            }
        }

        return $lists;
    }

    public function getSubIds($pid = '0', $res = '') {

        $config['where']['rta_pid'] = array('IN', $pid);
        $config['fields'] = 'rta_id';
        $tag = D($this->name, 'Model')->getAll($config);
        $res .= ',' . $pid;
        if ($tag) {
            return D('ResourceTag')->getSubIds(implode(',', $tag), $res);
        } else {
            return trim($res, ',');
        }
    }

    public function addTag($rta_title) {

        // 验证
        $res = D('ResourceTag', 'Model')->create(array('rta_title' => $rta_title));
        if ($res) {
            $id = D('ResourceTag', 'Model')->insert(array('rta_title' => $rta_title));
            $data['status'] = true;
            $data['id'] = $id;
        } else {
            $data['status'] = false;
            $data['msg'] = D('ResourceTag', 'Model')->getError();
        }
        
        return $data;
    }
}