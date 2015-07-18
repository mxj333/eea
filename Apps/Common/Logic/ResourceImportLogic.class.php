<?php
namespace Common\Logic;
class ResourceImportLogic extends Logic {
    
    /**
     * 资源导入
     * $type 1 平台后台
     */
    public function import($data, $config = array()) {

        if (!$data['file']['size'] || ($data['file']['type'] != 'application/vnd.ms-excel')) {
            $this->error = '上传文件错误';
            return false;
        }

        //要导入的xls文件
        $filename = $data['file']['tmp_name'];

        //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
        import("Org.Util.Excel.Spreadsheet_Excel_Reader");
        //创建Excel对象，注意，不能少了\
        $PHPExcel = new \Spreadsheet_Excel_Reader();
        // 文件编码
        $PHPExcel->setOutputEncoding('utf-8');
        // 读取文件
        $PHPExcel->read($filename);

        // 总行数
        $numRows = $PHPExcel->sheets[0]['numRows'];

        // 每次最大导入条数验证
        if ($numRows > C('IMPORT_EXCEL_MAX_NUMBER')) {
            $this->error = '超出文件最大导入数' . C('IMPORT_EXCEL_MAX_NUMBER') . ',请分批导入';
            return false;
        }

        // 检查模板
        $check = $this->checkTemplate($PHPExcel);
        if (!$check) {
            return false;
        }

        // 检查数据有效性
        $valid = $this->checkValid($PHPExcel);
        if (!$valid) {
            return false;
        }
        
        // 入库数据
        return $this->insert($PHPExcel, $config);
    }

    // 检查模板是否正确
    public function checkTemplate($PHPExcel) {

        // 列信息
        $columnInfo = $this->getColumnInfo();
        foreach ($columnInfo as $key => $info) {
            if ($PHPExcel->sheets[0]['cells'][1][$key] != $info) {
                $this->error = '模板错误,请下载资源文件模板';
                return false;
            }
        }
        
        return true;
    }

    // 检查数据有效性
    public function checkValid($PHPExcel) {

        // 总行数
        $numRows = $PHPExcel->sheets[0]['numRows'];
        $resourceCategory = loadCache('resourceCategory');
        $tag = loadCache('tag');
        $region = loadCache('region');
        $status = array('是' => 1, '否' => 9);
        $record = 0;

        for ($start = 2; $start <= $numRows; $start++) {
            
            // 过滤空行
            if (!implode('', $PHPExcel->sheets[0]['cells'][$start])) {
                continue;
            }

            // 标题
            $res_title = $PHPExcel->sheets[0]['cells'][$start][1];
            if (!$res_title) {
                $this->error = $start . '行:请填写标题';
                return false;
            }
            $res_title_length = get_string_total_length($res_title);
            if ($res_title_length < 2 || $res_title_length > 50) {
                $this->error = $start . '行:标题范围2-50个字';
                return false;
            }

            // 资源地址
            $res_url = $PHPExcel->sheets[0]['cells'][$start][2];
            if (!$this->check($res_url)) {
                $this->error = $start . '行:资源文件不存在,请填写有效资源地址';
                return false;
            }

            // 使用类型
            $rc_title = $PHPExcel->sheets[0]['cells'][$start][3];
            if (!in_array($rc_title, $resourceCategory)) {
                $this->error = $start . '行:非法类型';
                return false;
            }

            // 地区
            $province = $PHPExcel->sheets[0]['cells'][$start][4];
            if ($province) {
                $province_id = array_search($province, $region[1]);
                if (!$province_id) {
                    $this->error = $start . '行:省份无效';
                    return false;
                }
                $city = $PHPExcel->sheets[0]['cells'][$start][5];
                if ($city) {
                    $city_id = array_search($city, $region[$province_id]);
                    if (!$city_id) {
                        $this->error = $start . '行:城市无效';
                        return false;
                    }
                    $area = $PHPExcel->sheets[0]['cells'][$start][6];
                    if ($area) {
                        $area_id = array_search($area, $region[$city_id]);
                        if (!$area_id) {
                            $this->error = $start . '行:区域无效';
                            return false;
                        }
                    }
                }
            }

            // 原创 作者
            $res_is_original = $PHPExcel->sheets[0]['cells'][$start][11];
            $res_author = $PHPExcel->sheets[0]['cells'][$start][12];
            if ($status[$res_is_original] == 1 && !$res_author) {
                $this->error = $start . '行:请填写作者';
                return false;
            }

            // 学习者
            $res_audience_learner = $PHPExcel->sheets[0]['cells'][$start][15];
            if (!in_array($res_audience_learner, $tag[2])) {
                $this->error = $start . '行:非法学习者类型';
                return false;
            }

            // 教育类型
            $res_audience_educational_type = $PHPExcel->sheets[0]['cells'][$start][16];
            if (!in_array($res_audience_educational_type, $tag[3])) {
                $this->error = $start . '行:非法教育类型';
                return false;
            }

            // 智慧豆
            $res_download_points = $PHPExcel->sheets[0]['cells'][$start][23];
            if ($res_download_points && !preg_match('/^[0-9]+$/', $res_download_points)) {
                $this->error = $start . '行:智慧豆必须为数字';
                return false;
            }

            // 发行时间
            $res_issused = $PHPExcel->sheets[0]['cells'][$start][24];
            if ($res_issused && !strtotime($res_issused)) {
                $this->error = $start . '行:发行时间无效';
                return false;
            }
            
            // 显示时间
            $res_valid = $PHPExcel->sheets[0]['cells'][$start][27];
            if ($res_valid && !strtotime($res_valid)) {
                $this->error = $start . '行:显示时间无效';
                return false;
            }

            // 可利用时间
            $res_avaliable = $PHPExcel->sheets[0]['cells'][$start][28];
            if ($res_avaliable && !strtotime($res_avaliable)) {
                $this->error = $start . '行:可利用时间无效';
                return false;
            }

            $record++;
        }
        
        // 空数据
        if ($record == 0) {
            $this->error = '空数据,请填写数据';
            return false;
        }

        return true;
    }

