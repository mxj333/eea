<?php
namespace Api\Controller;
use Think\Controller;
class GradeSubjectRelationController extends OpenController {

    // 列表允许输出字段
    private $allowFields = array('gsr_id', 'gsr_school_type', 'gsr_grade', 'gsr_subject', 'gsr_status', 're_id', 're_title');
    // 允许添加字段
    private $allowFieldsInsert = array('gsr_school_type', 'gsr_grade', 'gsr_subject', 'gsr_status', 're_id', 're_title');
    // 允许修改字段
    private $allowFieldsUpdate = array('gsr_id', 'gsr_school_type', 'gsr_grade', 'gsr_subject', 'gsr_status', 're_id', 're_title');

    // 列表
    public function lists() {
        extract($_POST['args']);

        // 返回字段
        if ($fields) {
            // 有定义返回字段
            $returnFields = explode(',', $fields);
            $config['fields'] = array_intersect($this->allowFields, $returnFields);
        }
        if (!$config['fields']){
            $config['fields'] = $this->allowFields;
        }
        
        // 结果不处理
        $config['is_deal_result'] = $is_deal_result ? $is_deal_result : false;
        // api 请求
        $config['is_api'] = true;

        $result = D('GradeSubjectRelation')->lists($_POST['args'], $config);
        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无数据'));
        }

        $this->returnData($result);
    }

    // 信息
    public function shows() {
        extract($_POST['args']);

        // 校验
        if (!intval($gsr_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 结果不处理
        $config['is_deal_result'] = $is_deal_result ? $is_deal_result : false;

        $result = D('GradeSubjectRelation')->getById(intval($gsr_id), $config);
        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无'));
        }

        // 返回字段
        if ($fields) {
            // 有定义返回字段
            $returnFields = explode(',', $fields);
            $config['fields'] = array_intersect($this->allowFields, $returnFields);
        }
        if (!$config['fields']){
            $config['fields'] = $this->allowFields;
        }

        // 字段过滤
        foreach ($result as $key => $val) {
            if (!in_array($key, $config['fields'])) {
                unset($result[$key]);
            }
        }

        $this->returnData($result);
    }

    // 删除
    public function del() {
        extract($_POST['args']);

        // 校验
        if (!strval($gsr_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 删除
        $config['where']['gsr_id'] = array('IN', $gsr_id);
        $result = D('GradeSubjectRelation')->delete($config, $data);

        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '删除成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '删除失败'));
        }
    }

    // 添加
    public function add() {
        extract($_POST['args']);
        
        // 确定允许操作的字段
        foreach ($_POST['args'] as $key => $val) {
            if (!in_array($key, $this->allowFieldsInsert)) {
                unset($_POST['args'][$key]);
            }
        }

        $result = D('GradeSubjectRelation')->insert($_POST['args']);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '新增成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('GradeSubjectRelation')->getError()));
        }
    }

    // 修改
    public function edit() {
        extract($_POST['args']);
        
        // 校验
        if (!intval($gsr_id)) {
            $this->returnData($this->errCode[2]);
        }
        
        // 确定允许操作的字段
        foreach ($_POST['args'] as $key => $val) {
            if (!in_array($key, $this->allowFieldsUpdate)) {
                unset($_POST['args'][$key]);
            }
        }

        $_POST['args']['gsr_id'] = intval($gsr_id);
        $result = D('GradeSubjectRelation')->insert($_POST['args']);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '编辑成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('GradeSubjectRelation')->getError()));
        }
    }
}
?>
