<?php
namespace School\Controller;
class NoticeController extends BaseController {
    public function search() {
        set_time_limit(120);

        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . 'Search.css'));
        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Search.js'));

        $config['belong'] = 3;
        $config['page'] = $this->current_param['p'];
        $config['s_id'] = $this->sInfo['s_id'];
        $config['no_title'] = $this->current_param['no_title'];
        $config['no_starttime'] = time();
        $config['no_endtime'] = time();
        $config['order'] = 'no_starttime DESC,no_id DESC';
        $config['return_num'] = 20;
        $noticeList = $this->apiReturnDeal(getApi($config, 'Notice', 'lists'));
        $this->assign('noticeList', $noticeList);

        // 资源
        $today = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 3, 's_id' => $this->sInfo['s_id'], 'every_num' => 3, 'order' => 'art_created DESC'), 'Article', 'getShows'));
        $this->assign('today', $today[3]['list']);

        // 右侧资源
        $newList = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 3, 'every_num' => 7, 'is_page' => true, 'order' => 'res_created DESC,res_id DESC', 's_id' => $this->sInfo['s_id']), 'Resource', 'getShows'));
        $this->assign('newList', $newList);

        $this->display();
    }

    // 详情页
    public function detail() {
        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . 'Detail.css'));
        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Detail.js'));

        // 获取信息
        $vo = $this->apiReturnDeal(getApi(array('no_id' => $this->current_param['no_id'], 'is_deal_result' => true), 'Notice', 'shows'));
        $this->assign('vo', $vo);

        // 阅读数
        getApi(array('no_id' => $this->current_param['no_id']), 'Notice', 'increase');

        // 公告
        $config['belong'] = 3;
        $config['s_id'] = $this->sInfo['s_id'];
        $config['no_starttime'] = time();
        $config['no_endtime'] = time();
        $config['order'] = 'no_sort ASC,no_id DESC';
        $noticeList = $this->apiReturnDeal(getApi($config, 'Notice', 'lists'));
        $this->assign('noticeList', $noticeList);

        $this->assign('search_keywords_link', getUrlAddress(array('no_title' => 'keywords'), 'search', 'notice'));

        $this->display();
    }

}