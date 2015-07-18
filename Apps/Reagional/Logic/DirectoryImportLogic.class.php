<?php
namespace Reagional\Logic;
class DirectoryImportLogic extends ReagionalLogic {
    
    /**
     * 导入
     * $type 1 平台后台
     */
    public function import($data, $type = 1) {

        if (!$data['file']['size']) {
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
        
        // 入库
        return $this->insert($PHPExcel, $type);
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
        $record = 0;

        for ($start = 2; $start <= $numRows; $start++) {
            
            // 过滤空行
            if (!implode('', $PHPExcel->sheets[0]['cells'][$start])) {
                continue;
            }

            // 标题
            $res_title = $PHPExcel->sheets[0]['cells'][$start][6];
            if (!$res_title) {
                $this->error = $start . '行:请填写名称';
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

    public function insert($PHPExcel) {
        
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
            $rowData['re_id'] = session('re_id'); // 默认平台
            $rowData['re_title'] = session('re_title');; // 默认平台
            $rowData['d_creator_id'] = $_SESSION[C('USER_AUTH_KEY')];
            $rowData['d_creator_table'] = 'Member';
            
            // 版本
            $d_version = $PHPExcel->sheets[0]['cells'][$start][1];
            $rowData['d_version'] = intval($version[$d_version]);

            // 学制
            $d_school_type = $PHPExcel->sheets[0]['cells'][$start][2];
            $rowData['d_school_type'] = intval($school_type[$d_school_type]);
            
            // 年级
            $d_grade = $PHPExcel->sheets[0]['cells'][$start][3];
            $rowData['d_grade'] = intval($grade[$d_grade]);
            
            // 学期
            $d_semester = $PHPExcel->sheets[0]['cells'][$start][4];
            $rowData['d_semester'] = intval($semester[$d_semester]);

            // 学科
            $d_subject = $PHPExcel->sheets[0]['cells'][$start][5];
            $rowData['d_subject'] = intval($subject[$d_subject]);
            
            // 名称
            $rowData['d_title'] = $PHPExcel->sheets[0]['cells'][$start][6];
            
            // 排序
            $rowData['d_sort'] = intval($PHPExcel->sheets[0]['cells'][$start][7]);
            
            // 状态
            $d_status = $PHPExcel->sheets[0]['cells'][$start][8];
            $rowData['d_status'] = intval($status[$d_status]);
            
            // 上级名称
            $rowData['parent_title'] = $PHPExcel->sheets[0]['cells'][$start][9];
            
            // 时间
            $rowData['d_created'] = time();

            $data[] = $rowData;
        }
        
        // 完成
        return $data;
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

        return array(1 => '版本', '学制', '年级', '学期', '学科', '名称', '排序', '状态', '上级名称');
    }
}