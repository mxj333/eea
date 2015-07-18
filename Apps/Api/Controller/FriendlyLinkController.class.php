<?php
namespace Api\Controller;
use Think\Controller;
class FriendlyLinkController extends OpenController {

    // 用户列表允许输出字段
    private $allowFields = array('fl_id', 'fl_title', 'fl_url', 'fl_logo', 'fl_sort', 'fl_status', 'fl_created', 're_id', 're_title', 's_id');
    // 用户详情允许输出字段
    private $allowDetailFields = array('fl_id', 'fl_title', 'fl_url', 'fl_logo', 'fl_sort', 'fl_status', 'fl_created', 're_id', 're_title', 's_id');
    // 用户表允许添加字段
    private $allowFieldsInsert = array('fl_title', 'fl_url', 'fl_sort', 'fl_status', 're_id', 're_title', 's_id');
    // 用户表允许修改字段
    private $allowFieldsUpdate = array('fl_id', 'fl_title', 'fl_url', 'fl_sort', 'fl_status', 're_id', 're_title', 's_id');
    private $allowShowFields = array('fl_id', 'fl_title', 'fl_url', 'fl_sort', 'fl_created');

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

        $result = D('FriendlyLink')->lists($_POST['args'], $config);
        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无数据'));
        }

        $this->returnData($result);
    }

    // 用户信息
    public function shows() {
        extract($_POST['args']);

        // 校验
        if (!intval($fl_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 结果不处理
        $config['is_deal_result'] = $is_deal_result ? $is_deal_result : false;

        $result = D('FriendlyLink')->getById(intval($fl_id));
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
        if (!strval($fl_id)) {
            $this->returnData($this->errCode[2]);
        }

        $result = D('FriendlyLink')->delete(strval($fl_id));
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
        if ($_FILES['fl_logo']['size'] > 0) {
            
            $fl_logo = D('FriendlyLink')->uploadLogo($_FILES['fl_logo']);
            if ($fl_logo === false) {
                $this->returnData(array('status' => 0, 'info' => D('FriendlyLink')->getError()));
            }
            $_POST['args']['fl_logo'] = $fl_logo['savename'];
        }

        // 入库
        $result = D('FriendlyLink')->insert($_POST['args']);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '新增成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('FriendlyLink')->getError()));
        }
    }

    // 修改
    public function edit() {
        extract($_POST['args']);
        
        // 校验
        if (!intval($fl_id)) {
            $this->returnData($this->errCode[2]);
        }
        
        // 确定允许操作的字段
        foreach ($_POST['args'] as $key => $val) {
            if (!in_array($key, $this->allowFieldsUpdate)) {
                unset($_POST['args'][$key]);
            }
        }

        // 是否有 LOGO 上传
        if ($_FILES['fl_logo']['size'] > 0) {
            
            $fl_logo = D('FriendlyLink')->uploadLogo($_FILES['fl_logo']);
            if ($fl_logo === false) {
                $this->returnData(array('status' => 0, 'info' => D('FriendlyLink')->getError()));
            }
            $_POST['args']['fl_logo'] = $fl_logo['savename'];
        }

        // 入库
        $result = D('FriendlyLink')->insert($_POST['args']);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '编辑成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('FriendlyLink')->getError()));
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
        $type_num = intval($type_num) ? intval($type_num) : 8;

        $flinkData['fl_status'] = 1;
        $flinkData['re_id'] = strval($re_id);
        $flinkData['s_id'] = intval($s_id);
        $flinkConfig['order'] = strval($order) ? strval($order) : 'fl_sort ASC';
        $flinkConfig['every_page_num'] = $type_num;
        $flinkConfig['is_deal_result'] = false;
        $flinkList = D('FriendlyLink')->lists($flinkData, $flinkConfig);
        $result = $this->dealReturnResult($flinkList['list']);

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
        }
        
        return $list;
    }
}
?>
