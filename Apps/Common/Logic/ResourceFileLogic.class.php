<?php
namespace Common\Logic;
class ResourceFileLogic extends Logic {

    // 检查 hash 确定文件是否上传过
    public function checkHash($hash) {
        $rf_info = D('ResourceFile')->getOne(array('where' => array('rf_hash' => $hash)));
        if ($rf_info) {
            return $rf_info;
        }

        return false;
    }

    // 资源文件上传
    public function upload($files = array(), $config = array()) {
        
        // Chunking might be enabled
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
        if (isset($_REQUEST["name"])) {
            $name = $_REQUEST["name"];
        } elseif (!empty($_FILES)) {
            $name = $_FILES["file"]["name"];
        } else {
            $name = savename_rule();
        }
        
        // 临时目录
        $default['filePath'] = C('RESOURCE_TMP_PATH');
        $default['fileName'] = $name;
        $default['chunk'] = $chunk;
        $default['chunks'] = $chunks;

        $files = $files ? $files : $_FILES;
        $config = array_merge($default, $config);

        $return = plupload($_FILES, $config);
        // 上传成功
        if ($return['result']) {
            
            // 临时目录文件转移到真正位置
            $file_info = $this->uploadedFileDeal($return['result']);
            if (!$file_info['rf_id']) {
                // 上传成功之后记录到表
                $file_info = $this->insertTable($file_info);
            }

            if ($file_info['rf_id']) {
                // 返回资源文件 id
                $return['result'] = true;
                $return['id'] = $file_info['rf_id'];
                $return['rt_id'] = $file_info['rt_id'];
                $return['transform_status'] = $file_info['rf_transform_status'];
            } else {
                // 失败
                $return['error'] = array(
                    'code' => 105,
                    'message' => 'Failed to upload.',
                );
            }
        }

        return $return;
    }

    // 资源文件上传成功后记录到表
    public function insertTable($data) {

        // 入库数据
        $insert = array();
        $insert['rf_title'] = $data['uploadname'];
        $insert['rf_savename'] = $data['savename'];
        $insert['rf_ext'] = $data['ext'];
        $insert['rf_size'] = $data['size'];
        $insert['rt_id'] = get_resource_type_id_by_name($data['type']);
        $insert['rf_hash'] = $data['hash'];
        $insert['rf_created'] = $data['createTime'];
        $insert['rf_transform_status'] = $data['transform_status'];
        $insert['rf_transform_time'] = $data['transform_time'];
        
        $rf_id = D('ResourceFile')->insert($insert);
        // 上传成功
        if ($rf_id) {

            // 切图
            if ($data['type'] == 'image') {
                $this->dealImage($data['path']);
            }

            // 转码
            if ($data['type'] == 'document') {
                // 是否时时转码
                if (C('DOCUMENT_TRANSFORM_ALL_TIME')) {
                    D('ResourceFile')->trans(array($rf_id));
                }
            }
            if ($data['type'] == 'video') {
                // 是否时时转码
                if (C('VIDEO_TRANSFORM_ALL_TIME')) {
                    D('ResourceFile')->trans(array($rf_id));
                }
            }
            return array('rf_id' => $rf_id, 'rt_id' => $insert['rt_id'], 'rf_transform_status' => $insert['rf_transform_status']);
        }
        
        return false;
    }

