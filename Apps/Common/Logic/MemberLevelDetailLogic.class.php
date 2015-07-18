<?php
namespace Common\Logic;
class MemberLevelDetailLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default['order'] = 'mld_rank ASC';
        $default['where']['ml_id'] = intval($param['ml_id']);
        
        $config = array_merge($default, $config);

        // 获取数据
        $lists = D('MemberLevelDetail', 'Model')->getAll($config);

        $return = array();
        foreach ($lists as $key => $value) {
            
            // 图片
            $level = C('UPLOADS_ROOT_PATH') . C('MEMBER_LEVEL_PATH') . $value['mld_id'] . '.' . C('DEFAULT_IMAGE_EXT');
            $level = file_exists($level) ? $level : C('UPLOADS_ROOT_PATH') . C('CONFIG_FILE_PATH') . C('DEFAULT_IMAGE') . '.' . C('DEFAULT_IMAGE_EXT');
            $return[$key] = $value;
            $return[$key]['level'] = substr($level, 1);
        }

        // 输出数据
        return $return;
    }

    public function uploadLogo($data) {

        // 相关信息
        $config['exts'] = explode(',', strtolower(C('ALLOW_IMAGE_TYPE')));
        $config['savePath'] = C('MEMBER_LEVEL_PATH');
        
        return upload($data, $config);
    }

    public function insert($data, $upload_file = array()) {

        // 需要重命名的文件
        $rename_files = array();
        $insert_data = array();
        $msg = '';

        foreach ($data['title'] as $key => $title) {
            
            // 是否有 LOGO 上传
            if ($upload_file['file']['size'][$key] > 0) {

                // 文件上传
                $upload_info = array(
                    'name' => $upload_file['file']['name'][$key],
                    'type' => $upload_file['file']['type'][$key],
                    'tmp_name' => $upload_file['file']['tmp_name'][$key],
                    'error' => $upload_file['file']['error'][$key],
                    'size' => $upload_file['file']['size'][$key],
                );

                $file_info = D('MemberLevelDetail')->uploadLogo($upload_info);

                if (is_array($file_info)) {
                    $rename_files[$key] = array(
                        'savename' => $file_info['savename'],
                        'savepath' => $file_info['savepath'],
                        'ext' => $file_info['ext']
                    );
                } else {
                    // 上传失败 不需要输出错误消息
                    $msg .= '等级:' . $data['rank'][$key] . '上传文件失败,原因:' . $file_info . '<br/>';
                }
            }

            // 数据信息上传
            $detail_data = array(
                'ml_id' => intval($data['ml_id']),
                'mld_title' => $title,
                'mld_rank' => $data['rank'][$key],
                'mld_id' => intval($data['mld_id'][$key])
            );
            $add_data = D('MemberLevelDetail', 'Model')->create($detail_data);
            if ($add_data !== false) {
                $insert_data[$key] = $add_data;
            } else {
                // 不需要输出错误消息
                $msg .= '等级:' . $data['rank'][$key] . '保存信息错误,原因:' . D('MemberLevelDetail', 'Model')->getError() . '<br/>';
            }
        }

        if ($msg) {
            // 有错误
            $this->error = $msg;
            return false;
        }

        // 验证没问题的数据 入库
        foreach ($insert_data as $insert_key => $insert_value) {
            if ($insert_value['mld_id']) {
                // 编辑
                $mld_num = D('MemberLevelDetail', 'Model')->update($insert_value);
                // 修改上传文件名
                if ($mld_num !== false) {
                    // 有上传图的 先删除以前图片 在重命名
                    if ($rename_files[$insert_key]['savename']) {
                        // 删图
                        $this->deleteImage(C('UPLOADS_ROOT_PATH') . C('MEMBER_LEVEL_PATH') . $insert_value['mld_id'] . '.' . C('DEFAULT_IMAGE_EXT'));
                        // 重命名
                        fileRename($rename_files[$insert_key]['savename'], $insert_value['mld_id'] . '.' . C('DEFAULT_IMAGE_EXT'), C('UPLOADS_ROOT_PATH') . $rename_files[$insert_key]['savepath'], true);
                        // 切图
                        $this->dealImage(C('UPLOADS_ROOT_PATH') . C('MEMBER_LEVEL_PATH') . $insert_value['mld_id'] . '.' . C('DEFAULT_IMAGE_EXT'));
                    }
                }
            } else {
                // 添加
                $mld_id = D('MemberLevelDetail', 'Model')->insert($insert_value);
                // 修改上传文件名
                if ($mld_id !== false) {
                    fileRename($rename_files[$insert_key]['savename'], $mld_id . '.' . C('DEFAULT_IMAGE_EXT'), C('UPLOADS_ROOT_PATH') . $rename_files[$insert_key]['savepath'], true);
                    // 切图
                    $this->dealImage(C('UPLOADS_ROOT_PATH') . C('MEMBER_LEVEL_PATH') . $mld_id . '.' . C('DEFAULT_IMAGE_EXT'));
                }
            }
        }

        return true;
    }
}