<?php
namespace Schoolback\Controller;
class SchoolLeaderController extends SchoolbackController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'sl_id', 'percent' => '5', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'me_nickname', 'percent' => '25', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'sl_type', 'percent' => '25', 'title' => L('TYPE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'sl_status', 'percent' => '25', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '10', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '4', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '4', 'title' => L('DELETE'));

        // 检索器
        $search[] = array('title' => L('TITLE'), 'name' => 'me_nickname');
        $search[] = array('title' => L('TYPE'), 'name' => 'sl_type', 'inline' => true);

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        
        // 显示字段
        $addList[] = array('name' => 'me_nickname', 'title' => L('TITLE'));
        $addList[] = array('name' => 'me_id', 'type' => 'hidden');
        $addList[] = array('name' => 's_id', 'type' => 'hidden');
        $addList[] = array('name' => 'sl_logo', 'title' => L('LOGO'), 'type' => 'file', 'class' => 'head', 'labelClass' => 'logo');
        $addList[] = array('name' => 'sl_type', 'title' => L('TYPE'));
        $addList[] = array('name' => 'sl_description', 'title' => L('DESCRIPTION'), 'label' => 'textarea');
        $addList[] = array('name' => 'sl_sort', 'title' => L('SORT'));
        $addList[] = array('name' => 'sl_status', 'title' => L('STATUS'), 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));
        
        if (!$result) {
            // 默认值
            $result['sl_sort'] = 255;
            $result['s_id'] = session('s_id');
        }

        // 图片处理
        $file = D('SchoolLeader')->getLogo($result);
        if ($file) {
            $result['file']['sl_logo'] = $file;
            $result['ext']['sl_logo'] = C('DEFAULT_IMAGE_EXT');
        }

        return generationAddTpl($addList, 'sl_id', $title, $result);
    }

    public function edit() {
        parent::edit('sl_id');
    }

    public function insert() {

        // 图
        if ($_FILES['sl_logo']['size'] > 0) {
            $fields['sl_logo'] = '@'.realpath($_FILES['sl_logo']['tmp_name']).";type=".$_FILES['sl_logo']['type'].";filename=".$_FILES['sl_logo']['name'];
        }

        $apiFunction = intval($_POST['sl_id']) ? 'edit' : 'add';
        
        $_POST['s_id'] = session('s_id');
        $_POST['auth_id'] = $_POST['me_id'];

        $result = $this->apiReturnDeal(getApi($_POST, 'SchoolLeader', $apiFunction, 'json', $fields));

        $this->show($result);
    }

    public function delete() {
        
        $result = $this->apiReturnDeal(getApi(array('sl_id' => strval(I('id'))), 'SchoolLeader', 'del'));

        $this->show($result);
    }
}