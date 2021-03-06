<?php
namespace Schoolback\Controller;
class TeacherExcellentController extends SchoolbackController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'tex_id', 'percent' => '5', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'me_nickname', 'percent' => '25', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'tex_type', 'percent' => '25', 'title' => L('TYPE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'tex_status', 'percent' => '25', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '10', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '4', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '4', 'title' => L('DELETE'));

        // 检索器
        $type = explode(',', C('TEACHER_EXCELLENT_TYPE'));
        unset($type[0]);
        $search[] = array('title' => L('TITLE'), 'name' => 'me_nickname');
        $search[] = array('title' => L('TYPE'), 'name' => 'tex_type', 'label' => 'select', 'data' => $type, 'inline' => true);

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        
        $type = explode(',', C('TEACHER_EXCELLENT_TYPE'));
        unset($type[0]);

        // 显示字段
        $addList[] = array('name' => 'me_nickname', 'title' => L('TITLE'));
        $addList[] = array('name' => 'me_id', 'type' => 'hidden');
        $addList[] = array('name' => 's_id', 'type' => 'hidden');
        $addList[] = array('name' => 'tex_logo', 'title' => L('LOGO'), 'type' => 'file', 'class' => 'head', 'labelClass' => 'logo');
        $addList[] = array('name' => 'tex_description', 'title' => L('DESCRIPTION'), 'label' => 'textarea');
        $addList[] = array('name' => 'tex_sort', 'title' => L('SORT'));
        $addList[] = array('name' => 'tex_type', 'title' => L('TYPE'), 'label' => 'select', 'data' => $type);
        $addList[] = array('name' => 'tex_status', 'title' => L('STATUS'), 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));
        
        if (!$result) {
            // 默认值
            $result['tex_sort'] = 255;
            $result['s_id'] = session('s_id');
        }

        // 图片处理
        $file = D('TeacherExcellent')->getLogo($result);
        if ($file) {
            $result['file']['tex_logo'] = $file;
            $result['ext']['tex_logo'] = C('DEFAULT_IMAGE_EXT');
        }

        return generationAddTpl($addList, 'tex_id', $title, $result);
    }

    public function edit() {
        parent::edit('tex_id');
    }

    public function insert() {

        // 封面图
        if ($_FILES['tex_logo']['size'] > 0) {
            $fields['tex_logo'] = '@'.realpath($_FILES['tex_logo']['tmp_name']).";type=".$_FILES['tex_logo']['type'].";filename=".$_FILES['tex_logo']['name'];
        }

        $apiFunction = intval($_POST['tex_id']) ? 'edit' : 'add';
        
        $_POST['s_id'] = session('s_id');
        $_POST['auth_id'] = $_POST['me_id'];

        $result = $this->apiReturnDeal(getApi($_POST, 'TeacherExcellent', $apiFunction, 'json', $fields));

        $this->show($result);
    }

    public function delete() {
        
        $result = $this->apiReturnDeal(getApi(array('tex_id' => strval(I('id'))), 'TeacherExcellent', 'del'));

        $this->show($result);
    }
}