<?php
namespace Home\Controller;
class ArticleController extends BaseController {
    public function index() {
        set_time_limit(120);

        // 资讯栏目
        $cateList = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 1, 'return_num' => 7, 'ca_level' => 0), 'Category', 'lists'));
        $this->assign('cateList', $cateList['list']);

        // 广告列表
        $advertList = $this->apiReturnDeal(getApi(array('type' => '35', 'belong' => 1), 'Advert', 'getShows'));
        $this->assign('advertList', $advertList);

        // 最新推荐
        $recommendNew = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 1, 'every_num' => 10, 'art_position' => 1, 'order' => 'art_created DESC'), 'Article', 'getShows'));
        $this->assign('recommendNew', $recommendNew[3]['list']);

        // 摘要
        $recommendHot = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 1, 'every_num' => 3, 'art_position' => 1, 'order' => 'art_sort DESC,art_hits DESC'), 'Article', 'getShows'));
        $this->assign('recommendHot', $recommendHot[3]['list']);

        // 热文
        $videoHot = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 1, 'every_num' => 5, 'm_id' => 3, 'order' => 'art_hits DESC,art_created DESC'), 'Article', 'getShows'));
        $this->assign('videoHot', $videoHot[3]['list']);

        // 最新新闻
        $newList = $this->apiReturnDeal(getApi(array('type' => '1', 'type_num' => 5, 'belong' => 1, 'every_num' => 11, 'order' => 'art_created DESC'), 'Article', 'getShows'));
        $this->assign('newList', $newList[1]['list']);

        // 猜
        $guessList = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 1, 'every_num' => 10, 'order' => 'art_hits DESC,art_created DESC'), 'Article', 'getShows'));
        $this->assign('guessList', $guessList[3]['list']);

        // 排行榜
        $rankList = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 1, 'every_num' => 10, 'order' => 'art_comment_count DESC,art_created DESC'), 'Article', 'getShows'));
        $this->assign('rankList', $rankList[3]['list']);

        parent::index();
    }

    public function search() {
        set_time_limit(120);

        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . 'Search.css'));
        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Search.js'));

        $this->assign('ca_id', $this->current_param['ca_id']);

        // 资讯栏目
        $cateList = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 1, 'return_num' => 7, 'ca_level' => 0), 'Category', 'lists'));
        $this->assign('cateList', $cateList['list']);

        // 最新推荐
        $recommendNew = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 1, 'every_num' => 10, 'ca_id' => $this->current_param['ca_id'], 'art_position' => 1, 'order' => 'art_created DESC'), 'Article', 'getShows'));
        $this->assign('recommendNew', $recommendNew[3]['list']);

        // 列表
        $artList = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 1, 'ca_id' => $this->current_param['ca_id'], 'page' => $this->current_param['page'], 'every_num' => 10, 'order' => 'art_created DESC'), 'Article', 'getShows'));
        $this->assign('artList', $artList[3]);
        
        // 猜
        $guessList = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 1, 'ca_id' => $this->current_param['ca_id'], 'every_num' => 10, 'order' => 'art_hits DESC,art_created DESC'), 'Article', 'getShows'));
        $this->assign('guessList', $guessList[3]['list']);

        // 排行榜
        $rankList = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 1, 'ca_id' => $this->current_param['ca_id'], 'every_num' => 10, 'order' => 'art_comment_count DESC,art_created DESC'), 'Article', 'getShows'));
        $this->assign('rankList', $rankList[3]['list']);

        // 广告列表
        $advertList = $this->apiReturnDeal(getApi(array('type' => '36,37', 'belong' => 1), 'Advert', 'getShows'));
        $this->assign('advertList', $advertList);

        $this->display();
    }

    // 详情页
    public function detail() {
        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . 'Detail.css'));
        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Detail.js'));

        // 获取信息
        $vo = $this->apiReturnDeal(getApi(array('art_id' => $this->current_param['art_id'], 'is_deal_result' => true), 'Article', 'shows'));
        $this->assign('vo', $vo);

        // 阅读数
        getApi(array('art_id' => $this->current_param['art_id']), 'Article', 'increase');

        // 相关文章
        $relationList = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 1, 'every_num' => 6, 'ca_id' => $vo['article']['ca_id'], 'order' => 'art_created DESC'), 'Article', 'getShows'));
        $this->assign('relationList', $relationList[3]['list']);

        // 头条推荐
        $recommend = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 1, 'every_num' => 3, 'art_position' => 1, 'order' => 'art_sort ASC,art_created DESC'), 'Article', 'getShows'));
        $this->assign('recommend', $recommend[3]['list']);

        // 热门排行
        $rankList = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 1, 'every_num' => 10, 'order' => 'art_hits DESC,art_created DESC'), 'Article', 'getShows'));
        $this->assign('rankList', $rankList[3]['list']);

        $model = reloadCache('model');
        $this->display($model[$vo['article']['m_id']]['m_name']);
    }

}