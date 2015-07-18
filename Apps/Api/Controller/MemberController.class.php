<?php
namespace Api\Controller;
use Think\Controller;
class MemberController extends OpenController {

    // 用户列表允许输出字段
    private $allowFields = array('me_id', 'me_account', 'me_avatar', 'me_nickname', 'me_mobile', 'me_phone', 'me_email', 'me_last_login_ip', 'me_last_login_time', 'me_status', 'me_validity', 'me_type', 'me_note', 're_id', 're_title', 's_id', 'c_id');
    // 用户详情允许输出字段
    private $allowDetailFields = array('me_id', 'me_account', 'me_avatar', 'me_nickname', 'me_mobile', 'me_phone', 'me_email', 'me_last_login_ip', 'me_last_login_time', 'me_status', 'me_validity', 'md_sex', 'md_birthday', 'md_description', 'md_chinese_name', 'md_english_name', 'md_native_place', 'md_card_type', 'md_card_num', 'md_political_type', 'md_blood_type', 'me_type', 'me_note', 'me_created', 're_id', 're_title', 's_id', 's_title', 'c_id', 'c_title');
    // 检查区域管理员输出字段
    private $checkFields = array('me_id', 'me_account', 'me_password', 'me_avatar', 'me_nickname', 'me_mobile', 'me_phone', 'me_email', 'me_last_login_ip', 'me_last_login_time', 'me_login_count', 'me_status', 'me_validity', 'me_type', 'me_note', 're_id', 're_title', 's_id', 'c_id');
    // 用户表允许添加字段
    private $allowFieldsInsert = array('me_password', 'me_nickname', 'me_validity', 'me_mobile', 'me_phone', 'me_email', 'me_status', 'md_sex', 'md_birthday', 'md_description', 'md_chinese_name', 'md_english_name', 'md_native_place', 'md_card_type', 'md_card_num', 'md_political_type', 'md_blood_type', 'me_type', 'me_note', 'md_register_ip', 're_id', 're_title', 's_id', 'c_id');
    // 用户表允许修改字段
    private $allowFieldsUpdate = array('me_id', 'me_password', 'me_nickname', 'me_validity', 'me_mobile', 'me_phone', 'me_email', 'me_status', 'md_sex', 'md_birthday', 'md_description', 'md_chinese_name', 'md_english_name', 'md_native_place', 'md_card_type', 'md_card_num', 'md_political_type', 'md_blood_type', 'me_type', 'me_note', 're_id', 're_title', 's_id', 'c_id');
    private $allowShowFields = array('me_id', 'me_nickname', 'me_mobile', 'me_type', 'me_note', 're_id', 're_title', 's_id', 'c_id', 'me_avatar', 'tex_id', 'tex_logo', 'tex_description', 'tex_type', 'tex_created', 'se_id', 'se_logo', 'se_description', 'se_type', 'se_created');

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
        // 是否分页
        $config['is_page'] = true;

