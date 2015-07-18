<?php
namespace Api\Controller;
use Think\Controller;
class ArticleController extends OpenController {

    // 资讯列表允许输出字段
    private $allowFields = array('art_id', 'art_title', 'art_short_title', 'art_keywords', 'art_content', 'm_id', 'ca_id', 'art_summary', 'art_hits', 'art_comment_count', 'art_sort', 'art_cover_path', 'art_cover_name', 'art_cover_ext', 'art_is_allow_comment', 'art_position', 'art_created', 'art_updated', 'art_published', 'art_designated_published', 'art_status');
    // 资讯表允许添加字段
    private $allowFieldsInsert = array('art_id', 'art_title', 'art_short_title', 'art_keywords', 'art_content', 'm_id', 'ca_id', 'art_summary', 'art_sort', 'art_is_allow_comment', 'art_position', 'art_designated_published', 'art_status', 're_id', 're_title', 's_id');
    // 属性字段
    private $allowFieldsRelation = array('at_id', 'at_name', 'at_title', 'at_type', 'at_value');
    // 属性字段
    private $allowShowFields = array('art_id', 'art_title', 'art_short_title', 'art_summary', 'art_created', 'art_cover', 'art_cover_b', 'ca_id', 'ca_title', 'art_hits', 'art_comment_count', 'm_id', 'art_designated_published', 'art_keywords');

    // 用户列表
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
        
        // 结果不处理
        $config['is_deal_result'] = $is_deal_result ? $is_deal_result : false;
        // api 请求
        $config['is_api'] = true;
        if ($order) {
            $config['order'] = $order;
        }
        $config['is_open_sub'] = isset($is_open_sub) ? $is_open_sub : false;

        // 定义获取列表种类
        $_POST['args']['type'] = intval($_POST['args']['type']);
        $_POST['args']['p'] = intval($p) ? intval($p) : 1;

        $result = D('Article')->lists($_POST['args'], $config);

        if (!$result) {
            $this->returnData(array('status' => 0, 'info' => '暂无数据'));
        }

