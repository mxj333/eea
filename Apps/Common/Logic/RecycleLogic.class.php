<?php
namespace Common\Logic;
class RecycleLogic extends Logic {

    /**
     * art_status
     * 0 普通 1 发布  9回收站
     *
     * art_position
     * 0 普通 1 推荐 2 栏目推荐 9 首页推荐
     *
     */

    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;

        // 查询条件
        $where['art_is_deleted'] = array('NEQ', 1);
        $where['art_status'] = 9;

        if ($param['art_title']) {
            $where['art_title'] = array('like', '%' . $param['art_title'] . '%');
        }

        $default = array(
            'where' => $where,
            'order' => 'art_position DESC, art_id DESC',
            'p' => intval($param['p']),
        );

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('Article')->getListByPage($config);
        $category = D('Category')->getAll(array('fields' => 'ca_id,ca_title'));
        
        // 处理数据
        foreach ($lists['list'] as $key => $value) {
            if ($value['art_status']) {
                $lists['list'][$key]['art_status'] = getStatus($value['art_status']);
            }
            $lists['list'][$key]['ca_title'] = $category[$value['ca_id']];
        }

        return $lists;
    }
}