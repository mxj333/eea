<?php
namespace Common\Logic;
class ResourceTagRelationLogic extends Logic {

    public function insert($data) {

        if (!$data['res_id']) {
            return false;
        }

        // 先删除
        D('ResourceTagRelation', 'Model')->delete(array('where' => array('res_id' => intval($data['res_id']))));

        // 添加
        $addData = array();
        $tag_ids = explode(',', $data['rta_id']);
        foreach ($tag_ids as $tag_id) {
            if (intval($tag_id)) {
                $addData[] = array('res_id' => intval($data['res_id']), 'rta_id' => intval($tag_id));
            }
        }

        // 没有数据入库  直接返回true
        if (!$addData) {
            return true;
        }

        $config['fields'] = 'res_id,rta_id';
        $config['values'] = $addData;

        return D('ResourceTagRelation', 'Model')->insertAll($config);
    }

    public function getTags($res_id) {
        $config['where']['res_id'] = intval($res_id);
        $config['fields'] = 'rta_id';
        $lists = D('ResourceTagRelation', 'Model')->getAll($config);

        if (!$lists) {
            return false;
        }

        $tags = array();
        foreach ($lists as $tag_id) {
            $info = D('ResourceTag')->getById($tag_id);
            $tags[] = $info['rta_title'];
        }

        return array(
            'ids' => implode(',', $lists),
            'titles' => implode(',', $tags),
        );
    }
}