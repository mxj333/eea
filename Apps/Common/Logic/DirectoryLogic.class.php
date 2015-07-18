<?php
namespace Common\Logic;
class DirectoryLogic extends Logic {

    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;

        $default = array(
            'is_deal_result' => true,
            'order' => 'd_sort ASC',
            'p' => intval($param['p']),
        );

        // 查询条件
        if ($param['d_title']) {
            $where['d_title'] = array('LIKE', '%' . $param['d_title'] . '%');
        }

        if (isset($param['d_level'])) {
            if ($config['is_upper_level']) {
                $where['d_level'] = array('elt', $param['d_level']);
            } else {
                $where['d_level'] = $param['d_level'];
            }
        }

        if ($param['d_status']) {
            $where['d_status'] = $param['d_status'];
        }

        if ($param['d_version']) {
            $where['d_version'] = $param['d_version'];
        }
        if ($param['d_school_type']) {
            $where['d_school_type'] = $param['d_school_type'];
        }
        if ($param['d_grade']) {
            $where['d_grade'] = $param['d_grade'];
        }
        if ($param['d_semester']) {
            $where['d_semester'] = $param['d_semester'];
        }
        if ($param['d_subject']) {
            $where['d_subject'] = $param['d_subject'];
        }

        if ($param['d_id']) {
            $where['d_id'] = array('IN', $param['d_id']);
        }

        if (isset($param['d_pid'])) {
            if ($param['d_pid']) {
                if ($config['is_open_sub']) {
                    $childrenConfig['fields'] = 'd_id';
                    $childrenConfig['where']['d_pid'] = $param['d_pid'];
                    $children = D('Directory', 'Model')->getAll($childrenConfig);
                    $children_ids = implode(',', $children);
                    if ($children_ids) {
                        $param['d_pid'] .= ',' . $children_ids;
                    }
                }
                $where['d_pid'] = array('IN', strval($param['d_pid']));
            } else {
                $where['d_pid'] = 0;
            }
        }

        if ($param['re_id']) {
            $where['re_id'] = $param['re_id'];
        } else {
            $where['re_id'] = '';
        }

        $default['where'] = empty($where) ? 1 : $where;
        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D($this->name)->getListByPage($config);

        if ($config['is_deal_result']) {
            $tag = reloadCache('tag');

            foreach ($lists['list'] as $key => &$value) {
                $value['d_school_type'] = strval($tag[4][$value['d_school_type']]);
                $value['d_subject'] = strval($tag[5][$value['d_subject']]);
                $value['d_version'] = strval($tag[6][$value['d_version']]);
                $value['d_grade'] = strval($tag[7][$value['d_grade']]);
                $value['d_semester'] = strval($tag[8][$value['d_semester']]);
            }
        }

