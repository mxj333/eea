<?php
namespace Api\Controller;
use Think\Controller;
class SchoolManagerController extends OpenController {

    // 管理员允许输出字段
    private $allowFields = array('sm_id', 's_id', 'me_id', 'sm_status');
    private $allowDetailFields = array('sm_id', 's_id', 's_title', 'me_id', 'me_nickname', 'sm_status');
    private $checkFields = array('sm_id', 's_id', 's_title', 're_id', 're_title', 'me_id', 'is_president', 'sm_status', 'me_nickname', 'me_login_count', 'me_last_login_time');
    private $allowFieldsInsert = array('s_id', 'auth_id', 'sm_status');
    private $allowFieldsUpdate = array('sm_id', 's_id', 'auth_id', 'sm_status');

    // 管理员列表
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
        $config['is_page'] = isset($is_page) ? $is_page : true;
        
        $result = D('SchoolManager')->lists($_POST['args'], $config);
        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无数据'));
        }

        $this->returnData($result);
    }

    // 管理员信息
    public function shows() {
        extract($_POST['args']);

        // 校验
        if (!intval($sm_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 结果不处理
        $config['is_deal_result'] = $is_deal_result ? $is_deal_result : false;

        $result = D('SchoolManager')->getById(intval($sm_id), $config);

        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无'));
        }

        // 确定返回字段
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

    // 管理员检查
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

        // 检查是否为校长
        $sConfig['where']['s_id'] = $info['s_id'];
        $sConfig['where']['s_status'] = 1;
        $school = D('School', 'Model')->getOne($sConfig);
        if (!$info || !$school['s_status']) {
            $this->returnData(array('status' => 0, 'info' => '学校不存在或已关闭'));
        }

        if ($info['me_id'] == $school['me_id']) {
            $school['is_president'] = 1;
        } else {

            // 检查是否为管理员
            $manager = D('SchoolManager')->check(intval($info['me_id']), intval($info['s_id']));
            if (!$manager) {
                $this->returnData(array('status' => 0, 'info' => '不是管理员'));
            }
        }

        $manager = array_merge($school, (array)$manager);

        // 返回结果
        $result = $manager;
        $result['me_nickname'] = $info['me_nickname'];
        $result['me_login_count'] = $info['me_login_count'];
        $result['me_last_login_time'] = $info['me_last_login_time'];

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

    // 删除
    public function del() {
        extract($_POST['args']);

        // 校验
        if (!strval($sm_id)) {
            $this->returnData($this->errCode[2]);
        }

        $config['where']['sm_id'] = array('IN', $sm_id);
        $result = D('SchoolManager')->delete($config);

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

        // 默认值
        $_POST['args']['me_id'] = intval($auth_id);

        $result = D('SchoolManager')->insert($_POST['args']);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '新增成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('SchoolManager')->getError()));
        }
    }

    // 添加
    public function edit() {
        extract($_POST['args']);
        
        // 校验
        if (!intval($sm_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 确定允许操作的字段
        foreach ($_POST['args'] as $key => $val) {
            if (!in_array($key, $this->allowFieldsUpdate)) {
                unset($_POST['args'][$key]);
            }
        }

        // 默认值
        $_POST['args']['me_id'] = intval($auth_id);

        $result = D('SchoolManager')->insert($_POST['args']);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '编辑成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('SchoolManager')->getError()));
        }
    }
}
?>
