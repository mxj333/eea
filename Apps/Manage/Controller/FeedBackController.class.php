<?php
namespace Manage\Controller;
class FeedBackController extends ManageController {

    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'fb_id', 'percent' => '8', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'fb_title', 'percent' => '22', 'title' => L('FEED_BACK_PEOPLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'fb_email', 'percent' => '30', 'title' => L('EMAIL'), 'class' => 'showContent');
        $tplList[] = array('id' => 'fb_phone', 'percent' => '20', 'title' => L('CONTACT_NUMBER'), 'class' => 'showContent');
        $tplList['action'] = array('id' => 'action', 'percent' => '10', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '10', 'title' => L('EDIT'));

        // 检索器
        $search[] = array('title' => L('FEED_BACK_PEOPLE'), 'name' => 'fb_title');

        // 工具
        $tools = array('add', 'edit');

        // 标题栏
        $titleFlag = 1;

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        // 显示字段
        $addList[] = array('title' => L('YOUR_NANE'), 'name' => 'fb_title', 'class' => 'w460');
        $addList[] = array('title' => L('EMAIL'), 'name' => 'fb_email', 'class' => 'w460');
        $addList[] = array('title' => L('CONTACT_NUMBER'), 'name' => 'fb_phone', 'class' => 'w460');
        $addList[] = array('title' => L('FEED_BACK_CONTENT'), 'name' => 'fb_content', 'label' => 'textarea', 'class' => 'h80');

        return generationAddTpl($addList, 'fb_id', $title, $result);
    }
}
