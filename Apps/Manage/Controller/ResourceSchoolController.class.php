<?php
namespace Manage\Controller;
class ResourceSchoolController extends ManageController {

    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'res_id', 'percent' => '5', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'res_title', 'percent' => '20', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'rc_id', 'percent' => '10', 'title' => L('CODE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'rt_id', 'percent' => '10', 'title' => L('TYPE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'nickname', 'percent' => '15', 'title' => L('PUBLISHER'), 'class' => 'showContent');
        $tplList[] = array('id' => 'res_subject', 'percent' => '15', 'title' => L('SUBJECT'), 'class' => 'showContent');
        $tplList[] = array('id' => 'res_published_time', 'percent' => '15', 'title' => L('PUBLISHED_TIME'));
        $tplList['action'] = array('id' => 'action', 'percent' => '5', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'shows', 'percent' => '4', 'title' => L('SHOWS'));

        // 检索器
        $search[] = array('title' => L('TITLE'), 'name' => 'res_title' );
        $search[] = array('title' => L('PUBLISHER'), 'name' => 'publisher_name');
        $search[] = array('title' => L('AVALIABLE'), 'name' => 'res_avaliable', 'event' => 'onClick', 'eventValue' => "WdatePicker();", 'inline' => true);
        $search[] = array('title' => L('CODE'), 'name' => 'rc_id', 'label' => 'select', 'data' => reloadCache('resourceCategory'));
        $resourceType = reloadCache('resourceType');
        foreach ($resourceType as $key => $value) {
            $resourceType[$key] = $value['rt_title'];
        }
        $search[] = array('title' => L('TYPE'), 'name' => 'rt_id', 'label' => 'select', 'data' => $resourceType, 'inline' => true);
        $search[] = array('title' => L('TRANSFORM'), 'name' => 'res_transform_status', 'label' => 'select', 'data' => array(9 => L('NO_TRANSFORM'), 1 => L('TRANSFORM')), 'inline' => true);
        $search[] = array('title' => L('STATUS'), 'name' => 'res_is_published', 'label' => 'select', 'data' => array(9 => L('NO_PUBLISHED'), 1 => L('PUBLISHED')), 'inline' => true);
        $tag = reloadCache('tag');
        $search[] = array('title' => L('SCHOOL_TYPE'), 'name' => 'res_school_type', 'label' => 'select', 'data' => $tag[4]);
        $search[] = array('title' => L('GRADES'), 'name' => 'res_grade', 'label' => 'select', 'data' => $tag[7], 'inline' => true);
        $search[] = array('title' => L('SUBJECT'), 'name' => 'res_subject', 'label' => 'select', 'data' => $tag[5], 'inline' => true);

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }
    
    // 列表
    public function lists() {
        $config['type'] = 3; // 学校
        $config['where']['s_id'] = array('neq', 0);
        echo json_encode(D('Resource')->lists($_POST, $config));
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