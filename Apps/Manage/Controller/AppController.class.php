<?php
namespace Manage\Controller;
class AppController extends ManageController {
    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'a_id', 'percent' => '6', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'a_title', 'percent' => '20', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'ac_title', 'percent' => '10', 'title' => L('APP_CATEGORY'));
        $tplList[] = array('id' => 'id_title', 'percent' => '10', 'title' => L('ID_RANK'));
        $tplList[] = array('id' => 'a_online_time', 'percent' => '15', 'title' => L('ONLINE_TIME'));
        $tplList[] = array('id' => 'a_valided', 'percent' => '15', 'title' => L('VALIDED'));
        $tplList[] = array('id' => 'a_status', 'percent' => '10', 'title' => L('STATUS'));
        $tplList['action'] = array('id' => 'action', 'percent' => '10', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '5', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '5', 'title' => L('DELETE'));

        // 检索器
        $search[] = array('title' => L('NAME'), 'name' => 'a_title');
        $search[] = array('title' => L('APP_TYPE'), 'name' => 'aty_id', 'label' => 'select', 'data' => reloadCache('appType'));
        $search[] = array('title' => L('APP_CATEGORY'), 'name' => 'ac_id', 'label' => 'select', 'data' => reloadCache('appCategory'), 'inline' => true);
        $search[] = array('title' => L('ID_RANK'), 'name' => 'id_id', 'label' => 'select', 'data' => D('Identity')->getLevel('id_id,id_title'), 'inline' => true);

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function add() {

        if ($_POST) {
            $this->insert();
        } else {
            $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Add.js'));

            $this->assign('appType', reloadCache('appType'));
            $this->assign('appCategory', reloadCache('appCategory'));
            $this->assign('identityList', D('Identity')->getLevel('id_id,id_title'));
            $this->display();
        }
    }

    public function edit() {
        if ($_POST) {
            $this->insert();
        } else {
            $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Add.js'));

            // 获取数据
            $vo = D('App')->getById(intval(I('request.id')));

            foreach ($vo as &$value) {
                $value = stripFilter($value);
            }

            $this->assign('vo', $vo);

            $this->assign('appType', reloadCache('appType'));
            $this->assign('appCategory', reloadCache('appCategory'));
            $this->assign('identityList', D('Identity')->getLevel('id_id,id_title'));
            $this->display('add');
        }
    }

    public function insert() {
        
        // 上传文件验证
        if ($_FILES['a_logo']['name'] && $_FILES['a_logo']['error']) {
            $this->error('图标上传失败');
            exit;
        }
        if ($_FILES['a_code_logo']['name'] && $_FILES['a_code_logo']['error']) {
            $this->error('二维码上传失败');
            exit;
        }
        if ($_FILES['apk_phone']['name'] && $_FILES['apk_phone']['error']) {
            $this->error('Android手机端 上传失败');
            exit;
        }
        if ($_FILES['apk_plat']['name'] && $_FILES['apk_plat']['error']) {
            $this->error('Android平板端 上传失败');
            exit;
        }
        if ($_FILES['ipa_phone']['name'] && $_FILES['ipa_phone']['error']) {
            $this->error('IOS手机端 上传失败');
            exit;
        }
        if ($_FILES['ipa_plat']['name'] && $_FILES['ipa_plat']['error']) {
            $this->error('IOS平板端 上传失败');
            exit;
        }
        if ($_FILES['computer']['name'] && $_FILES['computer']['error']) {
            $this->error('电脑端 上传失败');
            exit;
        }

        // 是否有 LOGO 上传
        if ($_FILES['a_logo']['size'] > 0) {

            // 文件上传
            $a_logo = D('App')->uploadFile($_FILES['a_logo']);
            if ($a_logo === false) {
                $this->error('LOGO' . D('App')->getError());
                exit;
            }
            // 成功
            $_POST['a_logo'] = $a_logo['savename'];
        }

        // 是否有 二维码 上传
        if ($_FILES['a_code_logo']['size'] > 0) {

            // 文件上传
            $a_code_logo = D('App')->uploadFile($_FILES['a_code_logo'], 'code_logo');
            if ($a_code_logo === false) {
                $this->error('二维码' . D('App')->getError());
                exit;
            }
            // 成功
            $_POST['a_code_logo'] = $a_code_logo['savename'];
        }

        // 需要重命名的文件
        $rename_files = array();
        // 是否有 apk 手机端上传
        if ($_FILES['apk_phone']['size'] > 0) {

            $apk_phone = D('App')->uploadFile($_FILES['apk_phone'], 'apk_phone');
            if ($apk_phone === false) {
                $this->error('Android手机端' . D('App')->getError());
                exit;
            }

            $rename_files[] = array(
                'savename' => $apk_phone['savename'],
                'savepath' => $apk_phone['savepath'],
                'ext' => $apk_phone['ext']
            );
            $_POST['a_apk_phone'] = $apk_phone['savename'];
        }

        // 是否有 apk 平板端上传
        if ($_FILES['apk_plat']['size'] > 0) {

            $apk_plat = D('App')->uploadFile($_FILES['apk_plat'], 'apk_plat');
            if ($apk_plat === false) {
                $this->error('Android平板端' . D('App')->getError());
                exit;
            }

            $rename_files[] = array(
                'savename' => $apk_plat['savename'],
                'savepath' => $apk_plat['savepath'],
                'ext' => $apk_plat['ext']
            );
            $_POST['a_apk_plat'] = $apk_plat['savename'];
        }

        // 是否有 ipa 手机端上传
        if ($_FILES['ipa_phone']['size'] > 0) {

            $ipa_phone = D('App')->uploadFile($_FILES['ipa_phone'], 'ipa_phone');
            if ($ipa_phone === false) {
                $this->error('IOS手机端' . D('App')->getError());
                exit;
            }

            $rename_files[] = array(
                'savename' => $ipa_phone['savename'],
                'savepath' => $ipa_phone['savepath'],
                'ext' => $ipa_phone['ext']
            );
            $_POST['a_ipa_phone'] = $ipa_phone['savename'];
        }

        // 是否有 ipa 平板端上传
        if ($_FILES['ipa_plat']['size'] > 0) {

            $ipa_plat = D('App')->uploadFile($_FILES['ipa_plat'], 'ipa_plat');
            if ($ipa_plat === false) {
                $this->error('IOS平板端' . D('App')->getError());
                exit;
            }
            
            $rename_files[] = array(
                'savename' => $ipa_plat['savename'],
                'savepath' => $ipa_plat['savepath'],
                'ext' => $ipa_plat['ext']
            );
            $_POST['a_ipa_plat'] = $ipa_plat['savename'];
        }

        // 是否有 ipa 电脑端上传
        if ($_FILES['computer']['size'] > 0) {

            $computer = D('App')->uploadFile($_FILES['computer'], 'computer');
            if ($computer === false) {
                $this->error('电脑端' . D('App')->getError());
                exit;
            }
            
            $rename_files[] = array(
                'savename' => $computer['savename'],
                'savepath' => $computer['savepath'],
                'ext' => $computer['ext']
            );
            $_POST['a_computer'] = $computer['savename'];
        }

        if ($_POST['a_id']) {
            // 编辑
            $app_config['where']['a_id'] = I('a_id', 0, 'intval');
            $app_config['fields'] = 'a_id,a_logo';
            $app_info = D('App')->getOne($app_config);
            if ($_FILES['a_logo']['size'] > 0) {
                D('App')->deleteFile($app_info);
            }
            if ($_FILES['a_code_logo']['size'] > 0) {
                D('App')->deleteFile($app_info, 'code_logo');
            }
            if ($_FILES['apk_phone']['size'] > 0) {
                D('App')->deleteFile($app_info, 'apk_phone');
            }
            if ($_FILES['apk_plat']['size'] > 0) {
                D('App')->deleteFile($app_info, 'apk_plat');
            }
            if ($_FILES['ipa_phone']['size'] > 0) {
                D('App')->deleteFile($app_info, 'ipa_phone');
            }
            if ($_FILES['ipa_plat']['size'] > 0) {
                D('App')->deleteFile($app_info, 'ipa_plat');
            }
            if ($_FILES['computer']['size'] > 0) {
                D('App')->deleteFile($app_info, 'computer');
            }
        }
        
        // 数据过滤
        $_POST['a_platform_virtual_money'] = intval($_POST['a_platform_virtual_money']);
        $_POST['a_app_virtual_money'] = intval($_POST['a_app_virtual_money']);
        $app_id = parent::data();
        
        // 重命名文件
        if ($app_id !== false) {
            // 文件添加成功
            if ($_POST['a_id']) {
                // 编辑时 app_id
                $app_id = intval($_POST['a_id']);
            }

            foreach ($rename_files as $file) {
                fileRename($file['savename'], $app_id . '.' . $file['ext'], C('UPLOADS_ROOT_PATH') . $file['savepath'], true);
            }

            // 切图 不加水印
            if ($_POST['a_logo']) {
                D('App')->dealImage(C('UPLOADS_ROOT_PATH') . $a_logo['savepath'] . $a_logo['savename'], array(), false);
            }
            if ($_POST['a_code_logo']) {
                D('App')->dealImage(C('UPLOADS_ROOT_PATH') . $a_code_logo['savepath'] . $a_code_logo['savename'], array(), false);
            }

            // 应用图片
            $_POST['a_id'] = $app_id;
            $res_pic = D('App')->uploadPic($_FILES['pic'], $_POST);
            if ($res_pic === false) {
                $this->error(D('App')->getError());
            }
        }

        $this->show($app_id);
    }

    public function delete() {
        
        $this->show(D('App')->delete(I('id', 0, 'strval')), L('DELETE'));
    }

    public function delFile($id) {
        $res = D('App')->delFile(intval($id));
        if ($res !== false) {
            echo json_encode(array('status' => 1));
        } else {
            echo json_encode(array('status' => 0));
        }
    }
}