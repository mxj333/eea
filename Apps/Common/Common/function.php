<?php
// 密码Hash
function pwdHash($password, $type = 'md5') {
    return hash($type, $password);
}

// 缓存数据
function cacheData() {

    $arr = array('config', 'group', 'node', 'model', 'resourceType', 'resourceCategory', 'tag', 'region', 'appCategory', 'appType', 'runTask', 'knowledgePoints', 'directory', 'resourceSuppliers', 'permissionsGroup', 'permissionsNode', 'templateType', 'template', 'payments', 'gradeSubjectRelation');
    foreach ($arr as $value) {
        reloadCache($value);
    }
}

// 重载缓存数据
function reloadCache($name = 'config', $reload = false) {
    $cache = loadCache($name);
    if (!$cache || $reload) {
        // 初始化
        $config = array();
        switch ($name) {
            case 'group':
                $config['where']['g_status'] = 1;
                $config['order'] = 'g_sort ASC';
                $config['fields'] = 'g_id,g_title';
                $cache = D('Group')->getAll($config);
                break;
            case 'node':
                $config['where']['n_status'] = 1;
                $config['order'] = 'g_id ASC,n_sort ASC,n_id ASC';
                $config['fields'] = 'n_id,n_id,n_name,n_url,g_id,n_title,n_action';
                $cache = D('Node')->getAll($config);
                break;
            case 'permissionsGroup':
                $config['where']['pe_status'] = 1;
                $config['where']['pe_pid'] = 0;
                $config['order'] = 'pe_sort ASC,pe_id ASC';
                $config['fields'] = 'pe_id,pe_type,pe_name,pe_title';
                $list = D('Permissions')->getAll($config);
                foreach ($list as $pe_id => $pe_info) {
                    $cache[intval($pe_info['pe_type'])][intval($pe_info['pe_id'])] = $pe_info;
                }
                break;
            case 'permissionsNode':
                $config['where']['pe_status'] = 1;
                $config['where']['pe_pid'] = array('gt', 0);
                $config['order'] = 'pe_pid ASC,pe_sort ASC,pe_id ASC';
                $config['fields'] = 'pe_id,pe_type,pe_id,pe_name,pe_url,pe_pid,pe_title,pe_action';
                $list = D('Permissions')->getAll($config);
                foreach ($list as $pe_id => $pe_info) {
                    $cache[intval($pe_info['pe_type'])][intval($pe_info['pe_id'])] = $pe_info;
                }
                break;
            case 'model':
                $list  = D('Model')->getAll();
                foreach ($list as $key => $val){
                    $map['where']['at_id'] = array('IN', $val['m_list']);
                    $map['fields'] = 'at_id,at_name';
                    $val['attrs'] = D('Attribute')->getAll($map);
                    $cache[strtolower($val['m_id'])] = $val;
                }
                break;
            case 'resourceType':
                $config['where']['rt_status'] = 1;
                $config['fields'] = 'rt_id,rt_name,rt_exts,rt_title';
                $cache = D('ResourceType')->getAll($config);
                foreach ($cache as $key => $val){
                    $cache[$key]['rt_exts'] = explode(',', $val['rt_exts']);
                }
                break;
            case 'resourceCategory':
                $config['where']['rc_status'] = 1;
                $config['fields'] = 'rc_id,rc_title';
                $cache = D('ResourceCategory')->getAll($config);
                break;
            case 'tag':
                $config['where']['t_status'] = 1;
                $config['fields'] = 't_id,t_title,t_type';
                $list = D('Tag')->getAll($config);
                foreach ($list as $val) {
                    $cache[$val['t_type']][$val['t_id']] = $val['t_title'];
                }
                break;
            case 'region':
                $config['where']['re_status'] = 1;
                $config['fields'] = 're_id,re_title,re_pid';
                $list = D('Region')->getAll($config);
                foreach ($list as $val) {
                    $cache[$val['re_pid']][$val['re_id']] = $val['re_title'];
                }
                break;
            case 'appCategory':
                $config['fields'] = 'ac_id,ac_title';
                $cache = D('AppCategory')->getAll($config);
                break;
            case 'appType':
                $config['fields'] = 'aty_id,aty_title';
                $cache = D('AppType')->getAll($config);
                break;
            case 'runTask':
                // 生成定时命令
                $cache = D('RunTask')->createCrontab();
                break;
            case 'knowledgePoints':
                $config['where']['kp_status'] = 1;
                $config['order'] = 'kp_sort ASC,kp_id ASC';
                $config['fields'] = 'kp_id,kp_title,kp_pid';
                $cache = D('KnowledgePoints')->getAll($config);
                break;
            case 'directory':
                $config['where']['d_status'] = 1;
                $config['order'] = 'd_sort ASC,d_id ASC';
                $config['fields'] = 'd_id,d_title,d_pid';
                $cache = D('Directory')->getAll($config);
                break;
            case 'resourceSuppliers':
                $config['where']['rsu_status'] = 1;
                $config['fields'] = 'rsu_id,rsu_title';
                $cache = D('ResourceSuppliers')->getAll($config);
                $cache[0] = '系统';
                break;
            case 'templateType':
                $config['where']['tt_status'] = 1;
                $config['order'] = 'tt_id ASC';
                $config['fields'] = 'tt_id,tt_title';
                $cache = D('TemplateType')->getAll($config);
                break;
            case 'template':
                $config['where']['te_status'] = 1;
                $config['order'] = 'tt_id ASC,te_id ASC';
                $config['fields'] = 'te_id,tt_id,te_name,te_title';
                $list = D('Template')->getAll($config);
                foreach ($list as $info) {
                    $cache[$info['tt_id']][$info['te_name']] = $info['te_title'];
                }
                break;
            case 'payments':
                $config['where']['pa_status'] = 1;
                $config['order'] = 'pa_id ASC';
                $config['fields'] = 'pa_id,pa_type,pa_title,pa_partner,pa_key,pa_email';
                $list = D('Payments')->getAll($config);
                foreach ($list as $info) {
                    $cache['payments'][$info['pa_type']] = array(
                        'title' => $info['pa_title'],
                        'partner' => $info['pa_partner'],
                        'key' => $info['pa_key'],
                        'email' => $info['pa_email'],
                    );
                }
                break;
            case 'gradeSubjectRelation':
                $config['where']['gsr_status'] = 1;
                $config['order'] = 'gsr_id ASC';
                $config['fields'] = 'gsr_id,gsr_grade,gsr_subject,re_id';
                $list = D('GradeSubjectRelation')->getAll($config);
                foreach ($list as $info) {
                    $cache[$info['re_id']][$info['gsr_grade']] = explode(',', $info['gsr_subject']);
                }
                break;
            default:
                $config['fields'] = 'con_name,con_value';
                $list  = D('Config')->getAll($config);
                $cache = array_change_key_case($list, CASE_UPPER);
        }

        foreach ($cache as $key => $value) {
            $cache[$key] = stripFilter($value, 'myStripSlashes,html_entity_decode,htmlspecialchars_decode,htmlspecialchars_decode');
        }
        
        saveCache($name, $cache);
    }

    C($cache);
    return $cache;
}

// 加载缓存数据
function loadCache($name) {
    $cache = include DATA_PATH . '~' . $name . '.php';
    return $cache;
}

// 保存缓存数据
function saveCache($name, $value) {
    return file_put_contents(DATA_PATH . '~' . $name . '.php', "<?php return " . var_export($value, true) . ";?>");
}

function helloTime() {

    $h = date('G');

    if ($h < 11) {
        $return = '早上';
    } else if ($h < 13) {
        $return = '中午';
    } else if ($h<17) {
        $return = '下午';
    } else {
        $return = '晚上';
    }

    return $return;
}

/*
 * getValueByField
 * 获取数组字段值
 * @param array $array 数组 默认为 array()
 * @param string $field 字段名 默认为id
 *
 * @return array $result 数组(各字段值)
 *
 */
function getValueByField($array = array(), $field = 'id') {
    $result = array();
    if (is_array($array)) {
        foreach ($array as $key => $value) {
            $result[] = trim($value[$field]);
        }
    }
    return $result;
}

/*
 * setArrayByField
 * 根据字段重组数组
 * @param array $array 数组 默认为 array()
 * @param string $field 字段名 默认为id
 *
 * @return array $result 重组好的数组
 *
 */
