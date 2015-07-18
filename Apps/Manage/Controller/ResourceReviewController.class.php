<?php
namespace Manage\Controller;
use Think\Controller;
class ResourceReviewController extends ManageController {

    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'res_id', 'percent' => '5', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'res_title', 'percent' => '15', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'rc_id', 'percent' => '10', 'title' => L('CODE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'rt_id', 'percent' => '10', 'title' => L('TYPE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'nickname', 'percent' => '10', 'title' => L('PUBLISHER'), 'class' => 'showContent');
        $tplList[] = array('id' => 'res_subject', 'percent' => '10', 'title' => L('SUBJECT'), 'class' => 'showContent');
        $tplList[] = array('id' => 'res_published_time', 'percent' => '15', 'title' => L('PUBLISHED_TIME'));
        $tplList['action'] = array('id' => 'action', 'percent' => '18', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'forbid', 'percent' => '6', 'title' => L('NOAPPROVAL'));
        $action[] = array('id' => 'publish', 'percent' => '6', 'title' => L('APPROVAL'));
        $action[] = array('id' => 'shows', 'percent' => '6', 'title' => L('SHOWS'));

        // 检索器
        $search[] = array('title' => L('TITLE'), 'name' => 'res_title');
        $search[] = array('title' => L('PUBLISHER'), 'name' => 'publisher_name', 'inline' => true);
        $search[] = array('title' => L('CODE'), 'name' => 'rc_id', 'label' => 'select', 'data' => reloadCache('resourceCategory'), 'inline' => true);
        $search[] = array('title' => L('PUBLISHED_STARTTIME'), 'name' => 'published_starttime', 'event' => 'onClick', 'eventValue' => "WdatePicker();");
        $search[] = array('title' => L('PUBLISHED_ENDTIME'), 'name' => 'published_endtime', 'event' => 'onClick', 'eventValue' => "WdatePicker();", 'inline' => true);

        // 工具
        $tools = array('forbid', 'publish');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    // 列表
    public function lists() {
        if ($_POST['published_starttime']) {
            $_POST['published_starttime'] = strtotime($_POST['published_starttime']);
        }
        if ($_POST['published_endtime']) {
            $_POST['published_endtime'] = strtotime($_POST['published_endtime']);
        }
        echo json_encode(D(CONTROLLER_NAME)->lists($_POST));
    }

    // 审核不通过
    public function forbid() {
        
        $this->show(D('ResourceReview')->review(I('id', 0, 'strval'), 9, 0, 1), L('NOAPPROVAL'));
    }

    // 审核通过
    public function publish() {

        $this->show(D('ResourceReview')->review(I('id', 0, 'strval'), 1, 0, 1), L('APPROVAL'));
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