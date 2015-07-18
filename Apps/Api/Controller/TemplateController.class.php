<?php
namespace Api\Controller;
use Think\Controller;
class TemplateController extends OpenController {

    // 列表允许输出字段
    private $allowFields = array('te_id', 'te_title', 'tt_id', 'te_name', 'te_status');

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
        
        // 页码
        if ($p) {
            $config['p'] = intval($p) ? intval($p) : 1;
        }

        // 排序
        if ($order) {
            $config['order'] = $order;
        }

        // logic 列表类 结果不处理
        $config['is_deal_result'] = $is_deal_result ? $is_deal_result : false;
        // 是否分页
        $config['is_page'] = isset($is_page) ? $is_page : true;
        // api 请求
        $config['is_api'] = true;
        // 每页返回数
        if (isset($return_num)) {
            $config['every_page_num'] =  intval($return_num);
        }

        $result = D('Template')->lists($_POST['args'], $config);

        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无数据'));
        }

        $this->returnData($result);
    }

    // 信息
    public function shows() {
        extract($_POST['args']);

        // 校验
        if (!intval($te_id)) {
            $this->returnData($this->errCode[2]);
        }

        $result = D('Template')->getById(intval($te_id));
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
}
?>