    // 文件分割上传完成 后处理文件
    public function uploadedFileDeal($filePath) {
        // 检查文件是否上传过
        $fhash = sha1_file($filePath);
        $rf_info = D('ResourceFile')->checkHash($fhash);
        if ($rf_info) {
            // 有相同的 删除临时文件中的资源        并返回信息
            @unlink($filePath);
            return $rf_info;
        }

        // 真实目录
        $res_path = C('UPLOADS_ROOT_PATH') . C('RESOURCE_PATH');
        // 后缀
        $file_ext = get_file_ext($filePath);
        // 类型名
        $file_type = get_resource_type_name_by_ext($file_ext);
        // 子目录
        $resource_transform_path = explode(',', C('RESOURCE_TRANSFORM_PATH'));
        if (in_array($file_type, array('video', 'document'))) {
            $transform_status = 9;
            $transform_time = 0;
            $res_path .= $resource_transform_path[0] . '/' . $file_type . '/';
        } else {
            $transform_status = 1;
            $transform_time = time();
            $res_path .= $resource_transform_path[1] . '/' . $file_type . '/';
        }
        $create_time = time();
        $res_path .= date(C('RESOURCE_SUBNAME_RULE'), $create_time) . '/';

        // 检查目录是否存在
        if (!file_exists($res_path)) {
            mk_dir($res_path);
        }

        // 原名称
        $up_name = get_path_info($filePath, 'name');
        // 真实保存名称
        $res_savename = savename_rule();
        $file_ext = $file_ext ? $file_ext : 'other';

        // 文件重命名
        fileRename($filePath, $res_path . $res_savename . '.' . $file_ext);
        
        return array(
            'path' => $res_path . $res_savename . '.' . $file_ext,
            'uploadname' => $up_name,
            'savename' => $res_savename,
            'ext' => $file_ext,
            'hash' => sha1_file($res_path . $res_savename . '.' . $file_ext),
            'createTime' => $create_time,
            'size' => abs(filesize($res_path . $res_savename . '.' . $file_ext)),
            'type' => $file_type,
            'transform_status' => $transform_status,
            'transform_time' => $transform_time
        );
    }