function setArrayByField($array = array(), $field = 'id') {
    $result = array();
    if (is_array($array)) {
        foreach ($array as $key => $value) {
            $result[$value[$field]] = $value;
        }
    }
    return $result;
}

/**
 * getPathInfo
 * 根据文件名获取文件信息
 *
 * @param string $fileName 文件名
 *
 * @return array $result 文件名称的分割的数组
 */
function getPathInfo($fileName = '') {

    if ($fileName) {

        // 分割数组
        $pathinfo = explode('.', $fileName);

        // 获取上传文件扩展名
        $result['ext'] = end($pathinfo);

        // 删除扩展名
        array_pop($pathinfo);

        // 拼接其他数据
        $pathinfo = implode($pathinfo);

        // 按路径分割
        $pathinfo = explode('/', $pathinfo);

        // 获取文件名
        $result['name'] = end($pathinfo);

        // 删除文件名
        array_pop($pathinfo);

        // 留下路径
        $result['path'] = implode('/', $pathinfo);
    }
    return $result;
}

/*
 * maxSize 文件上传的最大文件大小（以字节为单位），0为不限大小
 * rootPath 文件上传保存的根路径
 * savePath 文件上传的保存路径（相对于根路径）
 * saveName 上传文件的保存规则，支持数组和字符串方式定义
 * saveExt 上传文件的保存后缀，不设置的话使用原文件后缀
 * replace 存在同名文件是否是覆盖，默认为false
 * exts 允许上传的文件后缀（留空为不限制），使用数组或者逗号分隔的字符串设置，默认为空
 * mimes 允许上传的文件类型（留空为不限制），使用数组或者逗号分隔的字符串设置，默认为空
 * autoSub 自动使用子目录保存上传文件 默认为true
 * subName 子目录创建方式，采用数组方式定义 array('date', 'Ymd')
 * hash 是否生成文件的hash编码 默认为true
 * callback 检测文件是否存在回调，如果存在返回文件信息数组
*/
function upload($file, $config = array()) {

    $default = array(
        'maxSize' => C('MAX_UPLOAD_FILE_SIZE'),
        'rootPath' => C('UPLOADS_ROOT_PATH'),
        'autoSub' => false,
    );

    $config = array_merge($default, $config);

    $upload = new \Think\Upload($config);
    $info = $upload->uploadOne($file);

    if(!$info) {
        // 上传错误提示错误信息
        return $upload->getError();
    } else {
        return $info;
    }
}

/**
 * 文件重命名
 */
function fileRename($name, $newname, $path = '', $is_cover = false){

    // 文件地址
    if ($path != '') {
        $name = $path . $name;
        $newname = $path . $newname;
    }

    if (!file_exists($name)) {
        // 源文件不存在
        return false;
    }

    if (file_exists($newname) && !$is_cover) {
        // 新文件名已经存在
        return false;
    }

    $path = get_path_info($newname, 'path');
    if (!file_exists('.' . $path)) {
        mk_dir('.' . $path);
    }

    return rename($name, $newname);
}

/**
 * 文件复制
 */
function fileCopy($name, $newname, $path = '', $is_cover = false){

    // 文件地址
    if ($path != '') {
        $name = $path . $name;
        $newname = $path . $newname;
    }

    if (!file_exists($name)) {
        // 源文件不存在
        return false;
    }

    if (file_exists($newname) && !$is_cover) {
        // 新文件名已经存在
        return false;
    }

    $path = get_path_info($newname, 'path');
    if (!file_exists('.' . $path)) {
        mk_dir('.' . $path);
    }

    return copy($name, $newname);
}

// 去除魔术引号
function myStripSlashes($data) {
    return stripslashes($data);
}

// 添加魔术引号
function myAddSlashes($data) {
    return get_magic_quotes_gpc() ? $data : addslashes($data);
}

function stripFilter($data, $rule = '') {
    $rule = $rule ? $rule : C('STRIP_FILTER');
    $filters = explode(',', $rule);

    foreach($filters as $filter){
        if (function_exists($filter)) {
            $data = is_array($data) ? array_map_recursive($filter, $data) : $filter($data);
        }
    }
    return $data;
}

function turnTpl($content) {
    return substr($content, 1);
}

// 文字过滤
function stringFilter($str = '') {
    // 默认返回值
    $return['status'] = 1;
    $return['str'] = $str;
    if ($str) {

        // 获取参数值其值为数值类型
        $status = C('BLACK_THESAURUS_FILTER_TYPE');

        // 读取词库黑名单词语
        $replace = explode(',', C('THESAURUS_BLACK_LIST'));

        switch ($status) {
            case 2:
                $str = str_replace($replace, '', $str);
                break;
            case 3 :
                // 遍历数组
                foreach ($replace as $key => $value) {

                    if (strpos($str, $value) !== false) {
                        $return['status'] = 0;
                        $return['info'] = '您提交的内容中包含非法字符';
                        break;
                    }
                }
                break;
            default:
                $str = str_replace($replace, '***', $str);
                break;
        }
    }

    $return['str'] = $str;
    return $return;
}

// 删除文件夹
function del_dir($directory, $subdir = true) {
    if (is_dir($directory) == false) {
        return ;
    }
    $handle = opendir($directory);
    while (($file = readdir($handle)) !== false) {
        if ($file != "." && $file != "..") {
            if (is_dir("$directory/$file")) {
                del_dir("$directory/$file");
            } else {
                unlink("$directory/$file");
            }
        }
    }
    if (readdir($handle) == false) {
        closedir($handle);
    }
}

function tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0) {
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = &$list[$key];
        }

        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent = &$refer[$parentId];
                    $parent[$child][] = &$list[$key];
                }
            }
        }
    }
    return $tree;
}

// 无限极 生成树
function generateTree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0){
    foreach ($list as $data) {
        $list[$data[$pid]][$child][$data[$pk]] = &$list[$data[$pk]];
    }
    return isset($list[$root][$child]) ? $list[$root][$child] : array();
}

// 无限极 查询子集
function getChildren($list, $field = 'id', $child = '_child') {
    static $return = array();

    foreach ($list as $data) {
        if (isset($data[$child])) {
            getChildren($data[$child], $field, $child);
        } else {
            $return[] = $data[$field];
        }
    }

    return $return;
}

function mk_dir($dir, $mode = 0755) {
    if (is_dir($dir) || @mkdir($dir,$mode)) return true;
    if (!mk_dir(dirname($dir),$mode)) return false;
    return @mkdir($dir,$mode);
}

function image($oldPath = '', $savePath = '', $config = array(), $water = true) {

    // 默认配置
    $default = array(
        'width' => C('DEFAULT_IMAGE_WIDTH'),
        'height' => C('DEFAULT_IMAGE_HEIGHT'),
        'type' => 'IMAGE_THUMB_SCALE',
    );

    $config = array_merge($default, $config);

    /**
     *
     * IMAGE_THUMB_SCALE     =   1 ; //等比例缩放类型
     * IMAGE_THUMB_FILLED    =   2 ; //缩放后填充类型
     * IMAGE_THUMB_CENTER    =   3 ; //居中裁剪类型
     * IMAGE_THUMB_NORTHWEST =   4 ; //左上角裁剪类型
     * IMAGE_THUMB_SOUTHEAST =   5 ; //右下角裁剪类型
     * IMAGE_THUMB_FIXED     =   6 ; //固定尺寸缩放类型
    */
    $typeArr = array(
        'IMAGE_THUMB_SCALE' => 1,
        'IMAGE_THUMB_FILLED' => 2,
        'IMAGE_THUMB_CENTER' => 3,
        'IMAGE_THUMB_NORTHWEST' => 4,
        'IMAGE_THUMB_SOUTHEAST' => 5,
        'IMAGE_THUMB_FIXED' => 6,
    );

    $image = new \Think\Image();
    $image->open($oldPath);
    $image->thumb($config['width'], $config['height'], $typeArr[$config['type']])->save($savePath);

    if ($water) {
        imageWater($savePath, $savePath);
    }
}

