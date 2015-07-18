<?php
namespace School\Controller;
class ArticleController extends BaseController {
    public function search() {
        set_time_limit(120);

        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . 'Search.css'));
        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Search.js'));

        // 聚焦
        $focus = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 3, 's_id' => $this->sInfo['s_id'], 'every_num' => 10, 'order' => 'art_hits DESC,art_sort DESC,art_created DESC'), 'Article', 'getShows'));
        $this->assign('focus', $focus[3]['list']);

        // 栏目
        $cateList = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 3, 's_id' => $this->sInfo['s_id'], 'return_num' => 8, 'ca_level' => 0), 'Category', 'lists'));
        $this->assign('cateList', $cateList['list']);
        // 当前栏目
        $cate_id = $this->current_param['ca_id'] ? $this->current_param['ca_id'] : $cateList['list'][0]['ca_id'];
        $this->assign('cate_id', $cate_id);

        // 列表
        $artList = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 3, 'ca_id' => $cate_id, 'page' => $this->current_param['page'], 's_id' => $this->sInfo['s_id'], 'every_num' => 10, 'order' => 'art_created DESC'), 'Article', 'getShows'));
        $this->assign('artList', $artList[3]['list']);

        // 今日
        $today = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 3, 's_id' => $this->sInfo['s_id'], 'every_num' => 3, 'order' => 'art_created DESC'), 'Article', 'getShows'));
        $this->assign('today', $today[3]['list']);

        // 图说
        $picList = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 3, 's_id' => $this->sInfo['s_id'], 'every_num' => 5, 'm_id' => 2, 'order' => 'art_created DESC'), 'Article', 'getShows'));
        $pics = array_chunk($picList[3]['list'], 2, true);
        $this->assign('picList', $pics[1]);
        $picShows = array();
        foreach ($pics[0] as $picInfo) {
            $picShows[] = array(
                'adv_url' => getUrlAddress($picInfo, 'detail', 'article'),
                'adv_image' => $picInfo['art_cover_b'],
                'adv_title' => $picInfo['art_title'],
                'adv_reminds' => $picInfo['art_summary']
            );
        }
        $this->assign('picShows', json_encode($picShows));

        // 视角
        $videoList = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 3, 's_id' => $this->sInfo['s_id'], 'every_num' => 5, 'm_id' => 3, 'order' => 'art_created DESC'), 'Article', 'getShows'));
        $this->assign('videoList', $videoList[3]['list']);

        // 广告列表
        $advertList = $this->apiReturnDeal(getApi(array('type' => '32', 'belong' => 3, 's_id' => $this->sInfo['s_id']), 'Advert', 'getShows'));
        $advertList[32]['list'] = json_encode($advertList[32]['list']);
        $this->assign('advertList', $advertList);

        $this->display();
    }

    // 下拉请求的数据
    public function lists() {
        $ca_id = I('request.ca_id', 0, 'intval');
        $keywords = I('request.keywords', '', 'strval');
        $page = I('request.page', 1, 'intval');

        // 列表
        $reData['art_designated_published'] = time();
        $reData['art_is_deleted'] = 9;
        $reData['art_status'] = 1;
        $reData['ca_is_show'] = 1;
        $reData['s_id'] = $this->sInfo['s_id'];
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

    // 详情页
    public function detail() {
        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . 'Detail.css'));
        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Detail.js'));

        // 获取信息
        $vo = $this->apiReturnDeal(getApi(array('art_id' => $this->current_param['art_id'], 'is_deal_result' => true), 'Article', 'shows'));
        $this->assign('vo', $vo);

        // 阅读数
        getApi(array('art_id' => $this->current_param['art_id']), 'Article', 'increase');

        // 图说
        $picList = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 3, 's_id' => $this->sInfo['s_id'], 'every_num' => 5, 'm_id' => 2, 'order' => 'art_created DESC'), 'Article', 'getShows'));
        $pics = array_chunk($picList[3]['list'], 2, true);
        $this->assign('picList', $pics[1]);
        $picShows = array();
        foreach ($pics[0] as $picInfo) {
            $picShows[] = array(
                'adv_url' => getUrlAddress($picInfo, 'detail', 'article'),
                'adv_image' => $picInfo['art_cover_b'],
                'adv_title' => $picInfo['art_title'],
                'adv_reminds' => $picInfo['art_summary']
            );
        }
        $this->assign('picShows', json_encode($picShows));

        // 视角
        $videoList = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 3, 's_id' => $this->sInfo['s_id'], 'every_num' => 5, 'm_id' => 3, 'order' => 'art_created DESC'), 'Article', 'getShows'));
        $this->assign('videoList', $videoList[3]['list']);

        $model = reloadCache('model');
        $this->display($model[$vo['article']['m_id']]['m_name']);
    }

}