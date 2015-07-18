<?php
namespace Manage\Controller;
class ResourceController extends ManageController {

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
        echo json_encode(D(CONTROLLER_NAME)->lists($_POST, $config));
    }

    // 添加
    public function add() {
        $this->commonData();
        parent::add('Resource/add');
    }

    // 编辑
    public function edit() {
        $this->commonData();
        parent::edit('Resource/add');
    }

    // 添加修改页面公共部分
    public function commonData() {
        
        $tag = reloadCache('tag');

        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . 'Add.css'));
        $this->assign('everyModelCss', $this->everyModelCss);

        $this->assign('category', reloadCache('resourceCategory'));
        $this->assign('learner', $tag[2]);
        $this->assign('educational', $tag[3]);
        $this->assign('school_type', $tag[4]);
        $this->assign('subject', $tag[5]);
        $this->assign('version', $tag[6]);
        $this->assign('grade', $tag[7]);
        $this->assign('semester', $tag[8]);
        $this->assign('max_num', C('RESOURCE_MAX_TAG_NUM'));
        $this->assign('p', intval($_REQUEST['p']));
        $this->assign('grade_num', json_encode(str_split(C('SCHOOLTYPE_GRADE_NUM'))));
    }

    // 获取关键词
    public function getKeywords() {
        $config['fields'] = 'rta_id,rta_title';
        $config['where']['rta_pid'] = I('pid', 0, 'intval');
        $config['p'] = I('p', 1, 'intval');
        $res = D('ResourceTag')->getListByPage($config);
        $this->ajaxReturn($res);
    }
    public function getKeywordsInfo() {
        $id = I('id', 0, 'intval');
        if (!$id) {
            $this->ajaxReturn(0);
        }

        $config['fields'] = 'rta_pid';
        $config['where']['rta_id'] = $id;
        $rta_pid = D('ResourceTag')->getOne($config);
        $this->ajaxReturn($rta_pid);
    }

    // 获取知识点
    public function getKnowledge() {
        $config['fields'] = 'kp_id,kp_title';
        $config['where']['kp_pid'] = I('pid', 0, 'intval');
        $config['where']['kp_subject'] = I('subject', 0, 'intval');
        $config['p'] = I('p', 1, 'intval');
        $res = D('KnowledgePoints')->getListByPage($config);
        $this->ajaxReturn($res);
    }
    public function getKnowledgeInfo() {
        $id = I('id', 0, 'intval');
        if (!$id) {
            $this->ajaxReturn(0);
        }

        $config['fields'] = 'kp_pid';
        $config['where']['kp_id'] = $id;
        $kp_pid = D('KnowledgePoints')->getOne($config);
        $this->ajaxReturn($kp_pid);
    }

    // 获取课文目录
    public function getDirectory() {
        $d_pid = I('pid', 0, 'intval');
        $config['fields'] = 'd_id,d_title';
        if ($d_pid) {
            $config['where']['d_pid'] = $d_pid;
        } else {
            $config['where']['d_level'] = 5;
        }
        $config['where']['d_school_type'] = I('school_type', 0, 'intval');
        $config['where']['d_grade'] = I('grade', 0, 'intval');
        $config['where']['d_subject'] = I('subject', 0, 'intval');
        $config['where']['d_version'] = I('version', 0, 'intval');
        $config['where']['d_semester'] = I('semester', 0, 'intval');
        $config['p'] = I('p', 1, 'intval');
        $res = D('Directory')->getListByPage($config);
        $this->ajaxReturn($res);
    }
    public function getDirectoryInfo() {
        $id = I('id', 0, 'intval');
        if (!$id) {
            $this->ajaxReturn(0);
        }

        $config['fields'] = 'd_pid';
        $config['where']['d_id'] = $id;
        $d_pid = D('Directory')->getOne($config);
        $this->ajaxReturn($d_pid);
    }

    // 获取地区
    public function getRegion() {
        $region = loadCache('region');
        $this->ajaxReturn($region[I('id', 0, 'intval')]);
    }

    public function insert() {
        
        $result = D('Resource')->insert();

        if (false === $result) {
            $this->error(D('Resource')->getError());
        }
        
        $this->show(L('OPERATION'));
    }

    public function delete() {

        $config['where']['res_id'] = array('in', I('id', 0, 'strval'));
        // 淘汰
        $this->show(D('Resource')->elimination($config), L('DELETE'));
    }

    public function upload() {
        
        // 上传
        $return = D('ResourceFile')->upload();

        echo json_encode($return);
    }
}