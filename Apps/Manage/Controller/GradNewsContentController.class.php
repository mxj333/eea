<?php
namespace Manage\Controller;
class GradNewsContentController extends ManageController {
    // 详情
    public function shows() {
        $config['where']['gnc_id'] = intval(I('request.id'));
        $this->assign('newsContent', D('GradNewsContent')->getOne($config));
        $this->assign('p', intval($_REQUEST['p']));
        $this->display();
    }

    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'gnc_id', 'percent' => '8', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'gnc_title', 'percent' => '10', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'gnc_url', 'percent' => '30', 'title' => L('ADDRESS'), 'class' => 'showContent');
        $tplList[] = array('id' => 'gn_title', 'percent' => '16', 'title' => L('BELONG_TO_COLUMN'), 'class' => 'showContent');
        $tplList[] = array('id' => 'gnc_created', 'percent' => '12', 'title' => L('RECRAWLING'), 'class' => 'showContent');
        $tplList['action'] = array('id' => 'action', 'percent' => '10', 'title' => L('OPERATION'));
        // 操作
        $action[] = array('id' => 'shows', 'percent' => '5', 'title' => L('SHOWS'));
        $action[] = array('id' => 'del', 'percent' => '5', 'title' => L('DELETE'));

        // 检索器
        $search[] = array('title' => L('TITLE'), 'name' => 'gnc_title');

        // 工具
        $tools = array('del');
        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }
}