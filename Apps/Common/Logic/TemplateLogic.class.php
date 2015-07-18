<?php
namespace Common\Logic;
class TemplateLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'is_deal_result' => true,
            'order' => 'te_id DESC',
            'p' => intval($param['p']),
        );

        if ($param['te_title']) {
            $default['where']['te_title'] = array('LIKE', '%' . $param['te_title'] . '%');
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('Template')->getListByPage($config);

        if ($config['is_deal_result']) {
            $type = loadCache('templateType');

            foreach ($lists['list'] as $key => $value) {
                if ($value['te_status']) {
                    $lists['list'][$key]['te_status'] = getStatus($value['te_status']);
                }
                if ($value['tt_id']) {
                    $lists['list'][$key]['tt_id'] = $type[$value['tt_id']];
                }
            }
        }
        // 输出数据
        return $lists;
    }

    public function insert($data) {

        $saveData = D('Template', 'Model')->create($data);
        if ($saveData === false) {
            $this->error = D('Template', 'Model')->getError();
            return false;
        }

        // 检查重复
        if ($saveData['te_id']) {
            $config['where']['te_name'] = array('NEQ', $saveData['te_name']);
        }
        $config['where']['te_name'] = $saveData['te_name'];
        $config['where']['tt_id'] = $saveData['tt_id'];
        $info = D('Template', 'Model')->getOne($config);
        if ($info) {
            $this->error = '英文名已经存在';
            return false;
        }

        if ($saveData['te_id']) {
            return D('Template', 'Model')->update($saveData);
        } else {
            return D('Template', 'Model')->insert($saveData);
        }
    }
}