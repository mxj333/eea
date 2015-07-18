<?php
namespace Schoolback\Controller;
use Think\Controller;
class ResourceReviewController extends SchoolbackController {

    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'res_id', 'percent' => '5', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'res_title', 'percent' => '20', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'rc_id', 'percent' => '10', 'title' => L('CODE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'rt_id', 'percent' => '10', 'title' => L('TYPE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'nickname', 'percent' => '10', 'title' => L('PUBLISHER'), 'class' => 'showContent');
        $tplList[] = array('id' => 'res_subject', 'percent' => '10', 'title' => L('SUBJECT'), 'class' => 'showContent');
        $tplList[] = array('id' => 'res_published_time', 'percent' => '15', 'title' => L('PUBLISHED_TIME'));
        $tplList['action'] = array('id' => 'action', 'percent' => '12', 'title' => L('OPERATION'));

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
        // 用户所属区域id
        $_POST['s_id'] = $_SESSION['s_id'];
        // 结果自动处理
        $_POST['is_deal_result'] = true;
        // 资源审核列表
        $_POST['type'] = 2;
        // 所属平台 （学校后台）
        $_POST['belong'] = 3;
        if ($_POST['published_starttime']) {
            $_POST['published_starttime'] = strtotime($_POST['published_starttime']);
        }
        if ($_POST['published_endtime']) {
            $_POST['published_endtime'] = strtotime($_POST['published_endtime']);
        }

        // api 请求数据
        $data = $this->apiReturnDeal(getApi($_POST, 'Resource', 'lists'));
        
        echo json_encode($data);
    }

    // 审核不通过
    public function forbid() {
        $config['res_id'] = I('id', 0, 'strval');
        $config['res_is_pass'] = 9;
        // 所属平台 （学校后台）
        $config['belong'] = 3;

        // api 请求 审核不通过
        $data = $this->apiReturnDeal(getApi($config, 'Resource', 'review'));

        // 提示
        $this->show($data);
    }

    // 审核通过
    public function publish() {
        $config['res_id'] = I('id', 0, 'strval');
        $config['res_is_pass'] = 1;
        // 所属平台 （学校后台）
        $config['belong'] = 3;

        // api 请求 审核通过
        $data = $this->apiReturnDeal(getApi($config, 'Resource', 'review'));

        // 提示
        $this->show($data);
    }

    // 展示
    public function shows() {
        $tag = loadCache('tag');

        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/resourceAdd.css'));
        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/resourceEdit.js'));

        $this->assign('category', loadCache('resourceCategory'));
        $this->assign('learner', $tag[2]);
        $this->assign('educational', $tag[3]);
        $this->assign('school_type', $tag[4]);
        $this->assign('subject', $tag[5]);
        $this->assign('version', $tag[6]);
        $this->assign('grade', $tag[7]);
        $this->assign('semester', $tag[8]);

        $vo = $this->apiReturnDeal(getApi(array('res_id' => intval(I('request.id')), 'belong' => 3), 'Resource', 'shows'));
        foreach ($vo as &$value) {
            $value = stripFilter($value);
        }
        
        $vo['res_issused'] = $vo['res_issused'] ? date('Y-m-d', $vo['res_issused']) : 0;
        $vo['res_valid'] = $vo['res_valid'] ? date('Y-m-d', $vo['res_valid']) : 0;
        $vo['res_avaliable'] = $vo['res_avaliable'] ? date('Y-m-d', $vo['res_avaliable']) : 0;
        
        // 资源文件
        if (intval($vo['rf_id'])) {
            $file_info = $this->apiReturnDeal(getApi(array('rf_id' => intval($vo['rf_id'])), 'Resource', 'getFile'));
            $vo = array_merge($vo, (array)$file_info);
        }

        $this->assign('vo', $vo);
        $this->display('Resource/edit');
    }

    // 获取地区
    public function getRegion() {
        $region = loadCache('region');
        $this->ajaxReturn($region[I('id', 0, 'intval')]);
    }
}