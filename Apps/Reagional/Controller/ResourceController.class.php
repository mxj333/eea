<?php
namespace Reagional\Controller;
class ResourceController extends ReagionalController {

    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'res_id', 'percent' => '5', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'res_title', 'percent' => '20', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'rc_id', 'percent' => '10', 'title' => L('CODE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'rt_id', 'percent' => '10', 'title' => L('TYPE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'nickname', 'percent' => '15', 'title' => L('PUBLISHER'), 'class' => 'showContent');
        $tplList[] = array('id' => 'res_subject', 'percent' => '10', 'title' => L('SUBJECT'), 'class' => 'showContent');
        $tplList[] = array('id' => 'res_published_time', 'percent' => '15', 'title' => L('PUBLISHED_TIME'));
        $tplList['action'] = array('id' => 'action', 'percent' => '8', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '4', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '4', 'title' => L('DELETE'));

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
        if ($_POST['res_avaliable']) {
            $_POST['res_avaliable'] = strtotime($_POST['res_avaliable']);
        }
        // 用户所属区域id
        $_POST['re_id'] = $_SESSION['re_id'];
        // 结果自动处理
        $_POST['is_deal_result'] = true;
        // 所属平台 （区域后台）
        $_POST['belong'] = 2;

        // api 请求数据
        $data = $this->apiReturnDeal(getApi($_POST, CONTROLLER_NAME, 'lists'));
        
