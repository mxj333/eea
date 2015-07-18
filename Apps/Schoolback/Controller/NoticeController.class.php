<?php
namespace Schoolback\Controller;
class NoticeController extends SchoolbackController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'no_id', 'percent' => '10', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'no_title', 'percent' => '25', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'no_starttime', 'percent' => '15', 'title' => L('STARTTIME'));
        $tplList[] = array('id' => 'no_endtime', 'percent' => '15', 'title' => L('ENDTIME'));
        $tplList[] = array('id' => 'no_sort', 'percent' => '15', 'title' => L('SORT'));
        $tplList['action'] = array('id' => 'action', 'percent' => '10', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '4', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '4', 'title' => L('DELETE'));

        // 检索器
        $search[] = array('title' => L('TITLE'), 'name' => 'no_title');

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        // 显示字段
        $addList[] = array('name' => 'no_title', 'title' => L('TITLE'), 'class' => 'w460');
        $addList[] = array('name' => 'no_url', 'title' => L('URL'), 'class' => 'w460');
        $addList[] = array('name' => 'no_starttime', 'title' => L('STARTTIME'), 'labelClass' => 'Wdate', 'event' => 'onClick', 'eventValue' => "WdatePicker();");
        $addList[] = array('name' => 'no_endtime', 'title' => L('ENDTIME'), 'labelClass' => 'Wdate', 'event' => 'onClick', 'eventValue' => "WdatePicker();", 'inline' => true);
        $addList[] = array('name' => 'no_sort', 'title' => L('SORT'), 'class' => 'w460');
        $addList[] = array('name' => 'no_content', 'title' => L('CONTENT'), 'class' => 'w460', 'label' => 'textarea');

        if (!$result) {
            $result['no_sort'] = 255;
        }
        return generationAddTpl($addList, 'no_id', $title, $result);
    }

    public function edit() {
        parent::edit('no_id');
    }

    public function delete() {
        parent::delete('no_id');
    }

    public function insert() {
        $apiFunction = intval($_POST['no_id']) ? 'edit' : 'add';

        $_POST['s_id'] = session('s_id');

        // 处理接口返回信息
        $data = $this->apiReturnDeal(getApi($_POST, 'Notice', $apiFunction));

        // 提示跳转
        $this->show($data);
    }
}