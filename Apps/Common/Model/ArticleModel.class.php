<?php
namespace Common\Model;
class ArticleModel extends ManageModel {

    /**
     * art_status
     * 0 普通 1 发布  9回收站
     *
     * art_position
     * 0 普通 1 推荐 2 栏目推荐 9 首页推荐
     *
     */

    protected $_auto = array(
        array('art_sort', 'intval', 3, 'function'),
        array('art_created', 'time', 1, 'function'),
        array('art_updated', 'time', 2, 'function'),
    );

    protected $_validate = array(
        array('art_title', 'require', '请输入名称'),
        array('art_sort', 'number', '排序必须为数字', self::VALUE_VALIDATE),
        array('art_designated_published', 'require', '请选择发布时间'),
    );
}