function imageWater($oldPath, $savePath) {

    if (!C('IS_STARTED_IMAGE_WATER')) {
        return;
    }

    // 默认配置
    $config = array(
        'image' => 'IMAGE_WATER_IMAGE',
        'text' => C('IMAGE_WATER_TEXT'),
        'fontSize' => C('IMAGE_WATER_TEXT_FONT_SIZE'),
        'fontColor' => C('IMAGE_WATER_TEXT_FONT_COLOR'),
        'alpha' => C('IMAGE_WATER_IMAGE_ALPHA'),
        'position' => C('IMAGE_WATER_POSITION'),
    );

    /**
     *
     * IMAGE_WATER_NORTHWEST =   1 ; //左上角水印
     * IMAGE_WATER_NORTH     =   2 ; //上居中水印
     * IMAGE_WATER_NORTHEAST =   3 ; //右上角水印
     * IMAGE_WATER_WEST      =   4 ; //左居中水印
     * IMAGE_WATER_CENTER    =   5 ; //居中水印
     * IMAGE_WATER_EAST      =   6 ; //右居中水印
     * IMAGE_WATER_SOUTHWEST =   7 ; //左下角水印
     * IMAGE_WATER_SOUTH     =   8 ; //下居中水印
     * IMAGE_WATER_SOUTHEAST =   9 ; //右下角水印
    */

    $image = new \Think\Image();

    // 文字
    if (C('IMAGE_WATER_TYPE') == 1) {
        $image->open($oldPath)->text($config['text'], './Public/Ttfs/3.ttf', $config['fontSize'], $config['fontColor'], $config['position'])->save($savePath);
    }

    // 图片
    if (C('IMAGE_WATER_TYPE') == 2) {
        $waterFile = C('UPLOADS_ROOT_PATH') . C('CONFIG_FILE_PATH') . strtolower($config['image']) . '.' . C('DEFAULT_IMAGE_EXT');
        $image->open($oldPath)->water($waterFile, $config['position'], $config['alpha'])->save($savePath);
    }
}
// 获取状态
function getStatus($status, $imageShow = true, $clickFunction = '', $id = 0, $clickVal = '') {
    switch($status) {
        case 1:
            $showText = '正常';
            $showImg  = '<img src="/Public/Images/ok.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="正常">';
            break;
        default:
            $showText = '禁用';
            $showImg  = '<img src="/Public/Images/locked.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="禁用">';

    }
    $showContent = ($imageShow===true)? ($showImg) : $showText;

    // 判断是否需要加载单击事件
    if ($clickFunction) {
        // 默认值
        if ($clickVal === '' && $status == 1) {
            $clickVal = 9;
        } elseif ($clickVal === '') {
            $clickVal = 1;
        }

        $showContent = '<a href="javascript:' . $clickFunction . '(' . $id . ', ' . $clickVal . ')">' . $showContent . '</a>';
    }

    return $showContent;
}

function byteFormat($size, $dec = 2) {
    $a = array("B", "KB", "MB", "GB", "TB", "PB");
    $pos = 0;
    while ($size >= 1024) {
        $size /= 1024;
        $pos++;
    }
    return round($size, $dec) . " " . $a[$pos];
}

// 时间戳日期格式化
function toDate($time, $format='Y-m-d H:i:s') {
    if (empty($time)) {
        return '';
    }
    return date(($format), $time);
}

function getShortTitle($title, $length = 12, $stat = '') {
    return msubstr($title, 0, $length, 'utf-8', $stat);
}

function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
    if(function_exists("mb_substr"))
        return mb_substr($str, $start, $length, $charset) . $suffix;
    elseif(function_exists('iconv_substr')) {
        return iconv_substr($str,$start,$length,$charset) . $suffix;
    }
    $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("",array_slice($match[0], $start, $length));
    if($suffix) return $slice."…";
    return $slice;
}

/**
 * getAdvertData
 *
 * param int $id 广告位ID
 * return array $result
 *
 */
function getAdvertData($id) {

    $result = array();
    if (!$id) {
        return $result;
    }

    // 获取广告位
    $result = M('AdvertPosition')->where(array('ap_id' => $id))->field('ap_id,ap_width,ap_height,at_id,ap_ad_num')->find();

    if (!$result) {
        return $result;
    }

    // 获取广告
    $result['advertList'] = M('Advert')->where(array('ap_id' => $id, 'adv_start_time' => array('lt', time()), 'adv_stop_time' => array('gt', time())))->field('adv_id,adv_title,adv_savepath,adv_savename,adv_ext,adv_url,adv_reminds')->order('adv_stop_time DESC,adv_id DESC')->limit($result['ap_ad_num'])->select();

    return $result;
}

//获取某个程序当前的进程数
function get_proc_count($name){
    $cmd =  "ps -e";//调用ps命令
    $output = shell_exec($cmd);
    $result = substr_count($output, ' '.$name);
    return $result;
}

// 下划线转驼峰
function Xhx2Tf($str = '') {
    $len = strlen($str);
    $res = '';
    for ($i = 0; $i < $len; $i ++) {
        $now = ord($str[$i]);
        $next = ord($str[$i + 1]);

        if ($i == 0 && $now >= 97 && $now <= 122) {
            $res .= chr($now - 32);
        } else {
            if ($str[$i] == '_') {
                $res .= chr($next - 32);
                $i ++;
            } else {
                $res .= $str[$i];
            }
        }
    }
    return $res;
}

// 驼峰转下划线
function Tf2Xhx($str = '') {
    $len = strlen($str);
    $res = '';
    for ($i = 0; $i < $len; $i ++) {
        $now = ord($str[$i]);
        $pre = ord($str[$i - 1]);

        if ($i != 0) {
            $res .= (($now >= 65 && $now <= 90) && ($pre >= 97 && $pre <= 122)) ? '_' . chr($now + 32) : $str[$i];
        } else {
            $res .= ($now >= 65 && $now <= 90) ? chr($now + 32) : $str[$i];
        }
    }
    return $res;
}

/**
 * 获取中英文混搭字符串的长度 1个英文计1  1个中文计1
 * @param string $string 字符串
 * @param string $charset 字符集
 * @return string
 */
function get_string_length($string,$charset='utf-8'){
    if($charset=='utf-8') {
        $string = iconv('utf-8','gbk',$string);
    }
    $num = strlen($string);
    $cnNum = 0;
    for($i=0;$i<$num;$i++){
        if(ord(substr($str,$i+1,1))>127){
            $cnNum++;
            $i++;
        }
    }
    $enNum = $num-($cnNum*2);
    $number = ($enNum/2)+$cnNum;
    return ceil($number);
}

/**
 * 获取字符串的长度 2个英文计1  1个中文计1
 * @param string $string 字符串
 * @param string $charset 字符集
 * @return string
 */
function get_string_total_length($string,$charset='utf-8'){
    if($charset=='utf-8') {
        $string = iconv('utf-8','gbk',$string);
    }
    $num = strlen($string);
    return ceil($num/2);
}

/**
 * 判断email格式是否正确
 * @param string $email
 * @return bool
 */
function is_email($email) {
    return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}

/**
 * 判断电话号格式是否正确
 * @param string $phone
 * @return bool
 */
function is_phone_number($phone, $type='') {
    // 正则
    $regxArr = array(  
        'phone' => '/^(\+?86-?)?(18|15|13)[0-9]{9}$/',  
        'tel' => '/^(010|02\d{1}|0[3-9]\d{2})-\d{7,9}(-\d+)?$/',  
        '400' => '/^400(-\d{3,4}){2}$/',  
    );

    // 单种验证
    if($type && isset($regxArr[$type])) {
        return preg_match($regxArr[$type], $phone) ? true : false;
    }

    // 混合验证
    foreach($regxArr as $regx) {
        if(preg_match($regx, $phone )) {
            return true;
        }
    }

    return false;  
}

// 判断是否为闰年
function is_leap_years($year) {
    if($year%100 == 0){ //判断世纪年
        if ($year%400 == 0 && $year%3200 != 0) {
            return true;//世纪年里的闰年
        } else {
            return false;
        }
    } else { //剩下的就是普通年了
        if ($year%4 == 0 && $year%100 != 0) {
            return true; //普通年里的闰年
        } else {
            return false;
        }
    }
}

/**
 * 安全IP检测，支持IP段检测
 * @param string $ip 要检测的IP
 * @param string|array $ips  白名单IP或者黑名单IP
 * @return boolean true 在白名单或者黑名单中，否则不在
 */
