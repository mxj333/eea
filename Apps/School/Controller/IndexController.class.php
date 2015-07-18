<?php
namespace School\Controller;
class IndexController extends BaseController {
    public function index() {
        set_time_limit(120);
        
        // 广告列表
        $advertList = $this->apiReturnDeal(getApi(array('type' => '7', 'belong' => 3, 's_id' => $this->sInfo['s_id']), 'Advert', 'getShows'));
        
        // 整理广告数据
        $advert = array();
        foreach ($advertList[7]['list'] as $advValue) {
            $advert[] = $advValue;
        }
        $advertList[7]['list'] = json_encode($advert);
        $this->assign('advertList', $advertList);

        // 资讯
        $articleTime = $this->apiReturnDeal(getApi(array('belong' => 3, 'type' => 3, 'every_num' => 4, 's_id' => $this->sInfo['s_id']), 'Article', 'getShows'));
        $this->assign('articleTime', $articleTime);
        $articleHot = $this->apiReturnDeal(getApi(array('belong' => 3, 'type' => 3, 'every_num' => 4, 'order' => 'art_hits DESC,art_id DESC', 's_id' => $this->sInfo['s_id']), 'Article', 'getShows'));
        $this->assign('articleHot', $articleHot);

        // 资源列文件
        $resource_new  = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 3, 's_id'=> $this->sInfo['s_id'], 'every_num' => 8, 'is_page' => true, 'order' => array('res_created DESC,res_id DESC')), 'Resource', 'getShows'));
        $resource_hot  = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 3, 's_id'=> $this->sInfo['s_id'], 'every_num' => 8, 'is_page' => true, 'order' => array('res_hits DESC,res_id DESC')), 'Resource', 'getShows'));
        $this->assign('resource_new', $resource_new);
        $this->assign('resource_hot', $resource_hot);

        // 班级
        $classList = $this->apiReturnDeal(getApi(array('belong' => 3, 'every_num' => 5, 's_id' => $this->sInfo['s_id']), 'Class', 'excellent'));
        $this->assign('classList', $classList);

        // 教师
        $teacherList = $this->apiReturnDeal(getApi(array('belong' => 3, 'every_num' => 8, 's_id' => $this->sInfo['s_id']), 'Member', 'excellent'));
        $this->assign('teacherList', $teacherList);

        // 学生
        $studentList = $this->apiReturnDeal(getApi(array('type' => 2, 'belong' => 3, 'every_num' => 5, 'size' => '_b', 's_id' => $this->sInfo['s_id']), 'Member', 'excellent'));
        $this->assign('studentList', $studentList);

        //公告 Notice
        $notices = $this->apiReturnDeal(getApi(array('belong' => 3, 'every_num' => 8, 's_id' => $this->sInfo['s_id'], 'order' => 'no_sort ASC,no_starttime DESC,no_id DESC'), 'Notice', 'getShows'));
        $this->assign('notices', $notices);

        parent::index();
    }
}