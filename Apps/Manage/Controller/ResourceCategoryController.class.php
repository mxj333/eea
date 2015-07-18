<?php
namespace Manage\Controller;
class ResourceCategoryController extends ManageController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'rc_id', 'percent' => '10', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'rc_title', 'percent' => '50', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'rc_sort', 'percent' => '10', 'title' => L('SORT'));
        $tplList[] = array('id' => 'rc_status', 'percent' => '10', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '10', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '10', 'title' => L('EDIT'));

        // 检索器
        $search[] = array('title' => L('TITLE'), 'name' => 'rc_title');

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        // 显示字段
        $addList[] = array('name' => 'rc_title', 'title' => L('TITLE'), 'class' => 'w460');
        $addList[] = array('name' => 'rc_sort', 'title' => L('SORT'), 'class' => 'w460');
        $addList[] = array('name' => 'rc_status', 'title' => L('STATUS'), 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));

        if (!$result) {
            $result['rc_sort'] = 255;
        }

        return generationAddTpl($addList, 'rc_id', $title, $result);
    }
}