<?php
namespace Api\Controller;
use Think\Controller;
class AppController extends OpenController {

    // 列表允许输出字段
    private $allowFields = array('a_id', 'a_title', 'a_logo', 'a_link', 'a_description', 'a_score_num', 'a_score_count', 'ac_id', 'aty_id', 'id_id', 'a_status', 'a_valided', 'a_online_time', 'a_hits', 'a_created', 'a_downloads');
    private $allowFieldsDetail = array('a_id', 'a_title', 'a_logo', 'a_link', 'a_description', 'a_score_num', 'a_score_count', 'ac_id', 'aty_id', 'id_id', 'a_status', 'a_valided', 'a_online_time', 'a_hits', 'a_created', 'ac_title', 'aty_title', 'a_logo_b', 'a_code_logo', 'apk_plat', 'ipa_plat', 'apk_plat', 'apk_phone', 'ipa_phone', 'computer', 'a_download_points', 'a_version', 'a_author', 'a_language', 'score', 'com_size', 'pic', 'a_downloads');
    private $allowShowFields = array('a_id', 'a_title', 'a_logo', 'a_logo_b', 'a_link', 'a_created', 'score', 'ac_id', 'ac_title', 'aty_id', 'aty_title', 'id_id', 'a_status', 'a_hits', 'a_downloads');
    
    // 列表
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

        if (intval($ac_id)) {
            $_POST['args']['ac_id'] = intval($ac_id);
        }

        if (isset($aty_id)) {
            $_POST['args']['aty_id'] = intval($aty_id) ? intval($aty_id) : 0;
        }
        $_POST['args']['a_status'] = 1; // 开启
        $_POST['args']['a_online_time'] = time(); // 上线
        $_POST['args']['a_valided'] = time(); // 有效期

        $_POST['args']['client_type'] = intval($client_type); // 客户端种类
        
        // 结果不处理
        $config['is_deal_result'] = $is_deal_result ? $is_deal_result : false;
        // api 请求
        $config['is_api'] = true;
        // 排序
        if ($order) {
            $config['order'] = $order;
        }
        // 每页返回数
        if (isset($return_num)) {
            $config['every_page_num'] =  intval($return_num);
        }

        $result = D('App')->lists($_POST['args'], $config);
        foreach ($result['list'] as $key => $value) {
            $result['list'][$key] = $this->dealReturnResult($value);
        }

