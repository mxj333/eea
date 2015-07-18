<?php
namespace Manage\Controller;
class MemberController extends ManageController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'me_id', 'percent' => '5', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'me_account', 'percent' => '10', 'title' => L('ACCOUNT'), 'class' => 'showContent');
        $tplList[] = array('id' => 'me_nickname', 'percent' => '15', 'title' => L('NICKNAME'));
        $tplList[] = array('id' => 're_title', 'percent' => '20', 'title' => L('REGION'));
        $tplList[] = array('id' => 'me_mobile', 'percent' => '15', 'title' => L('MOBILE'));
        $tplList[] = array('id' => 'me_status', 'percent' => '10', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '12', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '6', 'title' => L('EDIT'));
        $action[] = array('id' => 'identity', 'percent' => '6', 'title' => L('IDENTITY'));

        // 检索器
        $search[] = array('title' => L('NICKNAME'), 'name' => 'me_nickname');
        $search[] = array('title' => L('ACCOUNT'), 'name' => 'me_account','inline' => true);
        $search[] = array('title' => L('MOBILE'), 'name' => 'me_mobile');
        $search[] = array('title' => '', 'inline' => 1, 'display' => 'none', 'name' => 'me_id');

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        // 用户属性
        $attribute = D('MemberAttribute')->getAll();
        
        // 显示字段
        $addList[] = array('name' => 'me_account', 'title' => L('ACCOUNT'), 'class' => 'w460');
        $addList[] = array('name' => 'me_password', 'title' => L('PASSWORD'), 'type' => 'password', 'class' => 'w460');
		$addList[] = array('name' => 'me_nickname', 'title' => L('NICKNAME'), 'class' => 'w460', 'require' => true);
        $addList[] = array('name' => 'me_avatar', 'title' => L('LOGO'), 'type' => 'file', 'class' => 'w460');
        $addList[] = array('name' => 'region', 'title' => L('REGION'), 'require' => true);
        $addList[] = array('name' => 'md_sex', 'title' => L('SEX'), 'label' => 'select', 'data' => explode(',', C('MEMBER_SEX')));
        $addList[] = array('name' => 'md_birthday', 'title' => L('BIRTHDAY'), 'class' => 'w460', 'event' => 'onClick', 'eventValue' => "WdatePicker({dateFmt:'yyyy-MM-dd',maxDate:'#{%y}-{%M}-#{%d}'});");
        $addList[] = array('name' => 'md_blood_type', 'title' => L('BLOOD_TYPE'), 'label' => 'select', 'data' => explode(',', C('MEMBER_BLOOD_TYPE')));
        $addList[] = array('name' => 'me_note', 'title' => L('NOTE'), 'label' => 'textarea', 'class' => 'h80');
        $addList[] = array('name' => 'me_status', 'title' => L('STATUS'), 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));
        $addList[] = array('name' => 'me_validity', 'title' => L('VALIDITY'), 'class' => 'w460', 'event' => 'onClick', 'eventValue' => "WdatePicker({dateFmt:'yyyy-MM-dd',minDate:'#{%y}-{%M}-#{%d+1}'});", 'require' => true);
        $addList[] = array('name' => 'me_mobile', 'title' => L('MOBILE'), 'class' => 'w460');
        $addList[] = array('name' => 'me_phone', 'title' => L('PHONE'), 'class' => 'w460');
        $addList[] = array('name' => 'me_email', 'title' => L('EMAIL'), 'class' => 'w460');
        $addList[] = array('name' => 'md_chinese_name', 'title' => L('CNAME'), 'class' => 'w460');
        $addList[] = array('name' => 'md_english_name', 'title' => L('ENAME'), 'class' => 'w460');
        $addList[] = array('name' => 'md_native_place', 'title' => L('NATIVE_PLACE'), 'class' => 'w460');
        $addList[] = array('name' => 'md_card_type', 'title' => L('CARD_TYPE'), 'label' => 'select', 'data' => explode(',', C('MEMBER_CARD_TYPE')));
        $addList[] = array('name' => 'md_card_num', 'title' => L('CARD_NUM'), 'class' => 'w460');
        $addList[] = array('name' => 'md_political_type', 'title' => L('POLITICAL_TYPE'), 'label' => 'select', 'data' => explode(',', C('MEMBER_POLITICAL_TYPE')));
        $addList[] = array('name' => 'md_description', 'title' => L('DESCRIPTION'), 'label' => 'textarea', 'class' => 'h80');

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

        // 头像
        if ($result) {
            $file = D('Member')->getAvatar($result);
            if ($file) {
                $result['file'] = $file;
                $result['ext'] = C('DEFAULT_IMAGE_EXT');
            }
        }

        // 默认值
        if (!$result) {
            $result['md_birthday'] = '2000-01-01';
        }
        return generationAddTpl($addList, 'me_id', $title, $result);
    }

    public function getRegion() {
        $region = loadCache('region');
        $this->ajaxReturn($region[I('id', 0, 'intval')]);
    }
    
    public function insert() {

        // 是否有 LOGO 上传
        if ($_FILES['me_avatar']['size'] > 0) {
            
            $a_logo = D('Member')->uploadLogo($_FILES['me_avatar']);
            $_POST['me_avatar'] = $a_logo['savename'];
        }

        // 入库
        $me_res = D('Member')->insert($_POST, 'User');

        if ($me_res === false) {
            $this->error(D('Member')->getError());
        }
        $this->show($me_res, L('OPERATION'));
    }

    public function delete() {
        
        $config['where']['me_id'] = array('IN', strval($_REQUEST['id']));
        $result = D('Member')->signDeleted($config, array(), 'User');
        
        $this->show($result, L('DELETE'));
    }
}