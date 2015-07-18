<?php
namespace Manage\Controller;
class IpDenyListController extends ManageController {
    public function index() {
        
        if ($_POST) {

            // IP 黑名单内容
            $ip_list = I('ip_list', '', 'strval');
            
            $result = setIpDenyList($ip_list);
            if ($result['status']) {
                $this->success(L('SUCCESS'));
            } else {
                $this->error($result['info']);
            }
        } else {

            $this->assign('ip_list', implode(";\r\n", getIpDenyList()));
            $this->display();
        }
    }
}