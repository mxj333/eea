<?php
namespace Manage\Controller;
class ResourceRecycleController extends ManageController {

    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'res_id', 'percent' => '5', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'res_title', 'percent' => '30', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'rc_id', 'percent' => '20', 'title' => L('CODE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'res_eliminated_time', 'percent' => '20', 'title' => L('DELETE_TIME'));
        $tplList['action'] = array('id' => 'action', 'percent' => '12', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'del', 'percent' => '4', 'title' => L('DELETE'));
        $action[] = array('id' => 'resume', 'percent' => '4', 'title' => L('RESUME'));
        $action[] = array('id' => 'shows', 'percent' => '4', 'title' => L('SHOWS'));

        // 检索器
        $search[] = array('title' => L('TITLE'), 'name' => 'res_title');
        $search[] = array('title' => L('DELETER'), 'name' => 'deleter', 'inline' => true);
        $search[] = array('title' => L('CODE'), 'name' => 'rc_id', 'label' => 'select', 'data' => reloadCache('resourceCategory'), 'inline' => true);
        $search[] = array('title' => L('STARTTIME'), 'name' => 'deleted_starttime', 'event' => 'onClick', 'eventValue' => "WdatePicker();",);
        $search[] = array('title' => L('ENDTIME'), 'name' => 'deleted_endtime', 'event' => 'onClick', 'eventValue' => "WdatePicker();", 'inline' => true);

        // 工具
        $tools = array('add', 'edit', 'del', 'resume');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    // 列表
    public function lists() {
        if ($_POST['deleted_starttime']) {
            $_POST['deleted_starttime'] = strtotime($_POST['deleted_starttime']);
        }
        if ($_POST['deleted_endtime']) {
            $_POST['deleted_endtime'] = strtotime($_POST['deleted_endtime']);
        }
        echo json_encode(D(CONTROLLER_NAME)->lists($_POST));
    }

    public function delete() {

        $config['where']['res_id'] = array('IN', strval(I('request.id')));

        // 标记为删除
        $result = D('Resource')->signDeleted($config, array(), 'User');

        $this->show($result, L('DELETE'));
    }

    public function resume() {
        $data['where']['res_id'] = array('IN', strval(I('request.id')));
        
        $this->show(D('Resource')->resume($data), L('RESUME'));
    }

    // 展示
    public function shows() {
        $tag = loadCache('tag');

        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/resourceAdd.css'));
        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/resourceAdd.js'));

        $this->assign('category', loadCache('resourceCategory'));
        $this->assign('learner', $tag[2]);
        $this->assign('educational', $tag[3]);
        $this->assign('school_type', $tag[4]);
        $this->assign('subject', $tag[5]);
        $this->assign('version', $tag[6]);
        $this->assign('grade', $tag[7]);
        $this->assign('semester', $tag[8]);
        $this->assign('max_num', C('RESOURCE_MAX_TAG_NUM'));
        $this->assign('grade_num', json_encode(str_split(C('SCHOOLTYPE_GRADE_NUM'))));

        $vo = D('Resource')->getById(intval(I('request.id')));
        foreach ($vo as &$value) {
            $value = stripFilter($value);
        }

        $this->assign('vo', $vo);
        $this->display('Resource/add');
    }

    // 获取地区
    public function getRegion() {
        $region = loadCache('region');
        $this->ajaxReturn($region[I('id', 0, 'intval')]);
    }
}