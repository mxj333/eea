<?php
namespace Home\Controller;
class IndexController extends BaseController {
    public function index() {
        set_time_limit(120);

        // 公告
        $notices = $this->apiReturnDeal(getApi(array('belong' => 1, 'every_num' => 5, 'order' => 'no_sort ASC,no_starttime DESC,no_id DESC'), 'Notice', 'getShows'));
        $this->assign('notices', $notices);

        // 资源列文件
        $resource_new  = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 1, 'recommend' => 1, 'every_num' => 6, 'is_page' => true, 'order' => array('res_created DESC,res_id DESC')), 'Resource', 'getShows'));
        $resource_hot  = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 1, 'recommend' => 1, 'every_num' => 6, 'is_page' => true, 'order' => array('res_hits DESC,res_id DESC')), 'Resource', 'getShows'));
        $this->assign('resource_new', $resource_new);
        $this->assign('resource_hot', $resource_hot);
        
        // 资源统计
        $resource_list = getApi(array('re_id' => 1, 'way' => 'region'), 'Statistics', 'getResource');
        // 按省份统计
        unset($resource_list['中国']);
        $this->assign('resourceStatistics', $resource_list);

        // 应用超市
        $appCategory = $this->apiReturnDeal(getApi(array('fields' => 'ac_id,ac_id,ac_title', 'return_num' => 7), 'App', 'category'));
        $this->assign('appCategory', $appCategory);
        $appList = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 1, 'every_num' => 14), 'App', 'getShows'));
        $this->assign('appList', $appList);

        // 教育资讯
        $cateConfig['return_num'] = 7;
        $cateConfig['is_api'] = true;
        $cateConfig['ca_status'] = 1;
        $cateConfig['ca_level'] = 0;
        $category = $this->apiReturnDeal(getApi($cateConfig, 'Category', 'lists'));
        $this->assign('category', $category['list']);
        $articleList = $this->apiReturnDeal(getApi(array('type' => 3, 'belong' => 1, 'every_num' => 12, 'order' => 'art_created DESC'), 'Article', 'getShows'));
        $this->assign('articleList', $articleList);

        // 学校展示
        $schoolList = $this->apiReturnDeal(getApi(array('type' => '1', 'belong' => 1, 'every_num' => 10, 'order_by_resource_num' => true), 'School', 'getShows'));
        $this->assign('schoolList', $schoolList);

        // 空间
        $memberList = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 1, 'every_num' => 9, 'order' => 'me_hits DESC'), 'Member', 'getShows'));
        $this->assign('memberList', $memberList);

        // 广告列表
        $advertList = $this->apiReturnDeal(getApi(array('type' => '33', 'belong' => 1), 'Advert', 'getShows'));
        $this->assign('advertList', $advertList);

        parent::index();
    }
}