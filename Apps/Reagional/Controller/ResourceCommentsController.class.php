<?php
namespace Reagional\Controller;
class ResourceCommentsController extends ReagionalController {
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
        $action[] = array('id' => 'resume', 'percent' => '4', 'title' => L('RESUME'));
        $action[] = array('id' => 'child', 'percent' => '6', 'title' => L('CHILD'));

        // 检索器
        $search[] = array('title' => L('TITLE'), 'name' => 'res_title');
        $search[] = array('title' => L('NICKNAME'), 'name' => 'me_nickname', 'inline' => true);
        $search[] = array('title' => L('STARTTIME'), 'name' => 'starttime', 'event' => 'onClick', 'eventValue' => "WdatePicker();",);
        $search[] = array('title' => L('ENDTIME'), 'name' => 'endtime', 'event' => 'onClick', 'eventValue' => "WdatePicker();", 'inline' => true);
        $search[] = array('title' => L('STATUS'), 'name' => 'rco_status', 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));
        $search[] = array('title' => '', 'inline' => 1, 'display' => 'none', 'name' => 'rco_pid');

        // 工具
        $tools = array('del', 'forbid', 'resume', 'return');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    // 列表
    public function lists() {
        // 用户所属区域id
        $_POST['re_id'] = $_SESSION['re_id'];
        // 结果自动处理
        $_POST['is_deal_result'] = true;
        // 所属平台 （区域后台）
        $_POST['belong'] = 2;

        if ($_POST['starttime']) {
            $_POST['starttime'] = strtotime($_POST['starttime']);
        }
        if ($_POST['endtime']) {
            $_POST['endtime'] = strtotime($_POST['endtime']);
        }

        // api 请求数据
        $data = $this->apiReturnDeal(getApi($_POST, CONTROLLER_NAME, 'lists'));
        
        echo json_encode($data);
    }

    public function shows() {
        // 评论信息
        $config['rco_id'] = I('id', 0, 'intval');
        $config['is_deal_result'] = true;
        $vo = $this->apiReturnDeal(getApi($config, 'ResourceComments', 'shows'));
        $this->assign('vo', $vo);
        $this->display();
    }

    public function forbid() {
        $config['rco_id'] = strval(I('id'));
        $config['rco_status'] = 9;

        // api 请求 审核不通过
        $data = $this->apiReturnDeal(getApi($config, 'ResourceComments', 'review'));

        // 提示
        $this->show($data);
    }

    public function resume() {
        $config['rco_id'] = strval(I('id'));
        $config['rco_status'] = 1;

        // api 请求 审核不通过
        $data = $this->apiReturnDeal(getApi($config, 'ResourceComments', 'review'));

        // 提示
        $this->show($data);
    }

    // 默认删除操作
    public function delete() {
        parent::delete('rco_id');
    }
}