<?php
namespace Area\Controller;
class ResourceController extends AreaController {
    public function index() {
        set_time_limit(120);

        $this->assign('area_title', '资源首页');

        // 左侧轮播下 4 个
        $lefttopList = $this->apiReturnDeal(getApi(array('type' => '1', 'type_num' => 2, 'type_ids' => array('5,6'), 'belong' => 2, 'every_num' => 2, 'recommend' => 1, 're_id' => $this->current_region_info['re_ids']), 'Resource', 'getShows'));
        $this->assign('lefttopList', $lefttopList);

        // 左侧 资源推荐
        $leftrecommentList = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 2, 'every_num' => 24, 'recommend' => 1, 're_id' => $this->current_region_info['re_ids']), 'Resource', 'getShows'));
        $this->assign('leftrecommentList', $leftrecommentList);

        // 左侧 小学
        $primaryList = $this->apiReturnDeal(getApi(array('type' => '2', 'type_num' => 5, 'belong' => 2, 'every_num' => 12, 'order' => array('res_hits DESC,res_created DESC'), 'school_type' => 15, 're_id' => $this->current_region_info['re_ids']), 'Resource', 'getShows'));
        $this->assign('primaryList', $primaryList);

        // 左侧 初中
        $middleList = $this->apiReturnDeal(getApi(array('type' => '2', 'type_num' => 5, 'belong' => 2, 'every_num' => 12, 'order' => array('res_hits DESC,res_created DESC'), 'school_type' => 16, 're_id' => $this->current_region_info['re_ids']), 'Resource', 'getShows'));
        $this->assign('middleList', $middleList);

        // 左侧 高中
        $highList = $this->apiReturnDeal(getApi(array('type' => '2', 'type_num' => 5, 'belong' => 2, 'every_num' => 12, 'order' => array('res_hits DESC,res_created DESC'), 'school_type' => 17, 're_id' => $this->current_region_info['re_ids']), 'Resource', 'getShows'));
        $this->assign('highList', $highList);

        // 左侧学制
        $sTypeList = $this->apiReturnDeal(getApi(array('type' => '4', 'type_num' => 3, 'belong' => 2, 'every_num' => 13, 'order' => 'res_hits DESC,res_created DESC', 're_id' => $this->current_region_info['re_ids']), 'Resource', 'getShows'));
        $this->assign('sTypeList', $sTypeList);

        // 右侧热文
        $hotList = $this->apiReturnDeal(getApi(array('type' => '3', 'type_num' => 1, 'belong' => 2, 'every_num' => 3, 'is_page' => true, 'order' => 'res_hits DESC,res_created DESC', 're_id' => $this->current_region_info['re_ids']), 'Resource', 'getShows'));
        $this->assign('hotList', $hotList);

        // 右侧排行榜
        $rankList = $this->apiReturnDeal(getApi(array('type' => '3', 'type_num' => 1, 'belong' => 2, 'every_num' => 10, 'is_page' => true, 'order' => 'res_score_num DESC,res_created DESC', 're_id' => $this->current_region_info['re_ids']), 'Resource', 'getShows'));
        $this->assign('rankList', $rankList);

        // 右侧试题试卷
        $paperList = $this->apiReturnDeal(getApi(array('type' => '1', 'type_ids' => '4', 'belong' => 2, 'every_num' => 6, 'order' => 'res_hits DESC,res_created DESC', 're_id' => $this->current_region_info['re_ids']), 'Resource', 'getShows'));
        $this->assign('paperList', $paperList);

        // 广告列表
        $advertList = $this->apiReturnDeal(getApi(array('type' => '8,9,10,11,12', 'belong' => 2, 're_id' => $this->current_region_info['re_ids']), 'Advert', 'getShows'));
        $this->assign('advertList', $advertList);

        $this->autoHtml('index');

        parent::index();
    }

    // 资源检索页面
    public function search() {
        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . 'Search.css'));
        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Search.js'));

        // 搜索关键词
        if ($_POST['keywords']) {
            $keywords = strval($_POST['keywords']);
        } else {
            $keywords = $this->current_param['keywords'];
        }
        if ($keywords) {
            $this->assign('keywords', $keywords);
        }
        $this->assign('area_title', '资源检索');
        $this->assign('search_keywords_link', getUrlAddress(array('res_title' => 'keywords'), 'search', 'resource'));

        // 使用类型
        $resourceCategory = $this->apiReturnDeal(getApi(array(), 'Resource', 'category'));
        $this->assign('res_cate', $resourceCategory);
        
        // 获取子集地区
        $regConfig['re_pid'] = CURRENT_UUID;
        $regConfig['re_status'] = 1;
        $regConfig['is_page'] = false;
        $regConfig['fields'] = 're_id,re_title';
        $regionChildren = $this->apiReturnDeal(getApi($regConfig, 'Region', 'lists'));
        $this->assign('regionChildren', $regionChildren['list']);

        // 获取资源时间范围列表
        $resourceTime = $this->apiReturnDeal(getApi(array('type' => 5, 'belong' => 2, 're_id' => $this->current_region_info['re_ids']), 'Resource', 'getShows'));
        $time_scope = range(intval(date('Y', $resourceTime[5]['list']['min'])), intval(date('Y', $resourceTime[5]['list']['max'])));
        $this->assign('time_scope', $time_scope);

        // 根据参数查询 检索结果
        $resourceList = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 2, 'every_num' => 10, 'is_page' => true, 'page' => $this->current_param['p'], 're_id' => $this->current_region_info['re_ids'], 'keywords' => $keywords), 'Resource', 'getShows'));
        $this->assign('resourceList', $resourceList);
        
        $this->autoHtml('index');

        $this->display();
    }

    public function detail() {
        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . 'Detail.css'));
        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Detail.js'));

        $this->assign('area_title', '资源详情');

        // 获取资源信息
        $vo = $this->apiReturnDeal(getApi(array('res_id' => $this->current_param['res_id'], 'is_deal_result' => true), 'Resource', 'shows'));
        /*if (substr($vo['res_is_eliminated'], 1, 1) == 1) {
            $this->error('资源不存在');
        }
        if ($vo['res_is_published'] != 1) {
            $this->error('资源未发布');
        }
        if ($vo['res_is_pass'] != 1) {
            $this->error('资源审核中');
        }
        if ($vo['res_valid'] > time() && $vo['res_avaliable'] < time()) {
            $this->error('资源已失效');
        }*/
        $this->assign('vo', $vo);

        // 阅读数
        getApi(array('res_id' => $this->current_param['res_id']), 'Resource', 'increase');

        // 相关文章
        $relationList = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 2, 'every_num' => 5, 'is_page' => true, 'page' => 1, 'subject' => $vo['res_subject'], 'rt_id' => $vo['rt_id'], 're_id' => $this->current_region_info['re_ids']), 'Resource', 'getShows'));
        $this->assign('relationList', $relationList);

        // 还喜欢
        $like_con = array_rand(array('res_hits DESC', 'res_created DESC', 'res_downloads DESC'));
        $likeList = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 2, 'every_num' => 5, 'is_page' => true, 'page' => 1, 'subject' => $vo['res_subject'], 'rt_id' => $vo['rt_id'], 'order' => $like_con, 're_id' => $this->current_region_info['re_ids']), 'Resource', 'getShows'));
        $this->assign('likeList', $likeList);

        // 广告列表
        $advertList = $this->apiReturnDeal(getApi(array('type' => '14', 'belong' => 2, 're_id' => $this->current_region_info['re_ids']), 'Advert', 'getShows'));
        $this->assign('advertList', $advertList);

        // 导航
        $this->getNavigation('detail', $vo);

        $this->display();
    }

    public function getDirectory() {
        // 课文目录
        $d_id = I('request.id', 0, 'intval');
        if ($d_id) {
            $d_info = $this->apiReturnDeal(getApi(array('d_id' => $d_id), 'Directory', 'shows'));
            $config['d_pid'] = $d_id;
            $config['d_level'] = $d_info['d_level'] + 1;
        } else {
            $config['d_level'] = 0;
        }
        $config['is_upper_level'] = true;
        $config['is_page'] = false;
        //$config['is_open_sub'] = true;
        $directoryList = $this->apiReturnDeal(getApi($config, 'Directory', 'lists'));
        $tag = reloadCache('tag');
        foreach ($directoryList['list'] as $dir_key => $dir_val) {
            if ($dir_val['d_level'] == 0) {
                $directoryList['list'][$dir_key]['d_title'] = $tag[6][$dir_val['d_version']];
            } elseif ($dir_val['d_level'] == 1) {
                $directoryList['list'][$dir_key]['d_title'] = $tag[4][$dir_val['d_school_type']];
            } elseif ($dir_val['d_level'] == 2) {
                $directoryList['list'][$dir_key]['d_title'] = $tag[7][$dir_val['d_grade']];
            } elseif ($dir_val['d_level'] == 3) {
                $directoryList['list'][$dir_key]['d_title'] = $tag[8][$dir_val['d_semester']];
            } elseif ($dir_val['d_level'] == 4) {
                $directoryList['list'][$dir_key]['d_title'] = $tag[5][$dir_val['d_subject']];
            }
        }
        
        // 课文目录转树结构
        header("Content-Type:text/xml; charset=utf-8");
        $xml  = '<?xml version="1.0" encoding="utf-8" ?>'."\n";
        $xml .= '<tree caption="课文目录" id="0">'."\n";
        $xml .= html_entity_decode(toTree(tree($directoryList['list'], 'd_id', 'd_pid', 'list', $d_id), 'd_id', 'd_pid', 'd_level', 'd_title', 'list'));
        $xml .= '</tree>';
        exit($xml);
    }

    public function getKnowledge() {
        $kp_id = I('request.id', 0, 'intval');
        if ($kp_id) {
            $config['kp_pid'] = $kp_id;
        } else {
            $config['kp_pid'] = 0;
        }
        $config['is_page'] = false;
        // 知识点
        $knowledgeList = $this->apiReturnDeal(getApi($config, 'KnowledgePoints', 'lists'));
        // 知识点转树结构
        header("Content-Type:text/xml; charset=utf-8");
        $xml  = '<?xml version="1.0" encoding="utf-8" ?>'."\n";
        $xml .= '<tree caption="知识点" id="0">'."\n";
        $xml .= html_entity_decode(toTree(tree($knowledgeList['list'], 'kp_id', 'kp_pid', 'list', $kp_id), 'kp_id', 'kp_pid', 'kp_level', 'kp_title', 'list'));
        $xml .= '</tree>';
        exit($xml);
    }

    // 检索页面 返回数据
    public function getData() {
        $dataParam = array(
            'rc_id' => I('category'),
            're_id' => I('region'),
            'year' => I('time'),
        );
        
        // 类型
        $category = I('category', 0, 'intval');

        // 地区
        $region = I('region', 0, 'intval');
        $regionKey = array_search($region, explode(',', $this->current_region_info['re_children']));
        $regionIdsList = explode(',', $this->current_region_info['re_children']);
        $region = $regionIdsList[$regionKey] ? $regionIdsList[$regionKey] : $this->current_region_info['re_ids'];

        // 时间
        $year = I('time', 0, 'intval');
        $starttime = $year ? $year : strtotime($year . '-01-01 00:00:00');
        $endtime = $year ? $year : strtotime($year . '-12-31 23:59:59');

        // 关键词
        $keywords = I('keywords', '', 'strval');

        // 目录
        $directory = I('directory', 0, 'intval');
        if ($directory) {
            $dir_info = $this->apiReturnDeal(getApi(array('d_id' => $directory), 'Directory', 'shows'));
            if ($dir_info['d_level'] == 0) {
                $config['res_version'] = $dir_info['d_version'];
            } elseif ($dir_info['d_level'] == 1) {
                $config['res_school_type'] = $dir_info['d_school_type'];
            } elseif ($dir_info['d_level'] == 2) {
                $config['res_grade'] = $dir_info['d_grade'];
            } elseif ($dir_info['d_level'] == 3) {
                $config['res_semester'] = $dir_info['d_semester'];
            } elseif ($dir_info['d_level'] == 4) {
                $config['res_subject'] = $dir_info['d_subject'];
            } else {
                $config['d_id'] = $directory;
            }
        }

        // 知识点
        $knowledge = I('knowledge', 0, 'intval');

        // 分页
        $page = I('page', 1, 'intval');

        // 排序
        $order = intval($order) ? intval($order) : 1;
        if ($config['d_id'] || $knowledge) {
            $res_prev = 'r.';
        }
        $orderConfig = array(1 => $res_prev . 'res_created', $res_prev . 'res_score_num', $res_prev . 'res_downloads', $res_prev . 'res_comment_count', $res_prev . 'res_download_points');
        $orderField = $orderConfig[$order] ? $orderConfig[$order] : $res_prev . 'res_created';
        // 顺序
        $sort = I('sort', 0, 'intval');
        $sortOrder = $sort == 1 ? 'ASC' : 'DESC';

        $config['type'] = 3;
        $config['belong'] = 2;
        $config['every_num'] = 10;
        $config['is_page'] = true;
        $config['page'] = $page;
        $config['category'] = $category;
        $config['re_id'] = $region;
        $config['order'] = $orderField . ' ' . $sortOrder;
        $config['keywords'] = $keywords;
        $config['kp_id'] = $knowledge;
        $resourceList = $this->apiReturnDeal(getApi($config, 'Resource', 'getShows'));

        $return = array();
        $return['page'] = $resourceList[3]['page'];
        // 数据拼成html
        foreach ($resourceList[3]['list'] as $res_info) {
            $return['list'] .= $this->dataToHtml($res_info);
        }
        echo json_encode($return);
    }

    // 导航
    private function getNavigation($action = 'index', $data = array()) {
        $nav = '';
        if ($action == 'index') {
            // 区域首页
            $nav .= '<a href="' . getUrlAddress(array(), 'index', 'index') . '">区域首页</a>';
        }

        if ($action != 'index') {
            // 首页
            $nav .= '<a href="' . getUrlAddress(array(), 'index', 'resource') . '">首页</a>';
        }

        if ($action == 'detail') {
            // 列表
            $nav .= ' &gt; <a href="' . getUrlAddress(array(), 'search', 'resource') . '">资源列表</a>';
            // 学科
            $tag = reloadCache('tag');
            $nav .= ' &gt; <a href="' . getUrlAddress(array('res_subject' => $data['res_subject']), 'search', 'resource') . '">' . $tag[5][$data['res_subject']] . '</a>';
        }
        $this->assign('navigation', $nav);
    }

    // 列表页面
    private function dataToHtml($info) {
        
        $html = '<li>
                    <span class="re_detail">
                        <a href="' . getUrlAddress($info, 'detail', 'resource') . '">
                            <img width="80" height="80" alt="' . $info['res_title'] . '" src="' . $info['res_image'] . '" title="' . getShortTitle($info['res_title'], 35) . '">
                        </a>
                        <span>
                            <a href="' . getUrlAddress($info, 'detail', 'resource') . '">' . getShortTitle($info['res_title'], 25) . '</a>
                            <p>简介：</p>
                            <span>智慧豆：' . intval($info['res_download_points']) . '个</span>
                            <span>上传时间：' . date('Y-m-d', $info['res_created']) .'</span>
                            <span>下载量：' . intval($info['res_downloads']) . '</span>
                        </span>
                    </span>
                    <span class="re_infor">
                        <p>' . intval($info['res_hits']) . '人阅读</p>
                        <p class="star">';
                        if ($info['res_score_num']) {
                            $average = round($info['res_score_num']/$info['res_score_count']);
                        }
                        for ($i = 0; $i < $average; $i++) {
                            $html .= '<img src="' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Images/star1.png">';
                        }
                        for ($i = 0; $i < 5-$average; $i++) {
                            $html .= '<img src="' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Images/star2.png">';
                        }
                        $html .= '<span>' . intval($average) . '分</span>
                        </p>
                        <p>(' . intval($info['res_comment_count']) . '人评价)</p>
                    </span>
                </li>';
        return $html;
    }

    // 下载文件
    public function download() {

        // TO DO 验证是否登录

        $res_id = intval($this->current_param['res_id']);
        if (!$res_id) {
            echo '参数错误';
            exit;
        }

        $info = $this->apiReturnDeal(getApi(array('res_id' => $res_id, 'is_deal_result' => true), 'Resource', 'shows'));
        /*if (substr($info['res_is_eliminated'], 1, 1) == 1) {
            $this->error('资源不存在');
        }
        if ($info['res_is_published'] != 1) {
            $this->error('资源未发布');
        }
        if ($info['res_is_pass'] != 1) {
            $this->error('资源审核中');
        }
        if ($info['res_valid'] > time() && $info['res_avaliable'] < time()) {
            $this->error('资源已失效');
        }*/

        // TO DO 是否需要智慧豆   用户账号减豆,创建人加豆   记录日志

        if (!$info['res_original_path']) {
            echo '文件不存在';
            exit;
        }

        // TO DO 记录下载日志

        // 下载次数加 1
        getApi(array('res_id' => $this->current_param['res_id'], 'type' => 1), 'Resource', 'increase');

        header("Content-type:" . getContentType($info['rf_ext']) . ";charset=utf-8");
        header("Content-Disposition: attachment; filename=".$info['res_title'].'.'.$info['rf_ext']);
        @readfile($info['res_original_path']);
        exit;
    }

    // 评论
    public function comments() {

        // TO DO 验证是否登录
        $me_id = 2;

        // TO DO 限制评论频率

        $res_id = I('id', 0, 'intval');
        if (!$res_id) {
            echo '文件不存在';
            exit;
        }

        $info = $this->apiReturnDeal(getApi(array('res_id' => $res_id, 'is_deal_result' => true), 'Resource', 'shows'));
        /*if (substr($info['res_is_eliminated'], 1, 1) == 1) {
            $this->error('资源不存在');
        }
        if ($info['res_is_published'] != 1) {
            $this->error('资源未发布');
        }
        if ($info['res_is_pass'] != 1) {
            $this->error('资源审核中');
        }
        if ($info['res_valid'] > time() && $info['res_avaliable'] < time()) {
            $this->error('资源已失效');
        }*/
        $content = I('rco_content', '', 'strval');
        $pid = I('pid', 0, 'intval');

        // TO DO 评论接口
    }
}