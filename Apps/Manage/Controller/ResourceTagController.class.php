<?php
namespace Manage\Controller;
use Think\Controller;
class ResourceTagController extends ManageController {
    public function tree() {
        D('ResourceTag')->tree();
    }

    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'rta_id', 'percent' => '10', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'rta_title', 'percent' => '40', 'title' => L('TITLE'), 'class' => 'showContent');
        //$tplList[] = array('id' => 'rta_level', 'percent' => '15', 'title' => L('HIERARCHY'));
        $tplList[] = array('id' => 'rta_status', 'percent' => '25', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '20', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '6', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '6', 'title' => L('DELETE'));
        $action[] = array('id' => 'child', 'percent' => '6', 'title' => L('SUB_SECTION'));

        // 检索器
        $search[] = array('title' => L('TITLE'), 'name' => 'rta_title');
        $search[] = array('title' => '', 'inline' => 1, 'display' => 'none', 'name' => 'rta_pid');

        // 工具
        $tools = array('add', 'edit', 'del', 'return');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {

        $id = intval($_REQUEST['id']);
        $pid = intval($_REQUEST['rta_pid']);
        if (!$id) {
            $result['rta_pid'] = 0;
            $result['rta_level'] = 0;
        }
        // 父集标签信息
        if ($pid) {
            $tagConfig['where']['rta_id'] = $pid;
            $p_info = D('ResourceTag')->getOne($tagConfig);
            $result['rta_pid'] = $p_info['rta_id'];
            $result['rta_level'] = intval($tag['rta_level']) + 1;
        }

        // 显示字段
        $addList[] = array('title' => L('TITLE'), 'name' => 'rta_title', 'class' => 'w460');
        $addList[] = array('title' => L('STATUS'), 'name' => 'rta_status', 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));
        $addList[] = array('title' => '', 'name' => 'rta_pid', 'type' => 'hidden');
        $addList[] = array('title' => '', 'name' => 'rta_level', 'type' => 'hidden');
        return generationAddTpl($addList, 'rta_id', $title, $result);
    }

    public function delete() {
        $id = I('id', 0, 'strval');
        $config['where']['rta_pid'] = array('in', $id);
        $info = D('ResourceTag')->getOne($config);
        if ($info) {
            // 有子栏目
            $this->error(L('MESSAGE_SUB_SECTION_EXIST'));
        }
        
        $tag['where']['rta_id'] = array('in', $id);
        $tag_info = D('ResourceTag')->getOne($tag);

        parent::deleteData();

        $this->redirect(__CONTROLLER__ . '/index/rta_pid/' . $tag_info['rta_pid']);
    }

    public function insert() {

        parent::data();

        $this->redirect(__CONTROLLER__ . '/index/rta_pid/' . I('rta_pid', 0, 'intval'));
    }
}