        $this->returnData($result);
    }

    public function shows() {
        extract($_POST['args']);

        // 校验
        if (!intval($a_id)) {
            $this->returnData($this->errCode[2]);
        }

        $result = D('App')->getById(intval($a_id));
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

        // 字段过滤
        foreach ($result as $key => $val) {
            if (!in_array($key, $config['fields'])) {
                unset($result[$key]);
            }
        }

        $this->returnData($result);
    }

    // 总个数
    public function counts() {
        $config['where']['a_status'] = 1;
        $config['where']['a_online_time'] = array('ELT', time());
        $config['where']['a_valided'] = array('EGT', time());
        $result = D('App')->total($config);

        $this->returnData($result);
    }

    // 应用类型
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
        if ($aty_id) {
            $config['where']['aty_id'] = intval($aty_id);
        }
        if ($order) {
            $config['order'] = strval($order);
        }
        $config['fields'] = $config['fields'] ? $config['fields'] : 'ac_id,ac_title';
        $config['is_api'] = $is_api ? true : false;

        $result = D('AppCategory')->getAll($config);

        $this->returnData($result);
    }

    /* 前台展示列表
     * belong 所属  1 平台
     * aty_id  区域类型
     * type 类型  1 类型  3 应用列表
     * type_num  类型个数  默认 7 个
     * every_num 每种类型下应用个数  默认 12 个
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
        foreach ($type as $type_key => $app_type) {
            $config = array();
            $data = array();
            switch ($app_type) {
                case 1:
                    // 类型
                    $config['where']['ac_status'] = 1;
                    $config['order'] = 'ac_sort ASC';
                    $config['fields'] = 'ac_id,ac_title';
                    $config['every_page_num'] = intval($type_num[$type_key]) ? intval($type_num[$type_key]) : intval($type_num[0]);
                    $config['is_deal_result'] = false;
                    $appCate = D('AppCategory')->lists(array(), $config);
                    $result[$app_type]['type'] = $appCate['list'];
                    break;
                case 3:
                    // 列表
                    $result[3]['type'] = '应用列表';
                    break;
            }
        }

        if (!$every_num) {
            $every_num = array(12);
        } elseif (!is_array($every_num)) {
            $every_num = explode(',', $every_num);
        }

        $appData['a_status'] = 1;
        $appData['a_online_time'] = time();
        $appData['a_valided'] = time();
        $appData['aty_id'] = $aty_id;
        if (isset($is_recommend)) {
            $appData['a_is_recommend'] = $is_recommend;
        }

        // 类型应用
        $appConfig['type'] = intval($belong);
        $appConfig['every_page_num'] = intval($every_num[1]) ? intval($every_num[1]) : intval($every_num[0]);
        $appConfig['is_deal_result'] = false;
        $appConfig['order'] = strval($order) ? strval($order) : 'a_created DESC';
        foreach ($appCate['list'] as $ac_id => $ac_title) {
            $appData['ac_id'] = $ac_id;
            $appList = D('App')->lists($appData, $appConfig);
            foreach ($appList['list'] as $app_key => $app_info) {
                $appList['list'][$app_key] = $this->dealReturnResult($app_info);
            }
            $result[1]['list'][$ac_id] = (array)$appList['list'];
        }

        // 应用列表
        if ($result[3]['type']) {
            $listConfig['type'] = intval($belong);
            $listConfig['every_page_num'] = intval($every_num[3]) ? intval($every_num[3]) : intval($every_num[0]);
            $listConfig['is_deal_result'] = false;
            $listConfig['order'] = strval($order) ? strval($order) : 'a_sort ASC,a_created DESC';
            $aList = D('App')->lists($appData, $listConfig);
            foreach ($aList['list'] as $a_key => $a_info) {
                $aList['list'][$a_key] = $this->dealReturnResult($a_info);
            }
            $result[3]['list'] = (array)$aList['list'];
        }

        $this->returnData($result);
    }

    // 处理返回结果
    private function dealReturnResult($data) {

        if (!$data) {
            return array();
        }

        // 特殊处理
        $data['a_logo'] = D('App')->getFile($data);
        $data['a_logo_b'] = D('App')->getFile($data, 'logo', array('size' => '_b'));
        
        // 评分
        $data['score'] = round($data['a_score_num']/$data['a_score_count']);

        // 过滤处理
        foreach ($data as $key => $val) {
            if (!in_array($key, $this->allowShowFields)) {
                unset($data[$key]);
            }
        }
        
        $computer = D('App')->getFile($data, 'computer');
        if ($computer) {
            $data['com_size'] = byteFormat(filesize('.' . $computer));
        }

        if ($data['ac_id']) {
            $cate = reloadCache('appCategory');
            $data['ac_title'] = $cate[$data['ac_id']];
        }

        if ($data['aty_id']) {
            $type = reloadCache('appType');
            $data['aty_title'] = $type[$data['aty_id']];
        }

        return $data;
    }

    /*
     * 应用某字段累加
     * type 0 默认浏览量
     */
    public function increase() {
        extract($_POST['args']);

        // 校验
        if (!intval($a_id)) {
            $this->returnData($this->errCode[2]);
        }

        $type = intval($type) ? intval($type) : 0;

        if ($type == 1) {
            // res_downloads
            //D('Resource', 'Model')->increase('res_downloads', array('res_id' => intval($res_id)), 1);
        } elseif ($type == 2) {
            // res_comment_count
            //D('Resource', 'Model')->increase('res_comment_count', array('res_id' => intval($res_id)), 1);
        } else {
            // a_hits
            D('App', 'Model')->increase('a_hits', array('a_id' => intval($a_id)), 1);
        }
    }

    // 统计
    public function statistics() {
        extract($_POST['args']);

        $result = D('AppStatistics')->lists($_POST['args']);

        $this->returnData($result);
    }
}
?>
