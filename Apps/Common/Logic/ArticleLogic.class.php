<?php
namespace Common\Logic;
class ArticleLogic extends Logic {

    /**
     * art_status
     * 0 普通 1 发布  9回收站
     *
     * art_position
     * 0 普通 1 推荐 2 栏目推荐 9 首页推荐
     *
     */
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;

        // 查询条件
        // 确定列表种类
        switch (intval($param['type'])) {
            //case 2:
                // 审核列表(暂无)
                //break;
            case 3:
                // 回收站列表
                $where['art_is_deleted'] = array('NEQ', 1);
                $where['art_status'] = 9;
                if ($param['re_id']) {
                    $where['re_id'] = $param['re_id'];
                } elseif ($param['s_id']) {
                    $where['s_id'] = $param['s_id'];
                } else {
                    $where['re_id'] = '';
                    $where['s_id'] = 0;
                }
                break;
            case 4:
                // 资讯查看列表
                $where['art_is_deleted'] = array('NEQ', 1);
                $where['art_status'] = 1;
                if ($param['re_id']) {
                    $where['re_id'] = $param['re_id'];
                } elseif ($param['s_id']) {
                    $where['s_id'] = $param['s_id'];
                } else {
                    $where['re_id'] = '';
                    $where['s_id'] = 0;
                }
                break;
            default :
                // 资讯管理列表
                $where['art_is_deleted'] = array('NEQ', 1);
                $where['art_status'] = array('NEQ', 9);
                if (isset($param['re_id'])) {
                    $where['re_id'] = $param['re_id'];
                } elseif ($param['s_id']) {
                    $where['s_id'] = $param['s_id'];
                } else {
                    $where['re_id'] = '';
                    $where['s_id'] = 0;
                }
        }

        if ($param['art_title']) {
            $where['art_title'] = array('LIKE', '%' . $param['art_title'] . '%');
        }

        if ($param['m_id']) {
            $where['m_id'] = $param['m_id'];
        }

        $ca_id = strval(intval($param['ca_id']));

        if ($ca_id) {
            if (C('IS_AUTO_SEARCH_SUB_CATEGORY') || $config['is_open_sub']) {
                $caIds = D('Category')->getSubIds($ca_id);
                $where['ca_id'] = array('IN', $caIds);
            } else {
                $where['ca_id'] = $ca_id;
            }
        }

        if ($param['art_position']) {
            $where['art_position'] = intval($param['art_position']);
        }

        if ($param['starttime'] && $param['endtime']) {
            $where['art_designated_published'] = array('BETWEEN', array($param['starttime'], $param['endtime']));
        } elseif ($param['starttime']) {
            $where['art_designated_published'] = array('EGT', $param['starttime']);
        } elseif ($param['endtime']) {
            $where['art_designated_published'] = array('ELT', $param['endtime']);
        }

        if ($param['art_designated_published']) {
            $where['art_designated_published'] = array('ELT', $param['art_designated_published']);
        }

        $default = array(
            'where' => $where,
            'order' => 'art_position DESC,art_sort ASC,art_id DESC',
            'p' => intval($param['p']),
            'is_deal_result' => true,
        );

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D($this->name)->getListByPage($config);

        if ($config['is_deal_result']) {
            $category = D('Category')->getAll(array('fields' => 'ca_id,ca_title'));

            // 处理数据
            foreach ($lists['list'] as $key => $value) {
                if (isset($value['art_status'])) {
                    $lists['list'][$key]['art_status'] = getStatus($value['art_status']);
                }
                if ($value['ca_id']) {
                    $lists['list'][$key]['ca_title'] = $category[$value['ca_id']];
                }
                // 封面
                $lists['list'][$key]['art_cover'] = $this->getCover($value);

                // 获取图片列表
                if ($value['m_id'] == 2) {
                    $fileConfig['where']['f_table'] = 'Article';
                    $fileConfig['where']['f_record_id'] = $value['art_id'];
                    $fileConfig['order'] = 'f_sort ASC,f_id ASC';
                    $fileList = D('File')->getAll($fileConfig);
                    foreach ($fileList as $fileInfo) {
                        $filePath = $this->getFile($fileInfo);
                        if ($filePath) {
                            $fileInfo['filepath'] = $filePath;
                            $lists['list'][$key]['pic'][] = $fileInfo;
                        }
                    }
                }
            }
        }

