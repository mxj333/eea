<?php
namespace Manage\Controller;
class AppLevelController extends ManageController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'al_id', 'percent' => '10', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'al_title', 'percent' => '50', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'al_status', 'percent' => '25', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '10', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '4', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '4', 'title' => L('DELETE'));

        // 检索器
        $search[] = array('title' => L('NAME'), 'name' => 'al_title');

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        // 显示字段
        $addList[] = array('name' => 'al_title', 'title' => L('TITLE'), 'class' => 'w460');
        $addList[] = array('name' => 'al_logo', 'title' => L('LOGO'), 'type' => 'file', 'class' => 'w460');
        $addList[] = array('name' => 'al_status', 'title' => L('STATUS'), 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));
        
        // 图片处理
        if ($result) {
            $file = D('AppLevel')->getLogo($result);
            if ($file) {
                $result['file']['al_logo'] = $file;
                $result['ext']['al_logo'] = C('DEFAULT_IMAGE_EXT');
            }
        }

        return generationAddTpl($addList, 'al_id', $title, $result);
    }

    public function insert() {

        // 是否有 LOGO 上传
        if ($_FILES['al_logo']['size'] > 0) {
            
            $al_logo = D('AppLevel')->uploadLogo($_FILES['al_logo']);
            if ($al_logo === false) {
                $this->error(D('AppLevel')->getError());
            }
            $_POST['al_logo'] = $al_logo['savename'];
        }

        $result = D('AppLevel')->insert($_POST);
        if ($result === false) {
            $this->error(D('AppLevel')->getError());
        }
        $this->show($result);
    }

    public function delete() {
        
        // 接收参数
        $id = strval(I('request.id'));
        
        $result = D('AppLevel')->delete($id);

        $this->show($result, L('DELETE'));
    }
}