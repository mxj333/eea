<?php
namespace Manage\Controller;
class MemberIdentityController extends ManageController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'mi_id', 'percent' => '5', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'mi_account', 'percent' => '10', 'title' => L('ACCOUNT'), 'class' => 'showContent');
        $tplList[] = array('id' => 'mi_nickname', 'percent' => '15', 'title' => L('NICKNAME'));
        $tplList[] = array('id' => 'mi_mobile', 'percent' => '13', 'title' => L('MOBILE'));
        $tplList[] = array('id' => 'mi_phone', 'percent' => '13', 'title' => L('PHONE'));
        $tplList[] = array('id' => 'mi_email', 'percent' => '15', 'title' => L('EMAIL'));
        $tplList[] = array('id' => 'mi_status', 'percent' => '8', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '12', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '6', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '6', 'title' => L('DELETE'));

        // 检索器
        $search[] = array('title' => L('NICKNAME'), 'name' => 'mi_nickname');
        $search[] = array('title' => '', 'inline' => 1, 'display' => 'none', 'name' => 'me_id');

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        $me_id = I('get.me_id', 0, 'intval');

        // 用户属性
        $attribute = D('MemberIdentityAttribute')->getAll();

        // 显示字段
        $addList[] = array('name' => 'mi_nickname', 'title' => L('NICKNAME'), 'class' => 'w460', 'require' => true);
        //$addList[] = array('name' => 'mi_password', 'title' => L('PASSWORD'), 'type' => 'password', 'class' => 'w460');
        $addList[] = array('name' => 'mi_validity', 'title' => L('VALIDITY'), 'class' => 'w460', 'event' => 'onClick', 'eventValue' => "WdatePicker({dateFmt:'yyyy-MM-dd',minDate:'#{%y}-{%M}-#{%d+1}'});", 'require' => true);
        $addList[] = array('name' => 'region', 'title' => L('REGION'), 'require' => true);
        $addList[] = array('name' => 'mi_avatar', 'title' => L('LOGO'), 'type' => 'file', 'class' => 'w460');
        $addList[] = array('name' => 'mi_mobile', 'title' => L('MOBILE'), 'class' => 'w460');
        $addList[] = array('name' => 'mi_phone', 'title' => L('PHONE'), 'class' => 'w460');
        $addList[] = array('name' => 'mi_email', 'title' => L('EMAIL'), 'class' => 'w460');
        $addList[] = array('name' => 'mi_note', 'title' => L('NOTE'), 'label' => 'textarea', 'class' => 'h80');
        $addList[] = array('name' => 'mi_status', 'title' => L('STATUS'), 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));
        $addList[] = array('name' => 'mid_sex', 'title' => L('SEX'), 'label' => 'select', 'data' => explode(',', C('MEMBER_SEX')));
        $addList[] = array('name' => 'mid_birthday', 'title' => L('BIRTHDAY'), 'class' => 'w460', 'event' => 'onClick', 'eventValue' => "WdatePicker({dateFmt:'yyyy-MM-dd',maxDate:'#{%y}-{%M}-#{%d}'});");
        $addList[] = array('name' => 'mid_description', 'title' => L('DESCRIPTION'), 'label' => 'textarea', 'class' => 'h80');
        $addList[] = array('name' => 'mid_chinese_name', 'title' => L('CNAME'), 'class' => 'w460');
        $addList[] = array('name' => 'mid_english_name', 'title' => L('ENAME'), 'class' => 'w460');
        $addList[] = array('name' => 'mid_native_place', 'title' => L('NATIVE_PLACE'), 'class' => 'w460');
        $addList[] = array('name' => 'mid_card_type', 'title' => L('CARD_TYPE'), 'label' => 'select', 'data' => explode(',', C('MEMBER_CARD_TYPE')));
        $addList[] = array('name' => 'mid_card_num', 'title' => L('CARD_NUM'), 'class' => 'w460');
        $addList[] = array('name' => 'mid_political_type', 'title' => L('POLITICAL_TYPE'), 'label' => 'select', 'data' => explode(',', C('MEMBER_POLITICAL_TYPE')));
        $addList[] = array('name' => 'mid_blood_type', 'title' => L('BLOOD_TYPE'), 'label' => 'select', 'data' => explode(',', C('MEMBER_BLOOD_TYPE')));

        // 属性
        foreach ($attribute as $attr_info) {
            if ($attr_info['miat_type'] == 9) {
                // 单选
                $addList[] = array('name' => $attr_info['miat_name'], 'title' => $attr_info['miat_title'], 'label' => 'select', 'data' => explode(',', $attr_info['miat_value']));
            } elseif ($attr_info['miat_type'] == 2) {
                // 多选
                $addList[] = array('name' => $attr_info['miat_name'].'[]', 'title' => $attr_info['miat_title'], 'type' => 'checkbox', 'data' => explode(',', $attr_info['miat_value']));
            } else {
                // 文本
                $addList[] = array('name' => $attr_info['miat_name'], 'title' => $attr_info['miat_title'], 'class' => 'w460');
            }
        }

        // 图片处理
        if ($result) {
            $file = D('MemberIdentity')->getAvatar($result);
            if ($file) {
                $result['file'] = $file;
                $result['ext'] = C('DEFAULT_IMAGE_EXT');
            }
        }
        
        // 默认值
        if (!$result) {
            $result['mid_birthday'] = '2000-01-01';
        }

        if ($me_id) {
            $addList[] = array('name' => 'me_id', 'type' => 'hidden');
            $result['me_id'] = $me_id;
        }
        return generationAddTpl($addList, 'mi_id', $title, $result);
    }

    public function getRegion() {
        $region = reloadCache('region');
        $this->ajaxReturn($region[I('id', 0, 'intval')]);
    }
    
    public function insert() {

        // 是否有 LOGO 上传
        if ($_FILES['mi_avatar']['size'] > 0) {

            // 文件上传
            $a_logo = D('MemberIdentity')->uploadLogo($_FILES['mi_avatar']);
            $_POST['mi_avatar'] = $a_logo['savename'];
        }

        $mi_res = D('MemberIdentity')->insert($_POST, 'User');

        // 通过 用户列表进入的身份管理  保存完后 跳回
        $me_id = I('me_id', 0, 'intval');
        if ($me_id) {
            $redirect_url = __CONTROLLER__ . '/index/me_id/' . $me_id;
        }

        if ($mi_res === false) {
            $this->error(D('MemberIdentity')->getError());
        }

        $this->success(L('SUCCESS'), $redirect_url);
    }

    public function delete() {
        
        $config['where']['mi_id'] = array('IN', strval($_REQUEST['id']));
        $result = D('MemberIdentity')->signDeleted($config, array(), 'User');
        if ($result === false) {
            $this->error(D('MemberIdentity')->getError());
        }

        $me_id = I('get.me_id', 0, 'intval');
        if ($me_id) {
            $redirect_url = __CONTROLLER__ . '/index/me_id/' . $me_id;
        }

        $this->show($result, L('DELETE'), $redirect_url);
    }
}