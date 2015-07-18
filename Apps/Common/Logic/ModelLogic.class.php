<?php
namespace Common\Logic;
class ModelLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;

        $default = array(
            'order' => 'm_id ASC',
            'p' => intval($param['p']),
        );

        // 查询条件
        if ($param['m_title']) {
            $where['m_title'] = array('like', '%' . $param['m_title'] . '%');
        }

        $default['where'] = empty($where) ? 1 : $where;

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D($this->name)->getListByPage($config);

        foreach ($lists['list'] as $lKey => $lValue) {
            $mList[$lValue['m_id']] = $lValue['m_list'];
        }
        // 获取属性信息
        $attributeConfig['where']['at_id'] = array('in', implode(',', $mList));
        $attribute = D('Attribute', 'Model')->getAll($attributeConfig);
        $attribute = setArrayByField($attribute, 'at_id');

        foreach ($mList as $key => $value) {
            if ($value) {
                $tmp = explode(',', $value);

                $mList[$key] = array();
                foreach ($tmp as $tKey => $tValue) {
                    $mList[$key][$tKey] = $attribute[$tValue]['at_title'];
                }
            }
        }

        // 处理数据
        foreach ($lists['list'] as $nKey => $nValue) {
            $lists['list'][$nKey]['m_list'] = strval(implode(',', $mList[$nValue['m_id']]));
        }
        return $lists;
    }


    public function cache($id) {

        if (!$id) {
            return ;
        }

        $config['where']['m_id'] = array('in', $id);
        $lists = D($this->name, 'Model')->getAll($config);

        foreach ($lists as $key => $value) {
            $this->creatCache($value);
        }
    }

    public function creatCache($model) {
        // 字段类型
        $type = array(
            1 => 'varchar(255) DEFAULT NULL',
            2 => 'varchar(255) DEFAULT NULL',
        );

        $prefix = C('DB_PREFIX');
        $engine = C('CACHE_TABLE_ENGINE') ? C('CACHE_TABLE_ENGINE') : 'MyISAM';

        $sql = 'DROP TABLE IF EXISTS `'.$prefix.'cache_'.$model['m_name'].'`;';
        D('Model', 'Model')->querySql($sql);

        $sql = 'CREATE TABLE `' . $prefix . 'cache_' . $model['m_name'] . '` (
            `art_id` int(11) unsigned NOT NULL,
            `art_title` varchar(255) NOT NULL COMMENT "标题",
            `art_short_title` varchar(255) NOT NULL DEFAULT "" COMMENT "短标题",
            `art_keywords` varchar(255) NOT NULL DEFAULT "" COMMENT "关键词",
            `art_content` text COMMENT "内容",
            `m_id` smallint(5) unsigned NOT NULL DEFAULT "1" COMMENT "所属模型",
            `ca_id` int(11) unsigned NOT NULL COMMENT "所属栏目",
            `u_id` smallint(5) unsigned NOT NULL COMMENT "编辑",
            `art_summary` varchar(600) NOT NULL COMMENT "摘要",
            `art_hits` int(11) unsigned NOT NULL COMMENT "点击量",
            `art_sort` tinyint(3) unsigned NOT NULL DEFAULT "255" COMMENT "排序",
            `art_cover_path` varchar(60) NOT NULL DEFAULT "" COMMENT "文章封面路径",
            `art_cover_name` varchar(60) NOT NULL DEFAULT "" COMMENT "文章封面名称（含后缀）",
            `art_cover_ext` varchar(10) NOT NULL DEFAULT "" COMMENT "文章封面类型",
            `art_is_allow_comment` tinyint(3) unsigned NOT NULL COMMENT "是否允许评论",
            `art_position` tinyint(3) unsigned NOT NULL DEFAULT "0" COMMENT "推荐位",
            `art_created` int(11) unsigned NOT NULL COMMENT "创建时间",
            `art_updated` int(11) unsigned DEFAULT "0" COMMENT "更新时间",
            `art_published` int(11) unsigned NOT NULL COMMENT "发布时间",
            `art_designated_published` int(10) unsigned DEFAULT "0" COMMENT "指定发布时间",
            `art_deleted` int(11) unsigned DEFAULT "0" COMMENT "删除时间",
            `art_status` tinyint(3) unsigned DEFAULT "0" COMMENT "状态",';

        if ($model['m_list']) {

            // 获取属性列表
            $list = setArrayByField(D('Attribute', 'Model')->getAll(), 'at_id');

            // 获取本模型的属性
            $attributeList = explode(',', $model['m_list']);
            foreach ($attributeList as $value) {
                $sql .= '`' . $list[$value]['at_name'] . '` ' . ($type[$list[$value]['at_type']] ? $type[$list[$value]['at_type']] : 'varchar(1024) DEFAULT ""') . ',';
            }
        }

        $sql .= '
            PRIMARY KEY  (`art_id`)
            ) ENGINE=' . $engine . ' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';

        D('Model', 'Model')->querySql($sql);
    }

    // 批量同步数据到缓存表
    public function syncList($list, $lock = false) {
        // 读取模型名称列表
        $models = reloadCache('model');

        if ($lock) { // 锁表
            // 锁住所有的缓存表
            $sql = 'LOCK TABLES ';
            foreach ($models as $model) {
                $sql .= C('DB_PREFIX') . 'cache_' . $model['m_name'] . ' WRITE,';
            }
            $sql = substr($sql , 0, -1) . ';';

            D('Model', 'Model')->querySql($sql);
        }
        foreach ($list as $data) {
            $this->syncData($data, $models);
        }
        if ($lock) D('Model', 'Model')->querySql('UNLOCK TABLES;');
    }

    public function syncData($data, $model) {
        $tableName = C('DB_PREFIX') . 'cache_' . strtolower($model[$data['m_id']]['m_name']);
        D('Model', 'Model')->querySql('DELETE FROM ' . $tableName . ' WHERE art_id = ' . $data['art_id']);
        $fields = '';
        $values = '';
        foreach ($data as $key => $val) {
            $fields .= ',' . $key;
            $values .= ',"' . $val . '"';
        }
        
        $sql = 'INSERT INTO ' . $tableName . ' (' . substr($fields, 1) . ') VALUES (' . substr($values, 1) . ')';
        return D('Model', 'Model')->querySql($sql);
    }
}