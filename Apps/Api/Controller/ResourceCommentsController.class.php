<?php
namespace Api\Controller;
use Think\Controller;
class ResourceCommentsController extends OpenController {

    // 审核允许输出字段
    private $allowFields = array('rco_id', 'res_id', 'rco_content', 'me_id', 'rco_pid', 'rco_status', 'rco_created');
    private $allowFieldsDetail = array('rco_id', 'res_id', 'res_title', 'rco_content', 'me_id', 'me_nickname', 'rco_pid', 'rco_status', 'rco_created');
    private $allowFieldsInsert = array('res_id', 'rco_content', 'rco_pid');

    // 审核列表
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

        // 连表查询 加前缀
        foreach ($config['fields'] as $key => $val) {
            $config['fields'][$key] = 'rc.' . $val;
        }

        // 页码
        if ($p) {
            $config['p'] = intval($p) ? intval($p) : 1;
        }

        // logic 列表类 结果不处理
        $config['is_deal_result'] = $is_deal_result ? $is_deal_result : false;
        // api 请求
        $config['is_api'] = true;

        $result = D('ResourceComments')->lists($_POST['args'], $config);

        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无数据'));
        }

        $this->returnData($result);
    }

    public function shows() {
        extract($_POST['args']);

        // 校验
        if (!intval($rco_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 结果不处理
        $config['is_deal_result'] = $is_deal_result ? $is_deal_result : false;

        $result = D('ResourceComments')->getById(intval($rco_id), $config);
        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无'));
        }

        // 过滤返回结果
        foreach ($result as $key => $val) {
            if (!in_array($key, $this->allowFieldsDetail)) {
                unset($result[$key]);
            }
        }

        $this->returnData($result);
    }

    public function add() {
        extract($_POST['args']);

        // 确定允许操作的字段
        foreach ($_POST['args'] as $key => $val) {
            if (!in_array($key, $this->allowFieldsInsert)) {
                unset($_POST['args'][$key]);
            }
        }

        // 默认值
        $_POST['args']['me_id'] = intval($this->authInfo['me_id']);
        $_POST['args']['rco_created'] = time();

        $result = D('ResourceComments')->insert($_POST['args']);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'res_value' => $result, 'info' => '新增成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '新增失败'));
        }
    }

    public function del() {
        extract($_POST['args']);

        // 校验
        if (!strval($rco_id)) {
            $this->returnData($this->errCode[2]);
        }

        $config['where']['rco_id'] = array('IN', strval($rco_id));
        $result = D('ResourceComments')->delete($config);

        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '删除成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '删除失败'));
        }
    }

    public function review() {
        extract($_POST['args']);

        // 校验
        if (!strval($rco_id)) {
            $this->returnData($this->errCode[2]);
        }

        $config['where']['rco_id'] = array('IN', strval($rco_id));
        $data['rco_status'] = intval($rco_status);
        $result = D('ResourceComments')->update($data, $config);

        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '操作成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '操作失败'));
        }
    }
}
?>
