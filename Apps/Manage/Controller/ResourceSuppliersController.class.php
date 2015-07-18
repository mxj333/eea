<?php
namespace Manage\Controller;
class ResourceSuppliersController extends ManageController {

    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'rsu_id', 'percent' => '4', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'rsu_title', 'percent' => '15', 'title' => L('TITLE'));
        $tplList[] = array('id' => 'rsu_account', 'percent' => '15', 'title' => L('ACCOUNT'));
        $tplList[] = array('id' => 'rsu_contacts', 'percent' => '15', 'title' => L('CONTACTS'));
        $tplList[] = array('id' => 'rsu_mobile', 'percent' => '10', 'title' => L('MOBILE'));
        $tplList[] = array('id' => 'rsu_valid', 'percent' => '15', 'title' => L('VALID'));
        $tplList[] = array('id' => 'rsu_status', 'percent' => '10', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '8', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '4', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '4', 'title' => L('DELETE'));

        // 检索器
        $search[] = array('title' => L('TITLE'), 'name' => 'rsu_title');
        $search[] = array('title' => L('CONTACTS'), 'name' => 'rsu_contacts', 'inline' => true);

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        // 显示字段
        $addList[] = array('name' => 'rsu_title', 'title' => L('TITLE'), 'class' => 'w460');
        $addList[] = array('name' => 'rsu_account', 'title' => L('ACCOUNT'), 'class' => 'w460');
        $addList[] = array('name' => 'rsu_password', 'title' => L('PASSWORD'), 'type' => 'password', 'class' => 'w460');
        $addList[] = array('name' => 'rsu_contacts', 'title' => L('CONTACTS'), 'class' => 'w460');
        $addList[] = array('name' => 'rsu_mobile', 'title' => L('MOBILE'), 'class' => 'w460');
        $addList[] = array('name' => 'rsu_address', 'title' => L('ADDRESS'), 'class' => 'w460');
        $addList[] = array('name' => 'rsu_valid', 'title' => L('VALID'), 'class' => 'w460', 'event' => 'onClick', 'eventValue' => "WdatePicker({dateFmt:'yyyy-MM-dd',minDate:'#{%y}-{%M}-#{%d+1}'});");
        $addList[] = array('name' => 'rsu_status', 'title' => L('STATUS'), 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));

        return generationAddTpl($addList, 'rsu_id', $title, $result);
    }

    public function insert() {
        $_POST['u_id'] = intval($_SESSION[C('USER_AUTH_KEY')]);
        parent::insert();
    }
}