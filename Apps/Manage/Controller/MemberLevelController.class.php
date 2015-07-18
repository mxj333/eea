<?php
namespace Manage\Controller;
class MemberLevelController extends ManageController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'ml_id', 'percent' => '10', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'ml_title', 'percent' => '50', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'ml_status', 'percent' => '25', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '10', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '5', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '5', 'title' => L('DELETE'));

        // 检索器
        $search[] = array('title' => L('TITLE'), 'name' => 'ml_title');

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        // 显示字段
        $addList[] = array('name' => 'ml_title', 'title' => L('TITLE'), 'class' => 'w460');
        $addList[] = array('name' => 'ml_logo', 'title' => L('LOGO'), 'type' => 'file', 'class' => 'w460');
        $addList[] = array('name' => 'ml_status', 'title' => L('STATUS'), 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));
        
        // 图片处理
        if ($result) {
            $file = D('MemberLevel')->getLogo($result);
            if ($file) {
                $result['file']['ml_logo'] = $file;
                $result['ext']['ml_logo'] = C('DEFAULT_IMAGE_EXT');
            }
        }

        return generationAddTpl($addList, 'ml_id', $title, $result);
    }

    public function insert() {

        // 是否有 LOGO 上传
        if ($_FILES['ml_logo']['size'] > 0) {
            
            $ml_logo = D('MemberLevel')->uploadLogo($_FILES['ml_logo']);
            if ($ml_logo === false) {
                $this->error(D('MemberLevel')->getError());
            }
            $_POST['ml_logo'] = $ml_logo['savename'];
        }

        $result = D('MemberLevel')->insert($_POST);
        if ($result === false) {
            $this->error(D('MemberLevel')->getError());
        }
        $this->show($result);
    }

    public function delete() {
        
        // 接收参数
        $id = strval(I('request.id'));
        
        $result = D('MemberLevel')->delete($id);

        $this->show($result, L('DELETE'));
    }
}