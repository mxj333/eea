<?php
namespace Api\Controller;
use Think\Controller;
class DirectoryController extends OpenController {

    // 允许输出字段
    private $allowFields = array('d_id', 'd_title', 'd_subject', 'd_version', 'd_school_type', 'd_grade', 'd_semester', 'd_pid', 'd_level', 'd_sort', 'd_status', 're_id', 're_title');
    // 允许添加字段
    private $allowFieldsInsert = array('d_title', 'd_subject', 'd_version', 'd_school_type', 'd_grade', 'd_semester', 'd_pid', 'd_level', 'd_sort', 'd_created', 'd_status', 're_id', 're_title');
    // 允许修改字段
    private $allowFieldsUpdate = array('d_id', 'd_title', 'd_subject', 'd_version', 'd_school_type', 'd_grade', 'd_semester', 'd_pid', 'd_level', 'd_sort', 'd_created', 'd_status', 're_id', 're_title', 'd_updated');
    // 允许修改字段
    private $allowFieldsSync = array('d_id', 'd_subject', 'd_version', 'd_school_type', 'd_grade', 'd_semester', 'target_id');
    // 允许添加字段
    private $allowFieldsInsertAll = array('re_id', 're_title', 'd_creator_id', 'd_creator_table', 'd_version', 'd_school_type', 'd_grade', 'd_semester', 'd_subject', 'd_title', 'd_sort', 'd_status', 'd_created', 'd_pid', 'd_level');

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
        // 是否分页
        $config['is_page'] = isset($is_page) ? $is_page : true;
        $config['is_upper_level'] = isset($is_upper_level) ? $is_upper_level : false;
        $config['is_open_sub'] = isset($is_open_sub) ? $is_open_sub : false;

        $result = D('Directory')->lists($_POST['args'], $config);
        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无数据'));
        }

        $this->returnData($result);
    }

    // 资源信息
    public function shows() {
        extract($_POST['args']);

        // 校验
        if (!intval($d_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 结果不处理
        $config['is_deal_result'] = false;

        $result = D('Directory')->getById(intval($d_id), $config);
        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无'));
        }

        // 返回字段
        if ($fields) {
            // 有定义返回字段
            $returnFields = explode(',', $fields);
            $config['fields'] = array_intersect($this->allowFields, $returnFields);
        }
        if (!$config['fields']){
            $config['fields'] = $this->allowFields;
        }

        // 过滤返回结果
        foreach ($result as $key => $val) {
            if (!in_array($key, $config['fields'])) {
                unset($result[$key]);
            }
        }

        $this->returnData($result);
    }

    // 检查目录信息
    public function check() {
        extract($_POST['args']);

        $config = array();
        if ($d_school_type) {
            $config['where']['d_school_type'] = $d_school_type;
        }
        if ($d_subject) {
            $config['where']['d_subject'] = $d_subject;
        }
        if ($d_version) {
            $config['where']['d_version'] = $d_version;
        }
        if ($d_grade) {
            $config['where']['d_grade'] = $d_grade;
        }
        if ($d_semester) {
            $config['where']['d_semester'] = $d_semester;
        }
        if ($re_id) {
            $config['where']['re_id'] = strval($re_id);
        }
        if ($d_title) {
            $config['where']['d_title'] = $d_title;
        }
        if ($fields) {
            $config['fields'] = $fields;
        }
        $result = D('Directory', 'Model')->getOne($config);

        $this->returnData($result);
    }

    // 添加
    public function add() {
        extract($_POST['args']);
        
        // 确定允许操作的字段
        foreach ($_POST['args'] as $key => $val) {
            if (!in_array($key, $this->allowFieldsInsert)) {
                unset($_POST['args'][$key]);
            }
        }
        
        // 默认值
        $_POST['args']['d_pid'] = intval($d_pid);
        $_POST['args']['d_level'] = intval($d_level);
        $_POST['args']['d_sort'] = intval($d_sort);
        $_POST['args']['d_creator_id'] = intval($this->authInfo['me_id']);

        $result = D('Directory')->insert($_POST['args']);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'res_value' => $result, 'info' => '新增成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('Directory')->getError()));
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
        $result = D('Directory')->insertAll($insertData);
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
        if (!intval($d_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 确定允许操作的字段
        foreach ($_POST['args'] as $key => $val) {
            if (!in_array($key, $this->allowFieldsUpdate)) {
                unset($_POST['args'][$key]);
            }
        }

        // 默认值
        $_POST['args']['d_pid'] = intval($d_pid);
        $_POST['args']['d_level'] = intval($d_level);
        $_POST['args']['d_sort'] = intval($d_sort);
        
        $result = D('Directory')->insert($_POST['args']);
        if ($result !== false){
            $this->returnData(array('status' => 1, 'res_value' => $result, 'info' => '编辑成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('Directory')->getError()));
        }
    }

    // 删除
    public function del() {
        extract($_POST['args']);

        // 校验
        if (!strval($d_id)) {
            $this->returnData($this->errCode[2]);
        }

        $result = D('Directory')->delete(strval($d_id));

        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '删除成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '删除失败'));
        }
    }

    // 目录调整
    public function sync() {
        extract($_POST['args']);

        // 校验
        if (!intval($d_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 确定允许操作的字段
        foreach ($_POST['args'] as $key => $val) {
            if (!in_array($key, $this->allowFieldsSync)) {
                unset($_POST['args'][$key]);
            }
        }

        $result = D('Directory')->adjustment($_POST['args']);

        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '操作成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('Directory')->getError()));
        }
    }

    // 获取目录下知识点
    public function getRelation() {
        extract($_POST['args']);

        // 校验
        if (!intval($d_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 关联知识点
        $result = D('DirectoryKnowledgePointsRelation')->getKnowledgePoints(intval($d_id));

        $this->returnData($result);
    }

    // 目录下新增知识点
    public function addRelation() {
        extract($_POST['args']);

        // 校验
        if (!intval($d_id)) {
            $this->returnData($this->errCode[2]);
        }

        $addData = array(
            'd_id' => intval($d_id),
            'knowledgePoints' => $knowledgePoints
        );

        // 关联知识点
        $result = D('DirectoryKnowledgePointsRelation')->insert($addData);

        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '操作成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '操作失败'));
        }
    }

    /* 
     * 创建  要下载的文件
     * type  excel(默认)  word
     */
    public function download() {
        extract($_POST['args']);
        
        $type = in_array(strtolower(strval($type)), array('excel', 'word')) ? strtolower(strval($type)) : 'excel';

        // 文件名称定义
        $_POST['args']['fileName'] = 'directory_region' . str_replace('-', '', $re_id);

        if ($type == 'word') {
            // 生成 word 文件
            $result = D('Directory')->createWord($_POST['args']);
        } else {
            // 生成 excel 文件
            $result = D('Directory')->createExcel($_POST['args']);
        }

        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '操作成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => D('Directory')->getError()));
        }
    }

    public function import() {
        extract($_POST['args']);

        set_time_limit(300);

        // 默认值
        $config['re_id'] = strval($re_id);
        $config['re_title'] = strval($re_title);
        $config['d_creator_id'] = intval($this->authInfo['me_id']);
        $config['d_creator_table'] = 'Member';

        // 返回入库数据
        $result = D('DirectoryImport')->import($_FILES, $config);
        if ($result === false) {
            $this->returnData(array('status' => 0, 'info' => D('DirectoryImport')->getError()));
        } else {
            $this->returnData(array('status' => 1, 'info' => '导入成功'));
        }
    }
}
?>
