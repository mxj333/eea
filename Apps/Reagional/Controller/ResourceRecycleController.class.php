<?php
namespace Reagional\Controller;
class ResourceRecycleController extends ReagionalController {

    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'res_id', 'percent' => '5', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'res_title', 'percent' => '30', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'rc_id', 'percent' => '20', 'title' => L('CODE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'res_eliminated_time', 'percent' => '20', 'title' => L('DELETE_TIME'));
        $tplList['action'] = array('id' => 'action', 'percent' => '12', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'del', 'percent' => '4', 'title' => L('DELETE'));
        $action[] = array('id' => 'resume', 'percent' => '4', 'title' => L('RESUME'));
        $action[] = array('id' => 'shows', 'percent' => '4', 'title' => L('SHOWS'));

        // 检索器
        $search[] = array('title' => L('TITLE'), 'name' => 'res_title');
        $search[] = array('title' => L('DELETER'), 'name' => 'deleter', 'inline' => true);
        $search[] = array('title' => L('CODE'), 'name' => 'rc_id', 'label' => 'select', 'data' => reloadCache('resourceCategory'), 'inline' => true);
        $search[] = array('title' => L('STARTTIME'), 'name' => 'deleted_starttime', 'event' => 'onClick', 'eventValue' => "WdatePicker();",);
        $search[] = array('title' => L('ENDTIME'), 'name' => 'deleted_endtime', 'event' => 'onClick', 'eventValue' => "WdatePicker();", 'inline' => true);

        // 工具
        $tools = array('add', 'edit', 'del', 'resume');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    // 列表
    public function lists() {
        // 用户所属区域id
        $_POST['re_id'] = $_SESSION['re_id'];
        // 结果自动处理
        $_POST['is_deal_result'] = true;
        // 资源审核列表
        $_POST['type'] = 3;
        // 所属平台 （区域后台）
        $_POST['belong'] = 2;

        if ($_POST['deleted_starttime']) {
            $_POST['deleted_starttime'] = strtotime($_POST['deleted_starttime']);
        }
        if ($_POST['deleted_endtime']) {
            $_POST['deleted_endtime'] = strtotime($_POST['deleted_endtime']);
        }

        // api 请求数据
        $data = $this->apiReturnDeal(getApi($_POST, 'Resource', 'lists'));
        
        echo json_encode($data);
    }

    public function delete() {
        $config['res_id'] = I('request.id', '', 'strval');
        // 所属平台 （区域后台）
        $config['belong'] = 2;

        // api 请求 删除
        $data = $this->apiReturnDeal(getApi($config, 'ResourceRecycle', 'del'));

        // 提示
        $this->show($data);
    }

    public function resume() {
        $config['res_id'] = I('request.id', '', 'strval');
        // 所属平台 （区域后台）
        $config['belong'] = 2;

        // api 请求 删除
        $data = $this->apiReturnDeal(getApi($config, 'ResourceRecycle', 'resume'));

        // 提示
        $this->show($data);
    }

    // 展示
    public function shows() {
        $tag = loadCache('tag');

        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/resourceAdd.css'));
        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/resourceEdit.js'));

        $this->assign('category', loadCache('resourceCategory'));
        $this->assign('learner', $tag[2]);
        $this->assign('educational', $tag[3]);
        $this->assign('school_type', $tag[4]);
        $this->assign('subject', $tag[5]);
        $this->assign('version', $tag[6]);
        $this->assign('grade', $tag[7]);
        $this->assign('semester', $tag[8]);

        $vo = $this->apiReturnDeal(getApi(array('res_id' => intval(I('request.id')), 'belong' => 2), 'Resource', 'shows'));
        foreach ($vo as &$value) {
            $value = stripFilter($value);
        }
        
        $vo['res_issused'] = $vo['res_issused'] ? date('Y-m-d', $vo['res_issused']) : 0;
        $vo['res_valid'] = $vo['res_valid'] ? date('Y-m-d', $vo['res_valid']) : 0;
        $vo['res_avaliable'] = $vo['res_avaliable'] ? date('Y-m-d', $vo['res_avaliable']) : 0;
        
        // 资源文件
        if (intval($vo['rf_id'])) {
            $file_info = $this->apiReturnDeal(getApi(array('rf_id' => intval($vo['rf_id'])), 'Resource', 'getFile'));
            $vo = array_merge($vo, (array)$file_info);
        }

        $this->assign('vo', $vo);
        $this->display('Resource/edit');
    }
}