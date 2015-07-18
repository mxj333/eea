<?php
namespace Manage\Controller;
class MemberImportController extends ManageController {
    public function index() {
        
        D('MemberOnlineLog')->insert();
        $this->display();
    }

    public function insert(){

        // 返回入库数据
        $saveData = D('MemberImport')->import($_FILES, array('me_creator_table' => 'User'));
        if ($saveData === false) {
            $this->error(D('MemberImport')->getError());
        }

        // 入库
        $error_line = '';
        foreach ($saveData as $data) {
            $res = D('Member')->insert($data);
            if ($res === false) {
                $error_line .= ',' . $data['line_num'];
            }
        }

        if (!$error_line) {
            $this->success(L('IMPORT') . L('SUCCESS'));
        } else {
            $this->error('导入失败,错误行号:' . substr($error_line, 1));
        }
    }
}