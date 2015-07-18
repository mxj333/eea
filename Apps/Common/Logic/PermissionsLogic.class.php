<?php
namespace Common\Logic;
class PermissionsLogic extends Logic {

    public function getList($config = array()) {

        $default = array(
            'where' => array(),
            'fields' => '*',
            'order' => 'pe_sort ASC, pe_id ASC',
        );

        $config = array_merge($default, $config);
        return D($this->name, 'Model')->getAll($config);
    }

    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;

        $default = array(
            'order' => 'pe_type ASC, pe_sort ASC, pe_id DESC',
            'p' => intval($param['p']),
        );

        // 查询条件
        if ($param['pe_title']) {
            $where['pe_title'] = array('LIKE', '%' . $param['pe_title'] . '%');
        }

        if ($param['pe_id']) {
            $where['pe_id'] = array('IN', $param['pe_id']);
        }

        if (isset($param['pe_pid'])) {
            if ($param['pe_pid']) {
                $where['pe_pid'] = array('IN', strval($param['pe_pid']));
            } else {
                $where['pe_pid'] = 0;
            }
        }

        $default['where'] = empty($where) ? 1 : $where;

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D($this->name)->getListByPage($config);

        $type = explode(',', C('PERMISSIONS_TYPE'));
        foreach ($lists['list'] as $key => $value) {
            if ($value['pe_status']) {
                $lists['list'][$key]['pe_status'] = getStatus($value['pe_status']);
            }
            if ($value['pe_type']) {
                $lists['list'][$key]['pe_type'] = strval($type[$value['pe_type']]);
            }
        }

        return $lists;
    }

    public function getSubIds($pid = '0', $res = '') {

        $config['where']['pe_pid'] = array('IN', $pid);
        $config['fields'] = 'pe_id';
        $cate = D($this->name, 'Model')->getAll($config);
        $res .= ',' . $pid;
        if ($cate) {
            return D('Permissions')->getSubIds(implode(',', getValueByField($cate, 'pe_id')), $res);
        } else {
            return trim($res, ',');
        }
    }
}