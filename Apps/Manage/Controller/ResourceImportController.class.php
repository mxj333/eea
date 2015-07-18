<?php
namespace Manage\Controller;
class ResourceImportController extends ManageController {
    public function index() {
        $this->display();
    }

    public function insert(){

        $res = D('ResourceImport')->import($_FILES);
        if ($res === false) {
            $this->error(D('ResourceImport')->getError());
            exit;
        }

        if ($res) {
            $insert_id = D('Resource')->insertAll($res);
            if (!$insert_id) {
                $this->error(L('IMPORT') . L('FAILURE'));
                exit;
            }
        }

        $this->success(L('IMPORT') . L('SUCCESS'));
    }
}