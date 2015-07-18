<?php
namespace Reagional\Controller;
class MessageController extends ReagionalController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'mes_id', 'percent' => '10', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'a_title', 'percent' => '20', 'title' => L('A_TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'me_nickname', 'percent' => '15', 'title' => L('NICKNAME'));
        $tplList[] = array('id' => 'mes_content', 'percent' => '25', 'title' => L('CONTENT'));
        $tplList[] = array('id' => 'mes_created', 'percent' => '15', 'title' => L('CREATED'));
        $tplList['action'] = array('id' => 'action', 'percent' => '4', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'del', 'percent' => '4', 'title' => L('DELETE'));

        // 检索器
        $search[] = array('title' => L('CONTENT'), 'name' => 'mes_content');

        // 工具
        $tools = array('del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function delete() {
        parent::delete('mes_id');
    }
}