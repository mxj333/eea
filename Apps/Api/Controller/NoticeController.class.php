<?php
namespace Api\Controller;
use Think\Controller;
class NoticeController extends OpenController {

    // 用户列表允许输出字段
    private $allowFields = array('no_id', 'no_title', 'no_url', 'no_starttime', 'no_endtime', 'no_sort', 'no_content', 're_id', 're_title', 's_id', 'c_id');
    // 用户详情允许输出字段
    private $allowDetailFields = array('no_id', 'no_title', 'no_url', 'no_starttime', 'no_endtime', 'no_sort', 'no_content', 're_id', 're_title', 's_id', 'c_id', 'me_nickname');
    // 用户表允许添加字段
    private $allowFieldsInsert = array('no_title', 'no_url', 'no_starttime', 'no_endtime', 'no_sort', 'no_content', 're_id', 're_title', 's_id', 'c_id');
    // 用户表允许修改字段
    private $allowFieldsUpdate = array('no_id', 'no_title', 'no_url', 'no_starttime', 'no_endtime', 'no_sort', 'no_content', 're_id', 're_title', 's_id', 'c_id');
    private $allowShowFields = array('no_id', 'no_title', 'no_url', 'no_starttime', 'no_endtime', 'no_sort', 'no_content');

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
        if ($order) {
            $config['order'] = $order;
        }
        // 每页返回数
        if (isset($return_num)) {
            $config['every_page_num'] =  intval($return_num);
        }

        $result = D('Notice')->lists($_POST['args'], $config);
        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无数据'));
        }

        $this->returnData($result);
    }

    // 用户信息
    public function shows() {
        extract($_POST['args']);

        // 校验
        if (!intval($no_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 结果不处理
        $config['is_deal_result'] = $is_deal_result ? $is_deal_result : false;

        $result = D('Notice')->getById(intval($no_id), $config);
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
        if (!strval($no_id)) {
            $this->returnData($this->errCode[2]);
        }

        $config['where']['no_id'] = array('IN', strval($no_id));
        $result = D('Notice')->delete($config);
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
        
        $_POST['args']['no_creator_id'] = intval($this->authInfo['me_id']);
        $_POST['args']['no_creator_table'] = 'Member';

        $result = D('Notice')->insert($_POST['args']);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '新增成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('Notice')->getError()));
        }
    }

    // 修改
    public function edit() {
        extract($_POST['args']);
        
        // 校验
        if (!intval($no_id)) {
            $this->returnData($this->errCode[2]);
        }
        
        // 确定允许操作的字段
        foreach ($_POST['args'] as $key => $val) {
            if (!in_array($key, $this->allowFieldsUpdate)) {
                unset($_POST['args'][$key]);
            }
        }
        
        $_POST['args']['no_creator_id'] = intval($this->authInfo['me_id']);
        $_POST['args']['no_creator_table'] = 'Member';

        $result = D('Notice')->insert($_POST['args']);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '编辑成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('Notice')->getError()));
        }
    }

    /* 前台展示列表
     * belong 所属
     * type_num 通知个数
     * order 排序
     */ 
    public function getShows() {
        extract($_POST['args']);

        $result = array();

        // 个数
        $type_num = intval($type_num) ? intval($type_num) : 2;

        $noData['adv_status'] = 1;
        $noData['no_starttime'] = time();
        $noData['no_endtime'] = time();
        $noData['re_id'] = strval($re_id);
        $noData['s_id'] = intval($s_id);
        $noData['c_id'] = intval($c_id);
        $noConfig['order'] = strval($order) ? strval($order) : 'no_sort ASC';
        $noConfig['every_page_num'] = $type_num;
        $noConfig['is_deal_result'] = false;
        $noList = D('Notice')->lists($noData, $noConfig);
        $result = $this->dealReturnResult($noList['list']);

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

    /*
     * 资讯某字段累加
     * type 0 默认浏览量
     */
    public function increase() {
        extract($_POST['args']);

        // 校验
        if (!intval($no_id)) {
            $this->returnData($this->errCode[2]);
        }

        $type = intval($type) ? intval($type) : 0;

        if ($type == 1) {
            // res_downloads
            //D('Resource', 'Model')->increase('res_downloads', array('res_id' => intval($res_id)), 1);
        } elseif ($type == 2) {
            // res_comment_count
            //D('Resource', 'Model')->increase('res_comment_count', array('res_id' => intval($res_id)), 1);
        } else {
            // res_hits
            D('Notice', 'Model')->increase('no_hits', array('no_id' => intval($no_id)), 1);
        }
    }
}
?>