        return $lists;
    }

    public function detail($id, $config = array()) {

        // 获取文章数据
        $default = array(
            'art_id' => $id,
        );

        $where = array_merge($default, $config);
        $article = D('Article')->getOne(array('where' => $where));
        
        // 封面
        $article['art_cover'] = $this->getCover($article);

        // 获取模型属性
        $model = reloadCache('model');
        $attributeConfig['where']['at_id'] = array('in', $model[$article['m_id']]['m_list']);
        $attributeConfig['fields'] = 'at_id,at_name,at_title,at_type,at_value';
        $attribute = D('Attribute')->getAll($attributeConfig);
        $attribute = setArrayByField($attribute, 'at_name');

        // 获取图片列表
        if ($article['m_id'] != 1) {
            $fileConfig['where']['f_table'] = 'Article';
            $fileConfig['where']['f_record_id'] = $id;
            $fileConfig['order'] = 'f_sort ASC,f_id ASC';
            $fileList = D('File')->getAll($fileConfig);
            // 缩率图
            foreach ($fileList as $fileInfo) {
                $fileInfo['fileoriginalpath'] = $this->getFile($fileInfo, array('size' => ''));
                $fileInfo['filepath'] = $this->getFile($fileInfo, array('size' => '_m'));
                $res['pic'][] = $fileInfo;
            }
        }

        // 组织文章属性数据
        $attributeRecordConfig['where']['art_id'] = $id;
        $attributeRecord = D('AttributeRecord')->getAll($attributeRecordConfig);
        foreach ($attributeRecord as $aKey => $aValue) {
            $attribute[$aValue['are_name']]['art_value'] = $aValue['are_value'];
        }

        foreach ($article as &$value) {
            $value = stripFilter(htmlspecialchars_decode($value));
        }

        // 产品，解决方案
        if ($article['m_id'] == 4) {
            $relationConfig['where']['relatedProducts'] = array('LIKE', '%' . $attribute['relatedProducts']['art_value'] . '%');
            $relationConfig['fields'] = 'art_id,art_title,art_short_title,art_published,m_id';
            $relation = D('Cache' . ucfirst($model[5]['m_name']))->getAll($relationConfig);
        }
        if ($article['m_id'] == 5) {
            $relationConfig['where']['relatedSolutions'] = array('like', '%' . $attribute['relatedSolutions']['art_value'] . '%');
            $relationConfig['fields'] = 'art_id,art_title,art_short_title,art_published,m_id';
            $relation = D('Cache' . ucfirst($model[4]['m_name']))->getAll($relationConfig);
        }

        $res['relation'] = $relation;
        $res['article'] = $article;
        $res['attribute'] = $attribute;
        return $res;
    }

    public function insert($data) {
        $data['art_designated_published'] = strtotime($data['art_designated_published']);
        $data['art_status'] = C('ARTICLE_IS_DEFAULT_PUBLISHED');

        // 文章内容过滤
        $filter_res = stringFilter($data['art_content']);
        if (!$filter_res['status']) {
            $this->error = $filter_res['info'];
            return false;
        }
        $data['art_content'] = strval($filter_res['str']);

        $save_data = D('Article', 'Model')->create($data);
        if ($save_data === false) {
            $this->error = D('Article', 'Model')->getError();
            return false;
        }

        if ($data['art_id']) {
            $cover_config['where']['art_id'] = $data['art_id'];
            $cover_config['fields'] = 'art_id,art_cover_path,art_cover_name';
            $cover = D('Article', 'Model')->getOne($cover_config);

            $result = D('Article', 'Model')->update($save_data);
            if ($result !== false && $data['art_cover_name']) {
                
                $this->deleteImage(C('UPLOADS_ROOT_PATH') . C('ARTICLE_COVER_PATH') . $cover['art_cover_path'] . '/' . $cover['art_cover_name']);
                $this->dealImage(C('UPLOADS_ROOT_PATH') . C('ARTICLE_COVER_PATH') . $data['art_cover_path'] . '/' . $data['art_cover_name']);
            }
        } else {
            unset($save_data['art_id']);
            $result = D('Article', 'Model')->insert($save_data);
            if ($result !== false && $data['art_cover_name']) {
                $this->dealImage(C('UPLOADS_ROOT_PATH') . C('ARTICLE_COVER_PATH') . $data['art_cover_path'] . '/' . $data['art_cover_name']);
            }
        }

        return $result;
    }

    public function getCover($info, $config = array()) {
        $default = array(
            'root' => C('UPLOADS_ROOT_PATH'),
            'path' => C('ARTICLE_COVER_PATH'),
            'size' => '_m',
            'default' => C('DEFAULT_COVER'),
        );

        $config = array_merge($default, $config);

        $cover_info = get_path_info($info['art_cover_name']);
        $filename = $config['root'] . $config['path'] . $info['art_cover_path'] . '/' . $cover_info['name'] . $config['size'] . '.' . $cover_info['ext'];

        if (!file_exists($filename)) {
            // 默认图
            $filename = $config['root'] . $config['path'] . $config['default'] . $config['size']. '.' . C('DEFAULT_IMAGE_EXT');
        }

        return substr($filename, 1);
    }

    public function getFile($info, $config = array()) {
        $default = array(
            'root' => C('UPLOADS_ROOT_PATH'),
            'path' => C('ARTICLE_FILE_PATH'),
            'size' => '_b',
        );

        $config = array_merge($default, $config);

        if ($config['size']) {
            // 缩略图
            $filename = $config['root'] . $config['path'] . $info['f_savepath'] . '/' . $info['f_id'] . $config['size'] . '.' . $info['f_ext'];
        } else {
            // 原图
            $filename = $config['root'] . $config['path'] . $info['f_savepath'] . '/' . $info['f_savename'];
        }

        if (!file_exists($filename)) {
            return '';
        }

        return substr($filename, 1);
    }

    public function delFile($id) {
        $config['where']['f_id'] = intval($id);
        $info = D('File', 'Model')->getOne($config);
        if ($info) {
            // 删除记录
            $res = D('File', 'Model')->delete(intval($id));
            if ($res === false) {
                return false;
            }
            // 删除文件
            $path = C('UPLOADS_ROOT_PATH') . $info['savepath'];
            $this->deleteImage($path . $info['sf_id'] . '.' . $info['sf_ext']);
        }

        return true;
    }

    public function uploadPic($files, $data) {
        
        // 所有文件
        $config['exts'] = explode(',', strtolower(C('ALLOW_IMAGE_TYPE')));
        $config['savePath'] = C('ARTICLE_FILE_PATH');
        $config['autoSub'] = true;
        $config['subName'] = array('date', C('ARTICLE_SUBNAME_RULE'));
        $config['width'] = 900;
        $config['height'] = 563;
        foreach ($files['size'] as $sKey => $sValue) {
            
            if ($sValue > 0) {
                // 文件上传
                $file = array();
                $config['saveName'] = $data['art_id'] . '_' . savename_rule($sKey);
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
                $fileConfig['f_title'] = $data['title'][$sKey];
                $fileConfig['f_savepath'] = $savepath[1];
                $fileConfig['f_savename'] = $info['savename'];
                $fileConfig['f_ext'] = $info['ext'];
                $fileConfig['f_size'] = $info['size'];
                $fileConfig['m_id'] = $data['m_id'];
                $fileConfig['f_table'] = 'Article';
                $fileConfig['f_record_id'] = $data['art_id'];
                $fileConfig['f_creator_table'] = $data['art_creator_table'] ? $data['art_creator_table'] : 'User';
                $fileConfig['f_creator_id'] = $data['art_creator_id'];
                $fileConfig['f_hash'] = $info['sha1'];
                $fileConfig['f_remark'] = $data['remark'][$sKey];
                $fileConfig['f_sort'] = intval($data['sort'][$sKey]);
                $fileConfig['f_created'] = time();
                $f_id = D('File')->insert($fileConfig);

                // 裁剪并水印
                $path = C('UPLOADS_ROOT_PATH') . $info['savepath'];
                $this->dealImage($path . $info['savename'], $path . $f_id . '.' . $info['ext']);

                // 文件信息删除
                unset($data['title'][$sKey]);
            }
        }

        // 修改的文件信息
        foreach ($data['title'] as $sKey => $sValue) {
            $fileConfig = array();
            if ($data['id'][$sKey]) {
                $fileConfig['f_id'] = $data['id'][$sKey];
                $fileConfig['f_title'] = $sValue;
                $fileConfig['f_remark'] = $data['remark'][$sKey];
                $fileConfig['f_sort'] = $data['sort'][$sKey];
                D('File')->update($fileConfig);
            }
        }
    }

    public function uploadVideo($files, $data) {
        
        if ($files['size'] <= 0) {
            // 没有文件上传 直接返回
            return true;
        }

        $config['exts'] = explode(',', strtolower(C('ALLOW_VIDEO_TYPE')));
        $config['savePath'] = C('ARTICLE_FILE_PATH');
        $config['autoSub'] = true;
        $config['subName'] = array('date', C('ARTICLE_SUBNAME_RULE'));
        // 上传
        $config['saveName'] = $data['art_id'] . '_' . savename_rule($sKey);
        $info = upload($files, $config);
        if (!is_array($info)) {
            $this->error = $info;
            return false;
        }

        $savepath = explode('/', $info['savepath']);
        // 入库
        $fileConfig['f_title'] = $info['name'];
        $fileConfig['f_savepath'] = $savepath[1];
        $fileConfig['f_savename'] = $info['savename'];
        $fileConfig['f_ext'] = $info['ext'];
        $fileConfig['f_size'] = $info['size'];
        $fileConfig['m_id'] = $data['m_id'];
        $fileConfig['f_table'] = 'Article';
        $fileConfig['f_record_id'] = $data['art_id'];
        $fileConfig['f_creator_table'] = $data['art_creator_table'] ? $data['art_creator_table'] : 'User';
        $fileConfig['f_creator_id'] = $data['art_creator_id'];
        $fileConfig['f_hash'] = $info['sha1'];
        $fileConfig['f_remark'] = $data['remark'];
        $fileConfig['f_sort'] = intval($data['sort']);
        $fileConfig['f_created'] = time();
        $f_id = D('File', 'Model')->insert($fileConfig);
        
        $path = C('UPLOADS_ROOT_PATH') . $info['savepath'];
        copy($path . $info['savename'], $path . $f_id . '.' . $info['ext']);
    }

    public function publishData($data) {
        $config['where']['art_id'] = array('IN', getValueByField($data, 'art_id'));
        $config['fields'] = 'art_id,are_name,are_value';
        $attributeRecord = D('AttributeRecord')->getAll($config);

        $data = setArrayByField($data, 'art_id');
        foreach ($attributeRecord as $value) {
            $data[$value['art_id']][$value['are_name']] = $value['are_value'];
        }

        $result = D('Model')->syncList($data, true);
    }

    // 删除到回收站 或 恢复到列表
    public function statusUpdate($id, $status = 9) {
        $config['where']['art_id'] = array('IN', $id);
        $data['art_status'] = $status;
        if ($status == 9) {
            // 更新删除时间
            $data['art_status_time'] = time();
        }
        return D('Article')->update($data, $config);
    }

    // 标记为删除
    public function deleteSign($id) {
        $config['where']['art_id'] = array('IN', $id);
        $data['art_is_deleted'] = 1;
        $data['art_deleted_time'] = time();
        return D('Article')->update($data, $config);
    }

    // 删除
    public function delete($id) {
        return D('Article', 'Model')->delete($id);
    }

    public function uploadedCoverDeal($coverPath) {
        // 后缀
        $file_ext = get_file_ext($coverPath);
        // 允许的后缀
        $allow_exts = explode(',', strtolower(C('ALLOW_IMAGE_TYPE')));
        if (!in_array($file_ext, $allow_exts)) {
            $this->error = '上传文件后缀不允许';
            return false;
        }

        $create_time = time();
        $path = C('UPLOADS_ROOT_PATH') . C('ARTICLE_COVER_PATH') . date(C('ARTICLE_SUBNAME_RULE'), $create_time) . '/';
        $savename = savename_rule();

        fileRename($coverPath, $path . $savename . '.' . $file_ext);

        return array(
            'path' => $path,
            'savename' => $savename . '.' . $file_ext,
            'ext' => $file_ext,
            'create_time' => $create_time,
        );
    }
}