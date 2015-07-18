<?php
namespace Common\Logic;
class MemberLogic extends Logic {

    public function check($account = '', $status = 1) {

        if ($status) {
            $user['where']['me_status'] = $status;
        }
        $user['where']['me_is_deleted'] = 9;
        $user['where']['me_account'] = strval($account);
        $user['where']['me_validity'] = array('gt', time());
        return D($this->name, 'Model')->getOne($user);
    }

    public function saveLoginStatus($me_id, $count = 0, $config = array()) {

        if (!$me_id) {
            return false;
        }

        // 登录信息更新
        $member['me_id'] = $me_id;
        $member['me_last_login_time'] = time();
        $member['me_last_login_ip'] = rewrite_ip2long(get_client_ip());
        $member['me_login_count'] = $count + 1;
        $member = array_merge($member, (array)$config['member']);
        D($this->name, 'Model')->update($member);

        // 登录日志
        $log['me_id'] = $me_id;
        $log['mll_ip'] = rewrite_ip2long(get_client_ip());
        $log['mll_created'] = time();
        $log = array_merge($log, (array)$config['log']);
        D('MemberLoginLog', 'Model')->insert($log);

        return true;
    }

    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        // 用户的身份列表
        if ($param['id']) {
            $param['me_id'] = $param['id'];
            unset($param['id']);
            return D('MemberIdentity')->lists($param);
        }

        $default = array(
            'is_deal_result' => true,
            'order' => 'me_id DESC',
            'p' => intval($param['p']),
            'where' => array('me_is_deleted' => 9),
        );

        if ($param['me_id']) {
            $default['where']['me_id'] = $param['me_id'];
        }

        if ($param['me_nickname']) {
            $default['where']['me_nickname'] = array('LIKE', '%' . $param['me_nickname'] . '%');
        }

        if ($param['me_account']) {
            $default['where']['me_account'] = $param['me_account'];
        }

        if (isset($param['me_type'])) {
            $default['where']['me_type'] = intval($param['me_type']);
        }

        if (isset($param['s_id'])) {
            $default['where']['s_id'] = intval($param['s_id']);
        }

        if (isset($param['c_id'])) {
            $default['where']['c_id'] = intval($param['c_id']);
        }

        if ($param['re_id']) {
            $regConfig['where']['re_ids'] = $param['re_id'];
            $regConfig['fields'] = 're_ids_children';
            $region = D('Region', 'Model')->getOne($regConfig);
            $region = $region ? $region . ',' . $param['re_id'] : $param['re_id'];
            $default['where']['re_id'] = array('IN', $region);
        }

        if ($param['me_validity']) {
            $default['where']['me_validity'] = array('EGT', $param['me_validity']);
        }

        if ($param['me_status']) {
            $default['where']['me_status'] = $param['me_status'];
        }
        if ($param['me_mobile']) {
            $default['where']['me_mobile'] = $param['me_mobile'];
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('Member')->getListByPage($config);

        if ($config['is_deal_result']) {
            foreach ($lists['list'] as $key => $value) {
                if ($value['me_status']) {
                    $lists['list'][$key]['me_status'] = getStatus($value['me_status']);
                }
                if ($value['s_id']) {
                    $sinfo = D('School')->getById($value['s_id']);
                    $lists['list'][$key]['s_title'] = $sinfo['s_title'];
                }
            }
        }

        // 输出数据
        return $lists;
    }

    public function getById($id, $config = array()) {

        $default['is_deal_result'] = true;
        $config = array_merge($default, $config);

        $memberConfig['where']['me_id'] = intval($id);
        $info = D('Member', 'Model')->getOne($memberConfig);
        $dinfo = D('MemberDetail', 'Model')->getOne($memberConfig);
        $ainfo = D('MemberAttributeRecord')->getinfo($id);
        
        if ($config['is_deal_result']) {
            // 生日
            $dinfo['md_birthday'] = date('Y-m-d', $dinfo['md_birthday']);
            $info['me_validity'] = date('Y-m-d', $info['me_validity']);
            // 学校
            if ($info['s_id']) {
                $sinfo = D('School')->getById($info['s_id']);
                $info['s_title'] = $sinfo['s_title'];
            }

            // 教师
            if ($info['me_type'] == 1 && $info['s_id']) {
                // 教师所教学科
                $teaConfig['s_id'] = $info['s_id'];
                $teaConfig['me_id'] = $info['me_id'];
                $teacherSubject = D('ClassInstructors')->lists($teaConfig, array('fields' => 't_id,s_id,c_id,me_id'));
                $subject = array_keys($teacherSubject['list']);
                $info['subject'] = $subject;
                $tag = reloadCache('tag');
                foreach ($subject as $sub_id) {
                    $info['subject_title'][] = strval($tag[5][$sub_id]);
                }
            }

            // 学生
            if ($info['me_type'] == 2 && $info['c_id']) {
                $cinfo = D('Class')->getById($info['c_id']);
                $info['c_title'] = $cinfo['c_title'];
                $tag = reloadCache('tag');
                $info['c_grade'] = strval($tag[7][$cinfo['c_grade']]);
            }
        }
        
        return array_merge((array)$info, (array)$dinfo, (array)$ainfo);
    }

