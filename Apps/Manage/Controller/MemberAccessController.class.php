<?php
namespace Manage\Controller;
class MemberAccessController extends ManageController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'ma_id', 'percent' => '10', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'ma_type', 'percent' => '10', 'title' => L('TYPE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'ma_title', 'percent' => '20', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'ma_appid', 'percent' => '15', 'title' => L('APPID'), 'class' => 'showContent');
        $tplList[] = array('id' => 'ma_appkey', 'percent' => '20', 'title' => L('APPKEY'), 'class' => 'showContent');
        $tplList[] = array('id' => 'ma_status', 'percent' => '10', 'title' => L('STATUS'), 'class' => 'showContent');
        $tplList['action'] = array('id' => 'action', 'percent' => '10', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '10', 'title' => L('EDIT'));

        // 检索器
        $search[] = array('title' => L('TITLE'), 'name' => 'ma_title');

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        // 显示字段
        $addList[] = array('name' => 'ma_type', 'title' => L('TYPE'), 'class' => 'w460');
        $addList[] = array('name' => 'ma_title', 'title' => L('TITLE'), 'class' => 'w460');
        $addList[] = array('name' => 'ma_appid', 'title' => L('APPID'), 'class' => 'w460');
        $addList[] = array('name' => 'ma_appkey', 'title' => L('APPKEY'), 'class' => 'w460');
        $addList[] = array('name' => 'ma_status', 'title' => L('STATUS'), 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));
        $addList[] = array('name' => 'ma_description', 'title' => L('DESCRIPTION'), 'label' => 'textarea', 'class' => 'h80');
        
        return generationAddTpl($addList, 'ma_id', $title, $result);
    }
}