<?php
namespace Api\Controller;
use Think\Controller;
class StudentExcellentController extends OpenController {

    // 列表允许输出字段
    private $allowFields = array('se_id', 'se_logo', 'se_description', 'se_sort', 'se_type', 'se_status', 're_id', 're_title', 's_id', 'c_id', 'me_id');
    // 详情
    private $allowDetailFields = array('se_id', 'se_logo', 'se_description', 'se_sort', 'se_type', 'se_status', 're_id', 're_title', 's_id', 's_title', 'c_id', 'c_title', 'me_id', 'me_nickname', 'se_created', 'se_updated');
    // 添加
    private $allowFieldsInsert = array('se_logo', 'se_description', 'se_sort', 'se_type', 'se_status', 're_id', 're_title', 's_id', 'c_id');
    // 修改
    private $allowFieldsUpdate = array('se_id', 'se_logo', 'se_description', 'se_sort', 'se_type', 'se_status', 're_id', 're_title', 's_id', 'c_id');

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

        $result = D('StudentExcellent')->lists($_POST['args'], $config);
        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无数据'));
        }

        $this->returnData($result);
    }

    // 用户信息
    public function shows() {
        extract($_POST['args']);

        // 校验
        if (!intval($se_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 结果不处理
        $config['is_deal_result'] = $is_deal_result ? $is_deal_result : false;

        $result = D('StudentExcellent')->getById(intval($se_id), $config);
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
        if (!strval($se_id)) {
            $this->returnData($this->errCode[2]);
        }

        $result = D('StudentExcellent')->delete(strval($se_id));

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
        if ($_FILES['se_logo']['size'] > 0) {
            
            $se_logo = D('StudentExcellent')->uploadLogo($_FILES['se_logo']);
            if ($se_logo === false) {
                $this->returnData(array('status' => 0, 'info' => D('StudentExcellent')->getError()));
            }
            $_POST['args']['se_logo'] = $se_logo['savename'];
        }
        
        // 特殊值处理
        $_POST['args']['me_id'] = intval($auth_id);

        // 入库
        $result = D('StudentExcellent')->insert($_POST['args']);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '新增成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('StudentExcellent')->getError()));
        }
    }

    // 修改
    public function edit() {
        extract($_POST['args']);
        
        // 校验
        if (!strval($se_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 确定允许操作的字段
        foreach ($_POST['args'] as $key => $val) {
            if (!in_array($key, $this->allowFieldsUpdate)) {
                unset($_POST['args'][$key]);
            }
        }

        // 是否有 LOGO 上传
        if ($_FILES['se_logo']['size'] > 0) {
            
            $se_logo = D('StudentExcellent')->uploadLogo($_FILES['se_logo']);
            if ($se_logo === false) {
                $this->returnData(array('status' => 0, 'info' => D('StudentExcellent')->getError()));
            }
            $_POST['args']['se_logo'] = $se_logo['savename'];
        }
        
        // 特殊值处理
        $_POST['args']['me_id'] = intval($auth_id);

        // 入库
        $result = D('StudentExcellent')->insert($_POST['args']);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '编辑成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('StudentExcellent')->getError()));
        }
    }

    /* 前台展示列表
     * belong 所属  1 平台
     * type 类型  1 优秀学生类型
     * type_ids 类型id
     * every_num 每种类型下学生个数  默认 5 个
     */ 
    public function getShows() {
        extract($_POST['args']);

        $result = array();

        // 哪种类型的
        $type = $type ? $type : array(1);
        if (!is_array($type)) {
            $type = explode(',', $type);
        }

        if ($type_ids && !is_array($type_ids)) {
            $type_ids = explode(',', $type_ids);
        }

        foreach ($type as $type_key => $mem_type) {
            $config = array();
            $data = array();
            switch ($mem_type) {
                case 1:
                    $stuType = explode(',', C('STUDENT_EXCELLENT_TYPE'));
                    unset($stuType[0]);
                    if ($type_ids) {
                        foreach ($type_ids as $type_id) {
                            $result[$mem_type]['type'][$type_id] = $stuType[$type_id];
                        }
                    } else {
                        foreach ($stuType as $type_id => $type_value) {
                            $result[$mem_type]['type'][$type_id] = $type_value;
                        }
                    }
                    
                    break;
            }
        }

        if (!$every_num) {
            $every_num = array(9);
        } elseif (!is_array($every_num)) {
            $every_num = explode(',', $every_num);
        }

        $size = $size ? $size : '_b';
        
        // 学生
        $sData['se_status'] = 1;
        $sData['re_id'] = strval($re_id);
        $sData['s_id'] = strval($s_id);
        $sData['c_id'] = strval($c_id);
        $sConfig['every_page_num'] = intval($every_num[1]) ? intval($every_num[1]) : intval($every_num[0]);
        $sConfig['is_deal_result'] = false;
        $sConfig['order'] = strval($order) ? strval($order) : 'se_sort ASC,se_id DESC';
        foreach ($result[1]['type'] as $type_id => $type_name) {
            $sData['se_type'] = $type_id;
            $sList = D('StudentExcellent')->lists($sData, $sConfig);
            foreach ($sList['list'] as $s_key => $s_info) {
                $sList['list'][$s_key] = $this->dealReturnResult($s_info, array('size' => $size));
            }
            $result[1]['list'][$type_id] = (array)$sList['list'];
        }
        
        $this->returnData($result);
    }

    // 处理返回结果
    private function dealReturnResult($data, $config = array()) {

        if (!$data) {
            return array();
        }

        if ($data['me_id']) {
            $memConfig['where']['me_id'] = $data['me_id'];
            $me_info = D('Member', 'Model')->getOne($memConfig);
            $data['me_nickname'] = $me_info['me_nickname'];
            $data['me_avatar'] = $me_info['me_avatar'];
            $data['me_created'] = $me_info['me_created'];
            $data['me_type'] = $me_info['me_type'];
            $data['c_id'] = $me_info['c_id'];
        } else {
            $data['me_nickname'] = '';
        }

        if ($data['c_id']) {
            $cConfig['fields'] = 'c_title';
            $cConfig['where']['c_id'] = $data['c_id'];
            $c_title = D('Class', 'Model')->getOne($cConfig);
            $data['c_title'] = $c_title;
        } else {
            $data['c_title'] = '';
        }

        // 特殊处理
        $data['se_logo'] = D('StudentExcellent')->getLogo($data, array('size' => $config['size']));
        
        return $data;
    }
}
?>
