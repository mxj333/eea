<?php
namespace Manage\Controller;
class ResourceCommentsController extends ManageController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'rco_id', 'percent' => '10', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'res_title', 'percent' => '15', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'me_nickname', 'percent' => '10', 'title' => L('NICKNAME'), 'class' => 'showContent');
        $tplList[] = array('id' => 'rco_content', 'percent' => '30', 'title' => L('CONTENT'), 'class' => 'showContent');
        $tplList[] = array('id' => 'rco_status', 'percent' => '10', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '18', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'del', 'percent' => '4', 'title' => L('DELETE'));
        $action[] = array('id' => 'shows', 'percent' => '4', 'title' => L('SHOWS'));
        $action[] = array('id' => 'forbid', 'percent' => '4', 'title' => L('FORBID'));
        $action[] = array('id' => 'child', 'percent' => '6', 'title' => L('CHILD'));

        // 检索器
        $search[] = array('title' => L('TITLE'), 'name' => 'res_title');
        $search[] = array('title' => L('NICKNAME'), 'name' => 'me_nickname', 'inline' => true);
        $search[] = array('title' => L('STARTTIME'), 'name' => 'starttime', 'event' => 'onClick', 'eventValue' => "WdatePicker();",);
        $search[] = array('title' => L('ENDTIME'), 'name' => 'endtime', 'event' => 'onClick', 'eventValue' => "WdatePicker();", 'inline' => true);
        $search[] = array('title' => L('STATUS'), 'name' => 'rco_status', 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));
        $search[] = array('title' => '', 'inline' => 1, 'display' => 'none', 'name' => 'rco_pid');

        // 工具
        $tools = array('del', 'forbid', 'return');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    // 列表
    public function lists() {
        if ($_POST['starttime']) {
            $_POST['starttime'] = strtotime($_POST['starttime']);
        }
        if ($_POST['endtime']) {
            $_POST['endtime'] = strtotime($_POST['endtime']);
        }
        echo json_encode(D(CONTROLLER_NAME)->lists($_POST));
    }

    public function shows() {
        // 评论信息
        $vo = D('ResourceComments')->getById(I('id', 0, 'intval'));
        $this->assign('vo', $vo);
        $this->display();
    }

    public function forbid() {
        $id = I('id');
        $config['where']['rco_id'] = array('IN', $id);
        $data['rco_status'] = 9;
        $res = D('ResourceComments')->update($data, $config);
        $this->show($res);
    }

    // 默认删除操作
    public function delete() {
        
        $config['where']['rco_id'] = array('IN', I('id'));

        $res = D('ResourceComments')->delete($config);

        if ($res !== false) {
            $this->success(L('DELETE'). L('SUCCESS'));
        } else {
            $this->error(D('ResourceComments')->getError());
        }
    }
}