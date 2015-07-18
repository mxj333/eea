<?php
namespace Reagional\Controller;
class TemplateCacheController extends ReagionalController {
    public function index(){
        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . '.css'));

        $this->display();
    }

    public function edit(){
        
        foreach ($_POST['type'] as $val){
            switch ($val) {
                case 'Reagional' :
                    del_dir('./htm/Reagional/');
                    break;
            }
        }
        $this->success(L('THE_CACHE_WAS_SUCCESSFULLY'));
    }
}