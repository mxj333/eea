<?php
namespace Common\Logic;
class AppLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'is_deal_result' => true,
            'order' => 'a_id DESC',
            'p' => intval($param['p']),
        );

        if ($param['a_title']) {
            $default['where']['a_title'] = array('like', '%' . $param['a_title'] . '%');
        }

        if ($param['ac_id']) {
            $default['where']['ac_id'] = $param['ac_id'];
        }

        if ($param['id_id']) {
            $default['where']['id_id'] = $param['id_id'];
        }

        if (isset($param['aty_id'])) {
            if (!is_array($param['aty_id'])) {
                $param['aty_id'] = explode(',', $param['aty_id']);
            }
            $default['where']['aty_id'] = array('IN', $param['aty_id']);
        }

        if ($param['a_status']) {
            $default['where']['a_status'] = $param['a_status'];
        }

        if ($param['a_online_time']) {
            $default['where']['a_online_time'] = array('ELT', $param['a_online_time']);
        }

        if ($param['a_valided']) {
            $default['where']['a_valided'] = array('EGT', $param['a_valided']);
        }

        switch ($param['client_type']) {
            case 1:
                // 电脑端
                $default['where']['a_computer'] = array('neq', '');
                break;
            case 2:
                // iphone手机端
                $default['where']['a_ipa_phone'] = array('neq', '');
                break;
            case 3:
                // android手机端
                $default['where']['a_apk_phone'] = array('neq', '');
                break;
            case 4:
                // iphone ipad端
                $default['where']['a_ipa_plat'] = array('neq', '');
                break;
            case 5:
                // android ipad端
                $default['where']['a_apk_plat'] = array('neq', '');
                break;
            default :
                
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D('App')->getListByPage($config);

        if ($config['is_deal_result']) {
            $appIdentityLevel = D('Identity')->getLevel('id_id,id_title');
            $appCategory = reloadCache('appCategory');
            foreach ($lists['list'] as $key => $value) {
                if ($value['a_status']) {
                    $lists['list'][$key]['a_status'] = getStatus($value['a_status']);
                }
                if ($value['a_online_time']) {
                    $lists['list'][$key]['a_online_time'] = date('Y-m-d', $value['a_online_time']);
                }
                if ($value['a_valided']) {
                    $lists['list'][$key]['a_valided'] = date('Y-m-d', $value['a_valided']);
                }
                $lists['list'][$key]['id_title'] = strval($appIdentityLevel[$value['id_id']]);
                $lists['list'][$key]['ac_title'] = strval($appCategory[$value['ac_id']]);
                $lists['list'][$key]['a_logo'] = $this->getFile($lists['list'][$key]);
                $lists['list'][$key]['a_code_logo'] = $this->getFile($lists['list'][$key], 'code_logo');
            }
        }

        // 输出数据
        return $lists;
    }

    // 获取应用信息
    public function getById($id) {

        $config['where']['a_id'] = intval($id);
        $info = D('App', 'Model')->getOne($config);
        // 特殊字段处理
        if ($info['a_online_time']) {
            $info['a_online_time'] = date('Y-m-d', $info['a_online_time']);
        }
        if ($info['a_valided']) {
            $info['a_valided'] = date('Y-m-d', $info['a_valided']);
        }

        if ($info['ac_id']) {
            $cate = reloadCache('appCategory');
            $info['ac_title'] = $cate[$info['ac_id']];
        }

        if ($info['aty_id']) {
            $type = reloadCache('appType');
            $info['aty_title'] = $type[$info['aty_id']];
        }

        $info['a_logo'] = D('App')->getFile($info);
        $info['a_logo_b'] = D('App')->getFile($info, 'logo', array('size' => '_b'));
        $info['a_code_logo'] = D('App')->getFile($info, 'code_logo');
        $info['apk_plat'] = D('App')->getFile($info, 'apk_plat');
        $info['ipa_plat'] = D('App')->getFile($info, 'ipa_plat');
        $info['apk_plat'] = D('App')->getFile($info, 'apk_plat');
        $info['apk_phone'] = D('App')->getFile($info, 'apk_phone');
        $info['ipa_phone'] = D('App')->getFile($info, 'ipa_phone');
        $info['computer'] = D('App')->getFile($info, 'computer');
        
        // 评分
        $info['score'] = round($info['a_score_num']/$info['a_score_count']);
        if ($info['computer']) {
            $info['com_size'] = filesize('.' . $info['computer']);
        }

        // 应用图
        $fileConfig['where']['af_record_id'] = $id;
        $fileConfig['order'] = 'af_sort ASC,af_id ASC';
        $info['pic'] = D('AppFile')->getAll($fileConfig);

        return $info;
    }

    // 上传文件
    public function uploadFile($data, $type = 'logo') {

        $ufConfig = $this->uploadFileConfig($type);

        $config['exts'] = explode(',', $ufConfig['exts']);
        $config['savePath'] = $ufConfig['path'];

        $file = upload($data, $config);

        if (!is_array($file)) {
            $this->error = $file;
            return false;
        }

        return $file;
    }

    public function uploadPic($files, $data) {
        
        // 所有文件
        $config['exts'] = explode(',', strtolower(C('ALLOW_IMAGE_TYPE')));
        $config['savePath'] = C('APP_PIC_PATH');
        $config['autoSub'] = true;
        $config['subName'] = array('date', C('APP_SUBNAME_RULE'));
        $config['width'] = 180;
        $config['height'] = 180;
        foreach ($files['size'] as $sKey => $sValue) {
            
            if ($sValue > 0) {
                // 文件上传
                $file = array();
                $config['saveName'] = $data['a_id'] . '_' . savename_rule($sKey);
                $file['name'] = $files['name'][$sKey];
                $file['type'] = $files['type'][$sKey];
                $file['tmp_name'] = $files['tmp_name'][$sKey];
                $file['size'] = $sValue;
                $info = upload($file, $config);
                if (!is_array($info)) {
                    $this->error = $info;
                    return false;
                }

                $savepath = explode('/', $info['savepath']);

                // 入库
                $fileData = array();
                $fileConfig['af_title'] = $data['title'][$sKey];
                $fileConfig['af_savepath'] = $savepath[2];
                $fileConfig['af_savename'] = $info['savename'];
                $fileConfig['af_ext'] = $info['ext'];
                $fileConfig['af_size'] = $info['size'];
                $fileConfig['af_record_id'] = $data['a_id'];
                $fileConfig['af_hash'] = $info['sha1'];
                $fileConfig['af_remark'] = $data['remark'][$sKey];
                $fileConfig['af_sort'] = intval($data['sort'][$sKey]);
                $fileConfig['af_created'] = time();
                $af_id = D('AppFile')->insert($fileConfig);

                // 裁剪并水印
                $path = C('UPLOADS_ROOT_PATH') . $info['savepath'];
                $this->dealImage($path . $info['savename'], $path . $af_id . '.' . $info['ext']);

                // 文件信息删除
                unset($data['title'][$sKey]);
            }
        }

        // 修改的文件信息
        foreach ($data['title'] as $sKey => $sValue) {
            $fileConfig = array();
            if ($data['id'][$sKey]) {
                $fileConfig['af_id'] = $data['id'][$sKey];
                $fileConfig['af_title'] = $sValue;
                $fileConfig['af_remark'] = $data['remark'][$sKey];
                $fileConfig['af_sort'] = $data['sort'][$sKey];
                D('AppFile')->update($fileConfig);
            }
        }
    }

    // 删除文件
    public function deleteFile($info, $type = 'logo') {
        
        $ufConfig = $this->uploadFileConfig($type);

        if ($type == 'logo') {
            $this->deleteImage(C('UPLOADS_ROOT_PATH') . $ufConfig['path'] . $info['a_logo']);
        } elseif ($type == 'code_logo') {
            $this->deleteImage(C('UPLOADS_ROOT_PATH') . $ufConfig['path'] . $info['a_code_logo']);
        } else {
            @unlink(C('UPLOADS_ROOT_PATH') . $ufConfig['path'] . $info['a_id'] . '.' . $ufConfig['exts']);
        }
    }

    // 获取文件
    public function getFile($info, $type = 'logo', $config = array()) {

        $ufConfig = $this->uploadFileConfig($type);

        if (!in_array($type, array('logo', 'code_logo'))) {
            $filename = C('UPLOADS_ROOT_PATH') . $ufConfig['path'] . $info['a_id'] . '.' . $ufConfig['exts'];

            if (!file_exists($filename)) {
                $filename = '';
            }
        } else {
            $default['size'] = '_s';
            $config = array_merge($default, $config);

            $logo = $type == 'code_logo' ? $info['a_code_logo'] : $info['a_logo'];
            $logo_info = get_path_info($logo);

            $filename = C('UPLOADS_ROOT_PATH') . $ufConfig['path'] . $logo_info['name'] . $config['size'] . '.' .$logo_info['ext'];

            if (!file_exists($filename) && $type == 'logo') {
                $filename = C('UPLOADS_ROOT_PATH') . C('CONFIG_FILE_PATH') . C('DEFAULT_APP') . '.' . C('DEFAULT_IMAGE_EXT');
            } elseif (!file_exists($filename)) {
                $filename = '';
            }
        }

        return turnTpl($filename);
    }

    // 删除应用图
    public function delFile($id) {
        $config['where']['af_id'] = intval($id);
        $info = D('AppFile', 'Model')->getOne($config);
        if ($info) {
            // 删除记录
            $res = D('AppFile', 'Model')->delete(intval($id));
            if ($res === false) {
                return false;
            }
            // 删除文件
            $path = C('UPLOADS_ROOT_PATH') . C('APP_PIC_PATH') . $info['af_savepath'] . '/';
            $this->deleteImage($path . $info['af_id'] . '.' . $info['af_ext']);
        }

        return true;
    }

    public function uploadFileConfig($type = 'logo', $field = '') {
        $config = array(
            'logo' => array(
                'path' => C('APP_LOGO_PATH'),
                'exts' => strtolower(C('ALLOW_IMAGE_TYPE'))
            ),
            'code_logo' => array(
                'path' => C('APP_CODE_LOGO_PATH'),
                'exts' => strtolower(C('ALLOW_IMAGE_TYPE'))
            ),
            'apk_phone' => array(
                'path' => C('APP_PHONE_PATH'),
                'exts' => strtolower(C('ALLOW_ANDROID_TYPE'))
            ),
            'apk_plat' => array(
                'path' => C('APP_PLAT_PATH'),
                'exts' => strtolower(C('ALLOW_ANDROID_TYPE'))
            ),
            'ipa_phone' => array(
                'path' => C('APP_PHONE_PATH'),
                'exts' => strtolower(C('ALLOW_IOS_TYPE'))
            ),
            'ipa_plat' => array(
                'path' => C('APP_PLAT_PATH'),
                'exts' => strtolower(C('ALLOW_IOS_TYPE'))
            ),
            'computer' => array(
                'path' => C('APP_COMPUTER_PATH'),
                'exts' => strtolower(C('ALLOW_COMPUTER_TYPE'))
            ),
        );

        return $field ? $config[$type][$field] : $config[$type];
    }

    public function delete($id) {

        $config['where']['a_id'] = array('IN', $id);
        $app_list = D('App', 'Model')->getAll($config);

        // 删除记录
        $result = D('App', 'Model')->delete($config);

        if ($result !== false) {
            // 删除文件
            foreach ($app_list as $app_info) {
                // 删除图片
                $this->deleteImage(C('UPLOADS_ROOT_PATH') . C('APP_LOGO_PATH') . $app_info['a_logo']);
                // 删除二维码
                $this->deleteImage(C('UPLOADS_ROOT_PATH') . C('APP_LOGO_PATH') . $app_info['a_code_logo']);
                // 删除文件
                @unlink(C('UPLOADS_ROOT_PATH') . C('APP_PHONE_PATH') . $app_info['a_id'] . '.' . C('ALLOW_ANDROID_TYPE'));
                @unlink(C('UPLOADS_ROOT_PATH') . C('APP_PLAT_PATH') . $app_info['a_id'] . '.' . C('ALLOW_ANDROID_TYPE'));
                @unlink(C('UPLOADS_ROOT_PATH') . C('APP_PHONE_PATH') . $app_info['a_id'] . '.' . C('ALLOW_IOS_TYPE'));
                @unlink(C('UPLOADS_ROOT_PATH') . C('APP_PLAT_PATH') . $app_info['a_id'] . '.' . C('ALLOW_IOS_TYPE'));
                @unlink(C('UPLOADS_ROOT_PATH') . C('APP_COMPUTER_PATH') . $app_info['a_id'] . '.' . C('ALLOW_COMPUTER_TYPE'));
            }
        }

        return $result;
    }

    // 评分
    public function addScore($config) {

        // 记录日志
        $data = array();
        foreach($config['log'] as $info) {
            $data[] = array(
                'a_id' => intval($config['a_id']),
                'as_id' => intval($info['as_id']),
                'asl_score' => intval($info['asl_score']),
                'me_id' => intval($_SESSION[C('USER_AUTH_KEY')]),
                'asl_created' => time(),
            );
        }
        $rslConfig['fields'] = 'a_id,as_id,asl_score,me_id,asl_created';
        $rslConfig['values'] = $data;
        D('AppScoreLog', 'Model')->insertAll($rslConfig);

        // 更新资源信息
        D('App', 'Model')->increase(array('a_score_num', 'a_score_count'), array('a_id' => $config['a_id']), array($config['sum_score'], 1));
    }
}