    public function uploadLogo($data) {

        // 文件上传
        $config['exts'] = explode(',', strtolower(C('ALLOW_IMAGE_TYPE')));
        $config['savePath'] = C('MEMBER_LOGO_PATH');

        $logo = upload($data, $config);

        if (!is_array($logo)) {
            $this->error = $logo;
            return false;
        }

        return $logo;
    }

    // logo 图片处理
    public function uploadedLogoDeal($logo, $rule = '') {
        // 后缀
        $logo_ext = get_file_ext($logo);
        if (!in_array($logo_ext, explode(',', strtolower(C('ALLOW_IMAGE_TYPE'))))) {
            @unlink($logo);
            $this->error = '上传图片类型错误';
            return false;
        }

        $logoPath = C('UPLOADS_ROOT_PATH') . C('MEMBER_LOGO_PATH');
        // 检查目录是否存在
        if (!file_exists($logoPath)) {
            mk_dir($logoPath);
        }

        // 真实保存名称
        $logo_savename = savename_rule($rule);
        $logo_ext = $logo_ext ? $logo_ext : C('DEFAULT_IMAGE_EXT');

        // 文件重命名
        fileRename($logo, $logoPath . $logo_savename . '.' . $logo_ext);

        return $logo_savename . '.' . $logo_ext;
    }

