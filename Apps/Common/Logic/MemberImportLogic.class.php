<?php
namespace Common\Logic;
class MemberImportLogic extends Logic {
    
    /**
     * 用户导入
     */
    public function import($data, $config = array()) {

        if (!$data['file']['size']|| ($data['file']['type'] != 'application/vnd.ms-excel')) {
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

        //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能import导入
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
        $region = loadCache('region');
        $md_card_type = array_flip(explode(',', C('MEMBER_CARD_TYPE')));
        $record = 0;

        for ($start = 2; $start <= $numRows; $start++) {
            
            // 过滤空行
            if (!implode('', $PHPExcel->sheets[0]['cells'][$start])) {
                continue;
            }

            // 姓名
            $me_nickname = $PHPExcel->sheets[0]['cells'][$start][1];
            if (!$me_nickname) {
                $this->error = $start . '行:请填写姓名';
                return false;
            }
            $me_nickname_length = get_string_total_length($me_nickname);
            if ($me_nickname_length < 2 || $me_nickname_length > 12) {
                $this->error = $start . '行:姓名范围2-12个字';
                return false;
            }
            // 检查姓名是否添加过
            $nickname = D('Member')->getOne(array('where' => array('me_nickname' => $me_nickname)));
            if ($nickname) {
                $this->error = $start . '行:姓名已存在';
                return false;
            }

            // 头像
            $me_avatar = $PHPExcel->sheets[0]['cells'][$start][2];
            if ($me_avatar && !$this->checkIsExist($me_avatar)) {
                $this->error = $start . '行:头像不存在';
                return false;
            }

            // 手机
            $me_mobile = $PHPExcel->sheets[0]['cells'][$start][4];
            if ($me_mobile && !is_phone_number($me_mobile, 'phone')) {
                $this->error = $start . '行:手机格式错误';
                return false;
            }

            // 电话
            $me_phone = $PHPExcel->sheets[0]['cells'][$start][5];
            if ($me_phone && !is_phone_number($me_phone)) {
                $this->error = $start . '行:电话格式错误';
                return false;
            }

            // email
            $me_email = $PHPExcel->sheets[0]['cells'][$start][6];
            if ($me_email && !is_email($me_email)) {
                $this->error = $start . '行:E-mail格式错误';
                return false;
            }

            // 生日
            $birthday = $PHPExcel->sheets[0]['cells'][$start][9];
            if ($birthday && !strtotime($birthday)) {
                $this->error = $start . '行:生日无效';
                return false;
            }

            // 地区
            $re_id = 1;
            $re_title = $region[0][1];
            $province = $PHPExcel->sheets[0]['cells'][$start][11];
            if ($province) {
                $province_id = array_search($province, $region[1]);
                if (!$province_id) {
                    $this->error = $start . '行:省份无效';
                    return false;
                }
                $re_id .= '-' . $province_id;
                $re_title .= '-' . $province;
                $city = $PHPExcel->sheets[0]['cells'][$start][12];
                if ($city) {
                    $city_id = array_search($city, $region[$province_id]);
                    if (!$city_id) {
                        $this->error = $start . '行:城市无效';
                        return false;
                    }
                    $re_id .= '-' . $city_id;
                    $re_title .= '-' . $city;
                    $area = $PHPExcel->sheets[0]['cells'][$start][13];
                    if ($area) {
                        $area_id = array_search($area, $region[$city_id]);
                        if (!$area_id) {
                            $this->error = $start . '行:区域无效';
                            return false;
                        }
                        $re_id .= '-' . $area_id;
                        $re_title .= '-' . $area;
                    }
                }
            }

            // 证件类型
            $me_type = $PHPExcel->sheets[0]['cells'][$start][17];
            $me_type_number = $PHPExcel->sheets[0]['cells'][$start][18];
            if ($me_type_number && !$md_card_type[$me_type]) {
                if ($md_card_type[$me_type] == 0) {
                    if (!preg_match('/^(\d{14}|\d{17})(\d|x)$/', $me_type_number)) {
                        $this->error = $start . '行:身份证号格式错误';
                        return false;
                    }
                }
            }

            // 有效期
            $validity = $PHPExcel->sheets[0]['cells'][$start][21];
            if (!$validity) {
                $this->error = $start . '行:请填写有效期';
                return false;
            }
            if (!strtotime($validity)) {
                $this->error = $start . '行:有效期无效';
                return false;
            }

            // 学校
            $school = $PHPExcel->sheets[0]['cells'][$start][23];
            if ($school) {
                // 学校是否存在
                $sConfig['where']['s_is_deleted'] = 9;
                $sConfig['where']['s_title'] = strval($school);
                $sInfo = D('School', 'Model')->getOne($sConfig);
                if (!$sInfo) {
                    $this->error = $start . '行:学校不存在';
                    return false;
                }

                // 检查用户与学校是否为统一地
                if ($sInfo['re_id'] !== $re_id) {
                    $this->error = $start . '行:用户与学校地区不匹配';
                    return false;
                }
            }

            // 班级
            $class = $PHPExcel->sheets[0]['cells'][$start][24];
            if ($class && !$school) {
                $this->error = $start . '行:请填写学校';
                return false;
            } elseif ($class && $school) {
                // 班级是否存在
                $cConfig['fields'] = 'c_id';
                $cConfig['where']['s_id'] = intval($sInfo['s_id']);
                $cConfig['where']['c_is_deleted'] = 9;
                $cConfig['where']['c_title'] = strval($class);
                $c_id = D('Class', 'Model')->getOne($cConfig);
                if (!$c_id) {
                    $this->error = $start . '行:班级不存在';
                    return false;
                }
            }

            // 用户类型
            $member_type = $PHPExcel->sheets[0]['cells'][$start][3];
            if ($member_type == '家长' && ($school || $class)) {
                $this->error = $start . '行:家长不用填写学校、班级';
                return false;
            } elseif ($member_type == '教师' && $class) {
                $this->error = $start . '行:教师不用填写班级';
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
            'me_creator_table' => 'Member',
            'me_creator_id' => $_SESSION[C('USER_AUTH_KEY')],
            'md_register_ip' => get_client_ip(),
        );
        $config = array_merge($default, $config);

        // 总行数
        $numRows = $PHPExcel->sheets[0]['numRows'];
        $status = array('启用' => 1, '禁用' => 9);
        $region = loadCache('region');
        $md_card_type = array_flip(explode(',', C('MEMBER_CARD_TYPE')));
        $md_political_type = array_flip(explode(',', C('MEMBER_POLITICAL_TYPE')));
        $md_blood_type = array_flip(explode(',', C('MEMBER_BLOOD_TYPE')));
        $sex = array_flip(explode(',', C('MEMBER_SEX')));
        $me_type = array_flip(explode(',', C('MEMBER_TYPE')));
        $saveData = array();

        for ($start = 2; $start <= $numRows; $start++) {

            // 过滤空行
            if (!implode('', $PHPExcel->sheets[0]['cells'][$start])) {
                continue;
            }
            
            $rowData = array();
            // 创建人
            $rowData['me_creator_id'] = intval($config['me_creator_id']);
            $rowData['me_creator_table'] = strval($config['me_creator_table']);
            // 密码
            $rowData['me_password'] = C('MEMBER_DEFAULT_PASSWORD');
            // 姓名
            $rowData['me_nickname'] = strval($PHPExcel->sheets[0]['cells'][$start][1]);
            // 头像
            $avatar_path = $PHPExcel->sheets[0]['cells'][$start][2];
            $rowData['me_avatar'] = '';
            if ($avatar_path) {
                $avatar = '';
                $avatar = $this->dealAvatar($avatar_path, $start);
                if ($avatar !== false) {
                    $rowData['me_avatar'] = $avatar;
                }
            }
            // 类型
            $type = $PHPExcel->sheets[0]['cells'][$start][3];
            $rowData['me_type'] = $me_type[$type] ? intval($me_type[$type]) : 0;

            $rowData['me_mobile'] = strval($PHPExcel->sheets[0]['cells'][$start][4]);
            $rowData['me_phone'] = strval($PHPExcel->sheets[0]['cells'][$start][5]);
            $rowData['me_email'] = strval($PHPExcel->sheets[0]['cells'][$start][6]);
            // 状态
            $me_status = $PHPExcel->sheets[0]['cells'][$start][7];
            $rowData['me_status'] = $status[$me_status] ? intval($status[$me_status]) : 9;

            // 性别
            $md_sex = $PHPExcel->sheets[0]['cells'][$start][8];
            $rowData['md_sex'] = $sex[$md_sex] ? intval($sex[$md_sex]) : 0;
            $md_birthday = $PHPExcel->sheets[0]['cells'][$start][9];
            $rowData['md_birthday'] = $md_birthday ? strval($md_birthday) : 0;
            $rowData['me_note'] = strval($PHPExcel->sheets[0]['cells'][$start][10]);

            // 地区
            $re_id = 1;
            $re_title = $region[0][1];
            $province = $PHPExcel->sheets[0]['cells'][$start][11];
            if ($province) {
                $province_id = array_search($province, $region[1]);
                if ($province_id) {
                    $re_id .= '-' . $province_id;
                    $re_title .= '-' . $province;
                }
                $city = $PHPExcel->sheets[0]['cells'][$start][12];
                if ($province_id && $city) {
                    $city_id = array_search($city, $region[$province_id]);
                    if ($city_id) {
                        $re_id .= '-' . $city_id;
                        $re_title .= '-' . $city;
                    }
                    $area = $PHPExcel->sheets[0]['cells'][$start][13];
                    if ($city_id && $area) {
                        $area_id = array_search($area, $region[$city_id]);
                        if ($area_id) {
                            $re_id .= '-' . $area_id;
                            $re_title .= '-' . $area;
                        }
                    }
                }
            }
            $rowData['re_id'] = strval($re_id);
            $rowData['re_title'] = strval($re_title);
            $rowData['md_chinese_name'] = strval($PHPExcel->sheets[0]['cells'][$start][14]);
            $rowData['md_english_name'] = strval($PHPExcel->sheets[0]['cells'][$start][15]);
            $rowData['md_native_place'] = strval($PHPExcel->sheets[0]['cells'][$start][16]);
            $card_type = $PHPExcel->sheets[0]['cells'][$start][17];
            $rowData['md_card_type'] = $md_card_type[$card_type] ? intval($md_card_type[$card_type]) : 0;
            $rowData['md_card_num'] = strval($PHPExcel->sheets[0]['cells'][$start][18]);
            $political_type = $PHPExcel->sheets[0]['cells'][$start][19];
            $rowData['md_political_type'] = $md_political_type[$political_type] ? $md_political_type[$political_type] : 0;
            $blood_type = $PHPExcel->sheets[0]['cells'][$start][20];
            $rowData['md_blood_type'] = $md_blood_type[$blood_type] ? $md_blood_type[$blood_type] : 0;
            $rowData['md_register_ip'] = rewrite_ip2long($config['md_register_ip']);
            $me_validity = $PHPExcel->sheets[0]['cells'][$start][21];
            $rowData['me_validity'] = $me_validity ? $me_validity : 0;
            $rowData['md_description'] = strval($PHPExcel->sheets[0]['cells'][$start][22]);

            // 学校
            $school = $PHPExcel->sheets[0]['cells'][$start][23];
            $s_id = 0;
            if ($school) {
                $sConfig['fields'] = 's_id';
                $sConfig['where']['s_title'] = strval($school);
                $s_id = D('School', 'Model')->getOne($sConfig);
            }
            $rowData['s_id'] = intval($s_id);

            // 班级
            $class = $PHPExcel->sheets[0]['cells'][$start][24];
            $c_id = 0;
            if ($class) {
                // 班级是否存在
                $cConfig['fields'] = 'c_id';
                $cConfig['where']['s_id'] = intval($s_id);
                $cConfig['where']['c_title'] = strval($class);
                $c_id = D('Class', 'Model')->getOne($cConfig);
            }
            $rowData['c_id'] = intval($c_id);

            // 行号
            $rowData['line_num'] = $start;
            $saveData[] = $rowData;
        }
        
        return $saveData;
    }

    /**
     * 获取列信息
     */
    public function getColumnInfo() {

        return array(1 => '昵称', '头像', '类型', '手机', '电话', 'E-mail', '状态', '性别', '生日', '个人签名', '省份', '城市', '地区', '中文名', '英文名', '籍贯', '证件类型', '证件号', '政治面貌', '血型', '有效期', '简介', '学校', '班级');
    }

    // 检查头像是否存在
    public function checkIsExist($path) {
        // 头像存放位置
        $root_path = C('UPLOADS_ROOT_PATH') . C('AVATAR_FTP_PATH');
        $file_path = $root_path . $path;
        if (!file_exists($file_path)) {
            return false;
        }
        return true;
    }

    // 头像处理
    public function dealAvatar($path, $rule = '') {

        // 头像存放位置
        $root_path = C('UPLOADS_ROOT_PATH') . C('AVATAR_FTP_PATH');
        $file_path = $root_path . $path;

        if (file_exists($file_path)) {
            return D('Member')->uploadedLogoDeal($file_path, $rule);
        }

        return false;
    }
}