    public function insert($PHPExcel, $config = array()) {
        $default = array(
            'res_creator_table' => 'User',
            'res_creator_id' => $_SESSION[C('USER_AUTH_KEY')],
            'type' => 1
        );
        $config = array_merge($default, $config);

        // 总行数
        $numRows = $PHPExcel->sheets[0]['numRows'];
        $resourceCategory = array_flip(loadCache('resourceCategory'));
        $tag = loadCache('tag');
        $learner = array_flip($tag[2]);
        $educational = array_flip($tag[3]);
        $status = array('是' => 1, '否' => 9);
        $region = loadCache('region');
        $data = array();
        $dataFields = array();

        for ($start = 2; $start <= $numRows; $start++) {

            // 过滤空行
            if (!implode('', $PHPExcel->sheets[0]['cells'][$start])) {
                continue;
            }
            
            $rowData = array();
            $rowData['res_creator_id'] = intval($config['res_creator_id']);
            $rowData['res_creator_table'] = strval($config['res_creator_table']);

            // 检查资源是否存在
            $res_path = $PHPExcel->sheets[0]['cells'][$start][2];
            if (!$this->check($res_path)) {
                // 资源文件不存在
                continue;
            }
            // 资源处理
            $res_info = $this->dealResource($res_path);
            if (!$res_info['rf_id']) {
                // 资源入库资源表
                $res_info = D('ResourceFile')->insertTable($res_info);
                if (!$res_info) {
                    // 资源文件添加失败;
                    continue;
                }
            }
            
            // 资源id
            $rowData['rf_id'] = intval($res_info['rf_id']);
            $rowData['rt_id'] = intval($res_info['rt_id']);
            $rowData['res_transform_status'] = intval($res_info['rf_transform_status']);

            // 标题
            $rowData['res_title'] = strval($PHPExcel->sheets[0]['cells'][$start][1]);
            // 使用类型
            $rc_title = $PHPExcel->sheets[0]['cells'][$start][3];
            $rowData['rc_id'] = intval($resourceCategory[$rc_title]);
            // 地区
            $re_id = 1;
            $re_title = $region[0][1];
            $province = $PHPExcel->sheets[0]['cells'][$start][4];
            if ($province) {
                $province_id = array_search($province, $region[1]);
                if ($province_id) {
                    $re_id .= '-' . $province_id;
                    $re_title .= '-' . $province;
                }
                $city = $PHPExcel->sheets[0]['cells'][$start][5];
                if ($province_id && $city) {
                    $city_id = array_search($city, $region[$province_id]);
                    if ($city_id) {
                        $re_id .= '-' . $city_id;
                        $re_title .= '-' . $city;
                    }
                    $area = $PHPExcel->sheets[0]['cells'][$start][6];
                    if ($city_id && $area) {
                        $area_id = array_search($area, $region[$city_id]);
                        if ($area_id) {
                            $re_id .= '-' . $area_id;
                            $re_title .= '-' . $area;
                        }
                    }
                }
            }
            $rowData['re_id'] = $re_id;
            $rowData['re_title'] = $re_title;
            // 推荐
            $res_is_recommend = $PHPExcel->sheets[0]['cells'][$start][7];
            $rowData['res_is_recommend'] = $status[$res_is_recommend] ? $status[$res_is_recommend] : 9;
            $rowData['res_is_recommend'] = $this->dealTypeValue($rowData['res_is_recommend'], $config['type']);
            // 记录日志

            // 优质
            $res_is_excellent = $PHPExcel->sheets[0]['cells'][$start][8];
            $rowData['res_is_excellent'] = $status[$res_is_excellent] ? $status[$res_is_excellent] : 9;
            $rowData['res_is_excellent'] = $this->dealTypeValue($rowData['res_is_excellent'], $config['type']);
            // 记录日志

            // 推送
            $res_is_pushed = $PHPExcel->sheets[0]['cells'][$start][9];
            $rowData['res_is_pushed'] = $status[$res_is_pushed] ? $status[$res_is_pushed] : 9;
            $rowData['res_is_pushed'] = $this->dealTypeValue($rowData['res_is_pushed'], $config['type']);
            // 记录日志

            // 发布
            $res_is_published = $PHPExcel->sheets[0]['cells'][$start][10];
            $rowData['res_is_published'] = $status[$res_is_published] ? $status[$res_is_published] : 9;
            $rowData['res_is_published'] = $this->dealTypeValue($rowData['res_is_published'], $config['type']);
            // 记录日志
            $rowData['res_published_time'] = 0;
            if (substr($rowData['res_is_published'], ($config['type']-1), 1) == 1) {
                $rowData['res_published_time'] = time();
            }
            // 原创
            $res_is_original = $PHPExcel->sheets[0]['cells'][$start][11];
            $rowData['res_is_original'] = $status[$res_is_original] ? $status[$res_is_original] : 9;
            // 作者
            $rowData['res_author'] = strval($PHPExcel->sheets[0]['cells'][$start][12]);
            // 语言
            $res_language = $PHPExcel->sheets[0]['cells'][$start][13];
            $rowData['res_language'] = $res_language ? $res_language : '汉语';
            // 元数据语言
            $res_metadata_language = $PHPExcel->sheets[0]['cells'][$start][14];
            $rowData['res_metadata_language'] = $res_metadata_language ? $res_metadata_language : '汉语';
            // 学习者
            $res_audience_learner = $PHPExcel->sheets[0]['cells'][$start][15];
            $rowData['res_audience_learner'] = $learner[$res_audience_learner];
            // 教育类型
            $res_audience_educational_type = $PHPExcel->sheets[0]['cells'][$start][16];
            $rowData['res_audience_educational_type'] = $educational[$res_audience_educational_type];
            // 简介
            $rowData['res_summary'] = strval($PHPExcel->sheets[0]['cells'][$start][17]);
            // 出版者
            $rowData['res_published_name'] = strval($PHPExcel->sheets[0]['cells'][$start][18]);
            // 出版公司
            $rowData['res_published_company'] = strval($PHPExcel->sheets[0]['cells'][$start][19]);
            // 其他作者
            $rowData['res_other_author'] = strval($PHPExcel->sheets[0]['cells'][$start][20]);
            // 版本
            $res_version = $PHPExcel->sheets[0]['cells'][$start][21];
            //$rowData['res_version'] = $res_version ? $res_version : '0';
            // 收费
            $res_permissions = $PHPExcel->sheets[0]['cells'][$start][22];
            $rowData['res_permissions'] = $status[$res_permissions] ? $status[$res_permissions] : 9;
            // 智慧豆
            $rowData['res_download_points'] = intval($PHPExcel->sheets[0]['cells'][$start][23]);
            // 发行时间
            $res_issused = strtotime($PHPExcel->sheets[0]['cells'][$start][24]);
            $rowData['res_issused'] = $res_issused ? $res_issused : 0;
            // 元数据方案
            $rowData['res_metadata_scheme'] = strval($PHPExcel->sheets[0]['cells'][$start][25]);
            // 短标题
            $rowData['res_short_title'] = strval($PHPExcel->sheets[0]['cells'][$start][26]);
            // 显示时间
            $res_valid = strtotime($PHPExcel->sheets[0]['cells'][$start][27]);
            $rowData['res_valid'] = $res_valid ? $res_valid : 0;
            // 可利用时间
            $res_avaliable = strtotime($PHPExcel->sheets[0]['cells'][$start][28]);
            $rowData['res_avaliable'] = $res_avaliable ? $res_avaliable : 0;
            // 时间
            $rowData['res_created'] = time();

            if (!$dataFields) {
                $dataFields = array_keys($rowData);
            }
            $data[] = $rowData;
        }
        
        // 批量入库
        if ($data) {
            $insertConfig['fields'] = $dataFields;
            $insertConfig['values'] = $data;
        }

        // 完成
        return $insertConfig;
    }

