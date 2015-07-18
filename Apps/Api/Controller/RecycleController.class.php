<?php
namespace Api\Controller;
use Think\Controller;
class RecycleController extends OpenController {

    // 恢复
    public function resume() {
        extract($_POST['args']);

        // 校验
        if (!strval($art_id)) {
            $this->returnData($this->errCode[2]);
        }

        $result = D('Article')->statusUpdate(strval($art_id), C('ARTICLE_IS_DEFAULT_PUBLISHED'));

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
        if (!strval($art_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 注:删除不是真正的删除   标记删除
        $result = D('Article')->deleteSign(strval($art_id));

        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '删除成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '删除失败'));
        }
    }
}
?>
