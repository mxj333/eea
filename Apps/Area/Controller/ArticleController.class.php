<?php
namespace Area\Controller;
class ArticleController extends AreaController {
    public function index() {
        set_time_limit(120);

        $this->assign('area_title', '资讯首页');

        // 导航顶级栏目
        $category = $this->apiReturnDeal(getApi(array('belong' => 2, 're_id' => $this->current_region_info['re_ids'], 'ca_level' => 0, 'ca_status' => 1, 'ca_is_show' => 1, 'return_num' => 6), 'Category', 'lists'));
        $this->assign('category', $category['list']);

        // 4个推荐资讯
        $recommend = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 2, 're_id' => $this->current_region_info['re_ids'], 'every_num' => 4, 'art_position' => 1), 'Article', 'getShows'));
        $this->assign('recommend', $recommend[3]['list']);

        // 焦点
        $focus = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 2, 're_id' => $this->current_region_info['re_ids'], 'every_num' => 2, 'order' => 'art_hits DESC,art_sort DESC,art_created DESC'), 'Article', 'getShows'));
        $this->assign('focus', $focus[3]['list']);

        // 栏目文章
        $artList = $this->apiReturnDeal(getApi(array('type' => '1', 'belong' => 2, 're_id' => $this->current_region_info['re_ids'], 'type_num' => 6, 'every_num' => 5, 'order' => 'art_created DESC'), 'Article', 'getShows'));
        $this->assign('artList', $artList[1]);

        // 排行
        $rank = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 2, 're_id' => $this->current_region_info['re_ids'], 'every_num' => 10, 'order' => 'art_hits DESC,art_sort DESC,art_created DESC'), 'Article', 'getShows'));
        $this->assign('rank', $rank[3]['list']);

        // 学校列表
        $school = $this->apiReturnDeal(getApi(array('type' => '1', 'belong' => 2, 're_id' => $this->current_region_info['re_ids'], 'every_num' => 10), 'School', 'getShows'));
        $this->assign('school', $school[1]['list'][1]);

        // 广告列表
        $advertList = $this->apiReturnDeal(getApi(array('type' => '15,17,18,19', 'belong' => 2, 're_id' => $this->current_region_info['re_ids']), 'Advert', 'getShows'));
        $this->assign('advertList', $advertList);

        parent::index();
    }

    // 检索页面
    public function search() {
        set_time_limit(120);

        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . 'Search.css'));
        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Search.js'));

        // 导航顶级栏目
        $category = $this->apiReturnDeal(getApi(array('belong' => 2, 're_id' => $this->current_region_info['re_ids'], 'ca_level' => 0, 'ca_status' => 1, 'ca_is_show' => 1, 'return_num' => 6), 'Category', 'lists'));
        $this->assign('category', $category['list']);

        $this->assign('ca_id', $this->current_param['ca_id']);
        $cate_info = $this->apiReturnDeal(getApi(array('ca_id' => $this->current_param['ca_id']), 'Category', 'shows'));
        $this->assign('curr_cate_info', $cate_info);

        // 列表
        $reData['art_designated_published'] = time();
        $reData['art_is_deleted'] = 9;
        $reData['art_status'] = 1;
        $reData['ca_is_show'] = 1;
        $reData['re_id'] = $this->current_region_info['re_ids'];
        if ($this->current_param['ca_id']) {
            $reData['ca_id'] = $this->current_param['ca_id'];
        }
        if ($this->current_param['keywords']) {
            $reData['art_title'] = $this->current_param['keywords'];
        }
        $reData['p'] = $this->current_param['page'] ? $this->current_param['page'] : 1;
        $reData['order'] = 'art_created DESC';
        $reData['is_open_sub'] = true;
        $reData['is_deal_result'] = true;
        $artList = $this->apiReturnDeal(getApi($reData, 'Article', 'lists'));
        $this->assign('artList', $artList['list']);

        // 焦点
        $focus = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 2, 're_id' => $this->current_region_info['re_ids'], 'every_num' => 2, 'order' => 'art_hits DESC,art_sort DESC,art_created DESC'), 'Article', 'getShows'));
        $this->assign('focus', $focus[3]['list']);

        // 排行
        $rank = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 2, 're_id' => $this->current_region_info['re_ids'], 'every_num' => 10, 'order' => 'art_hits DESC,art_sort DESC,art_created DESC'), 'Article', 'getShows'));
        $this->assign('rank', $rank[3]['list']);

        // 学校列表
        $school = $this->apiReturnDeal(getApi(array('type' => '1', 'belong' => 2, 're_id' => $this->current_region_info['re_ids'], 'every_num' => 10), 'School', 'getShows'));
        $this->assign('school', $school[1]['list'][1]);

        // 广告列表
        $advertList = $this->apiReturnDeal(getApi(array('type' => '20,21,22,23', 'belong' => 2, 're_id' => $this->current_region_info['re_ids']), 'Advert', 'getShows'));
        $this->assign('advertList', $advertList);

        $this->display();
    }

    public function lists() {
        $ca_id = I('request.ca_id', 0, 'intval');
        $keywords = I('request.keywords', '', 'strval');
        $page = I('request.page', 1, 'intval');

        // 列表
        $reData['art_designated_published'] = time();
        $reData['art_is_deleted'] = 9;
        $reData['art_status'] = 1;
        $reData['ca_is_show'] = 1;
        $reData['re_id'] = $this->current_region_info['re_ids'];
        $reData['order'] = 'art_created DESC';
        $reData['is_open_sub'] = true;
        $reData['is_deal_result'] = true;
        $reData['p'] = intval($page) ? intval($page) : 1;
        $reData['fields'] = 'art_id,art_title,m_id,ca_id,art_summary,art_hits,ca_title,art_cover_path,art_cover_name';
        if ($ca_id) {
            $reData['ca_id'] = $ca_id;
        }
        if ($keywords) {
            $reData['art_title'] = $keywords;
        }
        
        $artList = $this->apiReturnDeal(getApi($reData, 'Article', 'lists'));
        echo json_encode($artList);
    }

    public function detail() {
        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . 'Detail.css'));
        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Detail.js'));

        $this->assign('area_title', '资讯详情');
        // 导航顶级栏目
        $category = $this->apiReturnDeal(getApi(array('belong' => 2, 're_id' => $this->current_region_info['re_ids'], 'ca_level' => 0, 'ca_status' => 1, 'ca_is_show' => 1, 'return_num' => 6), 'Category', 'lists'));
        $this->assign('category', $category['list']);
        $this->assign('ca_id', $this->current_param['ca_id']);

        // 获取信息
        $vo = $this->apiReturnDeal(getApi(array('art_id' => $this->current_param['art_id'], 'is_deal_result' => true), 'Article', 'shows'));
        $this->assign('vo', $vo);

        // 阅读数
        getApi(array('art_id' => $this->current_param['art_id']), 'Article', 'increase');

        if ($vo['article']['m_id'] == 3) {
            // 视频
            // 相关视频
            $relation = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 2, 're_id' => $this->current_region_info['re_ids'], 'ca_id' => $vo['article']['ca_id'], 'every_num' => 5, 'order' => 'art_sort DESC,art_created DESC'), 'Article', 'getShows'));
            $this->assign('relation', $relation[3]['list']);
        } elseif ($vo['article']['m_id'] == 1) {
            // 文章
            // 学校列表
            $school = $this->apiReturnDeal(getApi(array('type' => '1', 'belong' => 2, 're_id' => $this->current_region_info['re_ids'], 'every_num' => 10), 'School', 'getShows'));
            $this->assign('school', $school[1]['list'][1]);
        }

        if ($vo['article']['m_id'] == 3) {
            // 热播
            $focusData['every_num'] = 5;
            $focusData['m_id'] = 3;
        } else {
            // 焦点
            $focusData['every_num'] = 2;
        }
        $focusData['type'] = 3;
        $focusData['belong'] = 2;
        $focusData['re_id'] = $this->current_region_info['re_ids'];
        $focusData['order'] = 'art_hits DESC,art_sort DESC,art_created DESC';
        $focus = $this->apiReturnDeal(getApi($focusData , 'Article', 'getShows'));
        $this->assign('focus', $focus[3]['list']);

        // 排行
        $rank = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 2, 're_id' => $this->current_region_info['re_ids'], 'every_num' => 10, 'order' => 'art_hits DESC,art_sort DESC,art_created DESC'), 'Article', 'getShows'));
        $this->assign('rank', $rank[3]['list']);

        // 广告列表
        $advertList = $this->apiReturnDeal(getApi(array('type' => '24,25,26', 'belong' => 2, 're_id' => $this->current_region_info['re_ids']), 'Advert', 'getShows'));
        $this->assign('advertList', $advertList);

        $model = reloadCache('model');
        $this->display($model[$vo['article']['m_id']]['m_name']);
    }
}