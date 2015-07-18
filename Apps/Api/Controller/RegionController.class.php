<?php
namespace Api\Controller;
use Think\Controller;
class RegionController extends OpenController {

    // 列表允许输出字段
    private $allowFields = array('re_id', 're_ids', 're_title', 're_titles', 're_pid', 're_status', 're_children');

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
        $config['is_api'] = isset($is_api) ? $is_api : true;
        // 分页
        // api 请求
        $config['is_page'] = isset($is_page) ? $is_page : true;

        $result = D('Region')->lists($_POST['args'], $config);
        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无数据'));
        }

        $this->returnData($result);
    }

    // 检查校长
    public function shows() {
        extract($_POST['args']);

        // 校验
        if (!intval($re_id)) {
            $this->returnData($this->errCode[2]);
        }

        $reConfig['where']['re_id'] = intval($re_id);
        $result = D('Region', 'Model')->getOne($reConfig);

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
