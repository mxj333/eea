<?php
namespace Reagional\Controller;
class SchoolController extends ReagionalController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 's_id', 'percent' => '5', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 's_title', 'percent' => '20', 'title' => L('TITLE'));
        $tplList[] = array('id' => 're_title', 'percent' => '15', 'title' => L('REGION'));
        $tplList[] = array('id' => 's_type', 'percent' => '15', 'title' => L('TYPE'));
        $tplList[] = array('id' => 's_divide', 'percent' => '15', 'title' => L('DIVIDE'));
        $tplList[] = array('id' => 'me_nickname', 'percent' => '10', 'title' => L('NICKNAME'));
        $tplList['action'] = array('id' => 'action', 'percent' => '16', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '4', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '4', 'title' => L('DELETE'));
        $action[] = array('id' => 'president', 'percent' => '8', 'title' => L('PRESIDENT'));

        // 检索器
        $tag = loadCache('tag');
        $school_divide = explode(',', C('SCHOOL_DIVIDE'));
        unset($school_divide[0]);
        $search[] = array('title' => L('TITLE'), 'name' => 's_title');
        $search[] = array('title' => L('TYPE'), 'name' => 's_type', 'label' => 'select', 'data' => $tag[4], 'inline' => true);
        $search[] = array('title' => L('DIVIDE'), 'name' => 's_divide', 'label' => 'select', 'data' => $school_divide, 'inline' => true);
        $search[] = array('title' => L('NICKNAME'), 'name' => 'me_nickname');

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        
        $tag = reloadCache('tag');
        $school_divide = explode(',', C('SCHOOL_DIVIDE'));
        unset($school_divide[0]);

        // 显示字段
        $addList[] = array('name' => 's_title', 'title' => L('TITLE'));
        $addList[] = array('name' => 's_logo', 'title' => L('LOGO'), 'type' => 'file', 'class' => 'head', 'labelClass' => 'logo');
        $addList[] = array('name' => 'region', 'title' => L('REGION'), 'labelClass' => 'region_cascade');
        $addList[] = array('name' => 's_type', 'title' => L('TYPE'), 'label' => 'select', 'data' => $tag[4]);
        $addList[] = array('name' => 's_divide', 'title' => L('DIVIDE'), 'label' => 'select', 'data' => $school_divide);
        $addList[] = array('name' => 's_phone', 'title' => L('PHONE'));
        $addList[] = array('name' => 's_description', 'title' => L('DESCRIPTION'), 'label' => 'textarea');
        $addList[] = array('name' => 's_status', 'title' => L('STATUS'), 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));
        
        // 图片处理
        $file = D('School')->getLogo($result);
        if ($file) {
            $result['file']['s_logo'] = $file;
            $result['ext']['s_logo'] = C('DEFAULT_IMAGE_EXT');
        }

        return generationAddTpl($addList, 's_id', $title, $result);
    }

    public function edit() {
        parent::edit('s_id');
    }

    public function getRegion() {
        $region = loadCache('region');
        $this->ajaxReturn($region[I('id', 0, 'intval')]);
    }
    
    public function insert() {

        // 封面图
        if ($_FILES['s_logo']['size'] > 0) {
            $fields['s_logo'] = '@'.realpath($_FILES['s_logo']['tmp_name']).";type=".$_FILES['s_logo']['type'].";filename=".$_FILES['s_logo']['name'];
        }

        $apiFunction = intval($_POST['s_id']) ? 'edit' : 'add';

        $result = $this->apiReturnDeal(getApi($_POST, 'School', $apiFunction, 'json', $fields));

        $this->show($result);
    }

    public function delete() {
        
        $result = $this->apiReturnDeal(getApi(array('s_id' => strval(I('id'))), 'School', 'del'));

        $this->show($result);
    }

    // 指定校长
    public function user() {
        if ($_POST) {
            $saveData = array('president_id' => intval($_POST['me_id']), 's_id' => intval($_POST['s_id']));
            $data = getApi($saveData, 'School', 'user');
            
            echo json_encode($data);
        }
    }
}