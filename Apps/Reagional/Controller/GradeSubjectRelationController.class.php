<?php
namespace Reagional\Controller;
class GradeSubjectRelationController extends ReagionalController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'gsr_id', 'percent' => '5', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'gsr_school_type', 'percent' => '15', 'title' => L('SCHOOL_TYPE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'gsr_grade', 'percent' => '15', 'title' => L('GRADE'));
        $tplList[] = array('id' => 'gsr_subject', 'percent' => '50', 'title' => L('SUBJECT'));
        $tplList['action'] = array('id' => 'action', 'percent' => '12', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '6', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '6', 'title' => L('DELETE'));

        // 检索器
        $tag = reloadCache('tag');
        $search[] = array('title' => L('SCHOOL_TYPE'), 'name' => 'gsr_school_type', 'label' => 'select', 'data' => $tag[4]);
        $search[] = array('title' => L('GRADE'), 'name' => 'gsr_grade', 'label' => 'select', 'data' => $tag[7], 'inline' => true);

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    // 列表
    public function lists() {
        // 用户所属区域id
        $_POST['re_id'] = $_SESSION['re_id'];
        // 结果自动处理
        $_POST['is_deal_result'] = true;

        // api 请求数据
        $data = $this->apiReturnDeal(getApi($_POST, 'GradeSubjectRelation', 'lists'));
        
        echo json_encode($data);
    }
    
    public function add() {
        if ($_POST) {
            $this->insert();
        } else {
            $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Add.js'));
            $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . 'Add.css'));

            $tag = reloadCache('tag');
            $this->assign('school_type', $tag[4]);
            $this->assign('grade', $tag[7]);
            $this->assign('subject', $tag[5]);
            $this->assign('status', array(1 => L('ENABLE'), 9 => L('DISABLE')));
            $this->display();
        }
    }

    // 默认编辑操作
    public function edit() {
        if ($_POST) {
            $this->insert();
        } else {
            // 获取数据
            $config['gsr_id'] = intval(I('request.id'));
            $vo = $this->apiReturnDeal(getApi($config, 'GradeSubjectRelation', 'shows'));
            foreach ($vo as &$value) {
                $value = stripFilter($value);
            }

            $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Add.js'));
            $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . 'Add.css'));

            $this->assign('vo', $vo);
            $tag = reloadCache('tag');
            $this->assign('school_type', $tag[4]);
            $this->assign('grade', $tag[7]);
            $this->assign('subject', $tag[5]);
            $this->assign('status', array(1 => L('ENABLE'), 9 => L('DISABLE')));
            $this->display('add');
        }
    }

    public function insert() {

        $apiFunction = intval($_POST['gsr_id']) ? 'edit' : 'add';

        $_POST['re_id'] = session('re_id');
        $_POST['re_title'] = session('re_title');

        $result = $this->apiReturnDeal(getApi($_POST, 'GradeSubjectRelation', $apiFunction));

        reloadCache('gradeSubjectRelation', true);

        $this->show($result);
    }

    public function delete() {
        
        $result = $this->apiReturnDeal(getApi(array('gsr_id' => strval(I('id'))), 'GradeSubjectRelation', 'del'));

        reloadCache('gradeSubjectRelation', true);

        $this->show($result);
    }
}