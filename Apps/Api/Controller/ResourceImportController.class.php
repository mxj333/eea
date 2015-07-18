<?php
namespace Api\Controller;
use Think\Controller;
class ResourceImportController extends OpenController {

    // 资源导入 资源文件处理
    public function add() {
        extract($_POST['args']);

        // 校验
        if (!$res_path) {
            $this->returnData($this->errCode[2]);
        }

        $res_info = D('ResourceImport')->dealResource($res_path);
        if (is_array($res_info)) {
            // 资源入库资源表
            $rf_id = D('ResourceFile')->insertTable($res_info);
            if (!$rf_id) {
                // 资源文件添加失败 跳过
                $this->returnData(array('status' => 0, 'info' => '文件上传失败'));
            }
        } else {
            // 资源id
            $rf_id = $res_info;
        }

        $this->returnData(array('status' => 1, 'res_value' => $rf_id, 'info' => '文件上传成功'));
    }
}
?>
