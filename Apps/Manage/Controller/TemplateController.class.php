<?php
namespace Manage\Controller;
class TemplateController extends ManageController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'te_id', 'percent' => '10', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'te_title', 'percent' => '25', 'title' => L('CHINESE_NAME'), 'class' => 'showContent');
        $tplList[] = array('id' => 'te_name', 'percent' => '20', 'title' => L('ENGLISH_NAME'));
        $tplList[] = array('id' => 'tt_id', 'percent' => '10', 'title' => L('TYPE'));
        $tplList[] = array('id' => 'te_status', 'percent' => '10', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '20', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '10', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '10', 'title' => L('DELETE'));

        // 检索器
        $search[] = array('title' => L('CHINESE_NAME'), 'name' => 'te_title');

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {

        $type = loadCache('templateType');
        // 显示字段
        $addList[] = array('name' => 'tt_id', 'title' => L('TYPE'), 'label' => 'select', 'data' => $type);
        $addList[] = array('name' => 'te_title', 'title' => L('CHINESE_NAME'), 'class' => 'w460');
        $addList[] = array('name' => 'te_name', 'title' => L('ENGLISH_NAME'), 'class' => 'w460');
        $addList[] = array('name' => 'te_status', 'title' => L('STATUS'), 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));

        return generationAddTpl($addList, 'te_id', $title, $result);
    }

    public function insert() {

        $result = D('Template')->insert($_POST);
        if ($result === false) {
            $this->error(D('Template')->getError());
        } else {
            $this->show($result);
        }
    }
}