<?php
namespace Api\Controller;
use Think\Controller;
class SchoolController extends OpenController {

    // 列表允许输出字段
    private $allowFields = array('s_id', 's_title', 's_logo', 's_type', 's_divide', 're_id', 're_title', 's_phone', 's_description', 'me_id', 's_status', 's_created');
    // 学校添加
    private $allowFieldsInsert = array('s_title', 's_type', 's_divide', 're_id', 're_title', 's_phone', 's_description', 's_status');
    private $allowFieldsUpdate = array('s_id', 's_title', 's_type', 's_divide', 're_id', 're_title', 's_phone', 's_description', 's_status');
    private $checkFields = array('s_id', 's_title', 're_id', 're_title', 'me_nickname', 'me_id', 'me_login_count');
    private $allowShowFields = array('s_id', 's_title', 's_logo');

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

        $result = D('School')->lists($_POST['args'], $config);
        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无数据'));
        }

        $this->returnData($result);
    }

    // 检查校长
    public function check() {
        extract($_POST['args']);

        // 校验
        if (!strval($account) || !strval($password)) {
            $this->returnData($this->errCode[2]);
        }

        $info = D('Member')->check($account);
        // 使用用户名、密码和状态和有效期的方式进行认证
        if (!$info) {
            $this->returnData(array('status' => 0, 'info' => '账号不存在或已冻结'));
        }

        if ($info['me_password'] != pwdHash($password)) {
            $this->returnData(array('status' => 0, 'info' => '密码错误'));
        }
        
        // 用户存在，检查是否为校长
        $result = D('School')->check($info['me_id']);
        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '不是管理员'));
        }

        // 增加用户信息
        $result['me_nickname'] = $info['me_nickname'];
        $result['me_login_count'] = $info['me_login_count'];

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

    // 总个数
    public function counts() {
        extract($_POST['args']);

        $config = array();
        $config['where']['s_is_deleted'] = 9;
        $config['where']['s_status'] = 1;

        if (strval($re_id)) {
            $config['where']['re_id'] = array('LIKE', '%' . strval($re_id) . '%');
        }

        $result = D('School')->total($config);

        $this->returnData($result);
    }

    // 用户信息
    public function shows() {
        extract($_POST['args']);

        // 校验
        if (!intval($s_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 结果不处理
        $config['is_deal_result'] = $is_deal_result ? $is_deal_result : false;

        $result = D('School')->getById(intval($s_id), $config);
        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '学校不存在'));
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

    // 删除
    public function del() {
        extract($_POST['args']);

        // 校验
        if (!strval($s_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 注:删除不是真正的删除   标记删除
        $config['where']['s_id'] = array('IN', strval($s_id));
        // 需要用户id
        $data['s_deleted_extend_id'] = intval($this->authInfo['me_id']);
        $result = D('School')->signDeleted($config, $data);

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
        if ($_FILES['s_logo']['size'] > 0) {
            
            $s_logo = D('School')->uploadLogo($_FILES['s_logo']);
            if ($s_logo === false) {
                $this->returnData(array('status' => 0, 'info' => D('School')->getError()));
            }
            $_POST['args']['s_logo'] = $s_logo['savename'];
        }

        // 默认值
        $_POST['args']['s_creator_id'] = intval($this->authInfo['me_id']);
        
        // 入库
        $result = D('School')->insert($_POST['args']);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '新增成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('School')->getError()));
        }
    }

    // 修改
    public function edit() {
        extract($_POST['args']);
        
        // 校验
        if (!strval($s_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 确定允许操作的字段
        foreach ($_POST['args'] as $key => $val) {
            if (!in_array($key, $this->allowFieldsUpdate)) {
                unset($_POST['args'][$key]);
            }
        }

        // 是否有 LOGO 上传
        if ($_FILES['s_logo']['size'] > 0) {
            
            $s_logo = D('School')->uploadLogo($_FILES['s_logo']);
            if ($s_logo === false) {
                $this->returnData(array('status' => 0, 'info' => D('School')->getError()));
            }
            $_POST['args']['s_logo'] = $s_logo['savename'];
        }
        
        // 入库
        $result = D('School')->insert($_POST['args']);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '编辑成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('School')->getError()));
        }
    }

    // 定义校长
    public function user() {
        extract($_POST['args']);
        
        // 校验
        if (!intval($president_id) || !intval($s_id)) {
            $this->returnData($this->errCode[2]);
        }
        
        $result = D('School')->setPresident(intval($president_id), intval($s_id));
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '操作成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '操作失败'));
        }
    }

    // 学校展示图上传
    public function publish() {
        extract($_POST['args']);

        // 校验
        if (!intval($s_id)) {
            $this->returnData($this->errCode[2]);
        }

        $insertData = array();

        $insertData['sf_creator_table'] = 'Member';
        $insertData['sf_creator_id'] = intval($this->authInfo['me_id']);

        $insertData['s_id'] = $s_id;
        $insertData['id'] = $_POST['args']['id'];
        $insertData['remark'] = $_POST['args']['remark'];
        $insertData['sort'] = $_POST['args']['sort'];
        $insertData['title'] = $_POST['args']['title'];

        // 图上传
        $res_pic = D('School')->uploadPic($_FILES['pic'], $insertData);
        if ($res_pic === false) {
            $this->returnData(array('status' => 0, 'info' => D('School')->getError()));
        }

        $this->returnData(array('status' => 1, 'info' => '操作成功'));
    }

    // 删除展示图
    public function forbid() {
        extract($_POST['args']);

        // 校验
        if (!intval($sf_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 图
        $result = D('School')->delFile(intval($sf_id));

        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '操作成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '操作失败'));
        }
    }

    // 学校展示图
    public function showPic() {
        extract($_POST['args']);

        // 校验
        if (!intval($s_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 图
        $result = D('School')->showPic(intval($s_id));

        $this->returnData($result);
    }

    /* 前台展示列表
     * belong 所属  1 平台
     * type 类型  1 学校展示
     * type_num  类型个数  默认 1 个
     * every_num 每种类型下学校个数  默认 9 个
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
                    $result[$mem_type]['type'][1] = '学校展示';
                    break;
            }
        }

        if (!$every_num) {
            $every_num = array(9);
        } elseif (!is_array($every_num)) {
            $every_num = explode(',', $every_num);
        }
        
        // 学校
        $sData['s_status'] = 1;
        $sData['re_id'] = strval($re_id);
        $sData['s_type'] = intval($s_type);
        $sData['s_divide'] = intval($s_divide);
        $sData['order_by_resource_num'] = $order_by_resource_num;
        $sConfig['type'] = intval($belong);
        $sConfig['every_page_num'] = intval($every_num[1]) ? intval($every_num[1]) : intval($every_num[0]);
        $sConfig['is_deal_result'] = false;
        $sConfig['is_api'] = true;
        if (!$order_by_resource_num) {
            $sConfig['order'] = strval($order) ? strval($order) : 's_id DESC';
        } else {
            // 特殊处理需要传获取的字段
            foreach ($this->allowFields as $field) {
                $sConfig['fields'][] = 's.' . $field;
            }
        }
        $sList = D('School')->lists($sData, $sConfig);
        foreach ($sList['list'] as $s_key => $s_info) {
            $sList['list'][$s_key] = $this->dealReturnResult($s_info);
        }
        $result[1]['list'][1] = (array)$sList['list'];

        $this->returnData($result);
    }

    // 处理返回结果
    private function dealReturnResult($data) {

        if (!$data) {
            return array();
        }

        // 特殊处理
        $data['s_logo'] = D('School')->getLogo($data);

        // 过滤处理
        /*foreach ($data as $key => $val) {
            if (!in_array($key, $this->allowShowFields)) {
                unset($data[$key]);
            }
        }*/
        
        return $data;
    }
}
?>
