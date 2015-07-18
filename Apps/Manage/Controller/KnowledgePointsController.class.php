<?php
namespace Manage\Controller;
class KnowledgePointsController extends ManageController {

    public function tree() {
        D('KnowledgePoints')->tree();
    }

    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'kp_id', 'percent' => '10', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'kp_title', 'percent' => '30', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'kp_subject', 'percent' => '15', 'title' => L('SUBJECT'));
        $tplList[] = array('id' => 'kp_status', 'percent' => '15', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '20', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '4', 'title' => L('EDIT'));
        $action[] = array('id' => 'child', 'percent' => '8', 'title' => L('SUB_SECTION'));
        $action[] = array('id' => 'shows', 'percent' => '8', 'title' => L('SHOWS'));

        // 检索器
        $tag = reloadCache('tag');

        $search[] = array('title' => L('TITLE'), 'name' => 'kp_title');
        $search[] = array('title' => L('SUBJECT'), 'name' => 'kp_subject', 'label' => 'select', 'data' => $tag[5]);
        $search[] = array('title' => '', 'inline' => 1, 'display' => 'none', 'name' => 'kp_pid');

        // 工具
        $tools = array('add', 'edit', 'return');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function insert() {
        $res = D('KnowledgePoints')->insert($_POST, 'User');
        $this->assign('waitSecond', 0);
        $this->show($this->data(), '', __CONTROLLER__ . '/index/kp_pid/' . $_POST['kp_pid']);
    }

    public function addTpl($title = '', $result = array()) {

        $id = intval($_REQUEST['kp_id']);
        if (isset($_GET['kp_id'])) {
            $result['kp_pid'] = $id;
            $result['kp_level'] = 0;
        }
        if ($id) {
            $knowledgePointsConfig['where']['kp_id'] = $id;
            $knowledgePointsConfig['fields'] = 'kp_level,kp_subject';
            $kp_info = D('KnowledgePoints')->getOne($knowledgePointsConfig);
            $result['kp_level'] = intval($kp_info['kp_level']) + 1;
            $result['kp_subject'] = $kp_info['kp_subject'];
        }

        $tag = reloadCache('tag');
        // 显示字段
        $addList[] = array('title' => L('SUBJECT'), 'name' => 'kp_subject', 'label' => 'select', 'data' => $tag[5]);
        $addList[] = array('title' => L('TITLE'), 'name' => 'kp_title', 'class' => 'w460');
        $addList[] = array('title' => L('SORT'), 'name' => 'kp_sort', 'class' => 'w460');
        $addList[] = array('title' => L('STATUS'), 'name' => 'kp_status', 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));
        $addList[] = array('title' => '', 'name' => 'kp_pid', 'type' => 'hidden');
        $addList[] = array('title' => '', 'name' => 'kp_level', 'type' => 'hidden');
        return generationAddTpl($addList, 'kp_id', $title, $result);
    }

    //  目录调整
    public function shows() {

        if ($_POST) {

            $res = D('KnowledgePoints')->adjustment($_POST);
            $this->show($res);
        } else {

            $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Shows.js'));
            $vo = D('KnowledgePoints')->getById(I('id'));
            $this->assign('vo', $vo);
            $tag = loadCache('tag');
            $this->assign('tag', $tag);
            $this->display();
        }
    }

    // 获取知识点
    public function getKnowledgePoints() {
        $config['fields'] = 'kp_id,kp_title';
        $config['where']['kp_subject'] = I('subject', 0, 'intval');
        $config['where']['kp_title'] = array('LIKE', '%' . I('title') . '%');
        $res = D('KnowledgePoints')->getAll($config);
        $this->ajaxReturn($res);
    }
}