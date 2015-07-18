<?php
namespace Api\Controller;
use Think\Controller;
class AdvertController extends OpenController {

    // 列表允许输出字段
    private $allowFields = array('adv_id', 'adv_title', 'adv_start_time', 'adv_stop_time', 'ap_id', 'adv_hits', 'adv_url', 'adv_reminds', 'adv_sort', 'adv_people', 'adv_tel', 'adv_email', 'adv_created', 'adv_updated', 'adv_status', 'adv_savepath', 'adv_savename', 'adv_ext');
    // 用户表允许添加字段
    private $allowFieldsInsert = array('adv_title', 'adv_start_time', 'adv_stop_time', 'ap_id', 'adv_hits', 'adv_url', 'adv_reminds', 'adv_sort', 'adv_people', 'adv_tel', 'adv_email', 'adv_created', 'adv_updated', 'adv_status', 're_id', 're_title', 's_id');
    // 用户表允许修改字段
    private $allowFieldsUpdate = array('adv_id', 'adv_title', 'adv_start_time', 'adv_stop_time', 'ap_id', 'adv_hits', 'adv_url', 'adv_reminds', 'adv_sort', 'adv_people', 'adv_tel', 'adv_email', 'adv_created', 'adv_updated', 'adv_status', 're_id', 're_title', 's_id');
    private $allowShowFields = array('adv_id', 'adv_title', 'adv_hits', 'adv_url', 'adv_reminds', 'adv_sort', 'adv_people', 'adv_tel', 'adv_email', 'allowShowFields');

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

        $result = D('Advert')->lists($_POST['args'], $config);

        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无数据'));
        }

        $this->returnData($result);
    }

    // 信息
    public function shows() {
        extract($_POST['args']);

        // 校验
        if (!intval($adv_id)) {
            $this->returnData($this->errCode[2]);
        }

        $result = D('Advert')->getById(intval($adv_id));
        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无'));
        }

        // 图
        $adv_image = D('Advert')->getLogo($result);

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

        $result['adv_image'] = $adv_image;
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

        // 日期转换
        $_POST['args']['adv_start_time'] = strtotime($adv_start_time);
        $_POST['args']['adv_stop_time'] = strtotime($adv_stop_time);
        $_POST['args']['adv_sort'] = intval($adv_sort) ? intval($adv_sort) : 255;
        if ($_POST['args']['adv_start_time'] > $_POST['args']['adv_stop_time']) {
            $this->returnData(array('status' => 0, 'info' => '结束时间必须大于开始时间'));
        }

        // 是否有 文件 上传
        if ($_FILES['file']['size'] > 0) {
            
            $file = D('Advert')->uploadLogo($_FILES['file']);
            if ($file === false) {
                $this->returnData(array('status' => 0, 'info' => D('Advert')->getError()));
            }
            $savepath = explode('/', $file['savepath']);
            $_POST['args']['adv_savepath'] = $savepath[1];
            $_POST['args']['adv_savename'] = $file['savename'];
            $_POST['args']['adv_ext'] = $file['ext'];
        }

        $result = D('Advert')->insert($_POST['args']);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '新增成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('Advert')->getError()));
        }
    }

    // 修改
    public function edit() {
        extract($_POST['args']);
        
        // 校验
        if (!intval($adv_id)) {
            $this->returnData($this->errCode[2]);
        }
        
        // 确定允许操作的字段
        foreach ($_POST['args'] as $key => $val) {
            if (!in_array($key, $this->allowFieldsUpdate)) {
                unset($_POST['args'][$key]);
            }
        }

        // 日期转换
        $_POST['args']['adv_start_time'] = strtotime($adv_start_time);
        $_POST['args']['adv_stop_time'] = strtotime($adv_stop_time);
        $_POST['args']['adv_sort'] = intval($adv_sort) ? intval($adv_sort) : 255;
        if ($_POST['args']['adv_start_time'] > $_POST['args']['adv_stop_time']) {
            $this->returnData(array('status' => 0, 'info' => '结束时间必须大于开始时间'));
        }

        // 是否有 文件 上传
        if ($_FILES['file']['size'] > 0) {
            
            $file = D('Advert')->uploadLogo($_FILES['file']);
            if ($file === false) {
                $this->returnData(array('status' => 0, 'info' => D('Advert')->getError()));
            }
            $savepath = explode('/', $file['savepath']);
            $_POST['args']['adv_savepath'] = $savepath[1];
            $_POST['args']['adv_savename'] = $file['savename'];
            $_POST['args']['adv_ext'] = $file['ext'];
        }

        $result = D('Advert')->insert($_POST['args']);
        
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '编辑成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('Advert')->getError()));
        }
    }

    // 删除
    public function del() {
        extract($_POST['args']);

        // 校验
        if (!strval($adv_id)) {
            $this->returnData($this->errCode[2]);
        }

        $result = D('Advert')->delete(strval($adv_id));

        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '删除成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '删除失败'));
        }
    }

    public function getPosition() {
        extract($_POST['args']);

        // 广告位 1 区域  2学校
        $type = intval($type) ? intval($type) : 1;

        $advertPositionConfig['fields'] = 'ap_id,ap_title';
        $advertPositionConfig['where']['tt_id'] = $type;
        $advertPosition = D('AdvertPosition')->getAll($advertPositionConfig);

        $this->returnData($advertPosition);
    }

    /* 前台展示列表
     * belong 所属
     * type 类型  1 区域首页横幅1   2 区域首页横幅2 。。。
     */ 
    public function getShows() {
        extract($_POST['args']);

        $result = array();

        // 类型
        $type = $type ? $type : array(1);
        if (!is_array($type)) {
            $type = explode(',', $type);
        }

        // 类型信息
        $typeConfig['where']['ap_id'] = array('IN', $type);
        $adv_pos = D('AdvertPosition')->getAll($typeConfig);
        foreach ($adv_pos as $pos_info) {
            $result[$pos_info['ap_id']]['pos_info'] = (array)$pos_info;
        }

        $advData['adv_status'] = 1;
        $advData['adv_start_time'] = time();
        $advData['adv_stop_time'] = time();
        $advData['re_id'] = strval($re_id);
        $advData['s_id'] = intval($s_id);
        $advConfig['order'] = 'adv_sort ASC';
        $advConfig['is_deal_result'] = false;
        foreach ($result as $ap_id => $pos) {
            $advData['ap_id'] = $ap_id;
            $advConfig['every_page_num'] = intval($pos['pos_info']['ap_ad_num']);
            $advList = D('Advert')->lists($advData, $advConfig);

            if (!$advList['list']) {
                // 不存在 取默认
                $advData['re_id'] = '';
                $advData['s_id'] = 0;
                $advList = D('Advert')->lists($advData, $advConfig);
            }

            $result[$ap_id]['list'] = $this->dealReturnResult($advList['list']);
        }

        $this->returnData($result);
    }

    // 处理返回结果
    private function dealReturnResult($list) {

        if (!$list) {
            return array();
        }

        foreach ($list as $key => $info) {

            // 特殊处理
            $list[$key]['adv_image'] = D('Advert')->getLogo($info);

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
