<?php
namespace Api\Controller;
use Think\Controller;
class TermYearController extends OpenController {

    // 资讯列表允许输出字段
    private $allowFields = array('ty_id', 'ty_year', 'ty_last_starttime', 'ty_last_endtime', 'ty_next_starttime', 'ty_next_endtime', 's_id', 'ty_created', 'ty_updated');
    // 资讯表允许添加字段
    private $allowFieldsInsert = array('ty_year', 'ty_last_starttime', 'ty_last_endtime', 'ty_next_starttime', 'ty_next_endtime', 's_id');
    // 资讯表允许修改字段
    private $allowFieldsUpdate = array('ty_id', 'ty_year', 'ty_last_starttime', 'ty_last_endtime', 'ty_next_starttime', 'ty_next_endtime', 's_id');

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

        $result = D('TermYear')->lists($_POST['args'], $config);

        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无数据'));
        }

        $this->returnData($result);
    }

    // 信息
    public function shows() {
        extract($_POST['args']);

        // 校验
        if (!intval($ty_id)) {
            $this->returnData($this->errCode[2]);
        }

        $result = D('TermYear')->getById(intval($ty_id));
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

    // 添加
    public function add() {
        extract($_POST['args']);
        
        // 确定允许操作的字段
        foreach ($_POST['args'] as $key => $val) {
            if (!in_array($key, $this->allowFieldsInsert)) {
                unset($_POST['args'][$key]);
            }
        }

        $result = D('TermYear')->insert($_POST['args']);
        
        if ($result) {
            $this->returnData(array('status' => 1, 'res_value' => $result, 'info' => '新增成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('TermYear')->getError()));
        }
    }

    // 修改
    public function edit() {
        extract($_POST['args']);
        
        // 校验
        if (!intval($ty_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 确定允许操作的字段
        foreach ($_POST['args'] as $key => $val) {
            if (!in_array($key, $this->allowFieldsUpdate)) {
                unset($_POST['args'][$key]);
            }
        }

        $result = D('TermYear')->insert($_POST['args']);

        if ($result) {
            $this->returnData(array('status' => 1, 'res_value' => $result, 'info' => '编辑成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('TermYear')->getError()));
        }
    }

    // 删除
    public function del() {
        extract($_POST['args']);
        
        // 校验
        if (!strval($ty_id)) {
            $this->returnData($this->errCode[2]);
        }

        $config['where']['ty_id'] = array('IN', strval($ty_id));
        $result = D('TermYear')->delete($config);

        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '删除成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '删除失败'));
        }
    }
}
?>
