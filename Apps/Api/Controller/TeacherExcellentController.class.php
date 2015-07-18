<?php
namespace Api\Controller;
use Think\Controller;
class TeacherExcellentController extends OpenController {

    // 列表允许输出字段
    private $allowFields = array('tex_id', 'tex_logo', 'tex_description', 'tex_sort', 'tex_type', 'tex_status', 're_id', 're_title', 's_id', 'c_id', 'me_id');
    // 详情
    private $allowDetailFields = array('tex_id', 'tex_logo', 'tex_description', 'tex_sort', 'tex_type', 'tex_status', 're_id', 're_title', 's_id', 's_title', 'c_id', 'c_title', 'me_id', 'me_nickname', 'tex_created', 'tex_updated');
    // 添加
    private $allowFieldsInsert = array('tex_logo', 'tex_description', 'tex_sort', 'tex_type', 'tex_status', 're_id', 're_title', 's_id', 'c_id');
    // 修改
    private $allowFieldsUpdate = array('tex_id', 'tex_logo', 'tex_description', 'tex_sort', 'tex_type', 'tex_status', 're_id', 're_title', 's_id', 'c_id');

    // 用户列表
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

        $result = D('TeacherExcellent')->lists($_POST['args'], $config);
        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无数据'));
        }

        $this->returnData($result);
    }

    // 用户信息
    public function shows() {
        extract($_POST['args']);

        // 校验
        if (!intval($tex_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 结果不处理
        $config['is_deal_result'] = $is_deal_result ? $is_deal_result : false;

        $result = D('TeacherExcellent')->getById(intval($tex_id), $config);
        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无'));
        }

        // 返回字段
        if ($fields) {
            // 有定义返回字段
            $returnFields = explode(',', $fields);
            $config['fields'] = array_intersect($this->allowDetailFields, $returnFields);
        }
        if (!$config['fields']){
            $config['fields'] = $this->allowDetailFields;
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
        if (!strval($tex_id)) {
            $this->returnData($this->errCode[2]);
        }

        $result = D('TeacherExcellent')->delete(strval($tex_id));

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

        // 是否有 LOGO 上传
        if ($_FILES['tex_logo']['size'] > 0) {
            
            $tex_logo = D('TeacherExcellent')->uploadLogo($_FILES['tex_logo']);
            if ($tex_logo === false) {
                $this->returnData(array('status' => 0, 'info' => D('TeacherExcellent')->getError()));
            }
            $_POST['args']['tex_logo'] = $tex_logo['savename'];
        }
        
        // 特殊值处理
        $_POST['args']['me_id'] = intval($auth_id);

        // 入库
        $result = D('TeacherExcellent')->insert($_POST['args']);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '新增成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('TeacherExcellent')->getError()));
        }
    }

    // 修改
    public function edit() {
        extract($_POST['args']);
        
        // 校验
        if (!strval($tex_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 确定允许操作的字段
        foreach ($_POST['args'] as $key => $val) {
            if (!in_array($key, $this->allowFieldsUpdate)) {
                unset($_POST['args'][$key]);
            }
        }

        // 是否有 LOGO 上传
        if ($_FILES['tex_logo']['size'] > 0) {
            
            $tex_logo = D('TeacherExcellent')->uploadLogo($_FILES['tex_logo']);
            if ($tex_logo === false) {
                $this->returnData(array('status' => 0, 'info' => D('TeacherExcellent')->getError()));
            }
            $_POST['args']['tex_logo'] = $tex_logo['savename'];
        }
        
        // 特殊值处理
        $_POST['args']['me_id'] = intval($auth_id);

        // 入库
        $result = D('TeacherExcellent')->insert($_POST['args']);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '编辑成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('TeacherExcellent')->getError()));
        }
    }
}
?>
