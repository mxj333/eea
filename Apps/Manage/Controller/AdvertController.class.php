<?php
namespace Manage\Controller;
class AdvertController extends ManageController {
    public function insert() {

        // 日期转换
        $_POST['adv_start_time'] = strtotime($_POST['adv_start_time']);
        $_POST['adv_stop_time'] = strtotime($_POST['adv_stop_time']);
        $_POST['adv_sort'] = intval($_POST['adv_sort']) ? intval($_POST['adv_sort']) : 255;
        if ($_POST['adv_start_time'] > $_POST['adv_stop_time']) {
            $this->error(L('OFFLINE_TIME_GREATER_THAN_ONLINE_TIME'));
        }

        // 是否有上传
        if ($_FILES['file']['size'] > 0) {

            // 获取后缀
            $pathInfo = getPathInfo($_FILES['file']['name']);

            // 文件上传
            $config['exts'] = explode(',', strtolower(C('ALLOW_IMAGE_TYPE')));
            $config['savePath'] = C('ADVERT_PATH');
            $config['autoSub'] = true;
            $config['subName'] = array('date', 'Ymd');

            $file = parent::upload($_FILES['file'], $config);

            $savepath = explode('/', $file['savepath']);
            $_POST['adv_savepath'] = $savepath[1];
            $_POST['adv_savename'] = $file['savename'];
            $_POST['adv_ext'] = $pathInfo['ext'];
        }

        parent::insert();
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
        $action[] = array('id' => 'edit', 'percent' => '10', 'title' => L('EDIT'));

        // 检索器
        $search[] = array('title' => L('NAME'), 'name' => 'adv_title');

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {

        $advertPositionConfig['fields'] = 'ap_id,ap_title';
        $advertPosition = D('AdvertPosition')->getAll($advertPositionConfig);

        // 显示字段
        $addList[] = array('title' => L('NAME'), 'name' => 'adv_title', 'class' => 'w460', 'require' => true);
        $addList[] = array('title' => L('ADVERT_POSITION'), 'name' => 'ap_id', 'class' => 'w460', 'label' => 'select', 'data' => $advertPosition);
        $addList[] = array('title' => L('LINK_ADDRESS'), 'name' => 'adv_url', 'class' => 'w460');

        if ($result['adv_start_time']) {
            $result['adv_start_time'] = date('Y-m-d', $result['adv_start_time']);
        }

        if ($result['adv_stop_time']) {
            $result['adv_stop_time'] = date('Y-m-d', $result['adv_stop_time']);
        }

        $addList[] = array('title' => L('ONLINE_TIME'), 'name' => 'adv_start_time', 'class' => 'w460', 'event' => 'onClick', 'eventValue' => "WdatePicker({dateFmt:'yyyy-MM-dd',minDate:'#{%y}-{%M}-#{%d}'});", 'require' => true);
        $addList[] = array('title' => L('OFFLINE_TIME'), 'name' => 'adv_stop_time', 'class' => 'w460', 'event' => 'onClick', 'eventValue' => "WdatePicker({dateFmt:'yyyy-MM-dd',minDate:'#{%y}-{%M}-#{%d}'});", 'require' => true);
        $addList[] = array('title' => L('UPLOAD'), 'name' => 'file', 'type' => 'file', 'class' => 'w460');
        $addList[] = array('title' => L('ADVERT_PEOPLE'), 'name' => 'adv_people', 'class' => 'w460');
        $addList[] = array('title' => L('CELL_PHONE'), 'name' => 'adv_tel', 'class' => 'w460');
        $addList[] = array('title' => L('Email'), 'name' => 'adv_email', 'class' => 'w460');
        $addList[] = array('title' => L('WRITING_PROMPT'), 'name' => 'adv_reminds', 'class' => 'w460');

        if ($result['adv_savename']) {
            $file = C('UPLOADS_ROOT_PATH') . C('ADVERT_PATH') . strtolower($result['adv_savepath']) . '/' . $result['adv_savename'];
            if (file_exists($file)) {
                $result['file'] = turnTpl($file);
                $result['ext'] = $result['adv_ext'];
            }
        }

        return generationAddTpl($addList, 'adv_id', $title, $result);
    }

}