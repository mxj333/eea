<?php
namespace Manage\Controller;
class GradNewsController extends ManageController {

    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'gn_id', 'percent' => '8', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'gn_title', 'percent' => '22', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'gn_url', 'percent' => '40', 'title' => L('ADDRESS'), 'class' => 'showContent');
        $tplList[] = array('id' => 'gn_status', 'percent' => '10', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '10', 'title' => L('OPERATION'));
        // 操作
        $action[] = array('id' => 'edit', 'percent' => '10', 'title' => L('EDIT'));

        // 检索器
        $search[] = array('title' => L('NAME'), 'name' => 'gn_title');

        // 工具
        $tools = array('add', 'edit', 'del');
        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        // 显示字段
        $addList[] = array('title' => L('NAME'), 'name' => 'gn_title', 'class' => 'w460');
        $addList[] = array('title' => L('ADDRESS'), 'name' => 'gn_url', 'class' => 'w460');
        $addList[] = array('title' => L('ADDRESS_REG'), 'name' => 'gn_urlReg', 'class' => 'w460');
        $addList[] = array('title' => L('TITLE_REG'), 'name' => 'gn_titleReg', 'class' => 'w460');
        $addList[] = array('title' => L('REMARK_REG'), 'name' => 'gn_remarkReg', 'class' => 'w460');
        $addList[] = array('title' => L('CONTENT_REG'), 'name' => 'gn_contentReg', 'class' => 'w460');
        $addList[] = array('title' => L('PREV_UEL'), 'name' => 'gn_prevUrl', 'class' => 'w460');
        $addList[] = array('title' => L('CHECK_REG'), 'name' => 'gn_check', 'class' => 'w460');
        $addList[] = array('title' => L('STATUS'), 'name' => 'gn_status', 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));

        foreach ($result as $key => &$value) {
            $value = htmlspecialchars($value);
        }

        return generationAddTpl($addList, 'gn_id', $title, $result);
    }
}