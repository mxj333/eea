<?php
namespace School\Controller;
class StudentController extends BaseController {
    
    public function index() {
        
        // 广告列表
        $advertList = $this->apiReturnDeal(getApi(array('type' => '31', 'belong' => 3, 's_id' => $this->sInfo['s_id']), 'Advert', 'getShows'));
        $this->assign('advertList', $advertList);

        // 优秀学生
        $stuData['s_id'] = $this->sInfo['s_id'];
        $stuData['is_deal_result'] = true;
        $stuData['order'] = 'se_type ASC,se_sort ASC';
        $studentList = $this->apiReturnDeal(getApi($stuData, 'StudentExcellent', 'getShows'));
        $this->assign('studentList', $studentList);
        
        parent::index();
    }
}