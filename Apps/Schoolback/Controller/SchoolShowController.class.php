<?php
namespace Schoolback\Controller;
use Think\Controller;
class SchoolShowController extends SchoolbackController {
    
    public function insert() {
        // 图片
        if ($_FILES['pic']) {
            foreach ($_FILES['pic']['name'] as $pic_key => $pic_val) {
                if ($_FILES['pic']['size'][$pic_key] > 0) {
                    $fields['pic['.$pic_key.']'] = '@'.realpath($_FILES['pic']['tmp_name'][$pic_key]).";type=".$_FILES['pic']['type'][$pic_key].";filename=".$pic_val;
                }
            }

        }

        $_POST['s_id'] = session('s_id');
    
        $result = $this->apiReturnDeal(getApi($_POST, 'School', 'publish', 'json', $fields));
        if ($result['status']) {
            $this->redirect(__CONTROLLER__ . '/index');
        } else {
            $this->error($result['info']);
        }
    }
    
    // 编辑操作
    public function index() {

        if (!$_POST) {
            // 验证
            $res = $this->apiReturnDeal(getApi(array('s_id' => session('s_id')), 'School', 'showPic'));
            
            // 赋值
            $this->assign('everyModelJs', intval(file_exists('.' . MPUBLIC_NAME . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Add.js')));
            
            $this->assign('pic', $res);
            $this->assign('s_id', session('s_id'));
            
            $this->display('pic');
        } else {
            $this->insert();
        }
    }

    // 删除文件
    public function delFile($id) {
        
        $res = $this->apiReturnDeal(getApi(array('sf_id' => intval($id)), 'School', 'forbid'));
        echo json_encode($res);
    }
}