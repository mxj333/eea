<?php
namespace Schoolback\Controller;
class ClassExcellentController extends SchoolbackController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'ce_id', 'percent' => '5', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'c_title', 'percent' => '25', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'ce_type', 'percent' => '25', 'title' => L('TYPE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'ce_status', 'percent' => '25', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '10', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '4', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '4', 'title' => L('DELETE'));

        // 检索器
        $type = explode(',', C('CLASS_EXCELLENT_TYPE'));
        unset($type[0]);
        $search[] = array('title' => L('TITLE'), 'name' => 'c_title');
        $search[] = array('title' => L('TYPE'), 'name' => 'ce_type', 'label' => 'select', 'data' => $type, 'inline' => true);

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        
        $type = explode(',', C('CLASS_EXCELLENT_TYPE'));
        unset($type[0]);

        // 显示字段
        $addList[] = array('name' => 'c_title', 'title' => L('TITLE'));
        $addList[] = array('name' => 'c_id', 'type' => 'hidden');
        $addList[] = array('name' => 's_id', 'type' => 'hidden');
        $addList[] = array('name' => 'ce_logo', 'title' => L('LOGO'), 'type' => 'file', 'class' => 'head', 'labelClass' => 'logo');
        $addList[] = array('name' => 'ce_description', 'title' => L('DESCRIPTION'), 'label' => 'textarea');
        $addList[] = array('name' => 'ce_sort', 'title' => L('SORT'));
        $addList[] = array('name' => 'ce_type', 'title' => L('TYPE'), 'label' => 'select', 'data' => $type);
        $addList[] = array('name' => 'ce_status', 'title' => L('STATUS'), 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));
        
        if (!$result) {
            // 默认值
            $result['ce_sort'] = 255;
            $result['s_id'] = session('s_id');
        }

        // 图片处理
        $file = D('ClassExcellent')->getLogo($result);
        if ($file) {
            $result['file']['ce_logo'] = $file;
            $result['ext']['ce_logo'] = C('DEFAULT_IMAGE_EXT');
        }

        return generationAddTpl($addList, 'ce_id', $title, $result);
    }

    public function edit() {
        parent::edit('ce_id');
    }

    public function insert() {

        // 封面图
        if ($_FILES['ce_logo']['size'] > 0) {
            $fields['ce_logo'] = '@'.realpath($_FILES['ce_logo']['tmp_name']).";type=".$_FILES['ce_logo']['type'].";filename=".$_FILES['ce_logo']['name'];
        }

        $apiFunction = intval($_POST['ce_id']) ? 'edit' : 'add';
        
        $_POST['s_id'] = session('s_id');

        $result = $this->apiReturnDeal(getApi($_POST, 'ClassExcellent', $apiFunction, 'json', $fields));

        $this->show($result);
    }

    public function delete() {
        
        $result = $this->apiReturnDeal(getApi(array('ce_id' => strval(I('id'))), 'ClassExcellent', 'del'));

        $this->show($result);
    }
}