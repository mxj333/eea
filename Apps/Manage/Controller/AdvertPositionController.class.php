<?php
namespace Manage\Controller;
class AdvertPositionController extends ManageController {
    public function index() {
        // 显示字段

        $tplList['id'] = array('id' => 'ap_id', 'percent' => '8', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'ap_title', 'percent' => '25', 'title' => L('NAME'), 'class' => 'showContent');
        $tplList[] = array('id' => 'ap_money', 'percent' => '10', 'title' => L('PRICE'));
        $tplList[] = array('id' => 'ap_ad_num', 'percent' => '15', 'title' => L('SUPPORT_NUMBER'));
        $tplList[] = array('id' => 'te_name', 'percent' => '20', 'title' => L('THEME'));
        $tplList['action'] = array('id' => 'action', 'percent' => '10', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '5', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '5', 'title' => L('DELETE'));

        // 检索器
        $search[] = array('title' => L('NAME'), 'name' => 'ap_title');

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }
    public function addTpl($title = '', $result = array()) {

        $advertTypeConfig['fields'] = 'at_id,at_title';
        $advertType = D('AdvertType')->getAll($advertTypeConfig);
        $type = loadCache('templateType');
        $themes = loadCache('template');
        $theme = $result['tt_id'] ? $themes[$result['tt_id']] : $themes[1];
        
        // 显示字段
        $addList[] = array('title' => L('NAME'), 'name' => 'ap_title', 'class' => 'w460');
        $addList[] = array('title' => L('ADVERT_TYPE'), 'name' => 'at_id', 'label' => 'select', 'data' => $advertType);
        $addList[] = array('title' => L('THEME_TYPE'), 'name' => 'tt_id', 'label' => 'select', 'data' => $type);
        $addList[] = array('title' => L('THEME'), 'name' => 'te_name', 'label' => 'select', 'data' => $theme);
        $addList[] = array('title' => L('WIDTH'), 'name' => 'ap_width', 'class' => 'w460');
        $addList[] = array('title' => L('HEIGHT'), 'name' => 'ap_height', 'class' => 'w460');
        $addList[] = array('title' => L('PRICE'), 'name' => 'ap_money', 'class' => 'w460');
        $addList[] = array('title' => L('SUPPORT_NUMBER'), 'name' => 'ap_ad_num', 'class' => 'w460');
        $addList[] = array('title' => L('DESCRIPTION'), 'name' => 'ap_description', 'label' => 'textarea', 'class' => 'h80');
        return generationAddTpl($addList, 'ap_id', $title, $result);
    }

    public function getTemplate() {
        $tt_id = I('tt_id', 0, 'intval');
        $themes = reloadCache('template');
        echo json_encode($themes[$tt_id]);
    }
}