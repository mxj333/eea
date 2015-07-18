<?php
namespace Common\Logic;
class ResourceDirectoryRelationLogic extends Logic {

    public function insert($data) {

        if (!$data['res_id']) {
            return false;
        }

        // 先删除
        D('ResourceDirectoryRelation', 'Model')->delete(array('where' => array('res_id' => intval($data['res_id']))));

        // 添加
        $addData = array();
        $directory_ids = explode(',', $data['d_id']);
        foreach ($directory_ids as $directory_id) {
            if (intval($directory_id)) {
                $addData[] = array('res_id' => intval($data['res_id']), 'd_id' => intval($directory_id));
            }
        }

        // 没有数据入库  直接返回true
        if (!$addData) {
            return true;
        }

        $config['fields'] = 'res_id,d_id';
        $config['values'] = $addData;

        return D('ResourceDirectoryRelation', 'Model')->insertAll($config);
    }

    public function getDirectory($res_id) {
        $config['where']['res_id'] = intval($res_id);
        $config['fields'] = 'd_id';
        $lists = D('ResourceDirectoryRelation', 'Model')->getAll($config);

        if (!$lists) {
            return false;
        }

        $directory = array();
        foreach ($lists as $d_id) {
            $info = D('Directory')->getById($d_id);
            $directory[] = $info['d_title'];
        }

        return array(
            'ids' => implode(',', $lists),
            'titles' => implode(',', $directory),
        );
    }
}