<?php
namespace Api\Controller;
use Think\Controller;
class ResourceController extends OpenController {

    // 资源表允许输出字段
    private $allowFields = array('res_id', 'res_title', 'rc_id', 're_id', 're_title', 'res_is_recommend', 'res_is_excellent', 'res_is_pushed', 'res_is_published', 'res_published_time', 'res_published_time_area', 'res_published_time_school', 'res_publisher_id', 'res_publisher_id_area', 'res_publisher_id_school', 'res_eliminated_id', 'res_eliminated_id_area', 'res_eliminated_id_school', 'res_eliminated_time', 'res_eliminated_time_area', 'res_eliminated_time_school', 's_id', 'c_id', 'res_is_original', 'res_author', 'res_language', 'res_metadata_language', 'res_audience_learner', 'res_audience_educational_type', 'res_summary', 'res_published_name', 'res_published_company', 'res_other_author', 'res_permissions', 'res_download_points', 'res_issused', 'res_metadata_scheme', 'res_short_title', 'res_valid', 'res_avaliable', 'res_score_num', 'res_score_count', 'res_downloads', 'res_hits', 'res_is_pass', 'rsu_id', 'res_url', 'res_is_eliminated', 'res_created', 'res_updated', 'rf_id', 'res_is_sys', 'res_version', 'res_school_type', 'res_grade', 'res_semester', 'res_subject', 'rt_id', 'res_transform_status', 'res_comment_count');
    // 资源表允许添加字段
    private $allowFieldsInsert = array('res_title', 'rc_id', 're_id', 're_title', 'res_is_recommend', 'res_is_excellent', 'res_is_pushed', 'res_is_published', 's_id', 'c_id', 'res_is_original', 'res_author', 'res_language', 'res_metadata_language', 'res_audience_learner', 'res_audience_educational_type', 'res_summary', 'res_published_name', 'res_published_company', 'res_other_author', 'res_permissions', 'res_download_points', 'res_issused', 'res_metadata_scheme', 'res_short_title', 'res_valid', 'res_avaliable', 'rsu_id', 'res_url', 'res_created', 'rf_id', 'res_is_sys', 'directory', 'keywords', 'knowledge', 'res_version', 'res_school_type', 'res_grade', 'res_semester', 'res_subject', 'rt_id', 'res_transform_status', 'res_comment_count');
    // 资源表允许修改字段
    private $allowFieldsUpdate = array('res_id', 'res_title', 'rc_id', 're_id', 're_title', 'res_is_recommend', 'res_is_excellent', 'res_is_pushed', 'res_is_published', 's_id', 'c_id', 'res_is_original', 'res_author', 'res_language', 'res_metadata_language', 'res_audience_learner', 'res_audience_educational_type', 'res_summary', 'res_published_name', 'res_published_company', 'res_other_author', 'res_permissions', 'res_download_points', 'res_issused', 'res_metadata_scheme', 'res_short_title', 'res_valid', 'res_avaliable', 'res_score_num', 'res_score_count', 'res_downloads', 'res_hits', 'res_is_pass', 'rsu_id', 'res_url', 'res_is_eliminated', 'res_updated', 'res_is_sys', 'directory', 'keywords', 'knowledge', 'res_version', 'res_school_type', 'res_grade', 'res_semester', 'res_subject', 'res_comment_count');
    // 资源文件表允许输出字段
    private $allowFieldsDetail = array('res_id', 'res_title', 'rc_id', 're_id', 're_title', 'res_is_recommend', 'res_is_excellent', 'res_is_pushed', 'res_is_published', 's_id', 'c_id', 'res_is_original', 'res_author', 'res_language', 'res_metadata_language', 'res_audience_learner', 'res_audience_educational_type', 'res_summary', 'res_published_name', 'res_published_company', 'res_other_author', 'res_permissions', 'res_download_points', 'res_issused', 'res_metadata_scheme', 'res_short_title', 'res_valid', 'res_avaliable', 'res_score_num', 'res_score_count', 'res_downloads', 'res_hits', 'res_is_pass', 'rsu_id', 'res_url', 'res_is_eliminated', 'res_created', 'res_updated', 'rf_id', 'rf_savename', 'rf_ext', 'rf_size', 'rt_id', 'rf_created', 'rf_transform_status', 'res_is_sys', 'res_version', 'res_school_type', 'res_grade', 'res_semester', 'res_subject', 'directory', 'keywords', 'knowledge', 'res_image', 'res_path', 'res_original_path', 'rf_savename', 'rf_ext', 'rf_size', 'rt_id', 'rf_created', 'rf_transform_status', 'res_transform_status', 'res_comment_count', 'creator_nickname');
    // 批量入库允许字段
    private $allowFieldsInsertAll = array('res_title', 'rc_id', 're_id', 're_title', 'res_is_recommend', 'res_recommend_time', 'res_is_excellent', 'res_excellent_date', 'res_is_pushed', 'res_pushed_created', 'res_is_published', 'res_published_time', 'res_is_original', 'res_author', 'res_language', 'res_metadata_language', 'res_audience_learner', 'res_audience_educational_type', 'res_summary', 'res_published_name', 'res_published_company', 'res_other_author', 'res_permissions', 'res_download_points', 'res_issused', 'res_metadata_scheme', 'res_short_title', 'res_valid', 'res_avaliable', 'res_is_pass', 'rsu_id', 'res_url', 'res_is_eliminated', 'res_created', 'rf_id', 'res_creator_id', 'res_creator_table', 's_id', 'c_id', 'res_is_sys', 'res_version', 'res_school_type', 'res_grade', 'res_semester', 'res_subject', 'rt_id', 'res_transform_status', 'res_comment_count');
    // 资源文件允许输出字段
    private $allowFieldsFile = array('rf_id', 'rf_savename', 'rf_ext', 'rf_size', 'rt_id', 'rf_created', 'rf_transform_status');
    // 定义显示字段
    private $allowShowFields = array('res_id', 'res_title', 're_id', 're_title', 's_id', 'c_id', 'res_is_sys', 'res_short_title', 'res_url', 'res_created', 'rf_id', 'res_score_num', 'res_score_count', 'res_hits', 'res_downloads', 'res_school_type', 'res_subject', 'res_version', 'res_grade', 'res_summary', 'rt_id', 'res_transform_status', 'res_comment_count', 'rc_id', 'rc_title', 'res_download_points');

