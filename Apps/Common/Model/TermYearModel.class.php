<?php
namespace Common\Model;
use Think\Model;
class TermYearModel extends ManageModel {

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
        array('ty_year', 'require', '请选择学年'),
        array('ty_last_starttime', 'require', '请选择上学期开学时间'),
        array('ty_last_endtime', 'require', '请选择上学期放假时间'),
        array('ty_next_starttime', 'require', '请选择下学期开学时间'),
        array('ty_next_endtime', 'require', '请选择下学期放假时间'),
    );

    protected $_auto = array(
        array('ty_last_starttime', 'strtotime', 3, 'function'),
        array('ty_last_endtime', 'strtotime', 3, 'function'),
        array('ty_next_starttime', 'strtotime', 3, 'function'),
        array('ty_next_endtime', 'strtotime', 3, 'function'),
        array('ty_created', 'time', 1, 'function'),
        array('ty_updated', 'time', 2, 'function'),
    );
}