        $this->returnData($result);
    }

    // 用户信息
    public function shows() {
        extract($_POST['args']);

        // 校验
        if (!intval($art_id)) {
            $this->returnData($this->errCode[2]);
        }

        $result = D('Article')->detail(intval($art_id));
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

        // 字段过滤
        foreach ($result['article'] as $key => $val) {
            if (!in_array($key, $config['fields'])) {
                unset($result['article'][$key]);
            }
        }

        $this->returnData($result);
    }

    // 删除资讯
    public function del() {
        extract($_POST['args']);

        // 校验
        if (!strval($art_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 删除所属栏目id
        $articleConfig['where']['art_id'] = array('IN', strval($art_id));
        $articleConfig['fields'] = 'ca_id';
        $ca_id = D('Article')->getOne($articleConfig);

        // 注 删除到回收站
        $result = D('Article')->statusUpdate($art_id);

        if ($result !== false){
            $this->returnData(array('status' => 1, 'res_value' => $ca_id, 'info' => '删除成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '删除失败'));
        }
    }

    // 删除资讯文件
    public function forbid() {
        extract($_POST['args']);
        
        // 校验
        if (!intval($f_id)) {
            $this->returnData($this->errCode[2]);
        }

        $result = D('Article')->delFile(intval($f_id));

        if ($result !== false){
            $this->returnData(array('status' => 1, 'info' => '删除成功'));
        } else {
            $this->returnData(array('status' => 0, 'info' => '删除失败'));
        }
    }

    // 添加
    public function add() {
        extract($_POST['args']);

        $insertData = array();
        // 字段过滤
        foreach ($_POST['args'] as $key => $val) {
            if (in_array($key, $this->allowFieldsInsert)) {
                $insertData[$key] = $val;
            }
        }

        // 是否有上传
        if ($_FILES['art_cover']['size'] > 0) {

            // 封面图上传
            $config['exts'] = explode(',', strtolower(C('ALLOW_IMAGE_TYPE')));
            $config['savePath'] = C('ARTICLE_COVER_PATH');
            $config['autoSub'] = true;
            $config['subName'] = array('date', C('ARTICLE_SUBNAME_RULE'));

            $art_cover = upload($_FILES['art_cover'], $config);
            if (!is_array($art_cover)) {
                // 图片上传失败
                $this->returnData(array('status' => 0, 'info' => $art_cover));
            }
            $savepath = explode('/', $art_cover['savepath']);
            $insertData['art_cover_path'] = $savepath[1];
            $insertData['art_cover_name'] = $art_cover['savename'];
            $insertData['art_cover_ext'] = $art_cover['ext'];
        }

        $insertData['art_creator_table'] = 'Member';
        $insertData['art_creator_id'] = intval($this->authInfo['me_id']);
        
        // 文章内容
        $result = D('Article')->insert($insertData);
        if ($result === false) {
            $this->returnData(array('status' => 0, 'info' => D('Article')->getError()));
        }

        $insertData['art_id'] = $result;
        $insertData['remark'] = $_POST['args']['remark'];
        $insertData['sort'] = $_POST['args']['sort'];
        $insertData['title'] = $_POST['args']['title'];

        // 文章图上传
        $res_pic = D('Article')->uploadPic($_FILES['pic'], $insertData);
        if ($res_pic === false) {
            $this->returnData(array('status' => 0, 'info' => D('Article')->getError()));
        }

        // 文章视频上传
        $res_video = D('Article')->uploadVideo($_FILES['video'], $insertData);
        if ($res_video === false) {
            $this->returnData(array('status' => 0, 'info' => D('Article')->getError()));
        }

        $_POST['args']['art_id'] = $result;

        // 文章属性
        D('AttributeRecord')->insert($_POST['args']);
        
        // 是否默认发布
        if (C('ARTICLE_IS_DEFAULT_PUBLISHED')) {
            $publishConfig['where']['art_id'] = intval($result);
            D('Article')->publishData(D('Article')->getOne($publishConfig));
        }

        $this->returnData(array('status' => 1, 'info' => '新增成功'));
    }

    // 修改
    public function edit() {
        extract($_POST['args']);

        // 校验
        if (!intval($art_id)) {
            $this->returnData($this->errCode[2]);
        }

        $insertData = array();
        // 字段过滤
        foreach ($_POST['args'] as $key => $val) {
            if (in_array($key, $this->allowFieldsInsert)) {
                $insertData[$key] = $val;
            }
        }

        // 是否有上传
        if ($_FILES['art_cover']['size'] > 0) {

            // 封面图上传
            $config['exts'] = explode(',', strtolower(C('ALLOW_IMAGE_TYPE')));
            $config['savePath'] = C('ARTICLE_COVER_PATH');
            $config['autoSub'] = true;
            $config['subName'] = array('date', C('ARTICLE_SUBNAME_RULE'));

            $art_cover = upload($_FILES['art_cover'], $config);
            if (!is_array($art_cover)) {
                // 图片上传失败
                $this->returnData(array('status' => 0, 'info' => $art_cover));
            }
            $savepath = explode('/', $art_cover['savepath']);
            $insertData['art_cover_path'] = $savepath[1];
            $insertData['art_cover_name'] = $art_cover['savename'];
            $insertData['art_cover_ext'] = $art_cover['ext'];
        }
        
        $insertData['art_id'] = intval($art_id);
        $insertData['art_creator_table'] = 'Member';
        $insertData['art_creator_id'] = intval($this->authInfo['me_id']);
        
        // 文章内容
        $result = D('Article')->insert($insertData);
        if ($result === false) {
            $this->returnData(array('status' => 0, 'info' => D('Article')->getError()));
        }

        $insertData['art_id'] = intval($art_id);
        $insertData['remark'] = $_POST['args']['remark'];
        $insertData['sort'] = $_POST['args']['sort'];
        $insertData['title'] = $_POST['args']['title'];
        $insertData['id'] = $_POST['args']['id'];

        // 文章图上传
        $res_pic = D('Article')->uploadPic($_FILES['pic'], $insertData);
        if ($res_pic === false) {
            $this->returnData(array('status' => 0, 'info' => D('Article')->getError()));
        }

        // 文章视频上传
        $res_video = D('Article')->uploadVideo($_FILES['video'], $insertData);
        if ($res_video === false) {
            $this->returnData(array('status' => 0, 'info' => D('Article')->getError()));
        }

        $_POST['args']['art_id'] = intval($art_id);

        // 文章属性
        D('AttributeRecord')->insert($_POST['args']);
        
        // 是否默认发布
        if (C('ARTICLE_IS_DEFAULT_PUBLISHED')) {
            $publishConfig['where']['art_id'] = intval($art_id);
            D('Article')->publishData(D('Article')->getOne($publishConfig));
        }

        $this->returnData(array('status' => 1, 'info' => '编辑成功'));
    }

    public function publish() {
        extract($_POST['args']);

        // 校验
        if (!strval($art_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 更改发布状态
        $config['art_status'] = 1;
        $config['art_published'] = time();
        $config['art_published_table'] = 'Member';
        $config['art_publisher_id'] = intval($this->authInfo['me_id']);
        $where['where']['art_id'] = array('IN', strval($art_id));
        $result = D('Article')->update($config, $where);
        if ($result === false) {
            $this->returnData(array('status' => 0, 'info' => '发布失败'));
        }

        // 数据发布
        $articleConfig['where']['art_id'] = array('IN', strval($art_id));
        $data = D('Article')->getAll($articleConfig);
        D('Article')->publishData($data);

        // 返回发布所属栏目id
        $this->returnData(array('status' => 1, 'res_value' => $data[0]['ca_id'], 'info' => '发布成功'));
    }

    // 资讯属性
    public function getRelation() {
        extract($_POST['args']);

        // 校验
        if (!strval($at_id)) {
            $this->returnData($this->errCode[2]);
        }

        // 返回字段
        if ($fields) {
            // 有定义返回字段
            $returnFields = explode(',', $fields);
            $config['fields'] = array_intersect($this->allowFieldsRelation, $returnFields);
        }
        if (!$config['fields']){
            $config['fields'] = $this->allowFieldsRelation;
        }

        $config['where']['at_id'] = array('IN', strval($at_id));
        $result = D('Attribute')->getAll($config);

        $this->returnData($result);
    }

    /* 前台展示列表
     * belong 所属  1 平台  2 区域  3学校
     * type 类型  1 栏目  2 要闻速递（时间）  3 列表
     * type_num  类型个数  默认 7 个
     * every_num 每种类型下资讯个数  默认 12 个
     * re_id/s_id/
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
        foreach ($type as $type_key => $art_type) {
            $config = array();
            $data = array();
            switch ($art_type) {
                case 1:
                    // 栏目管理
                    $data['ca_status'] = 1;
                    $data['ca_is_show'] = 1;
                    $data['ca_level'] = isset($ca_level) ? intval($ca_level) : 0;
                    $data['re_id'] = strval($re_id);
                    $data['s_id'] = intval($s_id);
                    $config['fields'] = 'ca_id,ca_title';
                    $config['every_page_num'] = intval($type_num[$type_key]) ? intval($type_num[$type_key]) : intval($type_num[0]);
                    $config['order'] = 'ca_sort ASC';
                    $config['is_deal_result'] = false;
                    $cate = D('Category')->lists($data, $config);
                    // 特殊要求  过滤掉 首页
                    foreach ($cate['list'] as $ca_id => $ca_title) {
                        if ($ca_title == '首页') {
                            unset($cate['list'][$ca_id]);
                        }
                    }
                    $result[$art_type]['type'] = $cate['list'];
                    break;
                case 2:
                    $data['ca_status'] = 1;
                    $data['ca_is_show'] = 1;
                    $data['re_id'] = strval($re_id);
                    $data['s_id'] = intval($s_id);
                    $config['fields'] = 'ca_id,ca_title';
                    $config['every_page_num'] = 1;
                    $config['order'] = 'ca_sort ASC';
                    $config['is_deal_result'] = false;
                    $su = D('Category')->lists($data, $config);
                    $result[$art_type]['type'] = $su['list'];
                    break;
                case 3:
                    $result[3]['type'] = '资讯列表';
                    break;
            }
        }

        if (!$every_num) {
            $every_num = array(12);
        } elseif (!is_array($every_num)) {
            $every_num = explode(',', $every_num);
        }
        
        // 资讯
        $artData['art_designated_published'] = time();
        $artData['re_id'] = strval($re_id);
        $artData['s_id'] = intval($s_id);
        $artConfig['type'] = intval($belong);
        $artConfig['every_page_num'] = intval($every_num[1]) ? intval($every_num[1]) : intval($every_num[0]);
        $artConfig['is_deal_result'] = false;
        $artConfig['order'] = strval($order) ? strval($order) : 'art_sort DESC';
        $artConfig['is_open_sub'] = true;
        foreach ($cate['list'] as $ca_id => $ca_title) {
            $artData['ca_id'] = $ca_id;
            $articleList = D('Article')->lists($artData, $artConfig);
            foreach ($articleList['list'] as $art_key => $art_info) {
                $articleList['list'][$art_key] = $this->dealReturnResult($art_info);
            }
            $result[1]['list'][$ca_id] = (array)$articleList['list'];
        }

        // 要闻速递
        $suData['art_designated_published'] = time();
        $suData['re_id'] = strval($re_id);
        $suData['s_id'] = intval($s_id);
        $suConfig['type'] = intval($belong);
        $suConfig['every_page_num'] = intval($every_num[2]) ? intval($every_num[2]) : intval($every_num[0]);
        $suConfig['is_deal_result'] = false;
        $suConfig['order'] = 'art_created DESC';
        foreach ($su['list'] as $su_id => $su_title) {
            $suData['ca_id'] = $su_id;
            $suList = D('Article')->lists($suData, $suConfig);
            foreach ($suList['list'] as $su_key => $su_info) {
                $suList['list'][$su_key] = $this->dealReturnResult($su_info);
            }
            $result[2]['list'][1] = (array)$suList['list'];
        }

        // 资讯列表
        $reData['art_designated_published'] = time();
        $reData['art_is_deleted'] = 9;
        $reData['art_status'] = 1;
        $reData['ca_is_show'] = 1;
        if (isset($art_position)) {
            $reData['art_position'] = $art_position;
        }
        if ($ca_id) {
            $reData['ca_id'] = $ca_id;
        }
        if ($m_id) {
            $reData['m_id'] = $m_id;
        }
        $reData['re_id'] = strval($re_id);
        $reData['s_id'] = intval($s_id);
        $reConfig['type'] = intval($belong);
        $reConfig['every_page_num'] = intval($every_num[3]) ? intval($every_num[3]) : intval($every_num[0]);
        $reConfig['is_deal_result'] = true;
        $reConfig['order'] = strval($order) ? strval($order) : 'art_sort DESC,art_created DESC';
        $reConfig['is_open_sub'] = true;
        if ($result[3]['type']) {
            $reList = D('Article')->lists($reData, $reConfig);
            foreach ($reList['list'] as $re_key => $re_info) {
                $reList['list'][$re_key] = $this->dealReturnResult($re_info);
            }
            $result[3] = $reList;
        }

        $this->returnData($result);
    }

    private function getSubIds($pid = '0', $res = '') {

        $config['where']['ca_status'] = 1;
        $config['where']['ca_is_show'] = 1;
        $config['where']['ca_pid'] = array('IN', $pid);
        $config['fields'] = 'ca_id';
        $cate = D($this->name, 'Model')->getAll($config);
        $res .= ',' . $pid;
        if ($cate) {
            return D('Category')->getSubIds(implode(',', $cate), $res);
        } else {
            return trim($res, ',');
        }
    }

    // 处理返回结果
    private function dealReturnResult($data) {

        if (!$data) {
            return array();
        }

        // 特殊处理
        $data['art_cover'] = D('Article')->getCover($data);
        $data['art_cover_b'] = D('Article')->getCover($data, array('size' => '_b'));

        // 过滤处理
        foreach ($data as $key => $val) {
            if (!in_array($key, $this->allowShowFields)) {
                unset($data[$key]);
            }
        }
        
        return $data;
    }

    /*
     * 资讯某字段累加
     * type 0 默认浏览量
     */
    public function increase() {
        extract($_POST['args']);

        // 校验
        if (!intval($art_id)) {
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
            // res_hits
            D('Article', 'Model')->increase('art_hits', array('art_id' => intval($art_id)), 1);
        }
    }

    // 统计
    public function statistics() {
        extract($_POST['args']);

        $result = D('ArticleStatistics')->lists($_POST['args']);

        $this->returnData($result);
    }
}
?>
