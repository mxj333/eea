<?php
namespace Api\Controller;
use Think\Controller;
class ClassController extends OpenController {

    // 列表允许输出字段
    private $allowFields = array('c_id', 'c_title', 'c_logo', 'c_grade', 's_id', 'c_description', 'me_id', 'c_status', 'c_created');
    // 详情允许输出字段
    private $allowDetailFields = array('c_id', 'c_title', 'c_logo', 'c_grade', 's_id', 's_title', 'c_description', 'me_id', 'me_nickname', 're_id', 're_title', 'c_status', 'c_created', 'teachers', 'students');
    // 添加
    private $allowFieldsInsert = array('c_title', 's_id', 'c_grade', 'c_description', 'c_status');
    // 修改
    private $allowFieldsUpdate = array('c_id', 'c_title', 's_id', 'c_grade', 'c_description', 'c_status');
    // 任课教师
    private $allowFieldsPublish = array('t_id', 's_id', 'c_id', 'me_id');
    private $allowShowFields = array('ce_id', 'ce_logo', 'ce_description', 'ce_sort', 'ce_type', 'ce_status', 're_id', 're_title', 's_id', 'c_id', 'ce_created');

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

        $result = D('Class')->lists($_POST['args'], $config);
        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无数据'));
        }

        $this->returnData($result);
    }

    // 用户信息
    public function shows() {
        extract($_POST['args']);

        // 校验
        if (!intval($c_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 结果不处理
        $config['is_deal_result'] = $is_deal_result ? $is_deal_result : false;

        $result = D('Class')->getById(intval($c_id), $config);
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
        if (!strval($c_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 注:删除不是真正的删除   标记删除
        $config['where']['c_id'] = array('IN', strval($c_id));
        // 需要用户id
        $data['c_deleted_extend_id'] = intval($this->authInfo['me_id']);
        $result = D('Class')->signDeleted($config, $data);

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
        if ($_FILES['c_logo']['size'] > 0) {
            
            $c_logo = D('Class')->uploadLogo($_FILES['c_logo']);
            if ($c_logo === false) {
                $this->returnData(array('status' => 0, 'info' => D('Class')->getError()));
            }
            $_POST['args']['c_logo'] = $c_logo['savename'];
        }

        // 默认值
        $_POST['args']['c_creator_id'] = intval($this->authInfo['me_id']);
        
        // 入库
        $result = D('Class')->insert($_POST['args']);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '新增成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('Class')->getError()));
        }
    }

    // 修改
    public function edit() {
        extract($_POST['args']);
        
        // 校验
        if (!strval($c_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 确定允许操作的字段
        foreach ($_POST['args'] as $key => $val) {
            if (!in_array($key, $this->allowFieldsUpdate)) {
                unset($_POST['args'][$key]);
            }
        }

        // 是否有 LOGO 上传
        if ($_FILES['c_logo']['size'] > 0) {
            
            $c_logo = D('Class')->uploadLogo($_FILES['c_logo']);
            if ($c_logo === false) {
                $this->returnData(array('status' => 0, 'info' => D('Class')->getError()));
            }
            $_POST['args']['c_logo'] = $c_logo['savename'];
        }
        
        // 入库
        $result = D('Class')->insert($_POST['args']);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '编辑成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('Class')->getError()));
        }
    }

    // 定义班主任
    public function authorization() {
        extract($_POST['args']);
        
        // 校验
        if (!intval($auth_id) || !intval($c_id)) {
            $this->returnData($this->errCode[2]);
        }
        
        $result = D('Class')->setAdviser(intval($auth_id), intval($c_id));
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '操作成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '操作失败'));
        }
    }

    // 定义任课教师
    public function publish() {
        extract($_POST['args']);
        
        // 校验
        if (!intval($s_id) || !intval($c_id) || empty($relation)) {
            $this->returnData($this->errCode[2]);
        }
        
        // 学科  教师 设置
        $result = D('ClassInstructors')->insert(intval($s_id), intval($c_id), $relation);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '操作成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '操作失败'));
        }
    }

    // 定义任课教师
    public function getPublish() {
        extract($_POST['args']);
        
        // 校验
        if (!intval($c_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 返回字段
        if ($fields) {
            // 有定义返回字段
            $returnFields = explode(',', $fields);
            $config['fields'] = array_intersect($this->allowFieldsPublish, $returnFields);
        }
        if (!$config['fields']){
            $config['fields'] = $this->allowFieldsPublish;
        }
        
        // 学科  教师 设置
        $result = D('ClassInstructors')->getList($c_id, $config);
        
        $this->returnData($result);
    }

    // 定义班级学生
    public function user() {
        extract($_POST['args']);
        
        // 校验
        if (!intval($c_id) || !intval($auth_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 判断是否已经分配学校
        $config['fields'] = 'c_id';
        $config['where']['me_id'] = intval($auth_id);
        $class_id = D('Member')->getOne($config);
        if ($class_id) {
            // 已经定义过班级
            $this->returnData(array('status' => 0, 'info' => '用户已分配过班级'));
        }

        // 分配班级
        $result = D('Member')->update(array('c_id' => intval($c_id)), array('where' => array('me_id' => intval($auth_id))));
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '新增成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '新增失败'));
        }
    }

    // 删除班级学生
    public function delUser() {
        extract($_POST['args']);
        
        // 校验
        if (!intval($auth_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 分配班级
        $result = D('Member')->update(array('c_id' => 0), array('where' => array('me_id' => intval($auth_id))));
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '删除成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '删除失败'));
        }
    }

    // 优秀班级
    public function excellent() {
        extract($_POST['args']);

        $result = array();
        $every_num = intval($every_num) ? intval($every_num) : 5;
        $order = strval($order) ? strval($order) : 'ce_sort DESC';
        
        if ($ce_type) {
            $cData['ce_type'] = $ce_type;
        }

        $cData['re_id'] = $re_id;
        $cData['s_id'] = $s_id;
        $cData['c_id'] = $c_id;
        $cData['ce_status'] = 1;
        $cConfig['order'] = $order;
        $cConfig['every_page_num'] = $every_num;
        $cConfig['is_deal_result'] = false;
        $cList = D('ClassExcellent')->lists($cData, $cConfig);
        $result = $this->dealReturnResult($cList['list']);

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

            // 班级信息
            $cConfig['where']['c_id'] = intval($info['c_id']);
            $class = D('Class', 'Model')->getOne($cConfig);
            $info['c_title'] = $class['c_title'];
            $info['c_logo'] = $class['c_logo'];
            $info['c_grade'] = $class['c_grade'];
            $info['me_id'] = $class['me_id'];
            $info['c_created'] = $class['c_created'];

            // logo
            $list[$key]['c_title'] = $class['c_title'];
            $list[$key]['ce_logo'] = D('ClassExcellent')->getLogo($info);
            
        }
        
        return $list;
    }
}
?>
