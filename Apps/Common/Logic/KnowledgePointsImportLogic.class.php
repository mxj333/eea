<?php
namespace Common\Logic;
class KnowledgePointsImportLogic extends Logic {
    
    /**
     * 导入
     * $type 1 平台后台
     */
    public function import($data, $type = 1) {

        if (!$data['file']['size'] || ($data['file']['type'] != 'application/vnd.ms-excel')) {
            $this->error = '上传文件错误';
            return false;
        }

        // 大小验证
        if ($data['file']['size'] > C('IMPORT_EXCEL_MAX_SIZE')) {
            $this->error = '文件大小超出限制';
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
        $this->error = '';
        $res = $this->insert($PHPExcel, $type);
        if ($this->error) {
            return false;
        }

        return true;
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
            $res_title = $PHPExcel->sheets[0]['cells'][$start][2];
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

    public function insert($PHPExcel, $type = 1) {

        // 总行数
        $numRows = $PHPExcel->sheets[0]['numRows'];
        $tag = loadCache('tag');
        $subject = array_flip($tag[5]);
        $status = array('启用' => 1, '禁用' => 9);
        $data = array();
        $dataFields = array();

        for ($start = 2; $start <= $numRows; $start++) {

            // 过滤空行
            if (!implode('', $PHPExcel->sheets[0]['cells'][$start])) {
                continue;
            }
            
            $rowData = array();
            $rowData['kp_creator_id'] = $_SESSION[C('USER_AUTH_KEY')];
            $rowData['kp_creator_table'] = $type == 1 ? 'User' : 'Member';

            // 是否入库过相同数据
            $rowData['kp_data_md5'] = md5(implode(',', $PHPExcel->sheets[0]['cells'][$start]));

            $config['where']['kp_creator_table'] = $rowData['kp_creator_table'];
            $config['where']['kp_creator_id'] = $rowData['kp_creator_id'];
            $config['where']['kp_data_md5'] = $rowData['kp_data_md5'];
            $info = D('KnowledgePoints')->getOne($config);
            if ($info) {
                // 已经添加过
                continue;
            }
            
            // 学科
            $kp_subject = $PHPExcel->sheets[0]['cells'][$start][1];
            $rowData['kp_subject'] = intval($subject[$kp_subject]);
            
            // 名称
            $rowData['kp_title'] = $PHPExcel->sheets[0]['cells'][$start][2];
            
            // 排序
            $rowData['kp_sort'] = intval($PHPExcel->sheets[0]['cells'][$start][3]);
            
            // 状态
            $kp_status = $PHPExcel->sheets[0]['cells'][$start][4];
            $rowData['kp_status'] = intval($status[$kp_status]);
            
            
            // 上级名称
            $rowData['kp_pid'] = 0;
            $rowData['kp_level'] = 0;
            $parent_title = $PHPExcel->sheets[0]['cells'][$start][5];
            if ($parent_title) {
                $levelConfig['where']['kp_subject'] = $rowData['kp_subject'];
                $levelConfig['where']['kp_title'] = $parent_title;
                $pInfo = D('KnowledgePoints', 'Model')->getOne($levelConfig);
                $rowData['kp_pid'] = intval($pInfo['kp_id']);
                $rowData['kp_level'] = intval($pInfo['kp_level'] + 1);
            }

            // 时间
            $rowData['kp_created'] = time();

            $insert_id = D('KnowledgePoints')->insert($rowData);
            if ($insert_id == false) {
                $this->error .= $start . '行错误,原因:' . D('KnowledgePoints')->getError() . '<br/>';
            }
        }
        
        // 完成
        return true;
    }

    /**
     * 获取列信息
     */
    public function getColumnInfo() {

        return array(1 => '学科', '名称', '排序', '状态', '上级名称');
    }
}