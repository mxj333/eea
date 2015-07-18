<?php
namespace Reagional\Controller;
class DirectoryController extends ReagionalController {

    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'd_id', 'percent' => '4', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'd_version', 'percent' => '10', 'title' => L('VERSION'));
        $tplList[] = array('id' => 'd_school_type', 'percent' => '10', 'title' => L('SCHOOL_TYPE'));
        $tplList[] = array('id' => 'd_grade', 'percent' => '10', 'title' => L('GRADE'));
        $tplList[] = array('id' => 'd_semester', 'percent' => '10', 'title' => L('SEMESTER'));
        $tplList[] = array('id' => 'd_subject', 'percent' => '10', 'title' => L('SUBJECT'));
        $tplList[] = array('id' => 'd_title', 'percent' => '14', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList['action'] = array('id' => 'action', 'percent' => '28', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '4', 'title' => L('EDIT'));
        $action[] = array('id' => 'child', 'percent' => '6', 'title' => L('SUB_SECTION'));
        $action[] = array('id' => 'shows', 'percent' => '8', 'title' => L('SHOWS'));
        $action[] = array('id' => 'user', 'percent' => '6', 'title' => L('USER'));
        $action[] = array('id' => 'del', 'percent' => '4', 'title' => L('DELETE'));

        // 检索器
        $search[] = array('title' => L('TITLE'), 'name' => 'd_title');
        $search[] = array('title' => '', 'inline' => 1, 'display' => 'none', 'name' => 'd_pid');

        // 工具
        $tools = array('add', 'edit', 'del', 'return');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function insert() {
        // 默认
        $_POST['re_id'] = session('re_id');
        $_POST['re_title'] = session('re_title');
        $option = $_POST['d_id'] ? 'edit' : 'add';

        $res = $this->apiReturnDeal(getApi($_POST, 'Directory', $option));
        $this->assign('waitSecond', 0);
        $this->show($res, '', __CONTROLLER__ . '/index/d_pid/' . $_POST['d_pid']);
    }

    public function delete() {
        $config['d_id'] = I('id', 0, 'strval');
        $res = $this->apiReturnDeal(getApi($config, 'Directory', 'del'));
        $this->assign('waitSecond', 0);
        $this->show($res, '', __CONTROLLER__ . '/index/d_pid/' . $_POST['d_pid']);
    }

    public function edit() {
        parent::edit('d_id');
    }

    public function addTpl($title = '', $result = array()) {

        $id = intval($_REQUEST['d_id']);
        if (isset($_GET['d_id'])) {
            $result['d_pid'] = $id;
            $result['d_level'] = 0;
        }

        if ($id) {
            $directoryConfig['d_id'] = $id;
            $d_info = $this->apiReturnDeal(getApi($directoryConfig, 'Directory', 'shows'));
            $result['d_level'] = intval($d_info['d_level']) + 1;
            $result['d_school_type'] = $d_info['d_school_type'];
            $result['d_subject'] = $d_info['d_subject'];
            $result['d_version'] = $d_info['d_version'];
            $result['d_grade'] = $d_info['d_grade'];
            $result['d_semester'] = $d_info['d_semester'];
        }

        // 默认值
        $result['d_sort'] = $result['d_sort'] ? intval($result['d_sort']) : 255;

        $tag = reloadCache('tag');
        $addList[] = array('title' => L('CODE'), 'name' => 'd_code', 'class' => 'w460');
        switch (intval($result['d_level'])) {
            case 0:
                $addList[] = array('title' => L('VERSION'), 'name' => 'd_version', 'label' => 'select', 'data' => $tag[6]);
                break;
            case 1:
                $addList[] = array('title' => L('SCHOOL_TYPE'), 'name' => 'd_school_type', 'label' => 'select', 'data' => $tag[4]);
                break;
            case 2:
                $addList[] = array('title' => L('GRADE'), 'name' => 'd_grade', 'label' => 'select', 'data' => $tag[7]);
                break;
            case 3:
                $addList[] = array('title' => L('SEMESTER'), 'name' => 'd_semester', 'label' => 'select', 'data' => $tag[8]);
                break;
            case 4:
                $addList[] = array('title' => L('SUBJECT'), 'name' => 'd_subject', 'label' => 'select', 'data' => $tag[5]);
                break;
            default:
                $addList[] = array('title' => L('TITLE'), 'name' => 'd_title', 'class' => 'w460');
                $addList[] = array('title' => L('SORT'), 'name' => 'd_sort', 'class' => 'w460');
                $addList[] = array('title' => L('STATUS'), 'name' => 'd_status', 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));
        }

        // 显示字段
        $addList[] = array('title' => '', 'name' => 'd_pid', 'type' => 'hidden');
        $addList[] = array('title' => '', 'name' => 'd_level', 'type' => 'hidden');
        return generationAddTpl($addList, 'd_id', $title, $result);
    }

    // 关联知识点
    public function user() {
        if ($_POST) {

            $res = $this->apiReturnDeal(getApi($_POST, 'Directory', 'addRelation'));
            $this->show($res, '', __CONTROLLER__ . '/user/id/' . I('d_id'));
        } else {
            $vo = $this->apiReturnDeal(getApi(array('d_id' => I('id')), 'Directory', 'shows'));
            $this->assign('vo', $vo);
            // 知识点列表
            $config['fields'] = 'kp_id,kp_title';
            $config['kp_subject'] = intval($vo['d_subject']);
            $kp_list = $this->apiReturnDeal(getApi($config, 'KnowledgePoints', 'lists'));
            $this->assign('kp_list', $kp_list['list']);
            // 关联知识点
            $kp_relation = $this->apiReturnDeal(getApi(array('d_id' => I('id')), 'Directory', 'getRelation'));
            $this->assign('kp_relation', $kp_relation);
            $this->display();
        }
    }

    //  目录调整
    public function shows() {

        if ($_POST) {

            $res = $this->apiReturnDeal(getApi($_POST, 'Directory', 'sync'));
            $this->show($res);
        } else {

            $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Shows.js'));
            $vo = $this->apiReturnDeal(getApi(array('d_id' => I('id')), 'Directory', 'shows'));
            $this->assign('vo', $vo);
            $tag = loadCache('tag');
            $this->assign('tag', $tag);
            $config['d_level'] = 0;
            $config['re_id'] = session('re_id');
            $config['fields'] = 'd_id,d_version';
            $config['is_page'] = false;
            $version = $this->apiReturnDeal(getApi($config, 'Directory', 'lists'));
            $this->assign('version', $version['list']);
            $this->display();
        }
    }

    // 获取课文目录
    public function getDirectory() {
        $tag = loadCache('tag');
        $level = I('level', 0, 'intval');
        $config = array();

        switch ($level) {
            case 1:
                $config['fields'] = 'd_id,d_school_type';
                $title = $tag[4];
                break;
            case 2:
                $config['fields'] = 'd_id,d_grade';
                $title = $tag[7];
                break;
            case 3:
                $config['fields'] = 'd_id,d_semester';
                $title = $tag[8];
                break;
            case 4:
                $config['fields'] = 'd_id,d_subject';
                $title = $tag[5];
                break;
            default:
                
        }
        
        if ($config) {
            $config['d_level'] = $level;
            $config['d_pid'] = I('value', 0, 'intval');
            $config['re_id'] = session('re_id');
            $config['is_page'] = false;
            $res = $this->apiReturnDeal(getApi($config, 'Directory', 'lists'));
            foreach ($res['list'] as $key =>$val) {
                $res['list'][$key] = $title[$val];
            }
        }
        $this->ajaxReturn($res['list']);
    }

    // 获取课文目录title
    public function getDirectoryTitle() {
        $condition['fields'] = 'd_version,d_school_type,d_grade,d_semester,d_subject';
        $condition['d_id'] = I('subject', 0, 'intval');
        $where = $this->apiReturnDeal(getApi($condition, 'Directory', 'shows'));
        if ($where) {
            $where['d_title'] = I('title');
            $config = $where;
            $config['fields'] = 'd_id,d_title';
            $config['re_id'] = session('re_id');
            $config['is_page'] = false;
            $res = $this->apiReturnDeal(getApi($config, 'Directory', 'lists'));
        }
        
        $this->ajaxReturn($res['list']);
    }

    // 目录导出
    public function createExcel() {
        //$config['title'] = '目录导出excel文件';
        $config['re_id'] = session('re_id');
        $config['type'] = 'excel';
        $res = $this->apiReturnDeal(getApi($config, 'Directory', 'download'));
        $this->show($res);
    }

    // 生成word文件
    public function createWord() {
        //$config['title'] = '目录导出word文件';
        $config['re_id'] = session('re_id');
        $config['type'] = 'word';
        $res = $this->apiReturnDeal(getApi($config, 'Directory', 'download'));
        $this->show($res);
    }

    // 文件生成时间
    public function makeTime() {
        $type = I('type', 0 ,'intval');
        if ($type == 1) {
            $last_time = filemtime(C('UPLOADS_ROOT_PATH') . C('DIRECTORY_EXPORT_PATH') . 'directory_region' . str_replace('-', '', session('re_id')) . '.doc');
        } else {
            $last_time = filemtime(C('UPLOADS_ROOT_PATH') . C('DIRECTORY_EXPORT_PATH') . 'directory_region' . str_replace('-', '', session('re_id')) . '.xls');
        }
        echo json_encode($last_time);
    }

    // 下载word文件
    public function download() {
        $type = I('type', 0 ,'intval');
        if ($type == 1) {
            download(C('UPLOADS_ROOT_PATH') . C('DIRECTORY_EXPORT_PATH') . 'directory_region' . str_replace('-', '', session('re_id')) . '.doc');
        } else {
            download(C('UPLOADS_ROOT_PATH') . C('DIRECTORY_EXPORT_PATH') . 'directory_region' . str_replace('-', '', session('re_id')) . '.xls');
        }
    }
}