function is_safe_ip($ip = "", $ips = ""){
    if (!$ip) {
        //获取客户端IP
        $ip = get_client_ip();
    }

    if($ips){
        //ip用"," 例如IP：192.168.1.13,123.23.23.44,193.134.*.*
        if(is_string($ips)){
            $ips = explode(",", $ips);
        }
    }else{
        //读取后台配置 IP
        $ips = getIpDenyList(); 
    }

    if(in_array($ip, $ips)){
        return true;
    }

    // 匹配 ip 段
    $ipregexp = implode('|', str_replace( array('*','.'), array('\d+','\.') ,$ips)); 
    $rs = preg_match("/^(".$ipregexp.")$/", $ip); 
    if($rs) {
        return true;
    }

    return false;
}

/* 
 * ip 黑名单设置
 * 保存格式
 * deny 1.2.3.4;
 * deny 91.212.45.0/24;
 */ 
function setIpDenyList($ip_list) {
    $res['status'] = true;
    $res['info'] = '';

    $ip_array = explode("\r\n", $ip_list);
    foreach ($ip_array as $key => $ip) {
        if (!$ip) {
            // 过滤空
            unset($ip_array[$key]);
            continue;
        }

        // 验证是否符合规则
        if (!preg_match('/^(\d{1,3}|\*)(\.(\d{1,3}|\*)){3};$/', $ip) || $ip == '*.*.*.*') {
            $res['status'] = false;
            $res['info'] = 'IP格式不正确';
            return $res;
        }
    }
    
    $ip_list = implode("\r\ndeny ", $ip_array);
    if ($ip_list) {
        $ip_list = 'deny ' . $ip_list;
        $ip_list = str_replace('*', '0/255', $ip_list);
    }
    
    // IP黑名单保存路径
    if (!file_exists(C('IP_DENY_PATH'))) {
        mk_dir(C('IP_DENY_PATH'));
    }

    // 保存
    file_put_contents(C('IP_DENY_PATH') . 'ip_deny_list', $ip_list);
    // 结果
    if (file_exists(C('IP_DENY_PATH') . 'ip_deny_list')) {
        return $res;
    } else {
        $res['status'] = false;
        $res['info'] = 'IP黑名单保存失败';
        return $res;
    }
}

// ip 黑名单获取
function getIpDenyList() {

    // 保存路径
    if (!file_exists(C('IP_DENY_PATH') . 'ip_deny_list')) {
        return array();
    }

    $ip_list = file_get_contents(C('IP_DENY_PATH') . 'ip_deny_list');
    $ip_list = str_replace('0/255', '*', $ip_list);
    $ip_list = str_replace(array('deny ', "\r\n"), '', $ip_list);
    
    return explode(';', $ip_list);
}

/**
 * 根据文件名获取文件信息
 * @param string $fileName 文件名
 * @param string $field 文件字段名
 * @return array $result 文件名称的分割的数组
 */
function get_path_info($fileName = '', $field = '') {
    if ($fileName) {
        // 分割数组
        $pathinfo = explode('.', $fileName);
        // 获取上传文件扩展名
        $result['ext'] = end($pathinfo);
        // 删除扩展名
        array_pop($pathinfo);
        // 拼接其他数据
        $pathinfo = implode($pathinfo);
        // 按路径分割
        $pathinfo = explode('/', $pathinfo);
        // 获取文件名
        $result['name'] = end($pathinfo);
        // 删除文件名
        array_pop($pathinfo);
        // 留下路径
        $result['path'] = implode('/', $pathinfo);
    }

    if ($field) {
        return $result[$field];
    }

    return $result;
}

/*
 * 识别文件后缀
 * $filename  上传文件的tmp_name
 */
function get_file_ext($filename) {

    $filetype = false;

    if (!file_exists($filename)) {
        // 文件不存在
        return false;
    }

    $file = @fopen($filename, "rb");
    if (!$file) {
        // 没读权限
        return false;
    }
    
    // 文件后缀
    $ext = get_path_info($filename, 'ext');

    //只读15字节 各个不同文件类型，头信息不一样。
    $bin = fread($file, 15);
    $typelist = get_file_type_list();
    foreach ($typelist as $info) {

        $blen = strlen(pack("H*", $info[0])); //得到文件头标记字节数
        $tbin = substr($bin, 0, intval($blen)); ///需要比较文件头长度
        
        if(strtolower($info[0]) == strtolower(array_shift(unpack("H*", $tbin)))) {
            // 特殊处理 根据后缀 判断类型
            $special_exts = array(
                'jpg/jpeg' => array('jpg', 'jpeg'),
                'xls/doc/ppt/wps' => array('xls', 'doc', 'ppt', 'wps'),
                'eps/ps' => array('eps', 'ps'),
                'xlsx/docx/pptx' => array('xlsx', 'docx', 'pptx'),
                'wma/asf' => array('wma', 'asf'),
                'wav/riff' => array('wav', 'riff'),
                'rm/rmvb' => array('rm', 'rmvb'),
            );

            if ($special_exts[$info[1]]) {
                if (in_array($ext, $special_exts[$info[1]])) {
                    $filetype = $ext;
                } else {
                    $filetype = false;
                }
            } else {
                $filetype = $info[1];
            }

            break;
        }
    }

    // 判断是否开启后缀识别
    if ($filetype === false && C('OPEN_SUFFIX_IDENTIFY')) {
        $filetype = $ext;
    }

    return $filetype;
}

/*
 * 文件头与后缀名对应关系
 */
function get_file_type_list() {
    return array(
        array("ffd8ff", "jpg/jpeg"),
        array("89504e47", "png"),
        array("47494638", "gif"),
        array("424d", "bmp"),
        array("49492a00", "tif"),
        array("41433130", "dwg"),
        array("38425053", "psd"),
        array("7b5c727466", "rtf"),
        array("3c3f786d6c", "xml"),
        array("68746d6c3e", "html"),
        array("44656c69766572792d646174", "eml"),
        array("cfad12fec5fd746f", "dbx"),
        array("2142444e", "pst"),
        array("d0cf11e0", "xls/doc/ppt/wps"),
        array("5374616e64617264204a", "mdb"),
        array("ff575043", "wpd"),
        array("252150532d41646f6265", "eps/ps"),
        array("255044462d312e", "pdf"),
        array("ac9ebd8f", "qdf"),
        array("e3828596", "pwl"),
        array("504b030414", "xlsx/docx/pptx"),
        array("504b03040a", "zip"),
        array("1f8b08", "gz"), // tar.gz
        array("52617221", "rar"),
        array("41564920", "avi"),
        array("2e7261fd", "ram"),
        array("2e524d46", "rm/rmvb"),
        array("000001ba", "mpg"),
        array("000001b3", "mpg"),
        array("6d6f6f76", "mov"),
        array("3026b2758e66cf11", "wma/asf"),
        array("4d546864", "mid"),
        array("57415645", "wav"),
        array("52494646", 'wav/riff'),
        array("49443303", "mp3"),
        //array("0000002066747970", "mp4"),
        // txt wps m4a m4v rmvb 3gp wma wmv webm f4v mp4 flv mkv mpeg fla
    );
}

/*
 * 根据后缀获取资源类型
 */
function get_resource_type_name_by_ext($value) {

    $resource_type = reloadCache('resourceType');

    foreach ($resource_type as $rt_id => $rt_info){
        // 通过后缀名  获取 资源类型名
        if (in_array(strtolower($value), $rt_info['rt_exts'])) {
            return strtolower($rt_info['rt_name']);
        }
    }

    return 'other';
}

/*
 * 根据资源类型 name 获取资源类型 id
 */
function get_resource_type_id_by_name($value) {

    $resource_type = reloadCache('resourceType');

    foreach ($resource_type as $rt_id => $rt_info){
        // 获取 资源类型名
        if ($value == $rt_info['rt_name']) {
            return strtolower($rt_id);
        }
    }
}

