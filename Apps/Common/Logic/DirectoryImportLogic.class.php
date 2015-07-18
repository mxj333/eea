<?php
namespace Common\Logic;
class DirectoryImportLogic extends Logic {
    
    /**
     * 导入
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
        $valid = $this->checkValid($PHPExcel, $config);
        if (!$valid) {
            return false;
        }
        
        // 入库
        return $this->insert($PHPExcel, $config);
    }

    // 检查模板是否正确
    public function checkTemplate($PHPExcel) {

        // 列信息
        $columnInfo = $this->getColumnInfo();
        foreach ($columnInfo as $key => $info) {
            if ($PHPExcel->sheets[0]['cells'][1][$key] != $info) {
                $this->error = '模板错误,请下载目录文件模板';
                return false;
            }
        }
        
        return true;
    }

    // 检查数据有效性
    public function checkValid($PHPExcel, $config = array()) {

        // 总行数
        $numRows = $PHPExcel->sheets[0]['numRows'];
        $tag = loadCache('tag');
        $school_type = array_flip($tag[4]);
        $subject = array_flip($tag[5]);
        $version = array_flip($tag[6]);
        $grade = array_flip($tag[7]);
        $semester = array_flip($tag[8]);
        $status = array('启用' => 1, '禁用' => 9);
        $record = 0;
        $excelData = array();

        for ($start = 2; $start <= $numRows; $start++) {
            
            // 过滤空行
            if (!implode('', $PHPExcel->sheets[0]['cells'][$start])) {
                continue;
            }

            // 标题
            $res_title = $PHPExcel->sheets[0]['cells'][$start][7];
            // 检查学科
            $subject_value = $PHPExcel->sheets[0]['cells'][$start][6];
            // 检查学期
            $semester_value = $PHPExcel->sheets[0]['cells'][$start][5];
            // 检查年级
            $grade_value = $PHPExcel->sheets[0]['cells'][$start][4];
            // 检查学制
            $school_type_value = $PHPExcel->sheets[0]['cells'][$start][3];

            // 编号
            $code_value = strval($PHPExcel->sheets[0]['cells'][$start][1]);
            $code_value_length = get_string_total_length($code_value);
            if ($code_value && ($code_value_length < 1 || $code_value_length > 12)) {
                $this->error = $start . '行:编号范围1-50个字';
                return false;
            }

            // 检查版本
            $version_value = $PHPExcel->sheets[0]['cells'][$start][2];
            $d_version = $version[$version_value] ? intval($version[$version_value]) : 0;
            if (($res_title || $subject_value || $semester_value || $grade_value || $school_type_value) && !$d_version) {
                $this->error = $start . '行:版本错误，请重新选择';
                return false;
            }

            // 检查学制
            $school_type_value = $PHPExcel->sheets[0]['cells'][$start][3];
            $d_school_type = $school_type[$school_type_value] ? intval($school_type[$school_type_value]) : 0;
            if (($res_title || $subject_value || $semester_value || $grade_value) && !$d_school_type) {
                $this->error = $start . '行:学制错误，请重新选择';
                return false;
            }

            // 检查年级
            $grade_value = $PHPExcel->sheets[0]['cells'][$start][4];
            $d_grade = $grade[$grade_value] ? intval($grade[$grade_value]) : 0;
            if (($res_title || $subject_value || $semester_value) && !$d_grade) {
                $this->error = $start . '行:年级错误，请重新选择';
                return false;
            }

            // 检查学期
            $semester_value = $PHPExcel->sheets[0]['cells'][$start][5];
            $d_semester = $semester[$semester_value] ? intval($semester[$semester_value]) : 0;
            if (($res_title || $subject_value) && !$d_semester) {
                $this->error = $start . '行:学期错误，请重新选择';
                return false;
            }

            // 检查学科
            $subject_value = $PHPExcel->sheets[0]['cells'][$start][6];
            $d_subject = $subject[$subject_value] ? intval($subject[$subject_value]) : 0;
            if ($res_title && !$d_subject) {
                $this->error = $start . '行:学科错误，请重新选择';
                return false;
            }

            // 标题
            $res_title = $PHPExcel->sheets[0]['cells'][$start][7];
            //if (!$res_title) {
            //    $this->error = $start . '行:请填写名称';
            //    return false;
            //}
            if ($res_title) {
                $res_title_length = get_string_total_length($res_title);
                if ($res_title_length < 1 || $res_title_length > 12) {
                    $this->error = $start . '行:名称范围1-30个字';
                    return false;
                }
            }

            // 上级标题
            $res_ptitle = $PHPExcel->sheets[0]['cells'][$start][10];
            if ($res_ptitle) {
                $res_ptitle_length = get_string_total_length($res_ptitle);
                if ($res_ptitle_length < 1 || $res_ptitle_length > 12) {
                    $this->error = $start . '行:上级名称范围1-30个字';
                    return false;
                }
            }

            // 数据没问题  检查上下级关系
            if ($res_ptitle && !$excelData[$version_value][$school_type_value][$grade_value][$semester_value][$subject_value][$res_ptitle]) {
                // 检查数据库中是否有
                $default['where']['d_version'] = $d_version;
                $default['where']['d_school_type'] = $d_school_type;
                $default['where']['d_grade'] = $d_grade;
                $default['where']['d_semester'] = $d_semester;
                $default['where']['d_subject'] = $d_subject;
                $default['where']['re_id'] = strval($config['re_id']);

                $d_id = D('Directory', 'Model')->getOne($default);
                if (!$d_id) {
                    $this->error = $start . '行:上级名称不存在';
                    return false;
                }
            }

            // 检查没问题
            $excelData[$version_value][$school_type_value][$grade_value][$semester_value][$subject_value][$res_title] = 1;

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
            're_id' => '',
            're_title' => '',
            'd_creator_id' => $_SESSION[C('USER_AUTH_KEY')],
            'd_creator_table' => 'User',
        );
        $config = array_merge($default, $config);
        
        // 总行数
        $numRows = $PHPExcel->sheets[0]['numRows'];
        $tag = loadCache('tag');
        $school_type = array_flip($tag[4]);
        $subject = array_flip($tag[5]);
        $version = array_flip($tag[6]);
        $grade = array_flip($tag[7]);
        $semester = array_flip($tag[8]);
        $status = array('启用' => 1, '禁用' => 9);
        $data = array();
        $dataFields = array();

        for ($start = 2; $start <= $numRows; $start++) {

            // 过滤空行
            if (!implode('', $PHPExcel->sheets[0]['cells'][$start])) {
                continue;
            }
            
            $rowData = array();
            $rowData['re_id'] = strval($config['re_id']); // 默认平台
            $rowData['re_title'] = strval($config['re_title']); // 默认平台
            $rowData['d_creator_id'] = intval($config['d_creator_id']);
            $rowData['d_creator_table'] = strval($config['d_creator_table']);
            
            // 版本
            $d_version = $PHPExcel->sheets[0]['cells'][$start][2];
            $rowData['d_version'] = intval($version[$d_version]);
            if (false === $this->checkDirectory($rowData, 0)) {
                $this->error = '导入失败,'.$start.'行:版本添加失败';
                return false;
            }

            // 学制
            $d_school_type = $PHPExcel->sheets[0]['cells'][$start][3];
            $rowData['d_school_type'] = intval($school_type[$d_school_type]);
            if (false === $this->checkDirectory($rowData, 1)) {
                $this->error = '导入失败,'.$start.'行:学制添加失败';
                return false;
            }
            
            // 年级
            $d_grade = $PHPExcel->sheets[0]['cells'][$start][4];
            $rowData['d_grade'] = intval($grade[$d_grade]);
            if (false === $this->checkDirectory($rowData, 2)) {
                $this->error = '导入失败,'.$start.'行:年级添加失败';
                return false;
            }
            
            // 学期
            $d_semester = $PHPExcel->sheets[0]['cells'][$start][5];
            $rowData['d_semester'] = intval($semester[$d_semester]);
            if (false === $this->checkDirectory($rowData, 3)) {
                $this->error = '导入失败,'.$start.'行:学期添加失败';
                return false;
            }

            // 学科
            $d_subject = $PHPExcel->sheets[0]['cells'][$start][6];
            $rowData['d_subject'] = intval($subject[$d_subject]);
            $subject_id = $this->checkDirectory($rowData, 4);
            if (false === $subject_id) {
                $this->error = '导入失败,'.$start.'行:学科添加失败';
                return false;
            }
            
            // 名称
            $rowData['d_title'] = strval($PHPExcel->sheets[0]['cells'][$start][7]);
            if (!$rowData['d_title']) {
                // 名称不存在  不用导入
                continue;
            }
            
            // 排序
            $rowData['d_sort'] = intval($PHPExcel->sheets[0]['cells'][$start][8]);
            
            // 状态
            $d_status = $PHPExcel->sheets[0]['cells'][$start][9];
            $rowData['d_status'] = intval($status[$d_status]);
            
            // 编号
            $d_code = $PHPExcel->sheets[0]['cells'][$start][1];
            $rowData['d_code'] = strval(trim($d_code));
            
            // 上级名称
            $rowData['d_pid'] = intval($subject_id);
            $rowData['d_level'] = 5;
            $parent_title = $PHPExcel->sheets[0]['cells'][$start][10];
            if ($parent_title) {
                $levelConfig['where']['d_school_type'] = $rowData['d_school_type'];
                $levelConfig['where']['d_subject'] = $rowData['d_subject'];
                $levelConfig['where']['d_version'] = $rowData['d_version'];
                $levelConfig['where']['d_grade'] = $rowData['d_grade'];
                $levelConfig['where']['d_semester'] = $rowData['d_semester'];
                $levelConfig['where']['re_id'] = strval($rowData['re_id']);
                $levelConfig['where']['d_title'] = $parent_title;
                $pInfo = D('Directory', 'Model')->getOne($levelConfig);
                if (!$pInfo) {
                    $this->error = '导入失败,'.$start.'行:上级名称不存在';
                    return false;
                }
                $rowData['d_pid'] = intval($pInfo['d_id']);
                $rowData['d_level'] = intval($pInfo['d_level'] + 1);
            }
            // 时间
            $rowData['d_created'] = time();

            // 入库
            D('Directory', 'Model')->insert($rowData);
        }
        
        // 完成
        return true;
    }

    // 检查目录是否存在  不存在创建
    public function checkDirectory($data, $level = 0) {

        $whereConfig = array();

        // 检查目录是否存在
        switch (intval($level)) {
            case 4:
                $whereConfig['where']['d_subject'] = $data['d_subject'];
            case 3:
                $whereConfig['where']['d_semester'] = $data['d_semester'];
            case 2:
                $whereConfig['where']['d_grade'] = $data['d_grade'];
            case 1:
                $whereConfig['where']['d_school_type'] = $data['d_school_type'];
            case 0:
                $whereConfig['where']['d_version'] = $data['d_version'];
            default :
        }

        if ($whereConfig) {
            $whereConfig['where']['re_id'] = strval($data['re_id']);
            $whereConfig['fields'] = 'd_id';
            $d_id = D('Directory')->getOne($whereConfig);
            if (!$d_id) {
                // 不存在  获取父ID
                $pConfig = array();
                switch (intval($level)) {
                    case 4:
                        $pConfig['where']['d_semester'] = $data['d_semester'];
                    case 3:
                        $pConfig['where']['d_grade'] = $data['d_grade'];
                    case 2:
                        $pConfig['where']['d_school_type'] = $data['d_school_type'];
                    case 1:
                        $pConfig['where']['d_version'] = $data['d_version'];
                    default :
                }
                if ($pConfig) {
                    $pConfig['where']['d_level'] = intval($level) - 1;
                    $pConfig['where']['re_id'] = strval($data['re_id']);
                    $pConfig['fields'] = 'd_id';
                    $pid = D('Directory')->getOne($pConfig);
                } else {
                    $pid = 0;
                }
                // 增加
                $insertData['d_pid'] = intval($pid);
                $insertData['d_level'] = intval($level);
                $insertData['d_version'] = intval($data['d_version']);
                $insertData['d_school_type'] = intval($data['d_school_type']);
                $insertData['d_grade'] = intval($data['d_grade']);
                $insertData['d_semester'] = intval($data['d_semester']);
                $insertData['d_subject'] = intval($data['d_subject']);
                $insertData['d_creator_id'] = intval($data['d_creator_id']);
                $insertData['d_creator_table'] = strval($data['d_creator_table']);
                $insertData['re_id'] = strval($data['re_id']);
                $insertData['re_title'] = strval($data['re_title']);
                $insertData['d_created'] = time();
                $d_id = D('Directory', 'Model')->insert($insertData);
                if (!$d_id) {
                    return false;
                }
            }
        }

        return $d_id;
    }

    /**
     * 获取列信息
     */
    public function getColumnInfo() {

        return array(1 => '编号', '版本', '学制', '年级', '学期', '学科', '名称', '排序', '状态', '上级名称');
    }
}