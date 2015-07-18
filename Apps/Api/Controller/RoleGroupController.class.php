<?php
namespace Api\Controller;
use Think\Controller;
class RoleGroupController extends OpenController {

    // 用户列表允许输出字段
    private $allowFields = array('rg_id', 'rg_title', 'rg_pid', 'rg_status', 'rg_type');
    // 用户详情允许输出字段
    private $allowDetailFields = array('rg_id', 'rg_title', 'rg_pid', 'rg_status', 'rg_type');
    // 用户表允许添加字段
    private $allowFieldsInsert = array('rg_title', 'rg_pid', 'rg_status', 'rg_type');
    // 用户表允许修改字段
    private $allowFieldsUpdate = array('rg_id', 'rg_title', 'rg_pid', 'rg_status', 'rg_type');

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

        $result = D('RoleGroup')->lists($_POST['args'], $config);
        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无数据'));
        }

        $this->returnData($result);
    }

    // 用户信息
    public function shows() {
        extract($_POST['args']);

        // 校验
        if (!intval($rg_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 结果不处理
        $config['is_deal_result'] = $is_deal_result ? $is_deal_result : false;

        $result = D('RoleGroup')->getById(intval($rg_id), $config);
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
        if (!strval($rg_id)) {
            $this->returnData($this->errCode[2]);
        }

        $config['where']['rg_id'] = array('IN', strval($rg_id));
        $result = D('RoleGroup')->delete($config);
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
        
        $saveData = D('RoleGroup', 'Model')->create($_POST['args']);
        if ($saveData === false) {
            $this->returnData(array('status' => 0, 'info' => D('RoleGroup', 'Model')->getError()));
        }

        $result = D('RoleGroup', 'Model')->insert($saveData);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '新增成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '新增失败'));
        }
    }

    // 修改
    public function edit() {
        extract($_POST['args']);
        
        // 校验
        if (!intval($rg_id)) {
            $this->returnData($this->errCode[2]);
        }
        
        // 确定允许操作的字段
        foreach ($_POST['args'] as $key => $val) {
            if (!in_array($key, $this->allowFieldsUpdate)) {
                unset($_POST['args'][$key]);
            }
        }
        
        $saveData = D('RoleGroup', 'Model')->create($_POST['args']);
        if ($saveData === false) {
            $this->returnData(array('status' => 0, 'info' => D('RoleGroup', 'Model')->getError()));
        }

        $result = D('RoleGroup', 'Model')->update($saveData);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '编辑成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '编辑失败'));
        }
    }

    // 角色功能权限关系
    public function getRelation() {
        extract($_POST['args']);

        // 校验
        if (!intval($rg_id)) {
            $this->returnData($this->errCode[2]);
        }

        $accessConfig['where']['rg_id'] = intval($rg_id);
        $accessConfig['fields'] = 'pe_id,pe_action';
        $result = D('AccessMember')->getAll($accessConfig);

        $this->returnData($result);
    }

    // 添加角色功能权限关系
    public function setRelation() {
        extract($_POST['args']);

        // 校验
        if (!intval($rg_id)) {
            $this->returnData($this->errCode[2]);
        }

        $result = D('RoleGroup')->saveRole(intval($rg_id), strval($pe_action));
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '操作成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '操作失败'));
        }
    }

    // 添加角色用户
    public function user() {
        extract($_POST['args']);

        // 校验
        if (!intval($rg_id) || !strval($auth_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 是否批量操作
        $is_batch = $is_batch ? true : false;

        if ($is_batch) {
            $result = D('RoleGroup')->addUser(intval($rg_id), strval($auth_id));
        } else {
            $result = D('RoleGroup')->saveUser(intval($rg_id), intval($auth_id));
        }
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '操作成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '操作失败'));
        }
    }

    // 添加角色用户
    public function getUser() {
        extract($_POST['args']);

        // 校验
        if (!intval($rg_id)) {
            $this->returnData($this->errCode[2]);
        }

        $config = array();
        $config['is_deal_result'] = isset($is_deal_result) ? $is_deal_result : true;

        $result = D('RoleGroup')->user(intval($rg_id), $config);

        $this->returnData($result);
    }

    // 删除角色用户
    public function delUser() {
        extract($_POST['args']);

        // 校验
        if (!intval($rg_id) || !intval($auth_id)) {
            $this->returnData($this->errCode[2]);
        }

        $result = D('RoleGroup')->delUser(intval($rg_id), intval($auth_id));
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '删除成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '删除失败'));
        }
    }
}
?>
