<?php
namespace Api\Controller;
use Think\Controller;
class AreaManagerController extends OpenController {

    // 区域管理员允许输出字段
    private $allowFields = array('am_id', 're_id', 're_title', 'aty_id', 'me_id', 'am_is_main', 'am_status');
    private $allowDetailFields = array('am_id', 're_id', 're_title', 'aty_id', 'ac_title', 'me_id', 'me_nickname', 'am_is_main', 'am_status');
    private $checkFields = array('am_id', 're_id', 're_title', 'aty_id', 'me_id', 'am_is_main', 'am_status', 'me_nickname', 'me_login_count', 'me_last_login_time');
    private $allowFieldsInsert = array('re_id', 're_title', 'aty_id', 'auth_id', 'am_status');
    private $allowFieldsUpdate = array('am_id', 're_id', 're_title', 'aty_id', 'auth_id', 'am_status');

    // 区域管理员列表
    public function lists() {
        extract($_POST['args']);

        // 取列表种类
        switch (intval($_POST['args']['type'])) {
            case 2:
                // 搜索区域管理员
                // 返回字段(需要连表)
                $config['fields'] = 'am.me_id,me.me_nickname,me.me_mobile';
                $config['is_deal_result'] = false;
                break;
            default :
                // 区域管理员列表
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
        }

        // api 请求
        $config['is_api'] = true;
        
        $_POST['args']['am_is_main'] = 0;

        $result = D('AreaManager')->lists($_POST['args'], $config);
        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无数据'));
        }

        $this->returnData($result);
    }

    // 区域管理员信息
    public function shows() {
        extract($_POST['args']);

        // 校验
        if (!intval($am_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 结果不处理
        $config['is_deal_result'] = $is_deal_result ? $is_deal_result : false;

        $result = D('AreaManager')->getById(intval($am_id), $config);

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

    // 区域管理员检查
    public function check() {
        extract($_POST['args']);

        // 校验
        if (!strval($account) || !strval($password) || !intval($aty_id)) {
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

        // 用户存在检查是否为区域管理员
        $area = D('AreaManager')->check(intval($info['me_id']), intval($aty_id));
        if (!$area) {
            $this->returnData(array('status' => 0, 'info' => '不是管理员'));
        }

        // 返回结果
        $result = $area;
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
        if (!strval($am_id)) {
            $this->returnData($this->errCode[2]);
        }

        $config['where']['am_id'] = array('IN', $am_id);
        $result = D('AreaManager')->delete($config);

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
        $_POST['args']['am_is_main'] = 0;

        $result = D('AreaManager')->insert($_POST['args']);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '新增成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('AreaManager')->getError()));
        }
    }

    // 添加
    public function edit() {
        extract($_POST['args']);
        
        // 校验
        if (!intval($am_id)) {
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
        $_POST['args']['am_is_main'] = 0;

        $result = D('AreaManager')->insert($_POST['args']);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '编辑成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('AreaManager')->getError()));
        }
    }
}
?>
