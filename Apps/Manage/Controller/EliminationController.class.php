<?php
namespace Manage\Controller;
class EliminationController extends ManageController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'e_id', 'percent' => '10', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'e_title', 'percent' => '20', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'e_type', 'percent' => '20', 'title' => L('TYPE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'e_status', 'percent' => '20', 'title' => L('STATUS'), 'class' => 'showContent');
        $tplList['action'] = array('id' => 'action', 'percent' => '19', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '6', 'title' => L('EDIT'));
        $action[] = array('id' => 'shows', 'percent' => '6', 'title' => L('RULE'));
        $action[] = array('id' => 'forbid', 'percent' => '7', 'title' => L('RUN'));

        // 检索器
        $search[] = array('title' => L('TITLE'), 'name' => 'e_title');

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        // 显示字段
        $addList[] = array('name' => 'e_title', 'title' => L('TITLE'), 'class' => 'w460');
        $addList[] = array('name' => 'e_type', 'title' => L('TYPE'), 'label' => 'select', 'data' => array(1 => L('AUTO'), 2 => L('MANUAL')));
        $addList[] = array('name' => 'e_status', 'title' => L('STATUS'), 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));

        return generationAddTpl($addList, 'e_id', $title, $result);
    }

    // 规则页
    public function shows() {
        
        if (intval($_POST['e_id'])) {
            
            D('Elimination')->insertRule();

            $this->success(L('OPERATION_SUCCESS'));

        } else {

            $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . '.js'));

            $e_config['where']['e_id'] = intval($_GET['id']);
            $info = D('Elimination')->getOne($e_config);
            $er_config['where']['e_id'] = intval($_GET['id']);
            $list = D('EliminationRule')->getAll($er_config);

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
        $e_id = I('id', 0, 'strval');

        $config['where']['e_id'] = array('in', $e_id);

        $result = D('Elimination')->delete($config);

        if ($result !== false) {
            D('EliminationRule')->delete($config);
            $this->success(L('OPERATION_SUCCESS'));
        } else {
            $this->error(L('OPERATION_FAILURE'));
        }
    }

    // 执行淘汰机制
    public function forbid() {
        
        $e_id = I('id', 0, 'intval');
        $result = D('Elimination')->elimination($e_id);
        if ($result !== false) {
            $this->success(L('OPERATION_SUCCESS'));
        } else {
            $this->error(L('OPERATION_FAILURE'));
        }
    }
}