    /*
     * 资源转码
     * ids 转码id
     * $is_set_time 是否定义转码文件的时间范围
     */
    public function trans($ids = array(), $is_set_time = true) {

        // 获取转码的信息
        $res_path = C('UPLOADS_ROOT_PATH') . C('RESOURCE_PATH');

        // 搜索条件   rt_id 2 视频 4 文本
        if ($is_set_time) {
            $prevTime = time() - 24 * 3600;
            $config['where']['rf_transform_time'] = array('LT', $prevTime);
        }
        $config['where']['rf_transform_status'] = array('NEQ', '1');
        

        // 如果传ids
        if ($ids) {
            $config['where']['rf_id'] = array('IN', implode(',', $ids));
        }

        // 查询字段
        $config['fields'] = 'rf_id,rf_title,rf_savename,rf_ext,rf_size,rt_id,rf_hash,rf_created,rf_transform_status,rf_transform_time';

        // 查询
        $transData = D('ResourceFile', 'Model')->getAll($config);

        // 如果没有要转码的资源，便退出
        if (!$transData) {
            return;
        }

        // 把要转码的资源更新为2，说明此资源正在转码
        $edit_status_sql = 'UPDATE ' . C('DB_PREFIX') . 'resource_file SET rf_transform_status = 2,rf_transform_time = ' . time() . ' WHERE rf_id IN (' . implode(',', getValueByField($transData, 'rf_id')) . ')';
        $edit_config['where']['rf_id'] = array('IN', implode(',', getValueByField($transData, 'rf_id')));
        $edit_data['rf_transform_status'] = 2;
        $edit_data['rf_transform_time'] = time();
        D('ResourceFile', 'Model')->update($edit_data, $edit_config);
        
        // 获取允许的文件类型
        $allowType = reloadCache('resourceType');

        // 保存错误和成功id
        $error = array();
        $success = array();

        $latex = C('DOCUMENT_LATEX');

        $resource_transform_path = explode(',', C('RESOURCE_TRANSFORM_PATH'));

        // 循环资源数组，依次转码
        foreach ($transData as $key => $value) {

            $rtp = $value['rf_transform_status'] == 1 ? 1 : 0;
            // 未转码时完整路径
            $old_dir = $res_path . $resource_transform_path[$rtp] . '/' . $allowType[$value['rt_id']]['rt_name'] . '/' . date(C('RESOURCE_SUBNAME_RULE'), $value['rf_created']) . '/';
            $filePath = $old_dir . $value['rf_savename'] . '.' . $value['rf_ext'];
            
            // 转码后完整路径
            $dir = $res_path . $resource_transform_path[1] . '/' . $allowType[$value['rt_id']]['rt_name'] . '/' . date(C('RESOURCE_SUBNAME_RULE'), $value['rf_created']) . '/';

            mk_dir($dir);

            // 判断要转码的资源是文档还是音视频
            if (in_array(strtolower($value['rf_ext']), $allowType[4]['rt_exts'])) {
                // 文档类型
                $resDoc = array();
                $resTxt = array();
                $resSwf = '';

                // $filePath原路径   生成路径
                // txt 格式   图片转码会失败   所有不验证 状态
                $resTxt = word2txt($filePath, $old_dir . $value['rf_savename'] . '.txt', $value);

                //if ($resTxt['status'] !== FALSE) {
                    // txt 转码成功

                    // $filePath原路径  生成路径
                    $resDoc = word2pdf($filePath, $old_dir . $value['rf_savename'] . '.pdf', $value);
                    
                    if ($resDoc['status'] !== FALSE) {
                        // pdf 转码成功

                        $resSwf = pdf2Swf($old_dir . $value['rf_savename'] . '.pdf', $old_dir . $value['rf_savename'] . '.swf');

                        if ($resSwf !== FALSE) {
                            // swf 转码成功
                            $success[] = $value['rf_id'];
                            
                            // 转码完成 移动文件 到转码成功 路径
                            // txt
                            fileRename($old_dir . $value['rf_savename'] . '.txt', $dir . $value['rf_savename'] . '.txt');
                            // pdf
                            fileRename($old_dir . $value['rf_savename'] . '.pdf', $dir . $value['rf_savename'] . '.pdf');
                            // swf
                            fileRename($old_dir . $value['rf_savename'] . '.swf', $dir . $value['rf_savename'] . '.swf');
                            // 源文件
                            fileRename($filePath, $dir . $value['rf_savename'] . '.' . $value['rf_ext']);

                            // 修改状态
                            $success_config['where']['rf_id'] = $value['rf_id'];
                            $success_data['rf_transform_status'] = 1;
                            D('ResourceFile', 'Model')->update($success_data, $success_config);

                            // 转码成功更新资源表 状态
                            $upConfig['where']['rf_id'] = $value['rf_id'];
                            $upConfig['where']['res_transform_status'] = array('NEQ', 1);
                            $upData['res_transform_status'] = 1;
                            D('Resource', 'Model')->update($upData, $upConfig);
                            break;
                        }
                    }
                //}

                $error[] = $value['rf_id'];
            } else {

                // 视频转码

                $mp4Name = videoToMp4($filePath, $old_dir, $value);

                if (FALSE !== $mp4Name) {
                    $success[] = $value['rf_id'];

                    // 转码完成 移动文件 到转码成功 路径
                    // mp4
                    fileRename($old_dir . $value['rf_savename'] . '.mp4', $dir . $value['rf_savename'] . '.mp4');
                    // mp4
                    fileRename($old_dir . $value['rf_savename'] . '_h.mp4', $dir . $value['rf_savename'] . '_h.mp4');
                    // mp4
                    fileRename($old_dir . $value['rf_savename'] . '.flv', $dir . $value['rf_savename'] . '.flv');
                    // mp4
                    fileRename($old_dir . $value['rf_savename'] . '_s.jpg', $dir . $value['rf_savename'] . '.jpg');
                    if (file_exists($dir . $value['rf_savename'] . '.jpg')) {
                        $this->dealImage($dir . $value['rf_savename'] . '.jpg');
                    }
                    // 源文件
                    fileRename($filePath, $dir . $value['rf_savename'] . '.' . $value['rf_ext']);

                    // 修改状态
                    $success_config['where']['rf_id'] = $value['rf_id'];
                    $success_data['rf_transform_status'] = 1;
                    D('ResourceFile', 'Model')->update($success_data, $success_config);

                    // 转码成功更新资源表 状态
                    $upConfig['where']['rf_id'] = $value['rf_id'];
                    $upConfig['where']['res_transform_status'] = array('NEQ', 1);
                    $upData['res_transform_status'] = 1;
                    D('Resource', 'Model')->update($upData, $upConfig);
                } else {
                    $error[] = $value['rf_id'];
                }
            }
        }

        // 有错误的id,便把其恢复成0
        if ($error) {
            $success_config['where']['rf_id'] = array('IN', implode(',', $error));
            $success_data['rf_transform_status'] = 3;
            D('ResourceFile', 'Model')->update($success_data, $success_config);
        }

        // 回调
        return $this->trans($ids);
    }

