<?php
namespace Schoolback\Controller;
class SchoolManagerController extends SchoolbackController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'sm_id', 'percent' => '10', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'me_nickname', 'percent' => '35', 'title' => L('NICKNAME'), 'class' => 'showContent');
        $tplList[] = array('id' => 'sm_status', 'percent' => '25', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '10', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '5', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '5', 'title' => L('DELETE'));

        // 检索器
        $search[] = array('title' => L('NICKNAME'), 'name' => 'me_nickname');

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {

        $result['s_id'] = $_SESSION['s_id'];

        // 显示字段
        $addList[] = array('name' => 's_id', 'title' => L('SCHOOL'), 'type' => 'hidden');
        $addList[] = array('name' => 'me_id', 'type' => 'hidden');
        $addList[] = array('name' => 'me_nickname', 'title' => L('NICKNAME'));
        $addList[] = array('name' => 'sm_status', 'title' => L('STATUS'), 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));
        
        return generationAddTpl($addList, 'sm_id', $title, $result);
    }

    public function edit() {
        parent::edit('sm_id');
    }

    public function insert() {

        $_POST['auth_id'] = $_POST['me_id'];
        $operation = I('sm_id', 0, 'intval') ? 'edit' : 'add';

        $result = $this->apiReturnDeal(getApi($_POST, 'SchoolManager', $operation));

        $this->show($result);
    }

    public function delete() {
        $sm_id = I('id', 0, 'strval');

        $result = $this->apiReturnDeal(getApi(array('sm_id' => $sm_id), 'SchoolManager', 'del'));

        $this->show($result);
    }
}