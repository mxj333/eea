<?php
namespace Api\Controller;
use Think\Controller;
class TagController extends OpenController {

    // 资讯列表允许输出字段
    private $allowFields = array('t_id', 't_title', 't_type', 't_status');
    // 资讯表允许添加字段
    private $allowFieldsInsert = array('t_title', 't_type', 't_status');
    // 资讯表允许修改字段
    private $allowFieldsUpdate = array('t_id', 't_title', 't_type', 't_status');

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

        $result = D('Tag')->lists($_POST['args'], $config);

        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无数据'));
        }

        $this->returnData($result);
    }

    // 获取标签
    public function category() {
        extract($_POST['args']);

        $config['where']['t_status'] = 1;
        if (intval($t_type)) { // 标签类型
            $config['fields'] = 't_id,t_title';
            $config['where']['t_type'] = intval($t_type);
        } else {
            $config['fields'] = 't_id,t_title,t_type';
        }
        if (intval($return_num)) {
            $config['limit'] = intval($return_num);
        }

        $list = D('Tag')->getAll($config);
        if (intval($t_type)) {
            // 返回某种标签
            $this->returnData($list);
        }

        // 默认全部标签
        foreach ($list as $val) {
            $result[$val['t_type']][$val['t_id']] = $val['t_title'];
        }
        $this->returnData($result);
    }
}
?>
