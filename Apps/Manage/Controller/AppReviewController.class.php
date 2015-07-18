<?php
namespace Manage\Controller;
use Think\Controller;
class AppReviewController extends ManageController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'aa_id', 'percent' => '5', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'aa_title', 'percent' => '10', 'title' => L('TITLE'));
        $tplList[] = array('id' => 'aa_url', 'percent' => '20', 'title' => L('URL'));
        $tplList[] = array('id' => 'me_nickname', 'percent' => '10', 'title' => L('NICKNAME'), 'class' => 'showContent');
        $tplList[] = array('id' => 'aa_description', 'percent' => '25', 'title' => L('DESCRIPTION'), 'class' => 'showContent');
        $tplList[] = array('id' => 'aa_status', 'percent' => '10', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '14', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'forbid', 'percent' => '6', 'title' => L('FORBID'));
        $action[] = array('id' => 'publish', 'percent' => '4', 'title' => L('PUBLISH'));
        $action[] = array('id' => 'shows', 'percent' => '4', 'title' => L('SHOWS'));

        // 检索器
        $search[] = array('title' => L('TITLE'), 'name' => 'aa_title');
        $search[] = array('title' => L('STARTTIME'), 'name' => 'starttime', 'event' => 'onClick', 'eventValue' => "WdatePicker();");
        $search[] = array('title' => L('ENDTIME'), 'name' => 'endtime', 'inline' => true, 'event' => 'onClick', 'eventValue' => "WdatePicker();");

        // 工具
        $tools = array('forbid', 'publish');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    // 审核不通过
    public function forbid() {
        
        $this->show(D('AppReview')->noPass(I('id', 0, 'strval')), L('NOPASSED'));
    }

    // 审核通过
    public function publish() {

        $this->show(D('AppReview')->pass(I('id', 0, 'strval')), L('APPROVAL'));
    }

    // 展示
    public function shows() {
        // 评论信息
        $vo = D('AppApply')->getById(I('id', 0, 'intval'));
        $this->assign('vo', $vo);
        $this->display('AppApply/shows');
    }
}