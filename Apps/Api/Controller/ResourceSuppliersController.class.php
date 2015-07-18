<?php
namespace Api\Controller;
use Think\Controller;
class ResourceSuppliersController extends OpenController {

    // 用户列表允许输出字段
    private $allowFields = array('rsu_id', 'rsu_title', 'rsu_account', 'rsu_contacts', 'rsu_mobile', 'rsu_address', 'rsu_valid', 'rsu_status', 'rsu_created');
    // 检查账号
    private $checkFields = array('rsu_id', 'rsu_title', 'rsu_account', 'rsu_password', 'rsu_contacts', 'rsu_mobile', 'rsu_address', 'rsu_valid', 'rsu_status', 'rsu_created');

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
        $config['is_page'] = isset($is_page) ? $is_page : true;

        $result = D('ResourceSuppliers')->lists($_POST['args'], $config);
        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无数据'));
        }

        $this->returnData($result);
    }

    // 用户信息
    public function shows() {
        extract($_POST['args']);

        // 校验
        if (!intval($rsu_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 结果不处理
        $config['is_deal_result'] = $is_deal_result ? $is_deal_result : false;

        $result = D('ResourceSuppliers')->getById(intval($rsu_id), $config);
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

    // 账号检查
    public function check() {
        extract($_POST['args']);

        // 校验
        if (!strval($rsu_account)) {
            $this->returnData($this->errCode[2]);
        }

        $config = array(
            'rsu_account' => $rsu_account,
        );
        $result = D('ResourceSuppliers')->getOne($config);
        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无'));
        }

        // 返回字段
        if ($fields) {
            // 有定义返回字段
            $returnFields = explode(',', $fields);
            $config['fields'] = array_intersect($this->checkFields, $returnFields);
        }
        if (!$config['fields']){
            $config['fields'] = $this->checkFields;
        }

        // 字段过滤
        foreach ($result as $key => $val) {
            if (!in_array($key, $config['fields'])) {
                unset($result[$key]);
            }
        }

        $this->returnData($result);
    }
}
?>