    // 删除
    public function delete($config) {
        
        if (!$config['where']){
            return false;
        }

        $file_info = D('ResourceFile', 'Model')->getOne($config);
        $resource_type = loadCache('resourceType');

        // 删除记录
        $result = D('ResourceFile', 'Model')->delete($config);

        // 非公共资源删除源文件
        if ($resource_type[$file_info['rt_id']]['rt_name'] == 'image') {
            // 源文件
            $filename = $this->getResourceFullPath($file_info, array('imageSize' => ''));
            @unlink('.' . $filename);
            // 大图
            $filename = $this->getResourceFullPath($file_info, array('imageSize' => 'b'));
            @unlink('.' . $filename);
            // 中图
            $filename = $this->getResourceFullPath($file_info, array('imageSize' => 'm'));
            @unlink('.' . $filename);
            // 小图
            $filename = $this->getResourceFullPath($file_info, array('imageSize' => 's'));
            @unlink('.' . $filename);
        } elseif ($resource_type[$file_info['rt_id']]['rt_name'] == 'document') {
            // 源文件
            $filename = $this->getResourceFullPath($file_info, array('documentFormat' => ''));
            @unlink('.' . $filename);
            // .pdf
            $filename = $this->getResourceFullPath($file_info, array('documentFormat' => '.pdf'));
            @unlink('.' . $filename);
            // .txt
            $filename = $this->getResourceFullPath($file_info, array('documentFormat' => '.txt'));
            @unlink('.' . $filename);
            // .swf
            $filename = $this->getResourceFullPath($file_info, array('documentFormat' => '.swf'));
            @unlink('.' . $filename);
        } elseif ($resource_type[$file_info['rt_id']]['rt_name'] == 'video') {
            // 源文件
            $filename = $this->getResourceFullPath($file_info, array('videoFormat' => ''));
            @unlink('.' . $filename);
            // .mp4
            $filename = $this->getResourceFullPath($file_info, array('videoFormat' => '.mp4'));
            @unlink('.' . $filename);
            // _h.mp4
            $filename = $this->getResourceFullPath($file_info, array('videoFormat' => '_h.mp4'));
            @unlink('.' . $filename);
            // .flv
            $filename = $this->getResourceFullPath($file_info, array('videoFormat' => '.flv'));
            @unlink('.' . $filename);
            // .jpg
            $filename = $this->getResourceFullPath($file_info, array('videoFormat' => '.jpg'));
            $this->deleteImage('.' . $filename);
        } else {
            // 源文件
            $filename = $this->getResourceFullPath($file_info);
            @unlink('.' . $filename);
        }

        return true;
    }