    // 资源列表
    public function lists() {
        extract($_POST['args']);

        // 返回字段
        if ($fields) {
            // 有定义返回字段
            $returnFields = explode(',', $fields);
            $config['fields'] = array_intersect($this->allowFields, $returnFields);
        }
        if (!$config['fields']){
            $config['fields'] = $this->allowFields;
        }
        
        // 页码
        if ($p) {
            $config['p'] = intval($p) ? intval($p) : 1;
        }
        // logic 列表类 结果不处理
        $config['is_deal_result'] = $is_deal_result ? $is_deal_result : false;
        // api 请求
        $config['is_api'] = true;
        // 每页返回数
        if (isset($return_num)) {
            $config['every_page_num'] =  intval($return_num);
        }

        // 所属平台
        $config['type'] = in_array(intval($belong), array(1, 2, 3)) ? intval($belong) : 1;

        // 取列表种类
        switch (intval($_POST['args']['type'])) {
            case 2:
                // 资源审核列表
                $config['where']['SUBSTRING(res_is_published, '.$config['type'].', 1)'] = 1;
                $config['where']['SUBSTRING(res_is_pass, '.$config['type'].', 1)'] = 0;
                break;
            case 3:
                // 资源回收站列表
                $config['where']['SUBSTRING(res_is_deleted, '.$config['type'].', 1)'] = 9;
                $config['where']['SUBSTRING(res_is_eliminated, '.$config['type'].', 1)'] = 1;
                break;
            case 4:
                // 前台展示列表
                $config['where']['SUBSTRING(res_is_deleted, '.$config['type'].', 1)'] = 9;
                $config['where']['SUBSTRING(res_is_eliminated, '.$config['type'].', 1)'] = 9;
                $config['where']['SUBSTRING(res_is_published, '.$config['type'].', 1)'] = 1;
                $config['where']['SUBSTRING(res_is_pass, '.$config['type'].', 1)'] = 1;
                break;
            default :
                // 默认资源列表  审核过的
                //$config['where']['SUBSTRING(res_is_published, '.$config['type'].', 1)'] = array(9, '_logic' => 'OR');
                //$config['where']['SUBSTRING(res_is_pass, '.$config['type'].', 1)'] = array('NEQ', 0);
        }

        $result = D('Resource')->lists($_POST['args'], $config);
        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无数据'));
        }

