<?php
namespace Manage\Controller;
class CacheController extends ManageController {
    public function index(){
        $this->display();
    }

    public function edit(){
        foreach ($_POST['type'] as $val){
            switch ($val) {
                case 'config' :
                    del_dir('./Runtime/Data/');
                    break;
                case 'field' :
                    del_dir('./Runtime/Data/_fields');
                    break;
                case 'template' :
                    del_dir('./Runtime/Cache/');
                    break;
                case 'html' :
                    del_dir('.htm/');
                    break;
            }
        }
        $this->success(L('THE_CACHE_WAS_SUCCESSFULLY'));
    }
}