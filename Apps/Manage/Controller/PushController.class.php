<?php
namespace Manage\Controller;
class PushController extends ManageController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'p_id', 'percent' => '10', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'p_title', 'percent' => '20', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'p_type', 'percent' => '20', 'title' => L('TYPE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'p_status', 'percent' => '20', 'title' => L('STATUS'), 'class' => 'showContent');
        $tplList['action'] = array('id' => 'action', 'percent' => '19', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '6', 'title' => L('EDIT'));
        $action[] = array('id' => 'shows', 'percent' => '6', 'title' => L('RULE'));
        $action[] = array('id' => 'forbid', 'percent' => '7', 'title' => L('RUN'));

        // 检索器
        $search[] = array('title' => L('TITLE'), 'name' => 'p_title');

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        // 显示字段
        $addList[] = array('name' => 'p_title', 'title' => L('TITLE'), 'class' => 'w460');
        $addList[] = array('name' => 'p_type', 'title' => L('TYPE'), 'label' => 'select', 'data' => array(1 => L('AUTO'), 2 => L('MANUAL')));
        $addList[] = array('name' => 'p_status', 'title' => L('STATUS'), 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));

        return generationAddTpl($addList, 'p_id', $title, $result);
    }

    // 规则页
    public function shows() {
        
        if (intval($_POST['p_id'])) {
            
            D('Push')->insertRule();

            $this->success(L('OPERATION_SUCCESS'));

        } else {

            $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . '.js'));
            $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . 'Shows.css'));

            $p_config['where']['p_id'] = intval($_GET['id']);
            $info = D('Push')->getOne($p_config);
            $pr_config['where']['p_id'] = intval($_GET['id']);
            $list = D('PushRule')->getAll($pr_config);

            $rule_type = explode(',', C('RULE_CONDITION_TYPE'));
            unset($rule_type[0]);
            $rule_condition = explode(',', C('RULE_CONDITION'));
            $this->assign('type', $rule_type);
            $this->assign('condition', $rule_condition);
            $this->assign('info', $info);
            $this->assign('list', $list);
            $this->display();
        }
    }

    public function delete(){
        $p_id = I('id', 0, 'strval');

        $config['where']['p_id'] = array('in', $p_id);

        $result = D('Push')->delete($config);

        if ($result !== false) {
            D('PushRule')->delete($config);
            $this->success(L('OPERATION_SUCCESS'));
        } else {
            $this->error(L('OPERATION_FAILURE'));
        }
    }

    // 执行推优机制
    public function forbid() {
        
        $p_id = I('id', 0, 'intval');
        $result = D('Push')->push($p_id);
        if ($result !== false) {
            $this->success(L('OPERATION_SUCCESS'));
        } else {
            $this->error(L('OPERATION_FAILURE'));
        }
    }
}