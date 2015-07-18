<?php
namespace Manage\Controller;
class DirectoryImportController extends ManageController {
    public function index() {
        $this->display();
    }

    public function insert(){

        $res = D('DirectoryImport')->import($_FILES);
        if ($res === false) {
            $this->error(D('DirectoryImport')->getError());
            exit;
        }

        $this->success(L('IMPORT') . L('SUCCESS'));
    }
}