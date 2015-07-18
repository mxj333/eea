<?php
namespace Common\Model;
use Think\Model;
class TagModel extends ManageModel {

    // 验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]
    // 验证条件
    // self::EXISTS_VALIDATE 或者0 存在字段就验证（默认）
    // self::MUST_VALIDATE 或者1 必须验证
    // self::VALUE_VALIDATE或者2 值不为空的时候验证
    // 验证时间（可选）
    // self::MODEL_INSERT或者1新增数据时候验证
    // self::MODEL_UPDATE或者2编辑数据时候验证
    // self::MODEL_BOTH或者3全部情况下验证（默认）

    protected $_validate = array(
        array('t_title', 'require', '请输入标题'),
        array('t_title', 'checkTitle', '标题已经存在', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH),
        array('t_title', 'checkLength', '标题长度2-15个字', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 15)),
        array('t_sort', 'require', '排序请输入数字'),
    );

    // 检查是否已经存在
    public function checkTitle($title) {

        $t_id = I('t_id', 0, 'intval');
        $t_type = I('t_type', 0, 'intval');

        $config['where']['t_type'] = $t_type;
        $config['where']['t_title'] = $title;
        
        if ($t_id) {
            // 编辑
            $config['where']['t_id'] = array('neq', $t_id);
        }

        $tag_info = D('Tag', 'Model')->getOne($config);
        
        if ($tag_info) {
            return false;
        }

        return true;
    }
}