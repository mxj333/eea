<?php
namespace Api\Controller;
use Think\Controller;
class KnowledgePointsController extends OpenController {

    // 允许输出字段
    private $allowFields = array('kp_id', 'kp_title', 'kp_subject', 'kp_pid', 'kp_level', 'kp_sort', 'kp_status');

    // 资源列表
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
        
        // 页码
        if ($p) {
            $config['p'] = intval($p) ? intval($p) : 1;
        }
        // logic 列表类 结果不处理
        $config['is_deal_result'] = $is_deal_result ? $is_deal_result : false;
        // api 请求
        $config['is_api'] = true;
        // 是否分页
        $config['is_page'] = isset($is_page) ? $is_page : true;

        $result = D('KnowledgePoints')->lists($_POST['args'], $config);
        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无数据'));
        }

        $this->returnData($result);
    }

    // 资源信息
    public function shows() {
        extract($_POST['args']);

        // 校验
        if (!intval($kp_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 结果不处理
        $config['is_deal_result'] = false;

        $result = D('KnowledgePoints')->getById(intval($kp_id), $config);
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

        // 过滤返回结果
        foreach ($result as $key => $val) {
            if (!in_array($key, $config['fields'])) {
                unset($result[$key]);
            }
        }

        $this->returnData($result);
    }
}
?>
