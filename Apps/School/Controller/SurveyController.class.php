<?php
namespace School\Controller;
class SurveyController extends BaseController {
    // 简介
    public function index() {
        
        parent::index();
    }

    // 领导
    public function leader() {
        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . 'Leader.css'));
        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Leader.js'));

        $data['s_id'] = $this->sInfo['s_id'];
        $data['sl_status'] = 1;
        $data['order'] = 'sl_sort DESC';
        $data['is_deal_result'] = true;
        $leaders = $this->apiReturnDeal(getApi($data, 'SchoolLeader', 'lists'));
        $list = array();
        foreach ($leaders['list'] as $leader) {
            if ($list[$leader['sl_type']]) {
                $list[$leader['sl_type']] .= '<br/>' . $leader['me_nickname'];
            } else {
                $list[$leader['sl_type']] = $leader['me_nickname'];
            }
        }
        $this->assign('list', $list);

        $this->display();
    }

    // 组织架构
    public function organization() {
        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . 'Organization.css'));
        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Organization.js'));

        $data['s_id'] = $this->sInfo['s_id'];
        $data['so_status'] = 1;
        $data['order'] = 'so_sort DESC';
        $organizations = $this->apiReturnDeal(getApi($data, 'SchoolOrganization', 'lists'));
        $list = array();
        foreach ($organizations['list'] as $organization) {
            $list[$organization['so_type']][] = $organization['so_title'];
        }
        $this->assign('list', $list);

        $this->display();
    }

    // 组织架构
    public function shows() {
        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . 'Shows.css'));
        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Shows.js'));

        $data['s_id'] = $this->sInfo['s_id'];
        $images = $this->apiReturnDeal(getApi($data, 'School', 'showPic'));
        $this->assign('images', $images);

        $this->display();
    }
}