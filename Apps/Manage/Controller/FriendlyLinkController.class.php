<?php
namespace Manage\Controller;
class FriendlyLinkController extends ManageController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'fl_id', 'percent' => '10', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'fl_title', 'percent' => '30', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'fl_url', 'percent' => '30', 'title' => L('URL'), 'class' => 'showContent');
        $tplList[] = array('id' => 'belongs', 'percent' => '10', 'title' => L('BELONGS'));
        $tplList[] = array('id' => 'fl_sort', 'percent' => '5', 'title' => L('SORT'));
        $tplList['action'] = array('id' => 'action', 'percent' => '8', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '4', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '4', 'title' => L('DELETE'));

        // 检索器
        $search[] = array('title' => L('TITLE'), 'name' => 'fl_title');

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        // 显示字段
        $addList[] = array('name' => 'fl_title', 'title' => L('TITLE'), 'class' => 'w460', 'require' => true);
        $addList[] = array('name' => 'fl_url', 'title' => L('URL'), 'class' => 'w460', 'require' => true);
        $addList[] = array('name' => 'fl_logo', 'title' => L('LOGO'), 'type' => 'file', 'class' => 'w460 head', 'labelClass' => 'logo');
        $addList[] = array('name' => 'fl_sort', 'title' => L('SORT'), 'class' => 'w460');
        $addList[] = array('name' => 'fl_status', 'title' => L('STATUS'), 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));

        if (!$result) {
            $result['fl_sort'] = 255;
        }

        // 图片处理
        $file = D('FriendlyLink')->getLogo($result);
        if ($file) {
            $result['file']['fl_logo'] = $file;
            $result['ext']['fl_logo'] = C('DEFAULT_IMAGE_EXT');
        }
        return generationAddTpl($addList, 'fl_id', $title, $result);
    }

    public function insert() {

        $saveData = array();

        // 是否有 LOGO 上传
        if ($_FILES['fl_logo']['size'] > 0) {
            
            $fl_logo = D('FriendlyLink')->uploadLogo($_FILES['fl_logo']);
            if ($fl_logo === false) {
                $this->error(D('FriendlyLink')->getError());
            }
            $saveData['fl_logo'] = $fl_logo['savename'];
        }
        
        $saveData['fl_title'] = $_POST['fl_title'];
        $saveData['fl_url'] = $_POST['fl_url'];
        $saveData['fl_sort'] = intval($_POST['fl_sort']);
        $saveData['fl_status'] = $_POST['fl_status'];
        $saveData['fl_id'] = strval($_POST['fl_id']);

        // 入库
        $result = D('FriendlyLink')->insert($saveData);
        if ($result === false) {
            $this->error(D('FriendlyLink')->getError());
        } else {
            $this->show($result);
        }
    }

    public function delete() {
        $id = I('id', 0, 'strval');

        $result = D('FriendlyLink')->delete($id);

        $this->show($result);
    }
}