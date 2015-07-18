<?php
namespace Schoolback\Controller;
class AdvertController extends SchoolbackController {
    public function insert() {

        if (!intval($_POST['adv_id']) && $_FILES['file']['size'] <= 0) {
            $this->error('请选择上传文件');
            exit;
        }

        if ($_FILES['file']['size']) {
            $fields['file'] = '@'.realpath($_FILES['file']['tmp_name']).";type=".$_FILES['file']['type'].";filename=".$_FILES['file']['name'];
        }

        $apiFunction = intval($_POST['adv_id']) ? 'edit' : 'add';
        $_POST['s_id'] = session('s_id');

        $result = $this->apiReturnDeal(getApi($_POST, 'Advert', $apiFunction, 'json', $fields));

        $this->show($result);
    }

    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'adv_id', 'percent' => '6', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'adv_title', 'percent' => '14', 'title' => L('NAME'), 'class' => 'showContent');
        $tplList[] = array('id' => 'adv_people', 'percent' => '10', 'title' => L('ADVERT_PEOPLE'));
        $tplList[] = array('id' => 'adv_tel', 'percent' => '12', 'title' => L('CELL_PHONE'));
        $tplList[] = array('id' => 'adv_email', 'percent' => '14', 'title' => L('Email'), 'class' => 'showContent');
        $tplList[] = array('id' => 'adv_start_time', 'percent' => '12', 'title' => L('ONLINE_TIME'));
        $tplList[] = array('id' => 'adv_stop_time', 'percent' => '12', 'title' => L('OFFLINE_TIME'));
        $tplList['action'] = array('id' => 'action', 'percent' => '10', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '5', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '5', 'title' => L('DELETE'));

        // 检索器
        $search[] = array('title' => L('NAME'), 'name' => 'adv_title');

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {

        $advertPosition = $this->apiReturnDeal(getApi(array('type' => 2), 'Advert', 'getPosition'));

        // 显示字段
        $addList[] = array('title' => L('NAME'), 'name' => 'adv_title', 'class' => 'w460', 'require' => true);
        $addList[] = array('title' => L('ADVERT_POSITION'), 'name' => 'ap_id', 'class' => 'w460', 'label' => 'select', 'data' => $advertPosition, 'require' => true);
        $addList[] = array('title' => L('LINK_ADDRESS'), 'name' => 'adv_url', 'class' => 'w460', 'require' => true);

        if ($result['adv_start_time']) {
            $result['adv_start_time'] = date('Y-m-d', $result['adv_start_time']);
        }

        if ($result['adv_stop_time']) {
            $result['adv_stop_time'] = date('Y-m-d', $result['adv_stop_time']);
        }

        $addList[] = array('title' => L('ONLINE_TIME'), 'name' => 'adv_start_time', 'class' => 'w460', 'event' => 'onClick', 'eventValue' => "WdatePicker({dateFmt:'yyyy-MM-dd',minDate:'#{%y}-{%M}-#{%d}'});", 'require' => true);
        $addList[] = array('title' => L('OFFLINE_TIME'), 'name' => 'adv_stop_time', 'class' => 'w460', 'event' => 'onClick', 'eventValue' => "WdatePicker({dateFmt:'yyyy-MM-dd',minDate:'#{%y}-{%M}-#{%d}'});", 'require' => true);
        $addList[] = array('title' => L('UPLOAD'), 'name' => 'file', 'type' => 'file', 'class' => 'w460', 'require' => true);
        $addList[] = array('title' => L('ADVERT_PEOPLE'), 'name' => 'adv_people', 'class' => 'w460', 'require' => true);
        $addList[] = array('title' => L('CELL_PHONE'), 'name' => 'adv_tel', 'class' => 'w460', 'require' => true);
        $addList[] = array('title' => L('Email'), 'name' => 'adv_email', 'class' => 'w460');
        $addList[] = array('title' => L('WRITING_PROMPT'), 'name' => 'adv_reminds', 'class' => 'w460');

        if ($result['adv_image']) {
            $result['file'] = $result['adv_image'];
            $result['ext'] = $result['adv_ext'];
        }

        return generationAddTpl($addList, 'adv_id', $title, $result);
    }

    public function edit() {
        parent::edit('adv_id');
    }

    public function delete() {
        
        $result = $this->apiReturnDeal(getApi(array('adv_id' => strval(I('id'))), 'Advert', 'del'));

        $this->show($result);
    }

}