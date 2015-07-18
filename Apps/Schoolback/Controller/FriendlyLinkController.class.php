<?php
namespace Schoolback\Controller;
class FriendlyLinkController extends SchoolbackController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'fl_id', 'percent' => '10', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'fl_title', 'percent' => '30', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'fl_url', 'percent' => '30', 'title' => L('URL'), 'class' => 'showContent');
        $tplList[] = array('id' => 'fl_sort', 'percent' => '15', 'title' => L('SORT'));
        $tplList['action'] = array('id' => 'action', 'percent' => '10', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '4', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '4', 'title' => L('DELETE'));

        // 检索器
        $search[] = array('title' => L('TITLE'), 'name' => 'fl_title');

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        // 显示字段
        $addList[] = array('name' => 'fl_title', 'title' => L('TITLE'), 'class' => 'w460');
        $addList[] = array('name' => 'fl_url', 'title' => L('URL'), 'class' => 'w460');
        $addList[] = array('name' => 'fl_logo', 'title' => L('LOGO'), 'type' => 'file', 'class' => 'head', 'labelClass' => 'logo');
        $addList[] = array('name' => 'fl_sort', 'title' => L('SORT'), 'class' => 'w460');
        $addList[] = array('name' => 'fl_status', 'title' => L('STATUS'), 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));

        if (!$result) {
            $result['fl_sort'] = 255;
        }

        // 图片处理
        $file = D('FriendlyLink')->getLogo($result);
        if ($file) {
            $result['file']['fl_logo'] = $file;
            $result['ext']['fl_logo'] = C('DEFAULT_IMAGE_EXT');
        }
        return generationAddTpl($addList, 'fl_id', $title, $result);
    }

    public function edit() {
        parent::edit('fl_id');
    }

    public function delete() {
        parent::delete('fl_id');
    }

    public function insert() {
        // LOGO 图
        if ($_FILES['fl_logo']['size'] > 0) {
            $fields['fl_logo'] = '@'.realpath($_FILES['fl_logo']['tmp_name']).";type=".$_FILES['fl_logo']['type'].";filename=".$_FILES['fl_logo']['name'];
        }

        $apiFunction = intval($_POST['fl_id']) ? 'edit' : 'add';

        $_POST['s_id'] = session('s_id');

        $result = $this->apiReturnDeal(getApi($_POST, 'FriendlyLink', $apiFunction, 'json', $fields));

        $this->show($result);
    }
}