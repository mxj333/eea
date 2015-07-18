<?php
namespace Manage\Controller;
class TagController extends ManageController {
    public function index() {
        $tag_type = explode(',', C('SYSTEM_TAG_TYPE'));
        unset($tag_type[0]);

        // 显示字段
        $tplList['id'] = array('id' => 't_id', 'percent' => '10', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 't_title', 'percent' => '30', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 't_type', 'percent' => '10', 'title' => L('TYPE'));
        $tplList[] = array('id' => 't_sort', 'percent' => '10', 'title' => L('SORT'));
        $tplList[] = array('id' => 't_status', 'percent' => '10', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '20', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '20', 'title' => L('EDIT'));

        // 检索器
        $search[] = array('title' => L('TITLE'), 'name' => 't_title');
        $search[] = array('title' => L('TYPE'), 'name' => 't_type', 'label' => 'select', 'data' => $tag_type);

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {

        $tag_type = explode(',', C('SYSTEM_TAG_TYPE'));
        unset($tag_type[0]);

        // 显示字段
        $addList[] = array('name' => 't_title', 'title' => L('TITLE'), 'class' => 'w460');
        $addList[] = array('name' => 't_type', 'title' => L('TYPE'), 'label' => 'select', 'data' => $tag_type);
        $addList[] = array('name' => 't_sort', 'title' => L('SORT'), 'class' => 'w460');
        $addList[] = array('name' => 't_status', 'title' => L('STATUS'), 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));

        return generationAddTpl($addList, 't_id', $title, $result);
    }
}