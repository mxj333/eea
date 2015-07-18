<?php
namespace Manage\Controller;
class CategoryController extends ManageController {
    public function shows() {
        D('Category')->tree();
    }

    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'ca_id', 'percent' => '8', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'ca_title', 'percent' => '20', 'title' => L('CHINESE_NAME'), 'class' => 'showContent');
        $tplList[] = array('id' => 'ca_name', 'percent' => '20', 'title' => L('ENGLISH_NAME'), 'class' => 'showContent');
        $tplList[] = array('id' => 'm_title', 'percent' => '10', 'title' => L('MODEL'), 'class' => 'showContent', 'data' => reloadCache('model'));
        $tplList[] = array('id' => 'ca_level', 'percent' => '6', 'title' => L('HIERARCHY'));
        $tplList[] = array('id' => 'ca_status', 'percent' => '12', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '16', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '8', 'title' => L('EDIT'));
        $action[] = array('id' => 'child', 'percent' => '8', 'title' => L('SUB_SECTION'));

        // 检索器
        $search[] = array('title' => L('CHINESE_NAME'), 'name' => 'ca_title');
        $search[] = array('title' => '', 'inline' => 1, 'display' => 'none', 'name' => 'ca_pid');

        // 工具
        $tools = array('add', 'edit', 'return');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {

        $id = intval($_GET['ca_id']);
        if (isset($_GET['ca_id'])) {
            $result['ca_pid'] = $id;
            $result['ca_level'] = 0;
        }
        if ($id) {
            $categoryConfig['where']['ca_id'] = $id;
            $categoryConfig['fields'] = 'ca_level';
            $category = D('Category')->getOne($categoryConfig);
            $result['ca_level'] = intval($category['ca_level']) + 1;
        }

        $model = reloadCache('model');
        foreach ($model as $value) {
            $modelData[$value['m_id']] = $value['m_title'];
        }

        // 显示字段
        $addList[] = array('title' => L('CHINESE_NAME'), 'name' => 'ca_title', 'class' => 'w460');
        $addList[] = array('title' => L('ENGLISH_NAME'), 'name' => 'ca_name', 'class' => 'w460');
        $addList[] = array('title' => L('SORT'), 'name' => 'ca_sort', 'class' => 'w460');
        $addList[] = array('title' => L('MODEL'), 'name' => 'm_id', 'label' => 'select', 'data' => $modelData);
        $addList[] = array('title' => L('HOME_PAGE_TEMPLATE'), 'name' => 'ca_tpl_index', 'class' => 'w460');
        $addList[] = array('title' => L('DETAILS_TEMPLATE'), 'name' => 'ca_tpl_detail', 'class' => 'w460');
        $addList[] = array('title' => L('JUMP_AND_LINK'), 'name' => 'ca_url', 'class' => 'w460');
        $addList[] = array('title' => L('KEYWORD'), 'name' => 'ca_keywords', 'label' => 'textarea');
        $addList[] = array('title' => L('DESCRIPTION'), 'name' => 'ca_description', 'label' => 'textarea');
        $addList[] = array('title' => L('SHOW'), 'name' => 'ca_is_show', 'label' => 'select', 'data' => array(1 => L('YES'), 9 => L('NO')));
        $addList[] = array('title' => L('STATUS'), 'name' => 'ca_status', 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));
        $addList[] = array('title' => '', 'name' => 'ca_pid', 'type' => 'hidden');
        $addList[] = array('title' => '', 'name' => 'ca_level', 'type' => 'hidden');
        return generationAddTpl($addList, 'ca_id', $title, $result);
    }

    public function delete() {
        $res = D('Category')->delete(strval(I('id')));
        if ($res !== false) {
            $this->show($res, '', __CONTROLLER__ . '/index/ca_pid/' . $_POST['ca_pid']);
        } else {
            $this->error(D('Category')->getError());
        }
    }

    public function insert() {

        $res = D('Category')->insert($_POST);
        if ($res === false) {
            $this->error(D('Category')->getError());
        } else {
            $this->show($res, '', __CONTROLLER__ . '/index/ca_pid/' . $_POST['ca_pid']);
        }
    }
}