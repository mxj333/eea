<?php
namespace Manage\Controller;
class TemplateCacheController extends ManageController {
    public function index(){
        $this->display();
    }

    public function edit(){
        
        foreach ($_POST['type'] as $val){
            switch ($val) {
                case 'Manage' :
                    del_dir('./htm/Manage/');
                    break;
            }
        }
        $this->success(L('THE_CACHE_WAS_SUCCESSFULLY'));
    }
}