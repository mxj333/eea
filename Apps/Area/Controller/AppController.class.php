<?php
namespace Area\Controller;
class AppController extends AreaController {
    public function index() {
        set_time_limit(120);

        $this->assign('area_title', '应用首页');

        // 推荐应用
        $recommend = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 2, 're_id' => $this->current_region_info['re_ids'], 'is_recommend' => 1, 'every_num' => 7), 'App', 'getShows'));
        $this->assign('recommend', $recommend[3]['list']);

        // 最新应用
        $new = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 2, 're_id' => $this->current_region_info['re_ids'], 'order' => 'a_created DESC,a_sort ASC', 'every_num' => 9), 'App', 'getShows'));
        $this->assign('new', $new[3]['list']);

        // 应用分类
        $category = $this->apiReturnDeal(getApi(array('type' => '1', 'belong' => 2, 're_id' => $this->current_region_info['re_ids'], 'type_num' => 3, 'every_num' => 2), 'App', 'getShows'));
        $this->assign('category', $category);

        // 更多分类
        $cateList = reloadCache('appCategory');
        $this->assign('cateList', $cateList);

        // 推荐排行
        $rank = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 2, 're_id' => $this->current_region_info['re_ids'], 'is_recommend' => 1, 'order' => 'a_hits DESC,a_sort DESC', 'every_num' => 10), 'App', 'getShows'));
        $this->assign('rank', $rank[3]['list']);

        // 广告列表
        $advertList = $this->apiReturnDeal(getApi(array('type' => '27,28,29', 'belong' => 2, 're_id' => $this->current_region_info['re_ids']), 'Advert', 'getShows'));
        $this->assign('advertList', $advertList);

        parent::index();
    }

    // 检索页面
    public function search() {
        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . 'Search.css'));
        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Search.js'));

        $category = reloadCache('appCategory');
        if ($this->current_param['ac_id']) {
            $nav = array('ac_id' => $this->current_param['ac_id'], 'ac_title' => $category[$this->current_param['ac_id']]);
        } else {
            $nav = array('ac_id' => 0, 'ac_title' => '总列表');
        }
        $this->assign('category', $nav);
        $this->assign('client_type', $this->current_param['type']);
        
        // 列表
        $alist = $this->apiReturnDeal(getApi(array('return_num' => 24, 'ac_id' => $this->current_param['ac_id'], 'a_title' => $this->current_param['a_title'], 'client_type' => $this->current_param['type']), 'App', 'lists'));
        $this->assign('alist', $alist);
        
        $this->display();
    }

    public function lists() {
        
        $p = I('page', 1, 'intval');
        $ac_id = I('ac_id', 0, 'intval');
        $keywords = I('keywords', '', 'strval');
        $alist = $this->apiReturnDeal(getApi(array('return_num' => 24, 'p' => $p, 'ac_id' => $ac_id, 'a_title' => $keywords, 'client_type' => $this->current_param['type']), 'App', 'lists'));

        $alist['list'] = $this->dealList($alist['list']);

        echo json_encode($alist);
    }

    private function dealList($data) {
        $html = '';
        foreach ($data as $info) {
            $html .= '<div class="app_item">
            <a href="' . getUrlAddress($info, 'detail', 'app') . '"><img width="60" border="0" height="60" src="' . $info['a_logo'] . '" title="' . $info['a_title'] . '" alt="' . $info['a_title'] . '"></a>
            <div class="itemDetail">
                <a href="' . getUrlAddress($info, 'detail', 'app') . '">' . getShortTitle($info['a_title'], 8) . '</a>
                <a href="javascript:void(0);" class="my_recommend">下载</a>
                <p>1235人推荐</p>
                <p class="star">
                    <img src="' . substr(MODULE_PATH, 1) . 'Public/' . C('DEFAULT_THEME') . '/Images/star1.png">
                    <img src="' . substr(MODULE_PATH, 1) . 'Public/' . C('DEFAULT_THEME') . '/Images/star1.png">
                    <img src="' . substr(MODULE_PATH, 1) . 'Public/' . C('DEFAULT_THEME') . '/Images/star1.png">
                    <img src="' . substr(MODULE_PATH, 1) . 'Public/' . C('DEFAULT_THEME') . '/Images/star1.png">
                    <img src="' . substr(MODULE_PATH, 1) . 'Public/' . C('DEFAULT_THEME') . '/Images/star1.png">
                    <span>' . intval($info['score']) . '分</span>
                </p>
            </div>
        </div>';
        }

        return $html;
    }

    public function detail() {
        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . 'Detail.css'));
        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Detail.js'));

        $this->assign('area_title', '应用详情');
        $this->assign('client_type', $this->current_param['type']);
        
        // 获取信息
        $vo = $this->apiReturnDeal(getApi(array('a_id' => $this->current_param['a_id'], 'is_deal_result' => true), 'App', 'shows'));
        $this->assign('vo', $vo);

        // 阅读数
        getApi(array('a_id' => $this->current_param['a_id']), 'App', 'increase');

        // 推荐排行
        $rank = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 2, 're_id' => $this->current_region_info['re_ids'], 'is_recommend' => 1, 'order' => 'a_hits DESC,a_sort DESC', 'every_num' => 10), 'App', 'getShows'));
        $this->assign('rank', $rank[3]['list']);

        // 广告列表
        $advertList = $this->apiReturnDeal(getApi(array('type' => '30', 'belong' => 2, 're_id' => $this->current_region_info['re_ids']), 'Advert', 'getShows'));
        $this->assign('advertList', $advertList);

        $this->display();
    }
}