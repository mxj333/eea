<?php
namespace Schoolback\Controller;
class SchoolOrganizationController extends SchoolbackController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'so_id', 'percent' => '5', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'so_title', 'percent' => '25', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'so_type', 'percent' => '25', 'title' => L('TYPE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'so_status', 'percent' => '25', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '10', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '4', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '4', 'title' => L('DELETE'));

        // 检索器
        $search[] = array('title' => L('TITLE'), 'name' => 'me_nickname');
        $search[] = array('title' => L('TYPE'), 'name' => 'so_type', 'inline' => true);

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        
        // 显示字段
        $addList[] = array('name' => 'so_title', 'title' => L('TITLE'));
        $addList[] = array('name' => 'so_type', 'title' => L('TYPE'));
        $addList[] = array('name' => 'so_sort', 'title' => L('SORT'));
        $addList[] = array('name' => 'so_status', 'title' => L('STATUS'), 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));
        
        if (!$result) {
            // 默认值
            $result['so_sort'] = 255;
        }

        return generationAddTpl($addList, 'so_id', $title, $result);
    }

    public function edit() {
        parent::edit('so_id');
    }

    public function insert() {

        $apiFunction = intval($_POST['so_id']) ? 'edit' : 'add';
        
        $_POST['s_id'] = session('s_id');

        $result = $this->apiReturnDeal(getApi($_POST, 'SchoolOrganization', $apiFunction));

        $this->show($result);
    }

    public function delete() {
        
        $result = $this->apiReturnDeal(getApi(array('so_id' => strval(I('id'))), 'SchoolOrganization', 'del'));

        $this->show($result);
    }
}