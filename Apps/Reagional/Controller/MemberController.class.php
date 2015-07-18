<?php
namespace Reagional\Controller;
class MemberController extends ReagionalController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'me_id', 'percent' => '5', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'me_account', 'percent' => '10', 'title' => L('ACCOUNT'), 'class' => 'showContent');
        $tplList[] = array('id' => 'me_nickname', 'percent' => '15', 'title' => L('NICKNAME'));
        $tplList[] = array('id' => 'me_mobile', 'percent' => '13', 'title' => L('MOBILE'));
        $tplList[] = array('id' => 'me_phone', 'percent' => '13', 'title' => L('PHONE'));
        $tplList[] = array('id' => 'me_email', 'percent' => '15', 'title' => L('EMAIL'));
        $tplList['action'] = array('id' => 'action', 'percent' => '12', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '6', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '6', 'title' => L('DELETE'));

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
        // 普通用户
        //$_POST['me_type'] = 0;

        // api 请求数据
        $data = $this->apiReturnDeal(getApi($_POST, 'Member', 'lists'));
        
        echo json_encode($data);
    }

    public function addTpl($title = '', $result = array()) {
        // 用户属性
        $attribute = D('MemberAttribute')->getAll();
        
        // 显示字段
        $addList[] = array('name' => 'me_account', 'title' => L('ACCOUNT'), 'class' => 'w460');
        $addList[] = array('name' => 'me_nickname', 'title' => L('NICKNAME'), 'class' => 'w460');
        $addList[] = array('name' => 'me_password', 'title' => L('PASSWORD'), 'type' => 'password', 'class' => 'w460');
        $addList[] = array('name' => 'me_validity', 'title' => L('VALIDITY'), 'class' => 'w460', 'event' => 'onClick', 'eventValue' => "WdatePicker({dateFmt:'yyyy-MM-dd',minDate:'#{%y}-{%M}-#{%d+1}'});");
        $addList[] = array('name' => 'region', 'title' => L('REGION'));
        $addList[] = array('name' => 'me_avatar', 'title' => L('LOGO'), 'type' => 'file', 'class' => 'w460');
        $addList[] = array('name' => 'me_mobile', 'title' => L('MOBILE'), 'class' => 'w460');
        $addList[] = array('name' => 'me_phone', 'title' => L('PHONE'), 'class' => 'w460');
        $addList[] = array('name' => 'me_email', 'title' => L('EMAIL'), 'class' => 'w460');
        $addList[] = array('name' => 'me_note', 'title' => L('NOTE'), 'label' => 'textarea', 'class' => 'h80');
        $addList[] = array('name' => 'me_status', 'title' => L('STATUS'), 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));
        $addList[] = array('name' => 'md_sex', 'title' => L('SEX'), 'label' => 'select', 'data' => explode(',', C('MEMBER_SEX')));
        $addList[] = array('name' => 'md_birthday', 'title' => L('BIRTHDAY'), 'class' => 'w460', 'event' => 'onClick', 'eventValue' => "WdatePicker({dateFmt:'yyyy-MM-dd',maxDate:'#{%y}-{%M}-#{%d}'});");
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
        }
        // 头像
        $file = D('Member')->getAvatar($result);
        if ($file) {
            $result['file'] = $file;
            $result['ext'] = C('DEFAULT_IMAGE_EXT');
        }
        // 教师
        $result['me_type'] = 0;
        return generationAddTpl($addList, 'me_id', $title, $result);
    }

    public function getRegion() {
        $region = loadCache('region');
        $this->ajaxReturn($region[I('id', 0, 'intval')]);
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
}