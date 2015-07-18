<?php
namespace Schoolback\Controller;
class TermYearController extends SchoolbackController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'ty_id', 'percent' => '10', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'ty_year', 'percent' => '10', 'title' => L('YEAR'), 'class' => 'showContent');
        $tplList[] = array('id' => 'ty_last_starttime', 'percent' => '15', 'title' => L('LAST_START'), 'class' => 'showContent');
        $tplList[] = array('id' => 'ty_last_endtime', 'percent' => '15', 'title' => L('LAST_END'), 'class' => 'showContent');
        $tplList[] = array('id' => 'ty_next_starttime', 'percent' => '15', 'title' => L('NEXT_START'), 'class' => 'showContent');
        $tplList[] = array('id' => 'ty_next_endtime', 'percent' => '15', 'title' => L('NEXT_END'), 'class' => 'showContent');
        $tplList['action'] = array('id' => 'action', 'percent' => '10', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '4', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '4', 'title' => L('DELETE'));

        // 检索器
        $ty_year = range(2005, date('Y') + 10);
        $search[] = array('title' => L('YEAR'), 'name' => 'ty_year', 'label' => 'select', 'data' => array_combine($ty_year, $ty_year));

        // 工具
        $tools = array('add', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    // 列表
    public function lists() {
        // 用户所属区域id
        $_POST['s_id'] = $_SESSION['s_id'];
        // 结果自动处理
        $_POST['is_deal_result'] = true;
        // 所属平台 （学校后台）
        $_POST['belong'] = 3;

        // api 请求数据
        $data = $this->apiReturnDeal(getApi($_POST, CONTROLLER_NAME, 'lists'));
        
        echo json_encode($data);
    }

    public function addTpl($title = '', $result = array()) {
        // 显示字段
        $ty_year = range(2005, date('Y') + 10);
        $addList[] = array('name' => 'ty_year', 'title' => L('YEAR'), 'label' => 'select', 'data' => array_combine($ty_year, $ty_year), 'default' => array('' => L('PLEASE_SELECT')));
        $addList[] = array('name' => 'ty_last_starttime', 'title' => L('LAST_START'), 'labelClass' => 'Wdate', 'event' => 'onClick', 'eventValue' => "WdatePicker();");
        $addList[] = array('name' => 'ty_last_endtime', 'title' => L('LAST_END'), 'labelClass' => 'Wdate', 'event' => 'onClick', 'eventValue' => "WdatePicker();");
        $addList[] = array('name' => 'ty_next_starttime', 'title' => L('NEXT_START'), 'labelClass' => 'Wdate', 'event' => 'onClick', 'eventValue' => "WdatePicker();");
        $addList[] = array('name' => 'ty_next_endtime', 'title' => L('NEXT_END'), 'labelClass' => 'Wdate', 'event' => 'onClick', 'eventValue' => "WdatePicker();");
        
        return generationAddTpl($addList, 'ty_id', $title, $result);
    }

    // 默认删除操作
    public function delete() {
        parent::delete('ty_id');
    }

    // 默认删除操作
    public function edit() {
        parent::edit('ty_id');
    }

    public function insert() {
        $_POST['s_id'] = session('s_id');
        parent::insert($_POST);
    }
}