// 大文件上传
function plupload($data, $config = array()) {
    $default = array(
        'setTimeLimit' => 300, // 5 分钟
        'uploadRootPath' => C('UPLOADS_ROOT_PATH'), // 根目录
        'filePath' => '', // 文件保存路径
        'fileName' => '', // 文件名称（带后缀）
        'cleanupTargetDir' => true, // 是否清除旧文件
        'maxFileAge' => 18000, // 文件保存时长 5 小时
        'chunk' => 0, // Chunking might be enabled
        'chunks' => 0, // Chunking might be enabled
    );

    $config = array_merge($default, $config);

    header("Content-Type: text/html;charset=utf-8");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    // 5 minutes execution time
    @set_time_limit($config['setTimeLimit']);
    
    // 临时存放目录
    $targetDir = $config['uploadRootPath'] . $config['filePath'];
    
    // 检查目录是否存在
    if (!file_exists($targetDir)) {
        mk_dir($targetDir);
    }

    //windows fileName need convert code from utf-8 to GB2312
    //$config['fileName'] = iconv('UTF-8', 'GB2312//IGNORE', $config['fileName']);
    $filePath = $targetDir . $config['fileName'];

    // Remove old temp files	
    if ($config['cleanupTargetDir']) {
        if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
            return array(
                'jsonrpc' => '2.0',
                'error' => array(
                            'code' => 100,
                            'message' => 'Failed to open temp directory.',
                        ),
                'id' => 'id',
            );
        }

        while (($file = readdir($dir)) !== false) {
            $tmpfilePath = $targetDir . '/' . $file;

            // If temp file is current file proceed to the next
            if ($tmpfilePath == "{$filePath}.part") {
                continue;
            }

            // Remove temp file if it is older than the max age and is not the current file
            if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $config['maxFileAge'])) {
                @unlink($tmpfilePath);
            }
        }
        closedir($dir);
    }
    
    // Open temp file
    if (!$out = @fopen("{$filePath}.part", $config['chunks'] ? "ab" : "wb")) {
        return array(
            'jsonrpc' => '2.0',
            'error' => array(
                        'code' => 102,
                        'message' => 'Failed to open output stream.',
                    ),
            'id' => 'id',
        );
    }

    if (!empty($data)) {
        if ($data["file"]["error"] || !is_uploaded_file($data["file"]["tmp_name"])) {
            return array(
                'jsonrpc' => '2.0',
                'error' => array(
                            'code' => 103,
                            'message' => 'Failed to move uploaded file.',
                        ),
                'id' => 'id',
            );
        }

        // Read binary input stream and append it to temp file
        if (!$in = @fopen($data["file"]["tmp_name"], "rb")) {
            return array(
                'jsonrpc' => '2.0',
                'error' => array(
                            'code' => 101,
                            'message' => 'Failed to open input stream.',
                        ),
                'id' => 'id',
            );
        }
    } else {	
        if (!$in = @fopen("php://input", "rb")) {
            return array(
                'jsonrpc' => '2.0',
                'error' => array(
                            'code' => 101,
                            'message' => 'Failed to open input stream.',
                        ),
                'id' => 'id',
            );
        }
    }

    while ($buff = fread($in, 4096)) {
        fwrite($out, $buff);
    }

    @fclose($out);
    @fclose($in);

    // Check if file has been uploaded
    if (!$config['chunks'] || $config['chunk'] == $config['chunks'] - 1) {
        // Strip the temp .part suffix off 
        rename("{$filePath}.part", $filePath);

        return array(
            'jsonrpc' => '2.0',
            'result' => $filePath,
            'id' => 'id',
        );
    }

    // Return Success JSON-RPC response
    return array(
        'jsonrpc' => '2.0',
        'result' => null,
        'id' => 'id'
    );
}

// 文件命名规则
function savename_rule($key = '') {
    return uniqid($key);
}

// 将pdf转换为swf
function pdf2Swf($file_path, $save_path=''){

    // 文件不存在返回false
    if(!file_exists($file_path)) {
        return false;
    }

    $swf_bin = C('SWF_PATH');
    if (!file_exists($swf_bin)) {
        return false;
    }
    putenv('PATH=/usr/bin');
    $cmd = $swf_bin.' -t '.$file_path.' -o '.$save_path.' -T 9 -G -s poly2bitmap';
    exec($cmd, $out, $statue);
    if(file_exists($save_path)) {
        return $save_path;
    } else {
        return false;
    }
}

// 将word转换为txt
function word2txt($file_path, $save_path, $resource){

    // 文件不存在返回false
    if(!file_exists($file_path)) {

        $return['status'] = false;
        return $return;
    }

    // 设置转码后的文档名称
    // 如果是windows系统
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'){

    } else {

        // 否则就是linux系统
        $word_2_pdf_bin = C('OPEN_OFFICE_SERVER');
        $content = '';
        if (!file_exists($save_path)) {
            putenv('PATH=/usr/bin');
            $cmd = $word_2_pdf_bin." -o ".$save_path." -f txt ".$file_path;
            exec($cmd, $out, $statue);

            $content = file_get_contents($save_path);
        }

        // 写入数据库
        $edit_config['where']['rf_id'] = $resource['rf_id'];
        $edit_data['rf_content'] = $content;
        D('ResourceFile', 'Model')->update($edit_data, $edit_config);

        if (file_exists($save_path)) {
            $return['status'] = true;

            $content = stringFilter($content);

            if ($content['status'] == 0) {
                $insertContent = $content['info'];
            } else {
                $insertContent = $content['str'];
            }
            $return['rf_content'] = getShortTitle($insertContent, 200);
        }

        return $return;
    }
}

// 将word文档转为pdf格式
function word2pdf($file_path, $save_path, $resource){

    // 文件不存在返回false
    if(!file_exists($file_path)) {

        $return['status'] = false;
        return $return;
    }

    // 设置转码后的文档名称
    // 如果是windows系统
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'){

    } else {

        // 否则就是linux系统
        $word_2_pdf_bin = C('OPEN_OFFICE_SERVER');
        if (!file_exists($save_path)) {
            putenv('PATH=/usr/bin');
            $cmd = "sudo ". $word_2_pdf_bin." -o ".$save_path." -f pdf ".$file_path;
            exec($cmd, $out, $statue);
        }

        if (file_exists($save_path)) {
            $return['status'] = true;
        }

        return $return;
    }
}

// 转mp4
function videoToMp4($file_path, $save_path, $resource) {

    // 如果文件不存在，直接返回false
    if (!file_exists($file_path)) {
        return false;
    }

    $mp4_file_name = $save_path . $resource['rf_savename'] . ".mp4";
    $h_mp4_file_name = $save_path . $resource['rf_savename'] . "_h.mp4";
    $f_mp4_file_name = $save_path . $resource['rf_savename'] . ".flv";
    $mp4_jpg_name = $save_path . $resource['rf_savename'] . "_s.jpg";

    // 加载不同操作系统下转码工具
    $ffmpeg_bin = C('FFMPEG_BIN_PATH');

    // 如果工具不存在直接退出
    if (!file_exists($ffmpeg_bin)) return FALSE;

    // 视频相关设定
    $video_ab = explode(',', C('VIDEO_TRANSFORM_AB'));
    $video_ar = explode(',', C('VIDEO_TRANSFORM_AR'));
    $video_s = explode(',', C('VIDEO_TRANSFORM_S'));

    // 标清
    if (!file_exists($mp4_file_name)) {
        putenv('PATH=/usr/bin');
        $cmd = $ffmpeg_bin." -n -i ".$file_path." -vcodec libx264  -s ".$video_s[0]." -f mp4 -acodec libfaac -ab ".$video_ab[0]." -ar ".$video_ar[0]." -ac 2 ".$mp4_file_name;
        exec($cmd, $out, $statue);
    }

    // 高清
    if (!file_exists($h_mp4_file_name)) {
        putenv('PATH=/usr/bin');
        $cmd = $ffmpeg_bin." -n -i ".$file_path." -vcodec libx264  -s ".$video_s[1]." -f mp4 -acodec libfaac -ab ".$video_ab[1]." -ar ".$video_ar[1]." -ac 2 ".$h_mp4_file_name;
        exec($cmd, $out, $statue);
    }

    // FLV
    if (!file_exists($f_mp4_file_name)) {
        putenv('PATH=/usr/bin');
        $cmd = $ffmpeg_bin." -n -i ".$file_path." -ab ".$video_ab[2]." -ar ".$video_ar[2]." -b 500 -r 15 -s ".$video_s[2]." ".$f_mp4_file_name;
        exec($cmd, $out, $statue);
    }

    if (!file_exists($mp4_jpg_name)) {
        putenv('PATH=/usr/bin');
        $cmd2 = $ffmpeg_bin." -i " . $file_path." -y -f image2 -ss 25 -t 0.001 -s 180x180 " . $mp4_jpg_name;
        exec($cmd2, $out, $statue);
    }

    // 如果文件存在，返回true，否则返回false
    if (file_exists($mp4_file_name) && file_exists($h_mp4_file_name) && file_exists($f_mp4_file_name) && file_exists($mp4_jpg_name)) {
        return $mp4_file_name;
    } else {
        return FALSE;
    }
}