        $this->returnData($result);
    }

    // 总个数
    public function counts() {
        extract($_POST['args']);

        $config = array();
        $config['where']['res_is_deleted'] = 9;
        $config['where']['res_is_eliminated'] = 9;
        $config['where']['res_valid'] = array('ELT', time());
        $config['where']['res_avaliable'] = array('EGT', time());

        if (strval($re_id)) {
            $config['where']['re_id'] = array('LIKE', '%' . strval($re_id) . '%');
        }

        if (intval($s_id)) {
            $config['where']['s_id'] = intval($s_id);
        }

        if (intval($c_id)) {
            $config['where']['c_id'] = intval($c_id);
        }

        if (intval($res_is_published)) {
            $config['where']['res_is_published'] = intval($res_is_published);
        }

        if (intval($res_is_pass)) {
            $config['where']['res_is_pass'] = intval($res_is_pass);
        }

        $result = D('Resource')->total($config);

        $this->returnData($result);
    }

    // 资源信息
    public function shows() {
        extract($_POST['args']);

        // 校验
        if (!intval($res_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 结果不处理
        $config['is_deal_result'] = $is_deal_result ? $is_deal_result : false;
        // 所属平台
        $config['type'] = in_array(intval($belong), array(1, 2, 3)) ? intval($belong) : 1;

        $result = D('Resource')->getById(intval($res_id), $config);
        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无'));
        }

        // 返回字段
        if ($fields) {
            // 有定义返回字段
            $returnFields = explode(',', $fields);
            $config['fields'] = array_intersect($this->allowFieldsDetail, $returnFields);
        }
        if (!$config['fields']){
            $config['fields'] = $this->allowFieldsDetail;
        }

        // 过滤返回结果
        foreach ($result as $key => $val) {
            if (!in_array($key, $config['fields'])) {
                unset($result[$key]);
            }
        }

        $this->returnData($result);
    }

    // 淘汰
    public function del() {
        extract($_POST['args']);

        // 校验
        if (!strval($res_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 所属平台
        $belong = in_array(intval($belong), array(1, 2, 3)) ? intval($belong) : 1;

        $config['where']['res_id'] = array('IN', strval($res_id));
        $config['res_eliminated_id'] = intval($this->authInfo['me_id']);
        $result = D('Resource')->elimination($config, $belong);

        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '删除成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '删除失败'));
        }
    }

    // 添加
    public function add() {
        extract($_POST['args']);
        
        // 所属平台
        $belong = in_array(intval($belong), array(1, 2, 3)) ? intval($belong) : 1;
        // 是否自动审核通
        $pass_auto = $pass_auto ? true : false;

        // 确定允许操作的字段
        foreach ($_POST['args'] as $key => $val) {
            if (!in_array($key, $this->allowFieldsInsert)) {
                unset($_POST['args'][$key]);
            }
        }

        // 默认值
        $_POST['args']['res_metadata_language'] = strval($_POST['args']['res_metadata_language']) ? strval($_POST['args']['res_metadata_language']) : '汉语';
        $_POST['args']['res_download_points'] = intval($_POST['args']['res_download_points']) ? intval($_POST['args']['res_download_points']) : 0;
        $_POST['args']['res_creator_id'] = intval($this->authInfo['me_id']);
        if ($_POST['args']['res_is_published'] == 1) {
            // 发布
            $_POST['args']['res_publisher_id'] = intval($this->authInfo['me_id']);
        }
        if ($pass_auto) {
            // 发布
            $_POST['args']['res_pass_id'] = intval($this->authInfo['me_id']);
        }
        
        $result = D('Resource')->insert($_POST['args'], $belong, $pass_auto);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'res_value' => $result, 'info' => '新增成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('Resource')->getError()));
        }
    }

    public function addAll() {
        extract($_POST['args']);

        $fields = $_POST['args']['fields'];
        // 过滤非法字段
        foreach ($fields as $key => $val) {
            if (!in_array($val, $this->allowFieldsInsertAll)) {
                unset($fields[$key]);
            }
        }

        $insertData = array('fields' => $fields, 'values' => $_POST['args']['values']);
        $result = D('Resource')->insertAll($insertData);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'res_value' => $result, 'info' => '新增成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '新增失败'));
        }
    }

    // 修改
    public function edit() {
        extract($_POST['args']);
        
        // 校验
        if (!intval($res_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 所属平台
        $belong = in_array(intval($belong), array(1, 2, 3)) ? intval($belong) : 1;
        // 是否自动审核通
        $pass_auto = $pass_auto ? true : false;

        // 确定允许操作的字段
        foreach ($_POST['args'] as $key => $val) {
            if (!in_array($key, $this->allowFieldsUpdate)) {
                unset($_POST['args'][$key]);
            }
        }

        // 默认值
        $_POST['args']['res_metadata_language'] = $_POST['args']['res_metadata_language'] ? $_POST['args']['res_metadata_language'] : '汉语';
        $_POST['args']['res_download_points'] = intval($_POST['args']['res_download_points']) ? intval($_POST['args']['res_download_points']) : 0;
        if ($_POST['args']['res_is_published'] == 1) {
            // 发布
            $_POST['args']['res_publisher_id'] = intval($this->authInfo['me_id']);
        }
        if ($pass_auto) {
            // 发布
            $_POST['args']['res_pass_id'] = intval($this->authInfo['me_id']);
        }
        
        $result = D('Resource')->insert($_POST['args'], $belong, $pass_auto);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'res_value' => $result, 'info' => '编辑成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('Resource')->getError()));
        }
    }

    // 文件上传
    public function upload() {
        extract($_POST['args']);

        $result = D('ResourceFile')->upload($_FILES, array('fileName' => $name, 'chunk' => $chunk, 'chunks' => $chunks));

        $this->returnData($result);
    }

    // 资源文件信息
    public function getFile() {
        extract($_POST['args']);

        // 校验
        if (!intval($rf_id)) {
            $this->returnData($this->errCode[2]);
        }

        $result = D('ResourceFile')->getById(intval($rf_id));
        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无'));
        }

        // 返回字段
        if ($fields) {
            // 有定义返回字段
            $returnFields = explode(',', $fields);
            $config['fields'] = array_intersect($this->allowFieldsFile, $returnFields);
        }
        if (!$config['fields']){
            $config['fields'] = $this->allowFieldsFile;
        }
        
        // 过滤返回结果
        foreach ($result as $key => $val) {
            if (!in_array($key, $config['fields'])) {
                unset($result[$key]);
            }
        }

        // 图片路径
        $result['res_image'] = D('ResourceFile')->getResourceImage($result);
        // 资源路径
        $result['res_path'] = D('ResourceFile')->getResourceFullPath($result);

        $this->returnData($result);
    }

    // 审核
    public function review() {
        extract($_POST['args']);

        // 校验
        if (!strval($res_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 所属平台
        $belong = in_array(intval($belong), array(1, 2, 3)) ? intval($belong) : 1;

        $result = D('ResourceReview')->review(strval($res_id), intval($res_is_pass), intval($this->authInfo['me_id']), $belong);

        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '操作成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '操作失败'));
        }
    }

    public function category() {
        extract($_POST['args']);

        if ($fields && is_array($fields)) {
            $config['fields'] = implode(',', $fields);
        } elseif ($fields) {
            $config['fields'] = $fields;
        }
        if (intval($return_num)) {
            $config['limit'] = intval($return_num);
        }
        $config['where']['rc_status'] = 1;
        $config['order'] = 'rc_status ASC';
        $config['fields'] = $config['fields'] ? $config['fields'] : 'rc_id,rc_title';
        $config['is_api'] = $is_api ? true : false;

        $result = D('ResourceCategory')->getAll($config);

        $this->returnData($result);
    }

    public function tags() {
        extract($_POST['args']);

        $data['rta_pid'] = intval($rta_pid);
        $config['fields'] = strval($fields) ? strval($fields) : 'rta_id,rta_title';
        $config['p'] = intval($p) ? intval($p) : 1;
        $config['is_deal_result'] = false;
        $result = D('ResourceTag')->lists($data, $config);
        $this->returnData($result);
    }

    public function getTagInfo() {
        extract($_POST['args']);

        $config['fields'] = strval($fields) ? strval($fields) : 'rta_pid';
        $config['where']['rta_id'] = $rta_id;
        $result = D('ResourceTag')->getOne($config);

        $this->returnData($result);
    }

    public function knowledge() {
        extract($_POST['args']);

        $data['kp_pid'] = intval($kp_pid);
        $data['kp_subject'] = intval($kp_subject);
        $config['fields'] = strval($fields) ? strval($fields) : 'kp_id,kp_title';
        $config['p'] = intval($p) ? intval($p) : 1;
        $config['is_deal_result'] = false;
        $result = D('KnowledgePoints')->lists($data, $config);
        $this->returnData($result);
    }

    public function getKnowledgeInfo() {
        extract($_POST['args']);

        $config['fields'] = strval($fields) ? strval($fields) : 'kp_pid';
        $config['where']['kp_id'] = $kp_id;
        $result = D('KnowledgePoints')->getOne($config);

        $this->returnData($result);
    }

    public function directory() {
        extract($_POST['args']);

        $data['d_version'] = intval($d_version);
        $data['d_school_type'] = intval($d_school_type);
        $data['d_grade'] = intval($d_grade);
        $data['d_semester'] = intval($d_semester);
        $data['d_subject'] = intval($d_subject);
        if (intval($d_pid)) {
            $data['d_pid'] = intval($d_pid);
        }
        if (intval($d_level)) {
            $data['d_level'] = intval($d_level);
        }
        $config['fields'] = strval($fields) ? strval($fields) : 'd_id,d_title';
        $config['p'] = intval($p) ? intval($p) : 1;
        $config['is_deal_result'] = false;
        $result = D('Directory')->lists($data, $config);
        
        $this->returnData($result);
    }

    public function getDirectoryInfo() {
        extract($_POST['args']);

        $config['fields'] = strval($fields) ? strval($fields) : 'd_pid';
        $config['where']['d_id'] = $d_id;
        $result = D('Directory')->getOne($config);

        $this->returnData($result);
    }

    // 资源导入
    public function import() {
        extract($_POST['args']);

        set_time_limit(500);

        // 默认值
        $config['res_creator_id'] = intval($this->authInfo['me_id']);
        $config['res_creator_table'] = 'Member';
        // 所属平台
        $config['type'] = in_array(intval($belong), array(1, 2, 3)) ? intval($belong) : 1;

        // 返回入库数据
        $saveData = D('ResourceImport')->import($_FILES, $config);
        if ($saveData === false) {
            $this->returnData(array('status' => 0, 'info' => D('ResourceImport')->getError()));
        }

        // 入库
        if ($saveData) {
            $insert_id = D('Resource')->insertAll($saveData);
            if (!$insert_id) {
                $this->returnData(array('status' => 0, 'info' => '导入失败'));
            }
        }

        $this->returnData(array('status' => 1, 'info' => '导入成功'));
    }

    /* 前台展示列表
     * belong 所属  1 平台  2 区域  3学校
     * type 类型  1 使用类型  2 学科  3 推荐资源  4 学制  5 极限值
     * type_ids  指定具体类型  数组 下标为类型 值为对应类型的id
     * type_num  类型个数  默认 7 个
     * fields  字段名称(5极限值用)
     * every_num 每种类型下资源个数  默认 12 个
     * order 排序
     * size 图尺寸  _m  -b
     */ 
    public function getShows() {
        extract($_POST['args']);

        $result = array();

        // 哪种类型的
        $type = $type ? $type : array(1);
        if (!is_array($type)) {
            $type = explode(',', $type);
        }

        // 类型数量
        if (!$type_num) {
            $type_num = array(7);
        } elseif (!is_array($type_num)) {
            $type_num = explode(',', $type_num);
        }

        // 指定具体类型

        foreach ($type as $type_key => $res_type) {
            $config = array();
            $data = array();
            switch ($res_type) {
                case 1:
                    $data['rc_status'] = 1;
                    $config['order'] = 'rc_status ASC';
                    $config['fields'] = 'rc_id,rc_title';
                    $config['every_page_num'] = intval($type_num[$type_key]) ? intval($type_num[$type_key]) : intval($type_num[0]);
                    if ($type_ids[$type_key]) {
                        $config['where']['rc_id'] = array('IN', $type_ids[$type_key]);
                    }
                    $config['is_deal_result'] = false;
                    $resCate = D('ResourceCategory')->lists($data, $config);
                    $result[$res_type]['type'] = $resCate['list'];
                    break;
                case 2:
                    $data['t_status'] = 1;
                    $data['t_type'] = 5;
                    $config['fields'] = 't_id,t_title';
                    $config['order'] = 't_sort ASC';
                    $config['every_page_num'] = intval($type_num[$type_key]) ? intval($type_num[$type_key]) : intval($type_num[0]);
                    if ($type_ids[$type_key]) {
                        $config['where']['t_id'] = array('IN', $type_ids[$type_key]);
                    }
                    $config['is_deal_result'] = false;
                    $subj = D('Tag')->lists($data, $config);
                    $result[$res_type]['type'] = $subj['list'];
                    break;
                case 3:
                    $result[$res_type]['type'] = '总资源列表';
                    break;
                case 4:
                    $data['t_status'] = 1;
                    $data['t_type'] = 4;
                    $config['fields'] = 't_id,t_title';
                    $config['order'] = 't_sort ASC';
                    $config['every_page_num'] = intval($type_num[$type_key]) ? intval($type_num[$type_key]) : intval($type_num[0]);
                    if ($type_ids[$type_key]) {
                        $config['where']['t_id'] = array('IN', $type_ids[$type_key]);
                    }
                    $config['is_deal_result'] = false;
                    $school_type = D('Tag')->lists($data, $config);
                    $result[$res_type]['type'] = $school_type['list'];
                    break;
                case 5:
                    // 极限值
                    $result[$res_type]['type'] = '极限值';
                    break;
            }
        }

        if (!$every_num) {
            $every_num = array(12);
        } elseif (!is_array($every_num)) {
            $every_num = explode(',', $every_num);
        }
        
        if (!$order) {
            $order = array('res_created DESC');
        } elseif (!is_array($order)) {
            $order = explode(',', $order);
        }

        if (!$size) {
            $size = array('_m');
        } elseif (!is_array($size)) {
            $size = explode(',', $size);
        }

        // 资源获取公共条件
        $default = array(
            'res_is_published' => 1,
            'res_is_pass' => 1,
            'res_valid' => time(),
            'res_avaliable' => time(),
            're_id' => strval($re_id),
            's_id' => intval($s_id),
        );

        if ($recommend) {
            $default['res_is_recommend'] = $recommend;
        }
        if ($school_type) {
            $default['res_school_type'] = $school_type;
        }
        if ($subject) {
            $default['res_subject'] = $subject;
        }
        if ($grade) {
            $default['res_grade'] = $grade;
        }
        if ($category) {
            $default['rc_id'] = $category;
        }
        if ($rt_id) {
            $default['rt_id'] = $rt_id;
        }
        if ($starttime) {
            $default['starttime'] = $starttime;
        }
        if ($endtime) {
            $default['endtime'] = $endtime;
        }
        if ($keywords) {
            $default['keywords'] = $keywords;
        }
        if ($d_id) {
            $default['d_id'] = $d_id;
        }
        if ($kp_id) {
            $default['kp_id'] = $kp_id;
        }

        // 使用类型
        if ($resCate['list']) {
            $resConfig['type'] = intval($belong);
            $resConfig['every_page_num'] = intval($every_num[1]) ? intval($every_num[1]) : intval($every_num[0]);
            $resConfig['is_deal_result'] = false;
            $resConfig['order'] = $order[1] ? $order[1] : $order[0];
            $image_size = $size[1] ? $size[1] : $size[0];
            $resData = array_merge($default, (array)$resData);
            foreach ($resCate['list'] as $rc_id => $ca_title) {
                $resData['rc_id'] = $rc_id;
                $resourceList = D('Resource')->lists($resData, $resConfig);
                foreach ($resourceList['list'] as $res_key => $res_info) {
                    $resourceList['list'][$res_key] = $this->dealReturnResult($res_info, $image_size);
                }
                $result[1]['list'][$rc_id] = (array)$resourceList['list'];
            }
        }

        // 学科
        if ($subj['list']) {
            $directoryConfig['type'] = intval($belong);
            $directoryConfig['every_page_num'] = intval($every_num[2]) ? intval($every_num[2]) : intval($every_num[0]);
            $directoryConfig['is_deal_result'] = false;
            $directoryConfig['order'] = $order[2] ? $order[2] : $order[0];
            $image_size = $size[2] ? $size[2] : $size[0];
            $dirData = array_merge($default, (array)$dirData);
            foreach ($subj['list'] as $sub_id => $sub_title) {
                $dirData['res_subject'] = $sub_id;
                $sub_res = D('Resource')->lists($dirData, $directoryConfig);
                foreach ($sub_res['list'] as $sub_key => $sub_info) {
                    $sub_res['list'][$sub_key] = $this->dealReturnResult($sub_info, $image_size);
                }
                $result[2]['list'][$sub_id] = (array)$sub_res['list'];
            }
        }

        // 总资源列
        if ($result[3]['type']) {
            $allConfig['type'] = intval($belong);
            $allConfig['every_page_num'] = intval($every_num[3]) ? intval($every_num[3]) : intval($every_num[0]);
            $allConfig['is_page'] = $is_page ? true : false;
            $allConfig['p'] = intval($page) ? intval($page) : 1;
            $allConfig['is_deal_result'] = false;
            $allConfig['order'] = $order[3] ? $order[3] : $order[0];
            $image_size = $size[3] ? $size[3] : $size[0];
            $allData = array_merge($default, (array)$allData);
            $allList = D('Resource')->lists($allData, $allConfig);
            foreach ($allList['list'] as $all_key => $all_info) {
                $allList['list'][$all_key] = $this->dealReturnResult($all_info, $image_size);
            }
            if ($allConfig['is_page']) {
                $result[3]['page'] = $allList['page'];
            }
            $result[3]['list'] = (array)$allList['list'];
        }

        // 学制
        if ($school_type['list']) {
            $stConfig['type'] = intval($belong);
            $stConfig['every_page_num'] = intval($every_num[4]) ? intval($every_num[4]) : intval($every_num[0]);
            $stConfig['is_deal_result'] = false;
            $stConfig['order'] = $order[4] ? $order[4] : $order[0];
            $image_size = $size[4] ? $size[4] : $size[0];
            $stData = array_merge($default, (array)$stData);
            foreach ($school_type['list'] as $st_id => $st_title) {
                $stData['res_school_type'] = $st_id;
                $st_res = D('Resource')->lists($stData, $stConfig);
                foreach ($st_res['list'] as $st_key => $st_info) {
                    $st_res['list'][$st_key] = $this->dealReturnResult($st_info, $image_size);
                }
                $result[4]['list'][$st_id] = (array)$st_res['list'];
            }
        }

        // 极限值
        if ($result[5]['type']) {
            $limConfig['type'] = intval($belong);
            $limConfig['every_page_num'] = intval($every_num[5]) ? intval($every_num[5]) : intval($every_num[0]);
            $limConfig['is_deal_result'] = false;
            $limConfig['is_api'] = true;
            $limConfig['order'] = $order[5] ? $order[5] : $order[0];
            $fields = $fields ? $fields : 'res_created';
            $limConfig['fields'][] = $fields;
            $limConfig['fields']['max(' . $fields . ')'] = 'max';
            $limConfig['fields']['min(' . $fields . ')'] = 'min';
            $limData = array_merge($default, (array)$limData);
            $lim_res = D('Resource')->lists($limData, $limConfig);
            $result[5]['list'] = (array)$lim_res['list'][0];
        }

        $this->returnData($result);
    }

    // 处理返回结果
    private function dealReturnResult($data, $image_size) {

        if (!$data) {
            return array();
        }

        // 过滤处理
        foreach ($data as $key => $val) {
            if (!in_array($key, $this->allowShowFields)) {
                unset($data[$key]);
            }
        }
        
        // 特殊处理
        $data['score'] = round($data['res_score_num']/$data['res_score_count']);
        // 版本、学科、学制
        $tag = reloadCache('tag');
        $data['school_type'] = strval($tag[4][$data['res_school_type']]);
        $data['subject'] = strval($tag[5][$data['res_subject']]);
        $data['version'] = strval($tag[6][$data['res_version']]);
        $data['grade'] = strval($tag[7][$data['res_grade']]);
        // 类型
        $resCate = reloadCache('resourceCategory');
        $data['rc_title'] = strval($resCate[$data['rc_id']]);
        if ($data['rf_id']) {
            $fileConfig['where']['rf_id'] = $data['rf_id'];
            $file_info = D('ResourceFile')->getOne($fileConfig);
            $file_image = D('ResourceFile')->getResourceImage($file_info, array('size' => $image_size));
            $file_path = D('ResourceFile')->getResourceFullPath($file_info);
            $data['rt_id'] = $file_info['rt_id'];
            $data['res_image'] = $file_image;
            $data['res_path'] = $file_path;
        }

        return $data;
    }

    /*
     * 资源某字段累加
     * type 1 下载量  2 评论数  0 默认浏览量
     */
    public function increase() {
        extract($_POST['args']);

        // 校验
        if (!intval($res_id)) {
            $this->returnData($this->errCode[2]);
        }

        $type = intval($type) ? intval($type) : 0;

        if ($type == 1) {
            // res_downloads
            D('Resource', 'Model')->increase('res_downloads', array('res_id' => intval($res_id)), 1);
        } elseif ($type == 2) {
            // res_comment_count
            D('Resource', 'Model')->increase('res_comment_count', array('res_id' => intval($res_id)), 1);
        } else {
            // res_hits
            D('Resource', 'Model')->increase('res_hits', array('res_id' => intval($res_id)), 1);
        }
    }

    /*
     * 评论
     */
    public function comments() {
        extract($_POST['args']);

        // 校验
        if (!intval($res_id) || !intval($auth_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 资源评论

        // 增加评论次数
        D('Resource', 'Model')->increase('res_comment_count', array('res_id' => intval($res_id)), 1);
    }

    // 统计
    public function statistics() {
        extract($_POST['args']);

        $result = D('ResourceStatistics')->lists($_POST['args']);

        $this->returnData($result);
    }
}
?>
