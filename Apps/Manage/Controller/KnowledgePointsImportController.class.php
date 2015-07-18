<?php
namespace Manage\Controller;
class KnowledgePointsImportController extends ManageController {
    public function index() {
        $this->display();
    }

    public function insert(){

        $res = D('KnowledgePointsImport')->import($_FILES);
        $errorMsg = D('KnowledgePointsImport')->getError();
        if ($res && !$errorMsg) {
            $this->success(L('IMPORT') . L('SUCCESS'));
        } else {
            $this->error($errorMsg);
        }
    }
}