<?php
namespace Common\Model;
use Think\Model;
class GradeSubjectRelationModel extends ManageModel {

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
        array('gsr_school_type', 'number', '请选择学制'),
        array('gsr_grade', 'number', '请选择年级'),
        array('gsr_subject', 'require', '请选择学科'),
    );

    protected $_auto = array (
        array('gsr_created', 'time', self::MODEL_INSERT, 'function'),
        array('gsr_updated', 'time', self::MODEL_UPDATE, 'function')
    );
}