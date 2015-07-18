<?php
namespace Api\Controller;
use Think\Controller;
class ResourceRecycleController extends OpenController {

    // 恢复
    public function resume() {
        extract($_POST['args']);

        // 校验
        if (!strval($res_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 所属平台
        $belong = in_array(intval($belong), array(1, 2, 3)) ? intval($belong) : 1;

        $config['where']['res_id'] = array('IN', $res_id);
        $result = D('Resource')->resume($config, $belong);

        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '恢复成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '恢复失败'));
        }
    }

    // 删除
    public function del() {
        extract($_POST['args']);

        // 校验
        if (!strval($res_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 所属平台
        $belong = in_array(intval($belong), array(1, 2, 3)) ? intval($belong) : 1;

        // 注:删除不是真正的删除   标记删除
        $config['where']['res_id'] = array('IN', strval($res_id));
        // 需要用户id
        $data['res_deleted_id'] = intval($this->authInfo['me_id']);
        $result = D('Resource')->signDeleted($config, $data, $belong);

        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '删除成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '删除失败'));
        }
    }
}
?>
