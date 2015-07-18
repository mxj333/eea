<?php
namespace Area\Controller;
class IndexController extends AreaController {
    public function index() {
        set_time_limit(120);

        $this->assign('area_title', $this->current_region_info['re_title']);

        // 资讯栏目
        $cateConfig['return_num'] = 7;
        $cateConfig['is_api'] = true;
        $cateConfig['re_id'] = $this->current_region_info['re_ids'];
        $cateConfig['ca_status'] = 1;
        $category = $this->apiReturnDeal(getApi($cateConfig, 'Category', 'lists'));
        $this->assign('category', $category);

        // 资源使用类型
        $resourceCategory = $this->apiReturnDeal(getApi(array('return_num' => 7, 'fields' => 'rc_id,rc_id,rc_title', 'is_api' => true), 'Resource', 'category'));
        $this->assign('resourceCategory', $resourceCategory);

        // 应用类型
        $appCategory = $this->apiReturnDeal(getApi(array('return_num' => 5, 'fields' => 'ac_id,ac_id,ac_title', 'is_api' => true), 'App', 'category'));
        $this->assign('appCategory', $appCategory);

        // 资源列文件
        $resourceList = $this->apiReturnDeal(getApi(array('type' => '1,2', 'belong' => 2, 'type_num' => 7, 'every_num' => 12, 're_id' => $this->current_region_info['re_ids']), 'Resource', 'getShows'));
        $this->assign('resourceList', $resourceList);

        // 资讯列文件
        $articleList = $this->apiReturnDeal(getApi(array('type' => '1,2', 'belong' => 2, 'type_num' => 7, 'every_num' => array('1' => 20, '2' => 6), 'ca_level' => 0, 're_id' => $this->current_region_info['re_ids']), 'Article', 'getShows'));
        $this->assign('articleList', $articleList);

        // 应用列表
        $appList = $this->apiReturnDeal(getApi(array('type' => '1', 'belong' => 2, 'aty_id' => '0,1', 'type_num' => 6, 'every_num' => 12), 'App', 'getShows'));
        $this->assign('appList', $appList);

        // 用户列表
        $memberList = $this->apiReturnDeal(getApi(array('type' => '1', 'belong' => 2, 'type_num' => 1, 'every_num' => 12, 're_id' => $this->current_region_info['re_ids']), 'Member', 'getShows'));
        $this->assign('memberList', $memberList);

        // 学校列表
        $schoolList = $this->apiReturnDeal(getApi(array('type' => '1', 'belong' => 2, 'type_num' => 1, 'every_num' => 9, 're_id' => $this->current_region_info['re_ids']), 'School', 'getShows'));
        $this->assign('schoolList', $schoolList);

        // 广告列表
        $advertList = $this->apiReturnDeal(getApi(array('type' => '1,2,3,4,5,6', 'belong' => 2, 're_id' => $this->current_region_info['re_ids']), 'Advert', 'getShows'));
        $this->assign('advertList', $advertList);

        // 通知列表
        $noticeList = $this->apiReturnDeal(getApi(array('belong' => 2, 're_id' => $this->current_region_info['re_ids']), 'Notice', 'getShows'));
        $this->assign('noticeList', $noticeList);

        parent::index();
    }

    // 动态
    public function getMessage() {
        // 动态列表
        //$messageList = $this->apiReturnDeal(getApi(array('belong' => 2), 'Message', 'getShows'));
        $messageList = array(
            array('mes_url' => 'javascript:void(0)', 'mes_created' => '30秒前', 'mes_content' => '李老师在学问系统中回答了小名的问题'),
            array('mes_url' => 'javascript:void(0)', 'mes_created' => '1分钟前', 'mes_content' => '李老师在学问系统中回答了小名的问题'),
            array('mes_url' => 'javascript:void(0)', 'mes_created' => '10分钟前', 'mes_content' => '朱莉在平台发布了一年级语文...'),
            array('mes_url' => 'javascript:void(0)', 'mes_created' => '1小时前', 'mes_content' => '李老师在学问系统中回答了小名的问题'),
            array('mes_url' => 'javascript:void(0)', 'mes_created' => '5小时前', 'mes_content' => '李老师在学问系统中回答了小名的问题'),
            array('mes_url' => 'javascript:void(0)', 'mes_created' => '1天前', 'mes_content' => '李老师在学问系统中回答了小名的问题'),
            array('mes_url' => 'javascript:void(0)', 'mes_created' => '1天前', 'mes_content' => '李老师在学问系统中回答了小名的问题'),
            array('mes_url' => 'javascript:void(0)', 'mes_created' => '2天前', 'mes_content' => '李老师在学问系统中回答了小名的问题'),
            array('mes_url' => 'javascript:void(0)', 'mes_created' => '2天前', 'mes_content' => '朱莉在平台发布了一年级语文《沁园春...'),
            array('mes_url' => 'javascript:void(0)', 'mes_created' => '2天前', 'mes_content' => '朱莉在平台发布了一年级语文《沁园春...'),
        );
        echo json_encode($messageList);
    }

    // 广告
    public function getAdvert() {

        $adv_type = I('type', 5, 'intval');
        // 广告列表
        $advertList = $this->apiReturnDeal(getApi(array('type' => $adv_type, 'belong' => 2, 're_id' => $this->current_region_info['re_ids']), 'Advert', 'getShows'));
        echo json_encode($advertList[$adv_type]['list']);
    }
}