        return $lists;
    }

    public function insert($data, $table = 'Member') {

        // 数据处理
        $data['d_sort'] = intval($data['d_sort']);

        if (!$data['d_id']) {
            // 添加
            $data['d_creator_id'] = $data['d_creator_id'] ? $data['d_creator_id'] : $_SESSION[C('USER_AUTH_KEY')];
            $data['d_creator_table'] = $table;

            // 获取父级信息
            if (intval($data['d_pid'])) {
                $config['fields'] = 'd_version,d_subject,d_school_type,d_grade,d_semester';
                $config['where']['d_id'] = intval($data['d_pid']);
                $pinfo = D('Directory', 'Model')->getOne($config);
                $data = array_merge($pinfo, $data);
            }
        }

        $save_data = D('Directory', 'Model')->create($data);
        if (false === $save_data) {
            $this->error = D('Directory', 'Model')->getError();
            return false;
        }

        unset($save_data['d_id']);
        if (!$data['d_id']) {
            return D('Directory', 'Model')->insert($save_data);
        } else {

            // 修改   父级、层级不变
            unset($save_data['d_pid']);
            unset($save_data['d_level']);

            $res = D('Directory', 'Model')->update($save_data, array('where' => array('d_id' => intval($data['d_id']))));
            if ($res === false) {
                $this->error = '编辑失败';
                return false;
            }
            
            // 所有子集id
            $children = $this->getSubIds($data['d_id']);
            $child_data = array();
            if ($save_data['d_version']) {
                $child_data['d_version'] = $save_data['d_version'];
            }
            if ($save_data['d_school_type']) {
                $child_data['d_school_type'] = $save_data['d_school_type'];
            }
            if ($save_data['d_grade']) {
                $child_data['d_grade'] = $save_data['d_grade'];
            }
            if ($save_data['d_semester']) {
                $child_data['d_semester'] = $save_data['d_semester'];
            }
            if ($save_data['d_subject']) {
                $child_data['d_subject'] = $save_data['d_subject'];
            }
            // 更新子集
            if ($children && $child_data) {
                $config['where']['d_id'] = array('IN', $children);
                D('Directory', 'Model')->update($child_data, $config);
            }

            return $res;
        }
    }

    public function getSubIds($pid = '0', $res = '') {

        $config['where']['d_pid'] = array('IN', $pid);
        $config['fields'] = 'd_id';
        $cate = D($this->name, 'Model')->getAll($config);
        $res .= ',' . $pid;
        if ($cate) {
            return D($this->name)->getSubIds(implode(',', $cate), $res);
        } else {
            return trim($res, ',');
        }
    }

    // 目录调整
    public function adjustment($data) {

        // 当前目录
        $info = D('Directory')->getById($data['d_id']);
        if (!$info) {
            $this->error = '目录不存在';
            return false;
        }
        // 目标目录
        if ($data['target_id']) {
            $target_id = intval($data['target_id']);
        } elseif ($data['d_subject']) {
            $target_id = intval($data['d_subject']);
        } elseif ($data['d_semester']) {
            $target_id = intval($data['d_semester']);
        } elseif ($data['d_grade']) {
            $target_id = intval($data['d_grade']);
        } elseif ($data['d_school_type']) {
            $target_id = intval($data['d_school_type']);
        } elseif ($data['d_version']) {
            $target_id = intval($data['d_version']);
        } else {
            $target_id = 0;
        }
        
        if ($target_id) {
            $targetInfo = D('Directory')->getById($target_id);
            $targetPid = intval($targetInfo['d_id']);
            $targetLevel = intval($targetInfo['d_level']) + 1;
        } else {
            $targetPid = 0;
            $targetLevel = 0;
        }
        // 等级差
        $level_diff = $targetLevel - intval($info['d_level']);

        // 所有子集id
        $children = $this->getSubIds($data['d_id']);
        $children = $children ? $children . ',' . intval($data['d_id']) : intval($data['d_id']);

        // 当前目录 修改父id  子目录不修改
        D('Directory', 'Model')->update(array('d_id' => intval($data['d_id']), 'd_pid' => $targetPid));
        // 修改所有目录 level
        if ($level_diff > 0) {
            D('Directory', 'Model')->increase('d_level', array('d_id' => array('IN', $children)), $level_diff);
        } else {
            D('Directory', 'Model')->decrease('d_level', array('d_id' => array('IN', $children)), abs($level_diff));
        }
        // 修改信息
        if ($targetInfo) {
            if ($targetInfo['d_version']) {
                $saveData['d_version'] = $targetInfo['d_version'];
            }
            if ($targetInfo['d_school_type']) {
                $saveData['d_school_type'] = $targetInfo['d_school_type'];
            }
            if ($targetInfo['d_grade']) {
                $saveData['d_grade'] = $targetInfo['d_grade'];
            }
            if ($targetInfo['d_semester']) {
                $saveData['d_grade'] = $targetInfo['d_grade'];
            }
            if ($targetInfo['d_subject']) {
                $saveData['d_subject'] = $targetInfo['d_subject'];
            }
            
            $config['where']['d_id'] = array('IN', $children);
            D('Directory', 'Model')->update($saveData, $config);
        }

        return true;
    }

    // 关联知识点
    public function relation($data) {
        if (!$data['d_id']) {
            $this->error = L('DIRECTORY_NO_EXISTS');
            return false;
        }

        return D('DirectoryKnowledgePointsRelation')->insert($data);
    }

    public function delete($id) {
        
        // 所有子集id
        $children = $this->getSubIds($id);
        $children = $children ? $children . ',' . strval($id) : strval($id);

        // 删除 目录
        $config['where']['d_id'] = array('IN', $children);
        $res = D('Directory', 'Model')->delete($config);
        if ($res !== false) {

            // 删除 目录知识点关系
            D('DirectoryKnowledgePointsRelation', 'Model')->delete($config);
        }

        return $res;
    }

    public function createExcel($config) {

        $filename = $config['fileName'] ? $config['fileName'] : 'directory';
        $filename = $filename . '.xls';

        //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
        import("Org.Util.PHPExcel");
		import("Org.Util.PHPExcel.Writer.Excel5");
		import("Org.Util.PHPExcel.IOFactory.php");
        //创建Excel对象，注意，不能少了\
        $objPHPExcel = new \PHPExcel();
        
        $objPHPExcel->getProperties()->setTitle($filename);
        $objPHPExcel->setActiveSheetIndex(0);
        
        // 表头
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '编号');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', '版本');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', '学制');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', '年级');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1', '学期');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1', '学科');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1', '名称');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H1', '排序');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I1', '状态');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J1', '上级名称');

        // 数据
        $dataConfig['where']['re_id'] = strval($config['re_id']);
        //$dataConfig['where']['d_level'] = array('EGT', 5);
        $dataConfig['order'] = 'd_version,d_school_type,d_grade,d_semester,d_subject,d_level,d_sort,d_id';
        $dataConfig['fields'] = 'd_id,d_school_type,d_grade,d_semester,d_subject,d_version,d_sort,d_pid,d_level,d_title,d_status,d_code';
        $list = D('Directory', 'Model')->getAll($dataConfig);
        $data = $this->dealExcelContent($list);

        // 内容
        $key = 0;
        foreach ($data as $val) {
            // 宽度
            $cellLength = strlen($val['d_code']) * 2;
            $maxWidthA = $cellLength > $maxWidthA ? $cellLength : $maxWidthA;
            // 内容
            $objPHPExcel->getActiveSheet(0)->setCellValue('A' . ($key+2), ' ' . $val['d_code'], \PHPExcel_Cell_DataType::TYPE_STRING);

            // 宽度
            $cellLength = strlen($val['d_version']);
            $maxWidthB = $cellLength > $maxWidthB ? $cellLength : $maxWidthB;
            // 内容
            $objPHPExcel->getActiveSheet(0)->setCellValue('B' . ($key+2), $val['d_version']);

            // 宽度
            $cellLength = strlen($val['d_school_type']);
            $maxWidthC = $cellLength > $maxWidthC ? $cellLength : $maxWidthC;
            // 内容
            $objPHPExcel->getActiveSheet(0)->setCellValue('C' . ($key+2), $val['d_school_type']);

            // 宽度
            $cellLength = strlen($val['d_grade']);
            $maxWidthD = $cellLength > $maxWidthD ? $cellLength : $maxWidthD;
            // 内容
            $objPHPExcel->getActiveSheet(0)->setCellValue('D' . ($key+2), $val['d_grade']);

            // 宽度
            $cellLength = strlen($val['d_semester']);
            $maxWidthE = $cellLength > $maxWidthE ? $cellLength : $maxWidthE;
            // 内容
            $objPHPExcel->getActiveSheet(0)->setCellValue('E' . ($key+2), $val['d_semester']);

            // 宽度
            $cellLength = strlen($val['d_subject']);
            $maxWidthF = $cellLength > $maxWidthF ? $cellLength : $maxWidthF;
            // 内容
            $objPHPExcel->getActiveSheet(0)->setCellValue('F' . ($key+2), $val['d_subject']);

            // 宽度
            $cellLength = strlen($val['d_title']);
            $maxWidthG = $cellLength > $maxWidthG ? $cellLength : $maxWidthG;
            // 内容
            $objPHPExcel->getActiveSheet(0)->setCellValue('G' . ($key+2), $val['d_title']);

            // 宽度
            $cellLength = strlen($val['d_sort']);
            $maxWidthH = $cellLength > $maxWidthH ? $cellLength : $maxWidthH;
            // 内容
            $objPHPExcel->getActiveSheet(0)->setCellValue('H' . ($key+2), $val['d_sort']);

            // 宽度
            $cellLength = strlen($val['d_status']);
            $maxWidthI = $cellLength > $maxWidthI ? $cellLength : $maxWidthI;
            // 内容
            $objPHPExcel->getActiveSheet(0)->setCellValue('I' . ($key+2), $val['d_status']);

            // 宽度
            $cellLength = strlen($val['d_ptitle']);
            $maxWidthJ = $cellLength > $maxWidthJ ? $cellLength : $maxWidthJ;
            // 内容
            $objPHPExcel->getActiveSheet(0)->setCellValue('J' . ($key+2), strval($val['d_ptitle']));

            $key++;
        }

        // 宽度
        $maxWidth = 12;
        $width = ceil($maxWidthA/3)*2;
        $width = $width > $maxWidth ? $width : $maxWidth;
        $objPHPExcel->getActiveSheet(0)->getColumnDimension('A')->setWidth($width);
        $width = ceil($maxWidthB/3)*2;
        $width = $width > $maxWidth ? $width : $maxWidth;
        $objPHPExcel->getActiveSheet(0)->getColumnDimension('B')->setWidth($width);
        $width = ceil($maxWidthC/3)*2;
        $width = $width > $maxWidth ? $width : $maxWidth;
        $objPHPExcel->getActiveSheet(0)->getColumnDimension('C')->setWidth($width);
        $width = ceil($maxWidthD/3)*2;
        $width = $width > $maxWidth ? $width : $maxWidth;
        $objPHPExcel->getActiveSheet(0)->getColumnDimension('D')->setWidth($width);
        $width = ceil($maxWidthE/3)*2;
        $width = $width > $maxWidth ? $width : $maxWidth;
        $objPHPExcel->getActiveSheet(0)->getColumnDimension('E')->setWidth($width);
        $width = ceil($maxWidthF/3)*2;
        $width = $width > $maxWidth ? $width : $maxWidth;
        $objPHPExcel->getActiveSheet(0)->getColumnDimension('F')->setWidth($width);
        $width = ceil($maxWidthG/3)*2;
        $width = $width > $maxWidth ? $width : $maxWidth;
        $objPHPExcel->getActiveSheet(0)->getColumnDimension('G')->setWidth($width);
        $width = ceil($maxWidthH/3)*2;
        $width = $width > $maxWidth ? $width : $maxWidth;
        $objPHPExcel->getActiveSheet(0)->getColumnDimension('H')->setWidth($width);
        $width = ceil($maxWidthI/3)*2;
        $width = $width > $maxWidth ? $width : $maxWidth;
        $objPHPExcel->getActiveSheet(0)->getColumnDimension('I')->setWidth($width);
        $width = ceil($maxWidthJ/3)*2;
        $width = $width > $maxWidth ? $width : $maxWidth;
        $objPHPExcel->getActiveSheet(0)->getColumnDimension('J')->setWidth($width);

        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        //header('Content-Type: application/vnd.ms-excel');
        //header("Content-Disposition: attachment;filename=\"$filename\"");
        //header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        //$objWriter->save('php://output'); //文件通过浏览器下载

        $objWriter->save(C('UPLOADS_ROOT_PATH') . C('DIRECTORY_EXPORT_PATH') . $filename);

        if (!file_exists(C('UPLOADS_ROOT_PATH') . C('DIRECTORY_EXPORT_PATH') . $filename)) {
            $this->error = '操作失败';
            return false;
        }
        return true;
    }

    public function createWord($config) {

        $filename = $config['fileName'] ? $config['fileName'] : 'directory';
        $filename = $filename  . '.doc';

        // 数据
        $dataConfig['where']['re_id'] = strval($config['re_id']);
        $dataConfig['order'] = 'd_version,d_school_type,d_grade,d_semester,d_subject,d_level,d_sort,d_id';
        $data = D('Directory')->getAll($dataConfig);
        
        $list = tree($data, 'd_id', 'd_pid');
        
        $content = $this->dealWrodContent($list);
        
        return createWord(C('UPLOADS_ROOT_PATH') . C('DIRECTORY_EXPORT_PATH'), $filename, $content);
    }

    public function dealWrodContent($list, $level = 0, $child = '_child', $sign = '┗') {
        
        $tag = loadCache('tag');
        $status = array(1 => '启用', 9 => '禁用');

        $res = '';
        foreach($list as $info) {
            if ($level > 0) {
                for ($start = 0; $start < $level-1; $start++) {
                    $res .= '  ';
                }
                $res .= $sign;
            }
            switch (intval($level)) {
                case 0:
                    $res .= $tag[6][$info['d_version']];
                    break;
                case 1:
                    $res .= $tag[4][$info['d_school_type']];
                    break;
                case 2:
                    $res .= $tag[7][$info['d_grade']];
                    break;
                case 3:
                    $res .= $tag[8][$info['d_semester']];
                    break;
                case 4:
                    $res .= $tag[5][$info['d_subject']];
                    break;
                default :
                    $res .= $info['d_title'] . '  ';
                    $res .= $info['d_sort'] . '  ';
                    $res .= $status[$info['d_status']] . '  ';
            }

            $res .= $info['d_code'] . '  ';
            
            $res .= "\r\n";
            
            if ($info[$child]) {
                $res .= $this->dealWrodContent($info[$child], $level+1, $child, $sign);
            }
        }
        return $res;
    }

    public function dealExcelContent($data) {
        
        $tag = loadCache('tag');
        $status = array(1 => '启用', 9 => '禁用');

        foreach($data as $d_id => $info) {

            if ($info['d_level'] <= 5 && $data[$info['d_pid']]) {
                unset($data[$info['d_pid']]);
            }

            $data[$d_id]['d_title'] = strval($info['d_title']);
            $data[$d_id]['d_sort'] = intval($info['d_sort']);
            $data[$d_id]['d_status'] = strval($status[$info['d_status']]);
            $data[$d_id]['d_subject'] = strval($tag[5][$info['d_subject']]);
            $data[$d_id]['d_semester'] = strval($tag[8][$info['d_semester']]);
            $data[$d_id]['d_grade'] = strval($tag[7][$info['d_grade']]);
            $data[$d_id]['d_school_type'] = strval($tag[4][$info['d_school_type']]);
            $data[$d_id]['d_version'] = strval($tag[6][$info['d_version']]);
            
            if (intval($info['d_level']) >= 6) {
                // 显示父级id名称
                $data[$d_id]['d_ptitle'] = strval($data[$info['d_pid']]['d_title']);
            }
        }

        return $data;
    }
}