<?php
namespace Api\Controller;
use Think\Controller;
class MessageController extends OpenController {

    // 用户列表允许输出字段
    private $allowFields = array('mes.mes_id', 'mes.a_id', 'mes.me_id', 'me.me_nickname', 'mes.mes_content', 'mes.mes_created');
    // 用户详情允许输出字段
    private $allowDetailFields = array('mes_id', 'a_id', 'a_title', 'me_id', 'me_nickname', 're_id', 're_title', 's_id', 'mes_content', 'mes_created');
    // 用户表允许添加字段
    private $allowFieldsInsert = array('a_id', 'auth_id', 'mes_content');
    private $allowShowFields = array('mes_id', 'a_id', 'a_title', 'me_id', 'me_nickname', 'mes_content', 'mes_created', 'mes_url');

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

        $result = D('Message')->lists($_POST['args'], $config);
        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无数据'));
        }

        $this->returnData($result);
    }

    // 用户信息
    public function shows() {
        extract($_POST['args']);

        // 校验
        if (!intval($mes_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 结果不处理
        $config['is_deal_result'] = $is_deal_result ? $is_deal_result : false;

        $result = D('Message')->getById(intval($mes_id), $config);
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
        if (!strval($mes_id)) {
            $this->returnData($this->errCode[2]);
        }

        $config['where']['mes_id'] = array('IN', strval($mes_id));
        $result = D('Message')->delete($config);
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
        
        $_POST['args']['me_id'] = intval($_POST['args']['auth_id']);

        $result = D('Message')->insert($_POST['args']);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '新增成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('Message')->getError()));
        }
    }

    /* 前台展示列表
     * belong 所属
     * type_num 个数
     * order 排序
     */ 
    public function getShows() {
        extract($_POST['args']);

        $result = array();

        // 个数
        $type_num = intval($type_num) ? intval($type_num) : 2;

        $mesData['a_id'] = intval($a_id);
        $mesData['re_id'] = intval($re_id);
        $mesData['s_id'] = intval($s_id);
        $mesData['c_id'] = intval($c_id);
        $mesConfig['order'] = strval($order) ? strval($order) : 'mes.mes_created DESC';
        $mesConfig['every_page_num'] = $type_num;
        $mesConfig['is_deal_result'] = false;
        $mesList = D('Message')->lists($mesData, $mesConfig);
        $result = $this->dealReturnResult($mesList['list']);

        $this->returnData($result);
    }

    // 处理返回结果
    private function dealReturnResult($list) {

        if (!$list) {
            return array();
        }

        foreach ($list as $key => $info) {

            // 过滤处理
            foreach ($info as $k => $v) {
                if (!in_array($k, $this->allowShowFields)) {
                    unset($list[$key][$k]);
                }
            }

            // 时间处理
            $list[$key]['mes_created'] = date('Y-m-d H:i:s', $list[$key]['mes_created']);
        }
        
        return $list;
    }
}
?>
