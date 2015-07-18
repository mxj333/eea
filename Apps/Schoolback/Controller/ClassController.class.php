<?php
namespace Schoolback\Controller;
class ClassController extends SchoolbackController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'c_id', 'percent' => '5', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'c_title', 'percent' => '25', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'c_grade', 'percent' => '20', 'title' => L('GRADE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'me_nickname', 'percent' => '20', 'title' => L('NICKNAME'), 'class' => 'showContent');
        $tplList['action'] = array('id' => 'action', 'percent' => '26', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '4', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '4', 'title' => L('DELETE'));
        $action[] = array('id' => 'authorization', 'percent' => '6', 'title' => L('ADVISER'));
        $action[] = array('id' => 'shows', 'percent' => '8', 'title' => L('TEACHER'));
        $action[] = array('id' => 'user', 'percent' => '4', 'title' => L('STUDENT'));

        // 检索器
        $tag = reloadCache('tag');
        $search[] = array('title' => L('TITLE'), 'name' => 'c_title');
        $search[] = array('title' => L('GRADE'), 'name' => 'c_grade', 'label' => 'select', 'data' => $tag[7], 'inline' => true);

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        
        $tag = reloadCache('tag');

        // 显示字段
        $addList[] = array('name' => 'c_title', 'title' => L('TITLE'), 'require' => true);
        $addList[] = array('name' => 'c_logo', 'title' => L('LOGO'), 'type' => 'file', 'class' => 'head', 'labelClass' => 'logo');
        $addList[] = array('name' => 'c_grade', 'title' => L('GRADE'), 'label' => 'select', 'data' => $tag[7]);
        $addList[] = array('name' => 'c_description', 'title' => L('DESCRIPTION'), 'label' => 'textarea');
        $addList[] = array('name' => 'c_status', 'title' => L('STATUS'), 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));
        
        // 图片处理
        $file = D('Class')->getLogo($result);
        if ($file) {
            $result['file']['c_logo'] = $file;
            $result['ext']['c_logo'] = C('DEFAULT_IMAGE_EXT');
        }

        return generationAddTpl($addList, 'c_id', $title, $result);
    }

    public function edit() {
        parent::edit('c_id');
    }

    public function insert() {

        // 封面图
        if ($_FILES['c_logo']['size'] > 0) {
            $fields['c_logo'] = '@'.realpath($_FILES['c_logo']['tmp_name']).";type=".$_FILES['c_logo']['type'].";filename=".$_FILES['c_logo']['name'];
        }

        $apiFunction = intval($_POST['c_id']) ? 'edit' : 'add';
        
        $_POST['s_id'] = session('s_id');

        $result = $this->apiReturnDeal(getApi($_POST, 'Class', $apiFunction, 'json', $fields));

        $this->show($result);
    }

    public function delete() {
        
        $result = $this->apiReturnDeal(getApi(array('c_id' => strval(I('id'))), 'Class', 'del'));

        $this->show($result);
    }

    // 指定班主任
    public function authorization() {
        if ($_POST) {
            $saveData = array('auth_id' => intval($_POST['me_id']), 'c_id' => intval($_POST['c_id']));
            $data = getApi($saveData, 'Class', 'authorization');
            
            echo json_encode($data);
        }
    }

    // 任课教师
    public function shows() {
        if ($_POST) {
            $res = $this->apiReturnDeal(getApi($_POST, 'Class', 'publish'));
            $this->show($res, '', __CONTROLLER__ . '/Shows/id/' . intval($_POST['c_id']));
        } else {
            $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Shows.js'));
            $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . 'Shows.css'));

            // 获取班级详细信息
            $vo = $this->apiReturnDeal(getApi(array('c_id' => intval(I('id')), 'is_deal_result' => true), 'Class', 'shows'));
            // 获取任课关系信息
            $relation = $vo['teachers'];
            // 年级学科关系
            $gsr_list = reloadCache('gradeSubjectRelation');
            // 区域
            $re_id = $this->getSubjectRegion('-', session('s_re_id'), $gsr_list);
            // 学科
            $this->assign('gsRelation', $gsr_list[$re_id][$vo['c_grade']]);
            $this->assign('vo', $vo);
            $this->assign('relation', $relation);
            $tag = reloadCache('tag');
            $this->assign('grade', $tag[7]);
            $this->assign('subject', $tag[5]);
            $this->display();
        }
    }

    // 获取所属区域的学科
    public function getSubjectRegion($check, $string, $data) {
        if ($data[$string]) {
            return $string;
        }

        $list = explode($check, $string);
        array_pop($list);
        return $this->getSubjectRegion($check, implode($check, $list), $data);
    }

    // 班级学生
    public function user() {
        if ($_POST) {
            // 接口方法
            $operation = strval($_POST['operation']) == 'del' ? 'delUser' : 'user';
            // 请求接口
            $res = $this->apiReturnDeal(getApi($_POST, 'Class', $operation));
            // 返回值
            echo json_encode($res);
        } else {
            $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'User.js'));
            $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . 'User.css'));

            // 获取班级信息
            $vo = $this->apiReturnDeal(getApi(array('c_id' => intval(I('id')), 'is_deal_result' => true), 'Class', 'shows'));
            $this->assign('vo', $vo);

            // 班级学生
            $this->assign('student_list', $vo['students']);
            $this->display();
        }
    }
}