/**
 * 12年级转换成学制年级
 * $twelve 12年级的 id
 * $school_min 学制最小 id
 * $grade_min 年级最小 id
 */
function twelve2grade($twelve_id, $school_min = 15, $grade_min = 44) {
    
    // 学制年级数
    $config = str_split(C('SCHOOLTYPE_GRADE_NUM'));
    
    $twelve = $twelve_id - $grade_min;
    foreach ($config as $key => $number) {
        $twelve = $twelve - $number;
        if ($twelve <= 0) {
            $school_type = $key;
            $grade = $twelve + $number;
            break;
        }
    }

    return array(
        'res_school_type' => $school_min + $school_type,
        'res_grade' => $grade_min + $grade,
    );
}

/**
 * 学制年级转换成12年级
 * $school_type_id 学制的 id
 * $grade_id 年级的 id
 * $school_min 学制最小 id
 * $grade_min 年级最小 id
 */
function grade2twelve($school_type_id, $grade_id, $school_min = 15, $grade_min = 44) {
    // 学制年级数
    $config = str_split(C('SCHOOLTYPE_GRADE_NUM'));
    
    $school_type = $school_type_id - $school_min;

    $sum_config = array_slice($config, 0, $school_type);
    $sum = array_sum($sum_config);

    return $grade_id + $sum;
}

function toTree($list, $id = 'id', $pid = 'pid', $level = 'level', $title = 'title', $child = 'list') {

    foreach ($list as $key => $val){
        $tab = str_repeat("\t", $val[$level]);
        $xml .= $tab . '<level' . $val[$level] . ' id="' . $val[$id] . '" level="' . $val[$level] . '" parentId="' . $val[$pid] . '" caption="' . $val[$title] . '"';

        if (isset($val[$child])) {
            $xml .= '>' . toTree($val[$child], $id, $pid, $level, $title, $child) . $tab . '</level' . $val[$level] . '>';
        } else {
            $xml .= '/>';
        }
    }
    return $xml;
}

// 自动获取账号
function getAccount($name = 'account', $min = 6){

    // 账号文件不存在
    if (!file_exists(C('ACCOUNT_PATH') . $name . '_list_' . $min)) {
        // 生成
        if (!addAccount($name, $min)) {
            // 账号生成失败
            return false;
        }
    }

    // 账号随机取一个
    return getRandAccount($name, $min);
}

function addAccount($name = 'account', $min = 6){
    
    set_time_limit(0);

    // 账号路径
    $account_path = C('ACCOUNT_PATH');
    if (!file_exists($account_path)) {
        mk_dir($account_path);
    }
    // 账号文件
    $account_file = $account_path . $name . '_list_' . $min;

    // 目前支持 最小 6 位账号
    $min_num = pow(10,$min-1);
    $max_num = pow(10,$min);

    for ($start = $min_num; $start < $max_num; $start++) {
        if (filterAccount($start)) {
            // 过滤  只存有效账号
            file_put_contents($account_file, $start."\n", FILE_APPEND);
        }
    }

    if (file_exists($account_file)) {
        return true;
    } else {
        return false;
    }
}

function getRandAccount($name = 'account', $min = 6) {

    $rand_num = rand(0, 1000);
    $handle = fopen(C('ACCOUNT_PATH') . $name . '_list_' . $min, 'r+');
    $account = 0;
    $start = 0;
    while(!feof($handle)){
        $line = intval(fgets($handle));
        if ($line) {
            if ($start == $rand_num) {
                // 当前指针位置
                $key = ftell($handle);
                // 指针回滚到行首
                fseek($handle, $key-$min-1);
                // 当前行替换为空
                fwrite($handle, str_pad('', $min)."\n");
                // 随机位置的值 即账号
                $account = $line;
                break;
            }
            $start++;
        }
    }
    fclose($handle);

    return $account;
}

// 指定账号时 需要删除账号文件中已有的账号
// true 可用   false 需要查库确定是否可用
function deleteAccount($account, $name = 'account') {
    $min = strlen(intval($account));

    // 指定的账号没在自动生成账号的文件内
    if ($min < C('CURRENT_ACCOUNT_NUMBER')) {
        // 账号没在范围内
        return true;
    }

    $return = false;
    $handle = fopen(C('ACCOUNT_PATH') . $name . '_list_' . $min, 'r+');
    while(!feof($handle)){
        $line = intval(fgets($handle));
        if ($line == $account) {
            // 当前指针位置
            $key = ftell($handle);
            // 指针回滚到行首
            fseek($handle, $key-$min-1);
            // 当前行替换为空
            fwrite($handle, str_pad('', $min)."\n");
            $return = true;
            break;
        }
    }
    fclose($handle);

    return $return;
}

// 过滤账号
function filterAccount($account) {
    if (preg_match_all('/4|8|(^\d0+$)|(^(1+|2+|3+|4+|5+|6+|7+|8+|9+)$)/', $account)) {
        // 账号不能有 4 或 8 或 数字打头后面都是0 或 都是同一个数字
        return false;
    }

    return true;
}

function get_server_ip() {
    if (isset($_SERVER)) {
        if($_SERVER['SERVER_ADDR']) {
            $server_ip = $_SERVER['SERVER_ADDR'];
        } else {
            $server_ip = $_SERVER['LOCAL_ADDR'];
        }
    } else {
        $server_ip = getenv('SERVER_ADDR');
    }
    return $server_ip;
}

// 文件 转换成 二进制流
function file_to_binary($file) {

    //判断是否有这个文件 
    if(!file_exists($file)){ 
        // 没有这个文件 
        return '没有这个文件';
    }

    $fp = fopen($file,"a+");
    if(!$fp){ 
        // 文件打不开; 
        return '文件打不开';
    }
    
    //读取文件 
    $conn = fread($fp, filesize($file));
    fclose($fp);
    
    $len = strlen($conn); 
    $bin = ''; 
    for($i = 0; $i < $len; $i  ) 
    { 
        $bin .= strlen(decbin(ord($conn[$i]))) < 8 ? str_pad(decbin(ord($conn[$i])), 8, 0, STR_PAD_LEFT) : decbin(ord($conn[$i])); 
    }

    return $bin;
}

function binary_to_file($file){

    $content = $GLOBALS['HTTP_RAW_POST_DATA'];
    if(empty($content)){
        $content = file_get_contents('php://input');
    }

    $ret = file_put_contents($file, $content, true);
    return $ret;
}

function createWord($path, $name, $content) {
    if (!file_exists($path)) {
        mk_dir($path);
    }

    $fp = fopen($path . $name, "wb");
    fwrite($fp, $content);
    fclose($fp);

    if (!file_exists($path . $name)) {
        return false;
    }

    return true;
}

function download($file) {
    if(is_file($file)) {
        header("Content-Type: application/force-download");
        header("Content-Disposition: attachment; filename=".basename($file));
        readfile($file);
        exit;
    }else{
        echo "文件不存在！";
        exit;
    }
}

// ip2long 会出现负数，重写此方法
function rewrite_ip2long($ip) {
    return bindec(decbin(ip2long($ip)));
}

// 解析 url 中参数  为数组
function parseUrlParam($param = '', $function = 'index', $controller = 'index') {
    // 参数不存在
    if (!$param) {
        return false;
    }

    // 转换为数组
    $param_value = explode('-', $param);
    if (!$param_value) {
        return false;
    }

    // 获取规则
    $rules = getUrlParamRule($controller, $function);
    if (is_array($rules)) {
        $rules = implode('-', $rules);
    }

    // 获取规则字段
    $replace = getUrlRuleFields($controller, $function);

    // 规则模式替换成字段
    $fields = explode('-', str_replace(array_keys($replace), array_values($replace), $rules));
    // 字段与参数值合并
    $return = array_combine($fields, $param_value);

    return $return;
}

