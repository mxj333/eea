<?php
namespace Common\Logic;
class KnowledgePointsLogic extends Logic {

    public function tree() {
        $cate = $this->getList(array('fields' => 'kp_id,kp_pid,kp_level,kp_title'));
        header("Content-Type:text/xml; charset=utf-8");
        $xml  = '<?xml version="1.0" encoding="utf-8" ?>'."\n";
        $xml .= '<tree caption="栏目" id="0">'."\n";
        $xml .= html_entity_decode(toTree(tree($cate, 'kp_id', 'kp_pid', 'list'), 'kp_id', 'kp_pid', 'kp_level', 'kp_title', 'list'));
        $xml .= '</tree>';
        exit($xml);
    }

    public function venus($num) {
        $cate = $this->getList(array('fields' => array('kp_id' => 'id', 'kp_pid' => 'pid', 'kp_level' => 'level', 'kp_title' => 'name')));

        $type = array('star', 'circle');
        foreach ($cate as &$value) {
            $tmp['$color'] = '#fff';
            $tmp['$dim'] = $value['level'];
            $tmp['$type'] = $type[rand(0, 1)];
            $tmp['labelColor'] = '#fff';
            $value['data'] = $tmp;
            unset($value['level']);
        }
        $cate = tree($cate, 'id', 'pid', 'children');
        return $cate[$num];
    }

    public function getList($config = array()) {

        $default = array(
            'where' => array(),
            'fields' => '*',
            'order' => 'kp_sort ASC, kp_id ASC',
        );

        $config = array_merge($default, $config);
        return D($this->name, 'Model')->getAll($config);
    }

    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;

        $default = array(
            'is_deal_result' => true,
            'order' => 'kp_sort ASC, kp_id ASC',
            'p' => intval($param['p']),
        );

        // 查询条件
        if ($param['kp_title']) {
            $where['kp_title'] = array('LIKE', '%' . $param['kp_title'] . '%');
        }

        if ($param['kp_status']) {
            $where['kp_status'] = $param['kp_status'];
        }

        if ($param['kp_subject']) {
            $where['kp_subject'] = $param['kp_subject'];
        }

        if ($param['kp_id']) {
            $where['kp_id'] = array('IN', $param['kp_id']);
        }

        if (isset($param['kp_pid'])) {
            if ($param['kp_pid']) {
                $where['kp_pid'] = array('IN', strval($param['kp_pid']));
            } else {
                $where['kp_pid'] = 0;
            }
        }

        $default['where'] = empty($where) ? 1 : $where;

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D($this->name)->getListByPage($config);

        if ($config['is_deal_result']) {
            $tag = reloadCache('tag');

            foreach ($lists['list'] as $key => $value) {
                if ($value['kp_subject']) {
                    $lists['list'][$key]['kp_subject'] = $tag[5][$value['kp_subject']];
                }
                if ($value['kp_status']) {
                    $lists['list'][$key]['kp_status'] = getStatus($value['kp_status']);
                }
            }
        }

        return $lists;
    }

    public function insert($data, $table = 'Member') {

        if (!$data['kp_id']) {
            // 添加
            $data['kp_creator_id'] = $data['kp_creator_id'] ? $data['kp_creator_id'] : $_SESSION[C('USER_AUTH_KEY')];
            $data['kp_creator_table'] = $table;
        }

        return $this->data('KnowledgePoints', $data);
    }

    public function getSubIds($pid = '0', $res = '') {

        $config['where']['kp_pid'] = array('IN', $pid);
        $config['fields'] = 'kp_id';
        $cate = D($this->name, 'Model')->getAll($config);
        $res .= ',' . $pid;
        if ($cate) {
            return D('KnowledgePoints')->getSubIds(implode(',', $cate), $res);
        } else {
            return trim($res, ',');
        }
    }

    // 目录调整
    public function adjustment($data, $pid) {

        // 当前目录
        $info = D('KnowledgePoints')->getById($data['kp_id']);
        // 目标目录
        if ($data['target_id']) {
            $targetInfo = D('KnowledgePoints')->getById($data['target_id']);
            $targetPid = intval($targetInfo['kp_id']);
            $targetLevel = intval($targetInfo['kp_level']) + 1;
        } else {
            $targetPid = 0;
            $targetLevel = 0;
        }
        $level_diff = $targetLevel - intval($info['kp_level']);

        // 所有子集id
        $children = $this->getSubIds($data['kp_id']);
        $children = $children ? $children . ',' . intval($data['kp_id']) : intval($data['kp_id']);

        // 修改父id
        D('KnowledgePoints', 'Model')->update(array('kp_id' => intval($data['kp_id']), 'kp_pid' => $targetPid));
        // 修改level
        if ($level_diff > 0) {
            D('KnowledgePoints', 'Model')->increase('kp_level', array('kp_id' => array('IN', $children)), $level_diff);
        } else {
            D('KnowledgePoints', 'Model')->decrease('kp_level', array('kp_id' => array('IN', $children)), abs($level_diff));
        }
        // 修改信息
        $saveData['kp_subject'] = $data['kp_subject'];
        $config['where']['kp_id'] = array('IN', $children);
        D('KnowledgePoints', 'Model')->update($saveData, $config);

        return true;
    }
}