    /**
     * 获取列信息
     */
    public function getColumnInfo() {

        return array(1 => '标题', '资源地址', '使用类型', '省份', '城市', '区域', '推荐', '优质', '推送', '发布', '原创', '作者', '语种', '元数据语种', '学习者', '教育类型', '摘要', '出版者姓名', '出版者公司', '其他作者', '版本', '是否收费', '智慧豆', '发行时间', '元数据方案', '短标题', '显示时间', '可利用时间');
    }

    // 检查资源文件是否存在
    public function check($path) {
        $root_path = C('UPLOADS_ROOT_PATH') . C('RESOURCE_FTP_PATH');
        $filename = iconv("UTF-8", "GBK//IGNORE", $root_path . $path);
        if (!is_dir($filename) && file_exists($filename)) {
            return true;
        }
        return false;
    }

    // 上传资源处理
    public function dealResource($path) {
        // 资源存放位置
        $root_path = C('UPLOADS_ROOT_PATH') . C('RESOURCE_FTP_PATH');
        $file_path = iconv("UTF-8", "GBK//IGNORE", $root_path . $path);

        // 检查文件是否上传过
        $fhash = sha1_file($file_path);
        $rf_info = D('ResourceFile')->checkHash($fhash);
        if ($rf_info['rf_id']) {
            // 有相同的
            @unlink($file_path);
            return $rf_info;
        }

        // 没有上传过 移动到对应的目录
        // 真实目录
        $res_path = C('UPLOADS_ROOT_PATH') . C('RESOURCE_PATH');
        // 后缀
        $file_ext = get_file_ext($file_path);
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
        
        // 子目录
        $create_time = time();
        $res_path .= date(C('RESOURCE_SUBNAME_RULE'), $create_time) . '/';
        
        // 检查目录是否存在
        if (!file_exists($res_path)) {
            mk_dir($res_path);
        }

        // 真实保存名称
        $res_savename = savename_rule();

        // 上传时名称
        $upload_name = get_path_info($file_path, 'name');

        // 转移到指定目录
        fileRename($file_path, $res_path . $res_savename . '.' . $file_ext);
        
        return array(
            'path' => $res_path . $res_savename . '.' . $file_ext,
            'uploadname' => iconv("GBK", "UTF-8", $upload_name),
            'savename' => $res_savename,
            'ext' => $file_ext,
            'hash' => $fhash,
            'createTime' => $create_time,
            'size' => abs(filesize($res_path . $res_savename . '.' . $file_ext)),
            'type' => $file_type,
            'transform_status' => $transform_status,
            'transform_time' => $transform_time
        );
    }
}