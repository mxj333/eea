<?php
namespace Common\Logic;
class ResourceKnowledgePointsRelationLogic extends Logic {

    public function insert($data) {

        if (!$data['res_id']) {
            return false;
        }

        // 先删除
        D('ResourceKnowledgePointsRelation', 'Model')->delete(array('where' => array('res_id' => intval($data['res_id']))));

        // 添加
        $addData = array();
        $kp_ids = explode(',', $data['kp_id']);
        foreach ($kp_ids as $kp_id) {
            if (intval($kp_id)) {
                $addData[] = array('res_id' => intval($data['res_id']), 'kp_id' => intval($kp_id));
            }
        }

        // 没有数据入库  直接返回true
        if (!$addData) {
            return true;
        }

        $config['fields'] = 'res_id,kp_id';
        $config['values'] = $addData;

        return D('ResourceKnowledgePointsRelation', 'Model')->insertAll($config);
    }

    public function getKnowledgePoints($res_id) {
        $config['where']['res_id'] = intval($res_id);
        $config['fields'] = 'kp_id';
        $lists = D('ResourceKnowledgePointsRelation', 'Model')->getAll($config);

        if (!$lists) {
            return false;
        }

        $knowledge = array();
        foreach ($lists as $kp_id) {
            $info = D('KnowledgePoints')->getById($kp_id);
            $knowledge[] = $info['kp_title'];
        }

        return array(
            'ids' => implode(',', $lists),
            'titles' => implode(',', $knowledge),
        );
    }
}