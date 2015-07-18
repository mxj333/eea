<?php
namespace Common\Model;
class ResourceTagModel extends ManageModel {

    protected $_auto = array(
        array('rta_pid', 'intval', 3, 'function'),
        array('rta_level', 'intval', 3, 'function'),
    );

    protected $_validate = array(
        array('rta_title', 'require', '请输入名称'),
        array('rta_title', 'checkTitle', '标题已经存在', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH),
    );

    // 检查是否已经存在
    public function checkTitle($title) {

        $rta_id = I('rta_id', 0, 'intval');
        $rta_pid = I('rta_pid', 0, 'intval');

        $config['where']['rta_pid'] = $rta_pid;
        $config['where']['rta_title'] = $title;
        
        if ($rta_id) {
            // 编辑
            $config['where']['rta_id'] = array('neq', $rta_id);
        }

        $tag_info = D('ResourceTag', 'Model')->getOne($config);
        
        if ($tag_info) {
            return false;
        }

        return true;
    }
}