    public function insert($data, $type = 'Member') {

        if (!$data['me_id']) {
            // 添加时 默认值
            $data['me_creator_id'] = $data['me_creator_id'] ? $data['me_creator_id'] : $_SESSION[C('USER_AUTH_KEY')];
            $data['me_creator_table'] = $data['me_creator_table'] ? $data['me_creator_table'] : $type;
        }

        // 数据验证
        $member_data = D('Member', 'Model')->create($data);
        if (false === $member_data) {
            $this->error = D('Member', 'Model')->getError();
            return false;
        }
        
        // 数据验证
        $member_detail_data = D('MemberDetail', 'Model')->create($data);
        if (false === $member_detail_data) {
            $this->error = D('MemberDetail', 'Model')->getError();
            return false;
        }

        // 验证证件号
        if ($member_detail_data['md_card_type'] == 0 && $member_detail_data['md_card_num'] && !preg_match('/^(\d{14}|\d{17})(\d|x|X)$/', $member_detail_data['md_card_num'])) {
            $this->error = '证件号格式错误';
            return false;
        }

        // 验证账号
        if ($data['me_account'] && (!preg_match('/^(\d+)$/', $data['me_account']) || (strlen(intval($data['me_account'])) > C('CURRENT_ACCOUNT_NUMBER')))) {
            $this->error = '账号必需是数字，且小于等于' . C('CURRENT_ACCOUNT_NUMBER') . '位';
            return false;
        }

        if ($data['me_id']) {
            // 编辑  账号不能修改
            unset($member_data['me_account']);
            // 密码处理
            if ($member_data['me_password']) {
                $member_data['me_password'] = pwdHash($member_data['me_password']);
            } else {
                unset($member_data['me_password']);
            }
            
            // 获取用户信息
            $member_info = D('Member')->getById(intval($data['me_id']));

            // 更新用户信息
            $me_res = D('Member', 'Model')->update($member_data);
            if ($me_res !== false) {
                // 更新用户详情表
                $member_detail_data['md_id'] = $member_info['md_id'];
                D('MemberDetail', 'Model')->update($member_detail_data);
                // 添加用户属性
                D('MemberAttributeRecord')->insert($_POST);
                // 删除以前的图片
                if ($data['me_avatar']) {
                    $this->deleteImage(C('UPLOADS_ROOT_PATH') . C('MEMBER_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $member_info['me_created']) . '/' . $member_info['me_avatar']);
                    fileRename(C('UPLOADS_ROOT_PATH') . C('MEMBER_LOGO_PATH') . $data['me_avatar'], C('UPLOADS_ROOT_PATH') . C('MEMBER_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $member_info['me_created']) . '/' . $data['me_avatar']);
					$this->dealImage(C('UPLOADS_ROOT_PATH') . C('MEMBER_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $member_info['me_created']) . '/' . $data['me_avatar']);
                }

                // 用户更新完  更新默认身份
                D('MemberIdentity')->syncDefault(intval($data['me_id']));
            }
        } else {
            // 添加
            if ($data['me_account']) {
                // 大平台后台 指定账号
                $member_data['me_account'] = intval($data['me_account']);

                // 检查账号是否存在
                $info = D('Member')->getOne(array('where' => array('me_account' => $data['me_account'])));
                if ($info) {
                    $this->error = '账号已经存在';
                    return false;
                }

                // 删除账号文件中该账号
                deleteAccount($member_data['me_account']);
            } else {
                // 非大平台后台  自动生成账号
                $member_data['me_account'] = getAccount();
            }

            // 密码处理
            if ($member_data['me_password']) {
                $member_data['me_password'] = pwdHash($member_data['me_password']);
            } else {
                $member_data['me_password'] = pwdHash(C('MEMBER_DEFAULT_PASSWORD'));
            }
            
            $me_res = D('Member', 'Model')->insert($member_data);
            if ($me_res !== false) {
                // 添加用户详情
                $member_detail_data['me_id'] = $me_res;
                $member_detail_data['md_register_ip'] = is_numeric($member_detail_data['md_register_ip']) ? $member_detail_data['md_register_ip'] : rewrite_ip2long(get_client_ip());
                D('MemberDetail', 'Model')->insert($member_detail_data);
                // 添加用户属性
                $_POST['me_id'] = $me_res;
                D('MemberAttributeRecord')->insert($_POST);
                if ($data['me_avatar']) {
                    // 图片 子目录
                    fileRename(C('UPLOADS_ROOT_PATH') . C('MEMBER_LOGO_PATH') . $data['me_avatar'], C('UPLOADS_ROOT_PATH') . C('MEMBER_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $member_data['me_created']) . '/' . $data['me_avatar']);
                    // 切图
                    $this->dealImage(C('UPLOADS_ROOT_PATH') . C('MEMBER_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $member_data['me_created']) . '/' . $data['me_avatar']);
                }

                // 用户添加完  增加默认身份
                D('MemberIdentity')->syncDefault(intval($me_res));
            }
        }

        return $me_res;
    }

    // 获取用户头像
    public function getAvatar($info, $config = array()) {
        $default = array(
            'root' => C('UPLOADS_ROOT_PATH'),
            'path' => C('MEMBER_LOGO_PATH'),
            'size' => '_s',
            'default' => C('CONFIG_FILE_PATH') . C('DEFAULT_AVATAR'),
        );

        $config = array_merge($default, $config);

        $avatar_info = get_path_info($info['me_avatar']);
        $filename = $config['root'] . $config['path'] . date(C('AVATAR_SUBNAME_RULE'), $info['me_created']) . '/' . $avatar_info['name'] . $config['size'] . '.' . $avatar_info['ext'];

        if (!file_exists($filename)) {
            // 默认图
            $filename = $config['root'] . $config['default'] . '.' . C('DEFAULT_IMAGE_EXT');
        }

        return substr($filename, 1);
    }

    public function delete($id) {

        // 获取用户信息
        $config['where']['me_id'] = array('in', $id);
        $member_list = D('Member', 'Model')->getAll($config);

        // 删除记录
        $result = D('Member', 'Model')->delete($config);
        if ($result !== false) {
            // 删除子表
            D('MemberDetail', 'Model')->delete($config);
            // 删除属性
            D('MemberAttributeRecord', 'Model')->delete($config);
            // 删除文件
            foreach ($member_list as $member_info) {
                // 删除图片
                $this->deleteImage(C('UPLOADS_ROOT_PATH') . C('MEMBER_LOGO_PATH') . date(C('AVATAR_SUBNAME_RULE'), $member_info['me_created']) . '/' . $member_info['me_avatar']);
            }
        }

        return $result;
    }

    // 用户标记为删除资源
    public function signDeleted($config, $data = array(), $table = 'Member') {

        // 删除
        $default['me_is_deleted'] = 1;
        $default['me_deleted_time'] = time();
        $default['me_deleted_table'] = $table;
        $default['me_deleted_extend_id'] = $data['me_deleted_extend_id'] ? $data['me_deleted_extend_id'] : session(C('USER_AUTH_KEY'));

        $result = D('Member')->update($default, $config);
        if ($result !== false) {
            // 删除对应身份
            D('MemberIdentity')->signDeleted($config, array('mi_deleted_extend_id' => $default['me_deleted_extend_id']), 'User', false);
        }
    }
}