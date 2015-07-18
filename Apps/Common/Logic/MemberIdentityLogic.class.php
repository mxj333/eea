<?php
namespace Common\Logic;
class MemberIdentityLogic extends Logic {

    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'is_deal_result' => true,
            'order' => 'mi_id DESC',
            'p' => intval($param['p']),
            'where' => array('mi_is_deleted' => 9),
        );

        if ($param['me_id']) {
            $default['where']['me_id'] = $param['me_id'];
        }

        if ($param['mi_nickname']) {
            $default['where']['mi_nickname'] = array('LIKE', '%' . $param['mi_nickname'] . '%');
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('MemberIdentity')->getListByPage($config);

        if ($config['is_deal_result']) {
            foreach ($lists['list'] as $key => $value) {
                if ($value['mi_status']) {
                    $lists['list'][$key]['mi_status'] = getStatus($value['mi_status']);
                }
            }
        }

        // 输出数据
        return $lists;
    }

    public function getById($id, $config = array()) {

        $default['is_deal_result'] = true;
        $config = array_merge($default, $config);

        $identityConfig['where']['mi_id'] = intval($id);
        $info = D('MemberIdentity', 'Model')->getOne($identityConfig);
        $dinfo = D('MemberIdentityDetail', 'Model')->getOne($identityConfig);
        $ainfo = D('MemberIdentityAttributeRecord')->getinfo($id);

        // 生日
        if ($config['is_deal_result']) {
            $dinfo['mid_birthday'] = date('Y-m-d', $dinfo['mid_birthday']);
            $info['mi_validity'] = date('Y-m-d', $info['mi_validity']);
        }
        
        return array_merge((array)$info, (array)$dinfo, (array)$ainfo);
    }

    public function uploadLogo($data) {

        // 文件上传
        $config['exts'] = explode(',', strtolower(C('ALLOW_IMAGE_TYPE')));
        $config['savePath'] = C('MEMBER_IDENTITY_LOGO_PATH');

        $logo = upload($data, $config);

        if (!is_array($logo)) {
            $this->error = $logo;
            return false;
        }

        return $logo;
    }

    // logo 图片处理
    public function uploadedLogoDeal($logo) {
        // 后缀
        $logo_ext = get_file_ext($logo);
        if (!in_array($logo_ext, explode(',', strtolower(C('ALLOW_IMAGE_TYPE'))))) {
            @unlink($logo);
            $this->error = '上传图片类型错误';
            return false;
        }

        $logoPath = C('UPLOADS_ROOT_PATH') . C('MEMBER_IDENTITY_LOGO_PATH');
        // 检查目录是否存在
        if (!file_exists($logoPath)) {
            mk_dir($logoPath);
        }

        // 真实保存名称
        $logo_savename = savename_rule();
        $logo_ext = $logo_ext ? $logo_ext : C('DEFAULT_IMAGE_EXT');

        // 文件重命名
        fileRename($logo, $logoPath . $logo_savename . '.' . $logo_ext);

        return $logo_savename . '.' . $logo_ext;
    }

    public function insert($data, $type = 'Member') {

        if (!$data['mi_id']) {
            // 添加时 默认值
            $data['mi_creator_id'] = $data['mi_creator_id'] ? $data['mi_creator_id'] : $_SESSION[C('USER_AUTH_KEY')];
            $data['mi_creator_table'] = $data['mi_creator_table'] ? $data['mi_creator_table'] : $type;
        }

        // 数据验证
        $member_identity_data = D('MemberIdentity', 'Model')->create($data);
        if (false === $member_identity_data) {
            $this->error = D('MemberIdentity', 'Model')->getError();
            return false;
        }
        // 数据验证
        $member_identity_detail_data = D('MemberIdentityDetail', 'Model')->create($data);
        if (false === $member_identity_detail_data) {
            $this->error = D('MemberIdentityDetail', 'Model')->getError();
            return false;
        }

        // 验证证件号
        if ($member_identity_detail_data['mid_card_type'] == 0 && $member_detail_data['mid_card_num'] && !preg_match('/^(\d{14}|\d{17})(\d|x|X)$/', $member_detail_data['mid_card_num'])) {
            $this->error = '证件号格式错误';
            return false;
        }

        // 验证账号
        if ($data['mi_account'] && (!preg_match('/^(\d+)$/', $data['mi_account']) || (strlen(intval($data['mi_account'])) > C('CURRENT_ACCOUNT_NUMBER')))) {
            $this->error = '账号必需是数字，且小于等于' . C('CURRENT_ACCOUNT_NUMBER') . '位';
            return false;
        }

        if ($data['mi_id']) {
            // 编辑  账号不能修改
            unset($member_identity_data['mi_account']);

            // 获取身份信息
            $member_identity_info = D('MemberIdentity')->getById(intval($data['mi_id']));
            // 更新身份信息
            $mi_res = D('MemberIdentity', 'Model')->update($member_identity_data);
            if ($mi_res !== false) {
                // 更新身份详情信息
                $member_identity_detail_data['mid_id'] = $member_identity_info['mid_id'];
                D('MemberIdentityDetail', 'Model')->update($member_identity_detail_data);
                // 添加用户属性
                D('MemberIdentityAttributeRecord')->insert($_POST);
                // 删除以前的图片
                if ($data['mi_avatar']) {
                    $this->deleteImage(C('UPLOADS_ROOT_PATH') . C('MEMBER_IDENTITY_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $member_identity_info['mi_created']) . '/' . $member_identity_info['mi_avatar']);
                    fileRename(C('UPLOADS_ROOT_PATH') . C('MEMBER_IDENTITY_LOGO_PATH') . $data['mi_avatar'], C('UPLOADS_ROOT_PATH') . C('MEMBER_IDENTITY_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $member_identity_info['mi_created']) . '/' . $data['mi_avatar']);
					$this->dealImage(C('UPLOADS_ROOT_PATH') . C('MEMBER_IDENTITY_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $member_identity_info['mi_created']) . '/' . $data['mi_avatar']);
                }
            }
        } else {
            // 添加
            $member_identity_data['mi_account'] = getAccount('identity');
            $mi_res = D('MemberIdentity', 'Model')->insert($member_identity_data);
            if ($mi_res !== false) {
                $member_identity_detail_data['mi_id'] = $mi_res;
                $member_identity_detail_data['mid_register_ip'] = is_numeric($member_identity_detail_data['mid_register_ip']) ? $member_identity_detail_data['mid_register_ip'] : rewrite_ip2long(get_client_ip());
                D('MemberIdentityDetail', 'Model')->insert($member_identity_detail_data);
                // 添加用户属性
                $_POST['mi_id'] = $mi_res;
                D('MemberIdentityAttributeRecord')->insert($_POST);
                if ($data['mi_avatar']) {
                    // 图片 子目录
                    fileRename(C('UPLOADS_ROOT_PATH') . C('MEMBER_IDENTITY_LOGO_PATH') . $data['mi_avatar'], C('UPLOADS_ROOT_PATH') . C('MEMBER_IDENTITY_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $member_identity_data['mi_created']) . '/' . $data['mi_avatar']);
                    // 切图
                    $this->dealImage(C('UPLOADS_ROOT_PATH') . C('MEMBER_IDENTITY_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $member_identity_data['mi_created']) . '/' . $data['mi_avatar']);
                }
            }
        }

        return $mi_res;
    }

    // 获取用户头像
    public function getAvatar($info, $config = array()) {
        $default = array(
            'root' => C('UPLOADS_ROOT_PATH'),
            'path' => C('MEMBER_IDENTITY_LOGO_PATH'),
            'size' => '_s',
            'default' => C('CONFIG_FILE_PATH') . C('DEFAULT_AVATAR'),
        );

        $config = array_merge($default, $config);

        $avatar_info = get_path_info($info['mi_avatar']);
        $filename = $config['root'] . $config['path'] . date(C('AVATAR_SUBNAME_RULE'), $info['mi_created']) . '/' . $avatar_info['name'] . $config['size'] . '.' . $avatar_info['ext'];

        if (!file_exists($filename)) {
            // 默认图
            $filename = $config['root'] . $config['default'] . '.' . C('DEFAULT_IMAGE_EXT');
        }

        return substr($filename, 1);
    }

    public function delete($id) {

        // 获取用户身份列表
        $config['where']['mi_id'] = array('in', $id);
        $member_identity_list = D('MemberIdentity', 'Model')->getAll($config);

        // 删除记录
        $result = D('MemberIdentity', 'Model')->delete($config);
        if ($result !== false) {
            // 删除子表
            D('MemberIdentityDetail', 'Model')->delete($config);
            // 删除属性
            D('MemberIdentityAttributeRecord', 'Model')->delete($config);
            // 删除文件
            foreach ($member_identity_list as $member_identity_info) {
                // 删除图片
                $this->deleteImage(C('UPLOADS_ROOT_PATH') . C('MEMBER_IDENTITY_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $member_identity_info['mi_created']) . '/' . $member_identity_info['mi_avatar']);
            }
        }
    }

    // 用户标记为删除资源
    public function signDeleted($config, $data = array(), $table = 'Member', $is_check = true) {
        // 是否需要检查 默认身份
        if ($is_check) {
            // 默认身份
            $ckConfig = $config;
            $ckConfig['where']['mi_is_default'] = 1;
            $ckInfo = D('MemberIdentity', 'Model')->getOne($ckConfig);
            if ($ckInfo) {
                // 默认身份不允许删除
                $this->error = '默认身份不允许删除';
                return false;
            }
        }

        // 删除
        $default['mi_is_deleted'] = 1;
        $default['mi_deleted_time'] = time();
        $default['mi_deleted_table'] = $table;
        $default['mi_deleted_extend_id'] = $data['mi_deleted_extend_id'] ? $data['mi_deleted_extend_id'] : session(C('USER_AUTH_KEY'));

        return D('MemberIdentity', 'Model')->update($default, $config);
    }

    // 同步默认身份
    public function syncDefault($me_id) {
        // 读取用户表
        $config['where']['me_id'] = intval($me_id);
        $info = D('Member', 'Model')->getOne($config);
        if (!$info) {
            // 用户不存在
            return false;
        }

        // 检查当前操作是 添加 还是编辑
        $config['where']['mi_is_default'] = 1;
        $is_exist = D('MemberIdentity', 'Model')->getOne($config);
        if (!$is_exist) {
            // 默认身份不存在
            $type = 'add';
        }

        // 将用户信息导入到身份表
        $miInfo = array();
        foreach ($info as $key => $val) {
            if ($key != 'me_id') {
                $miKey = str_replace('me_', 'mi_', $key);
                $miInfo[$miKey] = $val;
            } else {
                $miInfo[$key] = $val;
            }
        }

        // 同步信息
        if ($type == 'add') {
            // 账号自动生成
            $miInfo['mi_account'] = getAccount('identity');
            $miInfo['mi_is_default'] = 1; // 默认身份
            // 增加用户默认身份
            $mi_id = D('MemberIdentity', 'Model')->insert($miInfo);
        } else {
            $defaultConfig['where']['me_id'] = $miInfo['me_id'];
            $defaultConfig['where']['mi_is_default'] = 1;

            // 身份信息
            $defaultInfo = D('MemberIdentity', 'Model')->getOne($defaultConfig);
            $mi_id = $defaultInfo['mi_id'];

            // 账号不用改
            unset($miInfo['mi_account']);
            unset($miInfo['me_id']);
            D('MemberIdentity', 'Model')->update($miInfo, array('where' => array('mi_id' => $mi_id)));
        }
        

        // 用户详情
        $detailConfig['where']['me_id'] = intval($me_id);
        $dinfo = D('MemberDetail', 'Model')->getOne($detailConfig);
        // 将用户信息导入到身份表
        $midInfo = array();
        foreach ($dinfo as $dkey => $dval) {
            $midKey = str_replace('md_', 'mid_', $dkey);
            $midInfo[$midKey] = $dval;
        }

        // 身份id
        unset($midInfo['me_id']);

        // 同步信息
        if ($type == 'add') {
            $midInfo['mi_id'] = intval($mi_id);
            // 增加身份详情
            D('MemberIdentityDetail', 'Model')->insert($midInfo);
        } else {
            D('MemberIdentityDetail', 'Model')->update($midInfo, array('where' => array('mi_id' => $mi_id)));
        }

        // 先删除 logo 图片
        if ($type != 'add') {
            $this->deleteImage(C('UPLOADS_ROOT_PATH') . C('MEMBER_IDENTITY_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $defaultInfo['mi_created']) . '/' . $defaultInfo['mi_avatar']);
        }

        // 再添加 logo 图片
        if ($info['me_avatar'] && file_exists(C('UPLOADS_ROOT_PATH') . C('MEMBER_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $info['me_created']) . '/' . $info['me_avatar'])) {
            // 复制原图
            fileCopy(C('UPLOADS_ROOT_PATH') . C('MEMBER_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $info['me_created']) . '/' . $info['me_avatar'], C('UPLOADS_ROOT_PATH') . C('MEMBER_IDENTITY_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $miInfo['mi_created']) . '/' . $miInfo['mi_avatar']);
            
            // 切图
            if (file_exists(C('UPLOADS_ROOT_PATH') . C('MEMBER_IDENTITY_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $miInfo['mi_created']) . '/' . $miInfo['mi_avatar'])) {
                $this->dealImage(C('UPLOADS_ROOT_PATH') . C('MEMBER_IDENTITY_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $miInfo['mi_created']) . '/' . $miInfo['mi_avatar']);
            }
        }

        return true;
    }
}