// 获取 url 参数规则
function getUrlParamRule($controller = 'index', $function = 'index') {
    $rules = array(
        'list' => array(
            'resource' => '{rc_id}-{version}-{school_type}-{grade}-{semester}-{subject}-{kp_id}-{d_id}-{s_year}-{re_id}-{p}-{order}-{sort}',
            'article' => '{ca_id}-{p}-{order}-{sort}',
            'app' => '{ac_id}-{type}-{p}-{order}-{sort}',
        ),
        'detail' => array(
            'resource' => '{rt_id}-{year}-{month}-{day}-{hour}-{id}-{p}',
            'article' => '{ca_id}-{year}-{month}-{day}-{hour}-{id}-{p}',
            'app' => '{ac_id}-{type}-{year}-{month}-{day}-{hour}-{id}-{p}',
            'notice' => '{year}-{month}-{day}-{hour}-{id}-{p}',
        ),
        'search' => array(
            'resource' => '{rc_id}-{version}-{school_type}-{grade}-{semester}-{subject}-{kp_id}-{d_id}-{s_year}-{re_id}-{p}-{order}-{sort}-{keywords}',
            'article' => '{ca_id}-{p}-{order}-{sort}-{keywords}',
            'app' => '{ac_id}-{type}-{p}-{order}-{sort}-{keywords}',
            'notice' => '{keywords}-{order}-{sort}-{p}',
        ),
        'download' => array(
            'resource' => '{id}',
            'app' => '{type}-{id}',
        ),
    );

    return strval($rules[strtolower($function)][strtolower($controller)]);
}

/*
 * 获取 url 规则字段
 * controller     resource   article  
 * function     index 首页  list 列表页  search 搜索页   detail 详情页
 */
function getUrlRuleFields($controller = 'index', $function = 'index') {
    
    $rules_fields = array(
        'list' => array(
            'resource' => array(
                '{rc_id}' => 'rc_id',
                '{version}' => 'res_version',
                '{school_type}' => 'res_school_type',
                '{grade}' => 'res_grade',
                '{semester}' => 'res_semester',
                '{subject}' => 'res_subject',
                '{kp_id}' => 'kp_id',
                '{d_id}' => 'd_id',
                '{s_year}' => 's_year',
                '{year}' => 'year',
                '{re_id}' => 're_id',
                '{p}' => 'p',
                '{order}' => 'order',
                '{sort}' => 'sort'
            ),
            'article' => array(
                '{ca_id}' => 'ca_id',
                '{p}' => 'p',
                '{order}' => 'order',
                '{sort}' => 'sort'
            ),
            'app' => array(
                '{ac_id}' => 'ac_id',
                '{type}' => 'type',
                '{p}' => 'p',
                '{order}' => 'order',
                '{sort}' => 'sort'
            ),
        ),
        'detail' => array(
            'resource' => array(
                '{rt_id}' => 'rt_id',
                '{year}' => 'res_created',
                '{month}' => 'res_created',
                '{day}' => 'res_created',
                '{hour}' => 'res_created',
                '{id}' => 'res_id',
                '{p}' => 'p',
            ),
            'article' => array(
                '{ca_id}' => 'ca_id',
                '{year}' => 'art_designated_published',
                '{month}' => 'art_designated_published',
                '{day}' => 'art_designated_published',
                '{hour}' => 'art_designated_published',
                '{id}' => 'art_id',
                '{p}' => 'p',
                '{m}' => 'm_id',
            ),
            'app' => array(
                '{ac_id}' => 'ac_id',
                '{type}' => 'type',
                '{year}' => 'a_created',
                '{month}' => 'a_created',
                '{day}' => 'a_created',
                '{hour}' => 'a_created',
                '{id}' => 'a_id',
                '{p}' => 'p',
            ),
            'notice' => array(
                '{year}' => 'no_starttime',
                '{month}' => 'no_starttime',
                '{day}' => 'no_starttime',
                '{hour}' => 'no_starttime',
                '{id}' => 'no_id',
                '{p}' => 'p',
            ),
        ),
        'search' => array(
            'resource' => array(
                '{rc_id}' => 'rc_id',
                '{version}' => 'res_version',
                '{school_type}' => 'res_school_type',
                '{grade}' => 'res_grade',
                '{semester}' => 'res_semester',
                '{subject}' => 'res_subject',
                '{kp_id}' => 'kp_id',
                '{d_id}' => 'd_id',
                '{s_year}' => 's_year',
                '{year}' => 'year',
                '{re_id}' => 're_id',
                '{p}' => 'p',
                '{order}' => 'order',
                '{sort}' => 'sort',
                '{keywords}' => 'res_title'
            ),
            'article' => array(
                '{ca_id}' => 'ca_id',
                '{p}' => 'p',
                '{order}' => 'order',
                '{sort}' => 'sort',
                '{keywords}' => 'art_title'
            ),
            'app' => array(
                '{ac_id}' => 'ac_id',
                '{type}' => 'type',
                '{p}' => 'p',
                '{order}' => 'order',
                '{sort}' => 'sort',
                '{keywords}' => 'a_title'
            ),
            'notice' => array(
                '{p}' => 'p',
                '{order}' => 'order',
                '{sort}' => 'sort',
                '{keywords}' => 'no_title'
            ),
        ),
        'download' => array(
            'resource' => array(
                '{id}' => 'res_id',
            ),
            'app' => array(
                '{id}' => 'a_id',
                '{type}' => 'type',
            ),
        ),
    );
    
    // 规则对应的字段名
    return (array)$rules_fields[strtolower($function)][strtolower($controller)];
}

/* 
 * 获取静态URL地址
 */
function getUrlAddress($data = array(), $function = 'index', $controller = 'index', $module = null, $uuid = null, $page = 1) {
    // 默认值定义
    if (!$module) {
        $module = MODULE_NAME;
    }
    if (!$uuid) {
        $uuid = CURRENT_UUID;
    }

    switch (strtolower($controller)) {
        case 'resource':
            $replace = array(
                '{rc_id}' => $data['rc_id'] ? $data['rc_id'] : 0,
                '{version}' => $data['res_version'] ? $data['res_version'] : 0,
                '{school_type}' => $data['res_school_type'] ? $data['res_school_type'] : 0,
                '{grade}' => $data['res_grade'] ? $data['res_grade'] : 0,
                '{semester}' => $data['res_semester'] ? $data['res_semester'] : 0,
                '{subject}' => $data['res_subject'] ? $data['res_subject'] : 0,
                '{kp_id}' => $data['kp_id'] ? $data['kp_id'] : 0,
                '{d_id}' => $data['d_id'] ? $data['d_id'] : 0,
                '{re_id}' => $data['re_id'] ? $data['re_id'] : '',
                '{rt_id}' => $data['rt_id'] ? $data['rt_id'] : 0,
                '{rt_name}' => $data['rt_name'] ? $data['rt_name'] : '',
                '{s_year}' => $data['year'] ? $data['year'] : 0,
                '{order}' => $data['order'] ? $data['order'] : 'res_created',
                '{sort}' => $data['sort'] ? $data['sort'] : 'desc',
                '{page}' => $page ? $page : 1,
                '{id}' => $data['res_id'],
                '{year}' => date('y', $data['res_created']),
                '{month}' => date('m', $data['res_created']),
                '{day}' => date('d', $data['res_created']),
                '{hour}' => date('H', $data['res_created']),
                '{p}' => $page ? $page : 1,
                '{keywords}' => $data['res_title'],
            );
            break;
        case 'article':
            $replace = array(
                '{ca_id}' => $data['ca_id'] ? $data['ca_id'] : 0,
                '{ca_name}' => $data['ca_name'] ? $data['ca_name'] : '',
                '{page}' => $page ? $page : 1,
                '{order}' => $data['order'] ? $data['order'] : 'art_created',
                '{sort}' => $data['sort'] ? $data['sort'] : 'desc',
                '{id}' => $data['art_id'],
                '{year}' => date('y', $data['art_designated_published']),
                '{month}' => date('m', $data['art_designated_published']),
                '{day}' => date('d', $data['art_designated_published']),
                '{hour}' => date('H', $data['art_designated_published']),
                '{p}' => $page ? $page : 1,
                '{m}' => $data['m_id'],
                '{keywords}' => $data['art_title'],
            );
            break;
        case 'app':
            $replace = array(
                '{ac_id}' => $data['ac_id'] ? $data['ac_id'] : 0,
                '{page}' => $page ? $page : 1,
                '{type}' => $data['type'] ? $data['type'] : 0,
                '{order}' => $data['order'] ? $data['order'] : 'a_created',
                '{sort}' => $data['sort'] ? $data['sort'] : 'desc',
                '{id}' => $data['a_id'],
                '{year}' => date('y', $data['a_created']),
                '{month}' => date('m', $data['a_created']),
                '{day}' => date('d', $data['a_created']),
                '{hour}' => date('H', $data['a_created']),
                '{p}' => $page ? $page : 1,
                '{keywords}' => $data['a_title'],
            );
            break;
        case 'notice':
            $replace = array(
                '{page}' => $page ? $page : 1,
                '{order}' => $data['order'] ? $data['order'] : 'no_starttime',
                '{sort}' => $data['sort'] ? $data['sort'] : 'desc',
                '{id}' => $data['no_id'],
                '{year}' => date('y', $data['no_starttime']),
                '{month}' => date('m', $data['no_starttime']),
                '{day}' => date('d', $data['no_starttime']),
                '{hour}' => date('H', $data['no_starttime']),
                '{p}' => $page ? $page : 1,
                '{keywords}' => $data['no_title'],
            );
            break;
        default :
            $replace = array();
            break;
            
    }
    
    // 获取规则
    $rules = getUrlParamRule($controller, $function);
    if (is_array($rules)) {
        $rules = implode('-', $rules);
    }

    $url = '';
    $url .= ($module && strtolower($module) != 'home') ? '/' . strtolower($module) : '';
    $url .= $uuid ? '/' . strtolower($uuid) : '';
    if ((strtolower($controller) == 'index' || !$controller) && (strtolower($function) == 'index' || !$function)) {
        // 首页
        if (strtolower($module) != 'home') {
            return $url . C('HTML_FILE_SUFFIX');
        } else {
            return '/';
        }
    }
    $url .= $controller ? '/' . strtolower($controller) : '/index';
    $url .= $function ? '/' . strtolower($function) : '/index';
    if ($function == 'index' || empty($data)) {
        // 二级首页
        return $url . C('HTML_FILE_SUFFIX');
    }
    
    return $url . '/' . str_replace(array_keys($replace), array_values($replace), $rules) . C('HTML_FILE_SUFFIX');
}

