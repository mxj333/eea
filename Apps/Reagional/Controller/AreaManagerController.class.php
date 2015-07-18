<?php
namespace Reagional\Controller;
class AreaManagerController extends ReagionalController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'am_id', 'percent' => '10', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 're_title', 'percent' => '20', 'title' => L('REGION'), 'class' => 'showContent');
        $tplList[] = array('id' => 'aty_title', 'percent' => '20', 'title' => L('APP_TYPE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'me_nickname', 'percent' => '20', 'title' => L('NICKNAME'), 'class' => 'showContent');
        $tplList[] = array('id' => 'am_status', 'percent' => '10', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '10', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '5', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '5', 'title' => L('DELETE'));

        // 检索器
        $app_type = reloadCache('appType');
        $search[] = array('title' => L('APP_TYPE'), 'name' => 'aty_id', 'label' => 'select', 'data' => $app_type);

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    // 获取地区
    public function getRegion() {
        $region = reloadCache('region');
        $this->ajaxReturn($region[I('id', 0, 'intval')]);
    }

    public function getMember() {
        // 用户所属区域id
        $_POST['re_id'] = $_SESSION['re_id'];
        // 结果自动处理
        $_POST['is_deal_result'] = true;

        // api 请求数据
        $data = $this->apiReturnDeal(getApi($_POST, 'Member', 'lists'));
        
        echo json_encode($data);
    }

    public function addTpl($title = '', $result = array()) {

        $appCategoryData = reloadCache('appType');

        // 显示字段
        $addList[] = array('name' => 'aty_id', 'title' => L('APP_TYPE'), 'class' => 'w460', 'label' => 'select', 'data' => $appCategoryData);
        $addList[] = array('name' => 'region', 'title' => L('REGION'));
        $addList[] = array('name' => 'me_id', 'type' => 'hidden');
        $addList[] = array('name' => 'me_nickname', 'title' => L('NICKNAME'));
        $addList[] = array('name' => 'am_status', 'title' => L('STATUS'), 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));
        
        return generationAddTpl($addList, 'am_id', $title, $result);
    }

    public function edit() {
        parent::edit('am_id');
    }

    public function insert() {

        $selectRegion = I('re_id', '', 'strval');
        $currentRegion = session('re_id');
        if (substr($selectRegion, 0, strlen($currentRegion)) != $currentRegion) {
            $this->error(L('NO_RIGHT'));
        }

        $_POST['auth_id'] = $_POST['me_id'];
        $operation = I('am_id', 0, 'intval') ? 'edit' : 'add';

        $result = $this->apiReturnDeal(getApi($_POST, 'AreaManager', $operation));

        $this->show($result);
    }

    public function delete() {
        $am_id = I('id', 0, 'strval');

        $result = $this->apiReturnDeal(getApi(array('am_id' => $am_id), 'AreaManager', 'del'));

        $this->show($result);
    }
}