<?php
namespace Manage\Controller;
class PaymentsController extends ManageController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'pa_id', 'percent' => '10', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'pa_title', 'percent' => '30', 'title' => L('TITLE'));
        $tplList[] = array('id' => 'pa_status', 'percent' => '30', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '20', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '10', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '10', 'title' => L('delete'));

        // 检索器
        $search[] = array('title' => L('TITLE'), 'name' => 'pa_title');

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        // 显示字段
        $addList[] = array('name' => 'pa_title', 'title' => L('TITLE'), 'class' => 'w460');
        $addList[] = array('name' => 'pa_type', 'title' => L('TYPE'), 'class' => 'w460');
        $addList[] = array('name' => 'pa_partner', 'title' => L('PARTNER'), 'class' => 'w460');
        $addList[] = array('name' => 'pa_key', 'title' => L('KEY'), 'class' => 'w460');
        $addList[] = array('name' => 'pa_email', 'title' => L('EMAIL'), 'class' => 'w460');
        $addList[] = array('name' => 'pa_status', 'title' => L('STATUS'), 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));
        
        return generationAddTpl($addList, 'pa_id', $title, $result);
    }
}