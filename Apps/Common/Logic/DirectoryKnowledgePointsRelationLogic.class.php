<?php
namespace Common\Logic;
class DirectoryKnowledgePointsRelationLogic extends Logic {

    public function insert($data) {

        // 删除以前关系
        D('DirectoryKnowledgePointsRelation', 'Model')->delete(array('where' => array('d_id' => intval($data['d_id']))));

        $config['fields'] = 'kp_id,d_id';
        foreach ($data['knowledgePoints'] as $kp_id) {
            $config['values'][] = array('kp_id' => $kp_id, 'd_id' => $data['d_id']);
        }

        // 没有数据入库  直接返回true
        if (!$config['values']) {
            return true;
        }

        // 添加新关系
        return D('DirectoryKnowledgePointsRelation', 'Model')->insertAll($config);
    }

    public function getKnowledgePoints($d_id) {

        $config['where']['d_id'] = intval($d_id);
        $config['fields'] = 'kp_id';
        return D('DirectoryKnowledgePointsRelation', 'Model')->getAll($config);
    }
}