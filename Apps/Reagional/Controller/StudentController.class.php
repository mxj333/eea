<?php
namespace Reagional\Controller;
class StudentController extends ReagionalController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'me_id', 'percent' => '5', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'me_account', 'percent' => '15', 'title' => L('ACCOUNT'), 'class' => 'showContent');
        $tplList[] = array('id' => 'me_nickname', 'percent' => '15', 'title' => L('NICKNAME'));
        $tplList[] = array('id' => 'me_mobile', 'percent' => '15', 'title' => L('MOBILE'));
        $tplList[] = array('id' => 'me_phone', 'percent' => '15', 'title' => L('PHONE'));
        $tplList[] = array('id' => 'me_email', 'percent' => '15', 'title' => L('EMAIL'));
        $tplList['action'] = array('id' => 'action', 'percent' => '12', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '4', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '4', 'title' => L('DELETE'));
        $action[] = array('id' => 'shows', 'percent' => '4', 'title' => L('ARCHIVES'));
        $action[] = array('id' => 'resume', 'percent' => '4', 'title' => L('PARENT'));

        // 检索器
        $search[] = array('title' => L('NICKNAME'), 'name' => 'me_nickname');
        $search[] = array('title' => '', 'inline' => 1, 'display' => 'none', 'name' => 'me_id');

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    // 列表
    public function lists() {
        // 用户所属区域id
        $_POST['re_id'] = $_SESSION['re_id'];
        // 结果自动处理
        $_POST['is_deal_result'] = true;
        // 家长
        $_POST['me_type'] = 2;

        // api 请求数据
        $data = $this->apiReturnDeal(getApi($_POST, 'Member', 'lists'));
        
        echo json_encode($data);
    }

    public function addTpl($title = '', $result = array()) {
        // 用户属性
        $attribute = D('MemberAttribute')->getAll();
        
        // 显示字段
        $addList[] = array('name' => 'me_account', 'title' => L('ACCOUNT'), 'label' => 'div');
        $addList[] = array('name' => 'me_nickname', 'title' => L('NICKNAME'), 'class' => 'w460');
        $addList[] = array('name' => 'me_password', 'title' => L('PASSWORD'), 'type' => 'password', 'class' => 'w460');
        $addList[] = array('name' => 'me_validity', 'title' => L('VALIDITY'), 'class' => 'w460', 'event' => 'onClick', 'labelClass' => 'Wdate', 'eventValue' => "WdatePicker({dateFmt:'yyyy-MM-dd',minDate:'#{%y}-{%M}-#{%d+1}'});");
        $addList[] = array('name' => 'region', 'title' => L('REGION'));
        $addList[] = array('name' => 's_id', 'type' => 'hidden');
        $addList[] = array('name' => 's_title', 'title' => L('S_TITLE'));
        $addList[] = array('name' => 'me_avatar', 'title' => L('LOGO'), 'type' => 'file', 'class' => 'w460');
        $addList[] = array('name' => 'me_mobile', 'title' => L('MOBILE'), 'class' => 'w460');
        $addList[] = array('name' => 'me_phone', 'title' => L('PHONE'), 'class' => 'w460');
        $addList[] = array('name' => 'me_email', 'title' => L('EMAIL'), 'class' => 'w460');
        $addList[] = array('name' => 'me_note', 'title' => L('NOTE'), 'label' => 'textarea', 'class' => 'h80');
        $addList[] = array('name' => 'me_status', 'title' => L('STATUS'), 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));
        $addList[] = array('name' => 'md_sex', 'title' => L('SEX'), 'label' => 'select', 'data' => explode(',', C('MEMBER_SEX')));
        $addList[] = array('name' => 'md_birthday', 'title' => L('BIRTHDAY'), 'class' => 'w460', 'event' => 'onClick', 'labelClass' => 'Wdate', 'eventValue' => "WdatePicker({dateFmt:'yyyy-MM-dd',maxDate:'#{%y}-{%M}-#{%d}'});");
        $addList[] = array('name' => 'md_description', 'title' => L('DESCRIPTION'), 'label' => 'textarea', 'class' => 'h80');
        $addList[] = array('name' => 'md_chinese_name', 'title' => L('CNAME'), 'class' => 'w460');
        $addList[] = array('name' => 'md_english_name', 'title' => L('ENAME'), 'class' => 'w460');
        $addList[] = array('name' => 'md_native_place', 'title' => L('NATIVE_PLACE'), 'class' => 'w460');
        $addList[] = array('name' => 'md_card_type', 'title' => L('CARD_TYPE'), 'label' => 'select', 'data' => explode(',', C('MEMBER_CARD_TYPE')));
        $addList[] = array('name' => 'md_card_num', 'title' => L('CARD_NUM'), 'class' => 'w460');
        $addList[] = array('name' => 'md_political_type', 'title' => L('POLITICAL_TYPE'), 'label' => 'select', 'data' => explode(',', C('MEMBER_POLITICAL_TYPE')));
        $addList[] = array('name' => 'md_blood_type', 'title' => L('BLOOD_TYPE'), 'label' => 'select', 'data' => explode(',', C('MEMBER_BLOOD_TYPE')));
        $addList[] = array('name' => 'me_type', 'type' => 'hidden');

        // 属性
        foreach ($attribute as $attr_info) {
            if ($attr_info['mat_type'] == 9) {
                // 单选
                $addList[] = array('name' => $attr_info['mat_name'], 'title' => $attr_info['mat_title'], 'label' => 'select', 'data' => explode(',', $attr_info['mat_value']));
            } elseif ($attr_info['mat_type'] == 2) {
                // 多选
                $addList[] = array('name' => $attr_info['mat_name'].'[]', 'title' => $attr_info['mat_title'], 'type' => 'checkbox', 'data' => explode(',', $attr_info['mat_value']));
            } else {
                // 文本
                $addList[] = array('name' => $attr_info['mat_name'], 'title' => $attr_info['mat_title'], 'class' => 'w460');
            }
        }

        // 默认值
        if (!$result) {
            $result['md_birthday'] = '2000-01-01';
            $result['me_account'] = '******';
        }
        // 头像
        $file = D('Member')->getAvatar($result);
        if ($file) {
            $result['file'] = $file;
            $result['ext'] = C('DEFAULT_IMAGE_EXT');
        }
        // 家长
        $result['me_type'] = 2;

        return generationAddTpl($addList, 'me_id', $title, $result);
    }

    public function getRegion() {
        $region = loadCache('region');
        $this->ajaxReturn($region[I('id', 0, 'intval')]);
    }
    
    // 默认编辑操作
    public function edit() {
        if ($_POST) {
            $this->insert();
        } else {
            // 获取数据
            $config['is_deal_result'] = true;
            $config['auth_id'] = intval(I('request.id'));
            $vo = $this->apiReturnDeal(getApi($config, 'Member', 'shows'));
            foreach ($vo as &$value) {
                $value = stripFilter($value);
            }

            $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Add.js'));
            $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . 'Add.css'));

            $this->assign('vo', $vo);
            $this->addTpl = $this->addTpl(L('EDIT'), $vo);
            $this->display('Public/add');
        }
    }

    public function insert() {

        if ($_FILES['me_avatar']['size'] > 0) {
            $fields['me_avatar'] = '@'.realpath($_FILES['me_avatar']['tmp_name']).";type=".$_FILES['me_avatar']['type'].";filename=".$_FILES['me_avatar']['name'];
        }

        $apiFunction = intval($_POST['me_id']) ? 'edit' : 'add';
        $_POST['md_register_ip'] = get_client_ip();
        $_POST['auth_id'] = $_POST['me_id'];

        $result = $this->apiReturnDeal(getApi($_POST, 'Member', $apiFunction, 'json', $fields));

        $this->show($result);
    }

    public function delete() {
        
        $result = $this->apiReturnDeal(getApi(array('auth_id' => strval(I('id'))), 'Member', 'del'));

        $this->show($result);
    }

    // 档案展示页面
    public function shows() {

        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/teacherShows.js'));
        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/teacherShows.css'));

        $archives_type = explode(',', C('ARCHIVES_TYPE'));
        $this->assign('archives_type', array(2 => $archives_type[2], 9 => $archives_type[9], 10 => $archives_type[10]));
        $this->display('Teacher/shows');
    }

    // 档案操作
    public function user() {
        $operation = I('operation');
        if ($operation == 'set') {
            // 添加、修改
            $result = $this->apiReturnDeal(getApi($_POST, 'Member', 'setArchives'));
            echo json_encode($result);
            exit;
        } elseif ($operation == 'del') {
            // 删除
            $result = $this->apiReturnDeal(getApi($_POST, 'Member', 'delArchives'));
            echo json_encode($result);
            exit;
        }

        // 展示
        $html = '';
        $result = $this->apiReturnDeal(getApi($_POST, 'Member', 'getArchives'));
        if ($result['list']) {
            foreach($result['list'] as $info) {
                $html .= getArchivesHtml($info, intval($_POST['mar_type']));
            }
        }

        $html .= getArchivesHtml(array(), intval($_POST['mar_type']));
        // 分页
        if ($result['list']) {
            $html .= '<div class="page">' . $result['page'] . '</div>';
        }
        echo json_encode($html);
    }

    // 家长管理
    public function resume() {

        if ($_POST) {
            $result = $this->apiReturnDeal(getApi($_POST, 'Member', 'setRelation'));
            $this->show($result, '', __CONTROLLER__ . '/resume/id/' . intval($_POST['auth_id']));
        } else {

            $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Resume.js'));
            $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . 'Resume.css'));

            $config['auth_id'] = I('id');
            $config['is_deal_result'] = true;
            $list = $this->apiReturnDeal(getApi($config, 'Member', 'getRelation'));
            $this->assign('mr_list', $list['list']);

            $mr_type = explode(',', C('MEMBER_RELATION_TYPE'));
            unset($mr_type[0]);
            $this->assign('mr_type', $mr_type);
            $this->assign('auth_id', I('id', 0, 'intval'));
            $this->display();
        }
    }
}