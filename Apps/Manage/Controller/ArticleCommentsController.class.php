<?php
namespace Manage\Controller;
class ArticleCommentsController extends ManageController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'aco_id', 'percent' => '10', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'art_title', 'percent' => '15', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'me_nickname', 'percent' => '10', 'title' => L('NICKNAME'), 'class' => 'showContent');
        $tplList[] = array('id' => 'aco_content', 'percent' => '30', 'title' => L('CONTENT'), 'class' => 'showContent');
        $tplList[] = array('id' => 'aco_status', 'percent' => '10', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '18', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'del', 'percent' => '4', 'title' => L('DELETE'));
        $action[] = array('id' => 'shows', 'percent' => '4', 'title' => L('SHOWS'));
        $action[] = array('id' => 'forbid', 'percent' => '4', 'title' => L('FORBID'));
        $action[] = array('id' => 'child', 'percent' => '6', 'title' => L('CHILD'));

        // 检索器
        $search[] = array('title' => L('STATUS'), 'name' => 'aco_status', 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));
        $search[] = array('title' => '', 'inline' => 1, 'display' => 'none', 'name' => 'aco_pid');

        // 工具
        $tools = array('del', 'forbid', 'return');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function shows() {
        // 评论信息
        $vo = D('ArticleComments')->getById(I('id', 0, 'intval'));
        $this->assign('vo', $vo);
        $this->display();
    }

    public function forbid() {
        $id = I('id');
        $config['where']['aco_id'] = array('IN', $id);
        $data['aco_status'] = 9;
        $res = D('ArticleComments')->update($data, $config);
        $this->show($res);
    }

    // 默认删除操作
    public function delete() {
        
        $config['where']['aco_id'] = array('IN', I('id'));

        $res = D('ArticleComments')->delete($config);

        if ($res !== false) {
            $this->success(L('DELETE'). L('SUCCESS'));
        } else {
            $this->error(D('ArticleComments')->getError());
        }
    }
}