        echo json_encode($data);
    }
    
    // 添加
    public function add() {
        $this->commonData();
        $this->assign('re_title', session('re_title'));
        parent::add('Resource/add');
    }

    // 编辑
    public function edit() {

        if ($_POST) {
            $this->insert();
        } else {
            $this->commonData();

            $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Edit.js'));
            $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . 'Add.css'));

            // 获取数据
            $vo = $this->apiReturnDeal(getApi(array('res_id' => intval(I('request.id')), 'belong' => 2, 'is_deal_result' => true), 'Resource', 'shows'));
            foreach ($vo as &$value) {
                $value = stripFilter($value);
            }
            
            $vo['res_issused'] = $vo['res_issused'] ? date('Y-m-d', $vo['res_issused']) : 0;
            $vo['published_local'] = D('Resource')->fieldsToLocal($vo, 2);
            
            $this->assign('vo', $vo);
            $this->display();
        }
    }

    // 添加修改页面公共部分
    public function commonData() {
        
        $tag = reloadCache('tag');

        $this->assign('category', reloadCache('resourceCategory'));
        $this->assign('learner', $tag[2]);
        $this->assign('educational', $tag[3]);
        $this->assign('school_type', $tag[4]);
        $this->assign('subject', $tag[5]);
        $this->assign('version', $tag[6]);
        $this->assign('grade', $tag[7]);
        $this->assign('semester', $tag[8]);
        $this->assign('max_num', C('RESOURCE_MAX_TAG_NUM'));
        $this->assign('grade_num', json_encode(str_split(C('SCHOOLTYPE_GRADE_NUM'))));

        // 默认地区为当前
        $vo['re_id'] = session('re_id');
        $vo['re_title'] = session('re_title');
        $this->assign('vo', $vo);
    }

    // 获取关键词
    public function getKeywords() {
        $res = $this->apiReturnDeal(getApi(array('rta_pid' => intval(I('request.pid')), 'p' => intval($p)), 'Resource', 'tags'));
        $this->ajaxReturn($res);
    }
    public function getKeywordsInfo() {
        $id = I('id', 0, 'intval');
        if (!$id) {
            $this->ajaxReturn(0);
        }

        $res = $this->apiReturnDeal(getApi(array('rta_id' => $id), 'Resource', 'getTagInfo'));

        $this->ajaxReturn($res);
    }

    // 获取知识点
    public function getKnowledge() {
        
        $config['kp_pid'] = I('pid', 0, 'intval');
        $config['kp_subject'] = I('subject', 0, 'intval');
        $config['p'] = I('p', 1, 'intval');
        $res = $this->apiReturnDeal(getApi($config, 'Resource', 'knowledge'));
        
        $this->ajaxReturn($res);
    }
    public function getKnowledgeInfo() {
        $id = I('id', 0, 'intval');
        if (!$id) {
            $this->ajaxReturn(0);
        }

        $config['kp_id'] = $id;
        $res = $this->apiReturnDeal(getApi($config, 'Resource', 'getKnowledgeInfo'));

        $this->ajaxReturn($res);
    }

    // 获取课文目录
    public function getDirectory() {
        $d_pid = I('pid', 0, 'intval');
        $config['fields'] = 'd_id,d_title';
        if ($d_pid) {
            $config['d_pid'] = $d_pid;
        } else {
            $config['d_level'] = 5;
        }
        $config['d_school_type'] = I('school_type', 0, 'intval');
        $config['d_grade'] = I('grade', 0, 'intval');
        $config['d_subject'] = I('subject', 0, 'intval');
        $config['d_version'] = I('version', 0, 'intval');
        $config['d_semester'] = I('semester', 0, 'intval');
        $config['p'] = I('p', 1, 'intval');
        
        $res = $this->apiReturnDeal(getApi($config, 'Resource', 'directory'));

        $this->ajaxReturn($res);
    }
    public function getDirectoryInfo() {
        $id = I('id', 0, 'intval');
        if (!$id) {
            $this->ajaxReturn(0);
        }

        $config['fields'] = 'd_pid';
        $config['d_id'] = $id;

        $res = $this->apiReturnDeal(getApi($config, 'Resource', 'getDirectoryInfo'));

        $this->ajaxReturn($res);
    }

    // 获取地区
    public function getRegion() {
        $region = reloadCache('region');
        $this->ajaxReturn($region[I('id', 0, 'intval')]);
    }

    public function insert() {
        if (intval($_POST['res_id']) && !is_array($_POST['res_id'])) {
            // 所属平台 （区域后台）
            $_POST['belong'] = 2;
            // 是否需要自动审核通过
            $_POST['pass_auto'] = true;
            // 处理发布位置
            $published_local = ($_POST['res_is_published'] == 1) ? intval($_POST['published_local']+2) : intval($_POST['published_local']);

            unset($saveData);
            $saveData = D('Resource')->localToFields($published_local);
            // 区域后台区域标识
            $saveData['re_id'] = session('re_id');
            $saveData['re_title'] = session('re_title');
            $_POST = array_merge($_POST, $saveData);

            // 编辑 只能单个编辑
            parent::insert('res_id');
        } else {
            // 默认所属平台为区域平台
            $belong = 2;
            // 添加  可多个
            foreach($_POST['res_title'] as $key => $val) {
                $data = array(
                    'rf_id' => intval($_POST['rf_id'][$key]),
                    'rt_id' => intval($_POST['rt_id'][$key]),
                    'res_transform_status' => intval($_POST['res_transform_status'][$key]),
                    'res_title' => strval($_POST['res_title'][$key]),
                    'rc_id' => intval($_POST['rc_id'][$key]),
                    'res_is_recommend' => $_POST['res_is_recommend'][$key],
                    'res_is_excellent' => $_POST['res_is_excellent'][$key],
                    'res_is_pushed' => $_POST['res_is_pushed'][$key],
                    'res_is_published' => $_POST['res_is_published'][$key],
                    'res_is_original' => intval($_POST['res_is_original'][$key]),
                    'res_author' => strval($_POST['res_author'][$key]),
                    'res_language' => strval($_POST['res_language'][$key]),
                    'res_metadata_language' => strval($_POST['res_metadata_language'][$key]),
                    'res_audience_learner' => intval($_POST['res_audience_learner'][$key]),
                    'res_audience_educational_type' => intval($_POST['res_audience_educational_type'][$key]),
                    'res_summary' => strval($_POST['res_summary'][$key]),
                    'res_published_name' => strval($_POST['res_published_name'][$key]),
                    'res_published_company' => strval($_POST['res_published_company'][$key]),
                    'res_other_author' => strval($_POST['res_other_author'][$key]),
                    'res_permissions' => intval($_POST['res_permissions'][$key]),
                    'res_download_points' => intval($_POST['res_download_points'][$key]),
                    'res_issused' => strval($_POST['res_issused'][$key]),
                    'res_metadata_scheme' => strval($_POST['res_metadata_scheme'][$key]),
                    'res_short_title' => strval($_POST['res_short_title'][$key]),
                    'res_valid' => strval($_POST['res_valid'][$key]),
                    'res_avaliable' => strval($_POST['res_avaliable'][$key]),
                    'res_creator_id' => $_SESSION[C('USER_AUTH_KEY')],
                    'res_creator_table' => 'Member',
                    'res_version' => intval($_POST['res_version'][$key]),
                    'res_school_type' => intval($_POST['res_school_type'][$key]),
                    'res_grade' => intval($_POST['res_grade'][$key]),
                    'res_semester' => intval($_POST['res_semester'][$key]),
                    'res_subject' => intval($_POST['res_subject'][$key]),
                );

                $saveData = D('Resource', 'Model')->create($data);
                if ($saveData === false) {
                    $this->error(D('Resource', 'Model')->getError());
                }

                // 特殊值处理
                $data['res_is_pass'] = 1; // 默认审核通过
                // 处理发布位置
                $published_local = ($data['res_is_published'] == 1) ? intval($_POST['published_local'][$key]+2) : 0;

                unset($saveData2);
                $saveData2 = D('Resource')->localToFields($published_local);
                // 区域后台标识
                $saveData2['re_id'] = session('re_id');
                $saveData2['re_title'] = session('re_title');

                $data = array_merge($data, $saveData2);
                
                // 目录、知识点、关键词
                $data['directory'] = $_POST['directory'][$key];
                $data['keywords'] = $_POST['keywords'][$key];
                $data['knowledge'] = $_POST['knowledge'][$key];
                $data['belong'] = $belong;
                $data['pass_auto'] = true;
                $result = $this->apiReturnDeal(getApi($data, 'Resource', 'add'));
            }

            $this->show($result, L('ADD'));
        }
    }

    public function delete() {
        parent::delete('res_id');
    }

    public function upload() {
        
        $fields['file'] = '@'.realpath($_FILES['file']['tmp_name']).";type=".$_FILES['file']['type'].";filename=".$_FILES['file']['name'];
        $data['chunk'] = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $data['chunks'] = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
        if (isset($_REQUEST["name"])) {
            $data['name'] = $_REQUEST["name"];
        } elseif (!empty($_FILES)) {
            $data['name'] = $_FILES["file"]["name"];
        } else {
            $data['name'] = savename_rule();
        }
        $return = getApi($data, 'Resource', 'upload', 'json', $fields);

        echo json_encode($return);
    }
}