    public function getResourceImage($info, $config = array()) {
        $default = array(
            'rootPath' => C('UPLOADS_ROOT_PATH'),
            'resourcePath' => C('RESOURCE_PATH'),
            'size' => '_m',
            'default' => C('DEFAULT_RESOURCE'),
        );

        $config = array_merge($default, $config);

        $resource_type = reloadCache('resourceType');
        $path = $config['rootPath'];

        if (in_array($resource_type[$info['rt_id']]['rt_name'], array('video', 'image'))) {
            // 资源文件根目录
            $path .= $config['resourcePath'];

            // 转码路径
            $form_path = explode(',', C('RESOURCE_TRANSFORM_PATH'));
            $status = $info['rf_transform_status'] == 1 ? 1 : 0;
            $path .= $form_path[$status] . '/';

            // 类型路径
            $path .= $resource_type[$info['rt_id']]['rt_name'] . '/';

            // 时间子目录
            $path .= date(C('RESOURCE_SUBNAME_RULE'), $info['rf_created']) . '/';

            // 图片名
            $path .= $info['rf_savename'];

            // 图片尺寸
            $path .= $config['size'] ? $config['size'] : '';

            // 后缀名  此jpg图片是资源转码时切的图
            $path .= $resource_type[$info['rt_id']]['rt_name'] == 'video' ? '.jpg' : '.' . $info['rf_ext'];

        }

        // 是路径(即非视频或图片)   或者 是文件且文件不存在   显示默认后缀图
        if (is_dir($path) || !file_exists($path)) {
            $path = $config['rootPath'] . $config['resourcePath'] . $info['rf_ext'] . $config['size'] . '.' . C('DEFAULT_IMAGE_EXT');
        }

        // 默认图
        if (!file_exists($path)) {
            $path = $config['rootPath'] . $config['resourcePath'] . $config['default'] . $config['size'] . '.' . C('DEFAULT_IMAGE_EXT');
        }

        return substr($path, 1);
    }

    /**
     * 获取资源文件全路径
     * $info 资源信息
     * imageSize 图片尺寸 _s _m _b
     * documentFormat 文档格式 .pdf .txt .swf
     * videoFormat 视频格式  .mp4 _h.mp4 .flv _s.jpg
     */
    public function getResourceFullPath($info, $config = array()) {

        $default = array(
            'rootPath' => C('UPLOADS_ROOT_PATH'),
            'resourcePath' => C('RESOURCE_PATH'),
            'imageSize' => '_b',
            'videoFormat' => '.mp4',
            'documentFormat' => '.swf',
            'is_original' => false,
        );

        $config = array_merge($default, $config);

        $resource_type = reloadCache('resourceType');
        $path = $config['rootPath'];

        // 资源文件根目录
        $path .= $config['resourcePath'];

        // 转码路径
        $form_path = explode(',', C('RESOURCE_TRANSFORM_PATH'));
        $status = $info['rf_transform_status'] == 1 ? 1 : 0;
        $path .= $form_path[$status] . '/';

        // 类型路径
        $path .= $resource_type[$info['rt_id']]['rt_name'] . '/';

        // 时间子目录
        $path .= date(C('RESOURCE_SUBNAME_RULE'), $info['rf_created']) . '/';

        // 文件名
        $path .= $info['rf_savename'];

        // 文件尺寸及后缀
        $resource_ext = '';
        if (!$config['is_original'] && $resource_type[$info['rt_id']]['rt_name'] == 'image') {
            $resource_ext = $config['imageSize'] ? $config['imageSize'] . '.' . $info['rf_ext'] : '.' . $info['rf_ext'];
        } elseif (!$config['is_original'] && $resource_type[$info['rt_id']]['rt_name'] == 'document') {
            $resource_ext = ($info['rf_transform_status'] == 1 && $config['documentFormat']) ? $config['documentFormat'] : '.' . $info['rf_ext'];
        } elseif (!$config['is_original'] && $resource_type[$info['rt_id']]['rt_name'] == 'video') {
            $resource_ext = ($info['rf_transform_status'] == 1 && $config['videoFormat']) ? $config['videoFormat'] : '.' . $info['rf_ext'];
        } else {
            $resource_ext = '.' . $info['rf_ext'];
        }

        // 文件不存在
        if (!file_exists($path . $resource_ext) && $info['rf_transform_status'] != 1) {
            // 未转吗成功的文件  显示源文件
            $path .= '.' . $info['rf_ext'];
        } else {
            $path .= $resource_ext;
        }

        // 源文件不存在
        if (!file_exists($path)) {
            return '';
        }

        return substr($path, 1);
    }

}