// 获取url地址前，字符串要先拼凑为数组
function getUrlData($key, $val, $data = array()) {
    if ($data && is_array($data)) {
        $data[$key] = $val;
        return $data;
    } else {
        return array($key => $val);
    }
}

// xml 转 数组
function simplest_xml_to_array($xmlstring) {
    return json_decode(json_encode((array) simplexml_load_string($xmlstring)), true);
}

/* 
 * 资源发布字段，将某数字转换成三位的字符串
 * 平台1   区域2   学校4
 */
function published_number_to_string($number) {
    $string = '';
    // 平台
    $string .= ($number & 1) ? '1' : '9';
    // 区域
    $string .= ($number & 2) ? '1' : '9';
    // 学校
    $string .= ($number & 4) ? '1' : '9';

    return $string;
}

/*
 * 获取下载文件的 contenttype
 * $key 文件后缀
 */
function getContentType($key) {

    $content_type = array(
        'ez' => 'application/andrew-inset',
        'hqx' => 'application/mac-binhex40',
        'cpt' => 'application/mac-compactpro',
        'doc' => 'application/msword',
        'docx' => 'application/msword',
        'bin' => 'application/octet-stream',
        'dms' => 'application/octet-stream',
        'lha' => 'application/octet-stream',
        'lzh' => 'application/octet-stream',
        'exe' => 'application/octet-stream',
        'class' => 'application/octet-stream',
        'so' => 'application/octet-stream',
        'dll' => 'application/octet-stream',
        'oda' => 'application/oda',
        'pdf' => 'application/pdf',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',
        'smi' => 'application/smil',
        'smil' => 'application/smil',
        'mif' => 'application/vnd.mif',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',
        'wbxml' => 'application/vnd.wap.wbxml',
        'wmlc' => 'application/vnd.wap.wmlc',
        'wmlsc' => 'application/vnd.wap.wmlscriptc',
        'bcpio' => 'application/x-bcpio',
        'vcd' => 'application/x-cdlink',
        'pgn' => 'application/x-chess-pgn',
        'cpio' => 'application/x-cpio',
        'csh' => 'application/x-csh',
        'dcr' => 'application/x-director',
        'dir' => 'application/x-director',
        'dxr' => 'application/x-director',
        'mp4' => 'octet-stream.mp4',
        'dvi' => 'application/x-dvi',
        'spl' => 'application/x-futuresplash',
        'gtar' => 'application/x-gtar',
        'hdf' => 'application/x-hdf',
        'js' => 'application/x-javascript',
        'skp' => 'application/x-koan',
        'skd' => 'application/x-koan',
        'skt' => 'application/x-koan',
        'skm' => 'application/x-koan',
        'latex' => 'application/x-latex',
        'nc' => 'application/x-netcdf',
        'cdf' => 'application/x-netcdf',
        'sh' => 'application/x-sh',
        'shar' => 'application/x-shar',
        'swf' => 'application/x-shockwave-flash',
        'sit' => 'application/x-stuffit',
        'sv4cpio' => 'application/x-sv4cpio',
        'sv4crc' => 'application/x-sv4crc',
        'tar' => 'application/x-tar',
        'tcl' => 'application/x-tcl',
        'tex' => 'application/x-tex',
        'texinfo' => 'application/x-texinfo',
        'texi' => 'application/x-texinfo',
        't' => 'application/x-troff',
        'tr' => 'application/x-troff',
        'roff' => 'application/x-troff',
        'man' => 'application/x-troff-man',
        'me' => 'application/x-troff-me',
        'ms' => 'application/x-troff-ms',
        'ustar' => 'application/x-ustar',
        'src' => 'application/x-wais-source',
        'xhtml' => 'application/xhtml+xml',
        'xht' => 'application/xhtml+xml',
        'zip' => 'application/zip',
        'au' => 'audio/basic',
        'snd' => 'audio/basic',
        'mid' => 'audio/midi',
        'midi' => 'audio/midi',
        'kar' => 'audio/midi',
        'mpga' => 'audio/mpeg',
        'mp2' => 'audio/mpeg',
        'mp3' => 'audio/mpeg',
        'aif' => 'audio/x-aiff',
        'aiff' => 'audio/x-aiff',
        'aifc' => 'audio/x-aiff',
        'm3u' => 'audio/x-mpegurl',
        'ram' => 'audio/x-pn-realaudio',
        'rm' => 'audio/x-pn-realaudio',
        'rpm' => 'audio/x-pn-realaudio-plugin',
        'ra' => 'audio/x-realaudio',
        'wav' => 'audio/x-wav',
        'pdb' => 'chemical/x-pdb',
        'xyz' => 'chemical/x-xyz',
        'bmp' => 'image/bmp',
        'gif' => 'image/gif',
        'ief' => 'image/ief',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'jpe' => 'image/jpeg',
        'png' => 'image/png',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'djvu' => 'image/vnd.djvu',
        'djv' => 'image/vnd.djvu',
        'wbmp' => 'image/vnd.wap.wbmp',
        'ras' => 'image/x-cmu-raster',
        'pnm' => 'image/x-portable-anymap',
        'pbm' => 'image/x-portable-bitmap',
        'pgm' => 'image/x-portable-graymap',
        'ppm' => 'image/x-portable-pixmap',
        'rgb' => 'image/x-rgb',
        'xbm' => 'image/x-xbitmap',
        'xpm' => 'image/x-xpixmap',
        'xwd' => 'image/x-xwindowdump',
        'igs' => 'model/iges',
        'iges' => 'model/iges',
        'msh' => 'model/mesh',
        'mesh' => 'model/mesh',
        'silo' => 'model/mesh',
        'wrl' => 'model/vrml',
        'vrml' => 'model/vrml',
        'css' => 'text/css',
        'html' => 'text/html',
        'htm' => 'text/html',
        'asc' => 'text/plain',
        'txt' => 'text/plain',
        'rtx' => 'text/richtext',
        'rtf' => 'text/rtf',
        'sgml' => 'text/sgml',
        'sgm' => 'text/sgml',
        'tsv' => 'text/tab-separated-values',
        'wml' => 'text/vnd.wap.wml',
        'wmls' => 'text/vnd.wap.wmlscript',
        'etx' => 'text/x-setext',
        'xsl' => 'text/xml',
        'xml' => 'text/xml',
        'mpeg' => 'video/mpeg',
        'mpg' => 'video/mpeg',
        'mpe' => 'video/mpeg',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',
        'mxu' => 'video/vnd.mpegurl',
        'avi' => 'video/x-msvideo',
        'movie' => 'video/x-sgi-movie',
        'ice' => 'x-conference/x-cooltalk',
    );

    return $content_type[$key];
}
?>