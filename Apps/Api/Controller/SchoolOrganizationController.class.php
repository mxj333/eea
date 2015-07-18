<?php
namespace Api\Controller;
use Think\Controller;
class SchoolOrganizationController extends OpenController {

    // 列表允许输出字段
    private $allowFields = array('so_id', 'so_title', 'so_type', 'so_sort', 'so_status', 's_id');
    // 详情
    private $allowDetailFields = array('so_id', 'so_title', 'so_type', 'so_sort', 'so_status', 's_id', 'so_created', 'so_updated');
    // 添加
    private $allowFieldsInsert = array('so_title', 'so_type', 'so_sort', 'so_status', 's_id');
    // 修改
    private $allowFieldsUpdate = array('so_id', 'so_title', 'so_type', 'so_sort', 'so_status', 's_id');

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

        $result = D('SchoolOrganization')->lists($_POST['args'], $config);
        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无数据'));
        }

        $this->returnData($result);
    }

    // 用户信息
    public function shows() {
        extract($_POST['args']);

        // 校验
        if (!intval($so_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 结果不处理
        $config['is_deal_result'] = $is_deal_result ? $is_deal_result : false;

        $result = D('SchoolOrganization')->getById(intval($so_id), $config);
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
        if (!strval($so_id)) {
            $this->returnData($this->errCode[2]);
        }

        $result = D('SchoolOrganization')->delete(strval($so_id));

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

        // 入库
        $result = D('SchoolOrganization')->insert($_POST['args']);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '新增成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('SchoolOrganization')->getError()));
        }
    }

    // 修改
    public function edit() {
        extract($_POST['args']);
        
        // 校验
        if (!strval($so_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 确定允许操作的字段
        foreach ($_POST['args'] as $key => $val) {
            if (!in_array($key, $this->allowFieldsUpdate)) {
                unset($_POST['args'][$key]);
            }
        }

        // 入库
        $result = D('SchoolOrganization')->insert($_POST['args']);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '编辑成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('SchoolOrganization')->getError()));
        }
    }
}
?>
