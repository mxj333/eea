<?php
namespace Manage\Controller;
class AppApplyController extends ManageController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'aa_id', 'percent' => '5', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'aa_title', 'percent' => '15', 'title' => L('TITLE'));
        $tplList[] = array('id' => 'aa_url', 'percent' => '20', 'title' => L('URL'));
        $tplList[] = array('id' => 'me_nickname', 'percent' => '15', 'title' => L('NICKNAME'), 'class' => 'showContent');
        $tplList[] = array('id' => 'aa_is_pass', 'percent' => '15', 'title' => L('PASS_STATUS'), 'class' => 'showContent');
        $tplList[] = array('id' => 'aa_status', 'percent' => '10', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '8', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'del', 'percent' => '4', 'title' => L('DELETE'));
        $action[] = array('id' => 'shows', 'percent' => '4', 'title' => L('SHOWS'));

        // 检索器
        $search[] = array('title' => L('TITLE'), 'name' => 'aa_title');

        // 工具
        $tools = array('del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    // 默认删除操作
    public function delete() {
        
        $config['where']['aa_id'] = array('IN', I('id'));

        $res = D('AppApply')->delete($config);

        if ($res !== false) {
            $this->success(L('DELETE'). L('SUCCESS'));
        } else {
            $this->error(D('ResourceComments')->getError());
        }
    }

    public function shows() {
        // 评论信息
        $vo = D('AppApply')->getById(I('id', 0, 'intval'));
        $this->assign('vo', $vo);
        $this->display();
    }
}