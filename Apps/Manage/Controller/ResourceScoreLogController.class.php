<?php
namespace Manage\Controller;
class ResourceScoreLogController extends ManageController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'rsl_id', 'percent' => '10', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'res_title', 'percent' => '15', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'rst_title', 'percent' => '15', 'title' => L('RST_TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'me_nickname', 'percent' => '10', 'title' => L('NICKNAME'), 'class' => 'showContent');
        $tplList[] = array('id' => 'rsl_score', 'percent' => '10', 'title' => L('SCORE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'rsl_status', 'percent' => '10', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '10', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'del', 'percent' => '4', 'title' => L('DELETE'));
        $action[] = array('id' => 'shows', 'percent' => '4', 'title' => L('SHOWS'));
        $action[] = array('id' => 'forbid', 'percent' => '4', 'title' => L('FORBID'));

        // 检索器
        $search[] = array('title' => L('STATUS'), 'name' => 'rsl_status', 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));

        // 工具
        $tools = array('del', 'forbid');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function shows() {
        // 评论信息
        $vo = D('ResourceScoreLog')->getById(I('id', 0, 'intval'));
        $this->assign('vo', $vo);
        $this->display();
    }

    public function forbid() {
        $id = I('id');
        $config['where']['rsl_id'] = array('IN', $id);
        $data['rsl_status'] = 9;
        $res = D('ResourceScoreLog')->update($data, $config);
        $this->show($res);
    }
}