        $result = D('Member')->lists($_POST['args'], $config);
        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无数据'));
        }

        $this->returnData($result);
    }

    // 总个数
    public function counts() {
        extract($_POST['args']);

        $config = array();
        $config['where']['me_is_deleted'] = 9;
        $config['where']['me_status'] = 1;
        $config['where']['me_validity'] = array('EGT', time());

        if (strval($re_id)) {
            $config['where']['re_id'] = array('LIKE', '%' . strval($re_id) . '%');
        }

        if (intval($s_id)) {
            $config['where']['s_id'] = intval($s_id);
        }

        if (intval($c_id)) {
            $config['where']['c_id'] = intval($c_id);
        }

        if (intval($me_type)) {
            $config['where']['me_type'] = intval($me_type);
        }

        $result = D('Member')->total($config);

        $this->returnData($result);
    }

    // 用户信息
    public function shows() {
        extract($_POST['args']);

        // 校验
        if (!intval($auth_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 结果不处理
        $config['is_deal_result'] = $is_deal_result ? $is_deal_result : false;

        $result = D('Member')->getById(intval($auth_id), $config);
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

    // 账号检查
    public function check() {
        extract($_POST['args']);

        // 校验
        if (!strval($account)) {
            $this->returnData($this->errCode[2]);
        }

        $result = D('Member')->check($account);
        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '账号不存在或已冻结'));
        }

        // 返回字段
        if ($fields) {
            // 有定义返回字段
            $returnFields = explode(',', $fields);
            $config['fields'] = array_intersect($this->checkFields, $returnFields);
        }
        if (!$config['fields']){
            $config['fields'] = $this->checkFields;
        }

        // 字段过滤
        foreach ($result as $key => $val) {
            if (!in_array($key, $config['fields'])) {
                unset($result[$key]);
            }
        }

        $this->returnData($result);
    }

    // 更新登录状态
    public function saveLoginStatus() {
        extract($_POST['args']);

        // 校验
        if (!intval($auth_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 用户登录次数
        $meConfig['fields'] = 'me_login_count';
        $meConfig['where']['me_id'] = intval($auth_id);
        $count = D('Member')->getOne($meConfig);

        // 更新
        $config['member']['me_last_login_ip'] = intval($login_ip);
        $config['log']['mll_ip'] = intval($login_ip);
        $result = D('Member')->saveLoginStatus(intval($auth_id), intval($count), $config);

        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '更新成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '更新失败'));
        }
    }

    // 删除
    public function del() {
        extract($_POST['args']);

        // 校验
        if (!strval($auth_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 注:删除不是真正的删除   标记删除
        $config['where']['me_id'] = array('IN', strval($auth_id));
        // 需要用户id
        $data['me_deleted_extend_id'] = intval($this->authInfo['me_id']);
        $result = D('Member')->signDeleted($config, $data);

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
        if ($_FILES['me_avatar']['size'] > 0) {
            
            $a_logo = D('Member')->uploadLogo($_FILES['me_avatar']);
            if ($a_logo === false) {
                $this->returnData(array('status' => 0, 'info' => D('Member')->getError()));
            }
            $_POST['args']['me_avatar'] = $a_logo['savename'];
        }

        // 默认值
        $_POST['args']['me_creator_id'] = intval($this->authInfo['me_id']);
        $_POST['args']['md_register_ip'] = rewrite_ip2long($_POST['args']['md_register_ip']);

        $result = D('Member')->insert($_POST['args']);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '新增成功', 'res_value' => $result));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('Member')->getError()));
        }
    }

    // 修改
    public function edit() {
        extract($_POST['args']);
        
        // 校验
        if (!intval($auth_id)) {
            $this->returnData($this->errCode[2]);
        }
        
        // 确定允许操作的字段
        foreach ($_POST['args'] as $key => $val) {
            if (!in_array($key, $this->allowFieldsUpdate)) {
                unset($_POST['args'][$key]);
            }
        }

        // 是否有 LOGO 上传
        if ($_FILES['me_avatar']['size'] > 0) {
            
            $a_logo = D('Member')->uploadLogo($_FILES['me_avatar']);
            if ($s_logo === false) {
                $this->returnData(array('status' => 0, 'info' => D('Member')->getError()));
            }
            $_POST['args']['me_avatar'] = $a_logo['savename'];
        }

        $_POST['args']['me_id'] = intval($auth_id);
        $result = D('Member')->insert($_POST['args']);
        
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '编辑成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('Member')->getError()));
        }
    }

    // 获取用户档案
    public function getArchives() {
        extract($_POST['args']);
        
        // 校验
        if (!intval($mar_type) || !intval($auth_id)) {
            $this->returnData($this->errCode[2]);
        }

        $_POST['args']['me_id'] = intval($auth_id);
        $_POST['args']['mar_type'] = intval($mar_type);
        if ($order) {
            $config['order'] = $order ? $order : 'mar_id DESC';
        }

        // 结果不处理
        $config['is_deal_result'] = $is_deal_result ? $is_deal_result : false;
        // api 请求
        $config['is_api'] = true;

        // 获取数据
        $result = D('MemberArchives')->lists($_POST['args'], $config);

        $this->returnData($result);
    }

    // 新增、编辑用户档案
    public function setArchives() {
        extract($_POST['args']);
        
        // 校验
        if (!intval($mar_type) || !intval($auth_id)) {
            $this->returnData($this->errCode[2]);
        }

        $saveData['me_id'] = intval($auth_id);
        $saveData['mar_type'] = intval($mar_type);
        $saveData['mar_starttime'] = intval(strtotime($mar_starttime));
        $saveData['mar_endtime'] = intval(strtotime($mar_endtime));
        $saveData['mar_school_type'] = intval($mar_school_type);
        $saveData['mar_title'] = strval($mar_title);
        $saveData['mar_subject'] = intval($mar_subject);
        $saveData['mar_score'] = intval($mar_score);
        $saveData['mar_text'] = strval($mar_text);
        
        if (!$saveData['mar_starttime'] && !$saveData['mar_endtime'] && !$saveData['mar_title'] && !$saveData['mar_text']) {
            $this->returnData(array('status' => 0, 'info' => '数据不允许为空'));
        }

        if (intval($mar_id)) {
            $saveData['mar_id'] = intval($mar_id);
            $result = D('MemberArchives')->update($saveData);
        } else {
            $result = D('MemberArchives')->insert($saveData);
        }

        if ($result !== false) {
            $this->returnData(array('status' => 1, 'info' => '操作成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '操作失败'));
        }
    }

    // 删除用户档案
    public function delArchives() {
        extract($_POST['args']);
        
        // 校验
        if (!strval($mar_id)) {
            $this->returnData($this->errCode[2]);
        }
        
        $config['where']['mar_id'] = array('IN', strval($mar_id));
        $result = D('MemberArchives')->delete($config);

        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '操作成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '操作失败'));
        }
    }

    // 展示亲属关系
    public function getRelation() {
        extract($_POST['args']);

        // 校验
        if (!intval($auth_id) && !intval($parent_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 结果不处理
        $config['is_deal_result'] = $is_deal_result ? $is_deal_result : false;
        // api 请求
        $config['is_api'] = true;

        $result = D('MemberRelation')->lists(array('me_id' => intval($auth_id), 'parent_id' => intval($parent_id)), $config);

        $this->returnData($result);
    }

    // 添加亲属关系
    public function setRelation() {
        extract($_POST['args']);

        // 校验
        if (!intval($auth_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 过滤重复用户
        $_POST['args']['parent_id'] = array_unique($_POST['args']['parent_id']);

        $saveData = array();
        foreach ($_POST['args']['parent_id'] as $key => $parent_id) {
            if ($parent_id) {
                $saveData[] = array('me_id' => intval($auth_id), 'mr_type' => intval($_POST['args']['mr_type'][$key]), 'parent_id' => $parent_id);
            }
        }

        $result = D('MemberRelation')->insert(intval($auth_id), $saveData);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '操作成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '操作失败'));
        }
    }

    // 用户导入
    public function import() {
        extract($_POST['args']);

        set_time_limit(300);

        // 默认值
        $config['me_creator_id'] = intval($this->authInfo['me_id']);
        $config['md_register_ip'] = rewrite_ip2long($_POST['args']['md_register_ip']);

        // 返回入库数据
        $saveData = D('MemberImport')->import($_FILES, $config);
        if ($saveData === false) {
            $this->returnData(array('status' => 0, 'info' => D('MemberImport')->getError()));
        }

        // 入库
        $error_line = '';
        foreach ($saveData as $data) {
            $res = D('Member')->insert($data);
            if ($res === false) {
                $error_line .= ',' . $data['line_num'];
            }
        }

        if (!$error_line){
            $this->returnData(array('status' => 1, 'info' => '导入成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '导入失败,错误行号:' . substr($error_line, 1)));
        }
    }

    /*
     * 优秀用户
     * type 1 教师   2 学生
     */
    public function excellent() {
        extract($_POST['args']);

        $result = array();
        $type = intval($type) ? intval($type) : 1;
        $size = strval($size) ? strval($size) : '_s';
        $every_num = intval($every_num) ? intval($every_num) : 8;
        
        $comData['re_id'] = $re_id;
        $comData['s_id'] = $s_id;
        $comData['c_id'] = $c_id;

        if ($type == 2) {
            // 学生
            if ($ce_type) {
                $comData['se_type'] = $se_type;
            }
            $comData['se_status'] = 1;
            $order = strval($order) ? strval($order) : 'se_sort DESC,se_id DESC';
            $table = 'StudentExcellent';
        } else {
            // 教师
            if ($tex_type) {
                $comData['tex_type'] = $tex_type;
            }
            $comData['tex_status'] = 1;
            $order = strval($order) ? strval($order) : 'tex_sort DESC,tex_id DESC';
            $table = 'TeacherExcellent';
        }

        $config['order'] = $order;
        $config['every_page_num'] = $every_num;
        $config['is_deal_result'] = false;

        $List = D($table)->lists($comData, $config);

        $dealConfig['type'] = $type;
        $dealConfig['size'] = $size;
        foreach ($List['list'] as $mem_key => $mem_info) {
            $result[$mem_key] = $this->dealReturnResult($mem_info, $dealConfig);
        }

        $this->returnData($result);
    }

    /* 前台展示列表
     * belong 所属  1 平台
     * type 类型  1 优秀空间  3 用户列表
     * type_num  类型个数  默认 1 个
     * every_num 每种类型下用户个数  默认 12 个
     */ 
    public function getShows() {
        extract($_POST['args']);

        $result = array();

        // 哪种类型的
        $type = $type ? $type : array(1);
        if (!is_array($type)) {
            $type = explode(',', $type);
        }

        // 类型数量
        if (!$type_num) {
            $type_num = array(1);
        } elseif (!is_array($type_num)) {
            $type_num = explode(',', $type_num);
        }
        foreach ($type as $type_key => $mem_type) {
            $config = array();
            $data = array();
            switch ($mem_type) {
                case 1:
                    $result[$mem_type]['type'][1] = '优秀教师';
                    $result[$mem_type]['type'][2] = '优秀学生';
                    break;
                case 3:
                    $result[$mem_type]['type'] = '用户列表';
                    break;
            }
        }

        if (!$every_num) {
            $every_num = array(12);
        } elseif (!is_array($every_num)) {
            $every_num = explode(',', $every_num);
        }
        
        if (!$order) {
            $order = array('me_id DESC');
        } elseif (!is_array($order)) {
            $order = explode(',', $order);
        }

        // 用户
        $default['me_status'] = 1;
        $default['me_validity'] = time();
        if (strval($re_id)) {
            $default['re_id'] = strval($re_id);
        }
        if (intval($s_id)) {
            $default['s_id'] = intval($s_id);
        }
        if (intval($c_id)) {
            $default['c_id'] = intval($c_id);
        }

        // 优秀空间
        if ($result[1]) {
            $memConfig['type'] = intval($belong);
            $memConfig['every_page_num'] = intval($every_num[1]) ? intval($every_num[1]) : intval($every_num[0]);
            $memConfig['is_deal_result'] = false;
            $memConfig['order'] = $order[1] ? $order[1] : $order[0];
            $memData = array_merge($default, (array)$memData);
            // 优秀教师
            $memData['me_type'] = 1;
            $memList = D('Member')->lists($memData, $memConfig);
            foreach ($memList['list'] as $mem_key => $mem_info) {
                $memList['list'][$mem_key] = $this->dealReturnResult($mem_info);
            }
            $result[1]['list'][1] = (array)$memList['list'];
            // 优秀学生
            $memData['me_type'] = 2;
            $memList = D('Member')->lists($memData, $memConfig);
            foreach ($memList['list'] as $mem_key => $mem_info) {
                $memList['list'][$mem_key] = $this->dealReturnResult($mem_info);
            }
            $result[1]['list'][2] = (array)$memList['list'];
        }

        // 用户列表
        if ($result[3]) {
            $listConfig['type'] = intval($belong);
            $listConfig['every_page_num'] = intval($every_num[3]) ? intval($every_num[3]) : intval($every_num[0]);
            $listConfig['is_deal_result'] = false;
            $listConfig['is_page'] = isset($is_page) ? $is_page : true;
            $listConfig['p'] = intval($page) ? intval($page) : 1;
            $listConfig['order'] = $order[3] ? $order[3] : $order[0];
            $listData = array_merge($default, (array)$listData);
            $memList = D('Member')->lists($listData, $listConfig);
            foreach ($memList['list'] as $mem_key => $mem_info) {
                $memList['list'][$mem_key] = $this->dealReturnResult($mem_info);
            }
            $result[3] = $memList;
        }

        $this->returnData($result);
    }

    // 处理返回结果
    private function dealReturnResult($data, $config = array()) {

        if (!$data) {
            return array();
        }

        // 过滤处理
        foreach ($data as $key => $val) {
            if (!in_array($key, $this->allowShowFields)) {
                unset($data[$key]);
            }
        }

        $type = $config['type'] ? $config['type'] : 0;
        $size = $config['size'] ? $config['size'] : '_s';

        if ($type && intval($data['me_id'])) {
            $member = D('Member')->getById($data['me_id']);
            $data['me_nickname'] = $member['me_nickname'];
            $data['me_avatar'] = $member['me_avatar'];
            $data['me_created'] = $member['me_created'];
            $data['me_type'] = $member['me_type'];
            $data['s_title'] = $member['s_title'];
            $data['subject'] = $member['subject'];
            $data['subject_title'] = $member['subject_title'];
            $data['c_id'] = $member['c_id'];
            $data['c_title'] = $member['c_title'];
            $data['c_grade'] = $member['c_grade'];
        }

        // 特殊处理
        if ($type == 1) {
            $data['me_avatar'] = D('TeacherExcellent')->getLogo($data, array('size' => $size));
        } elseif ($type == 2) {
            $data['me_avatar'] = D('StudentExcellent')->getLogo($data, array('size' => $size));
        } else {
            $data['me_avatar'] = D('Member')->getAvatar($data, array('size' => $size));
        }

        return $data;
    }

    // 统计
    public function statistics() {
        extract($_POST['args']);

        $result = D('MemberStatistics')->lists($_POST['args']);

        $this->returnData($result);
    }
}
?>
