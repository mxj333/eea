<?php
namespace Manage\Controller;
class UserController extends ManageController {

    public function index() {

        $tplList['id'] = array('id' => 'u_id', 'percent' => '8', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'u_nickname', 'percent' => '20', 'title' => L('FULL_NAME'), 'class' => 'showContent');
        $tplList[] = array('id' => 'u_account', 'percent' => '20', 'title' => L('ACCOUNT'), 'class' => 'showContent');
        $tplList[] = array('id' => 'u_last_login_ip', 'percent' => '15', 'title' => L('CURRENTLY_LOGGED_IP'));
        $tplList[] = array('id' => 'u_last_login_time', 'percent' => '15', 'title' => L('CURRENTLY_LOGGED_TIME'));
        $tplList['action'] = array('id' => 'action', 'percent' => '12', 'title' => L('OPERATION'));

        $action[] = array('id' => 'edit', 'percent' => '6', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '6', 'title' => L('DELETE'));

        $search[] = array('title' => L('ACCOUNT'), 'name' => 'u_account');
        $search[] = array('title' => L('FULL_NAME'), 'name' => 'u_nickname', 'inline' => 1);

        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        // 显示字段
        $addList[] = array('title' => L('ACCOUNT'), 'name' => 'u_account', 'class' => 'w460');
        $addList[] = array('title' => L('PASSWORD'), 'name' => 'u_password', 'class' => 'w460', 'type' => 'password');
        $addList[] = array('title' => L('NAME'), 'name' => 'u_nickname', 'class' => 'w460');
        $addList[] = array('title' => L('STATUS'), 'name' => 'u_status', 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));

        return generationAddTpl($addList, 'u_id', $title, $result);
    }
}