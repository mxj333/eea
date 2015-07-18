<?php
namespace Reagional\Controller;
class DirectoryImportController extends ReagionalController {
    public function index() {
        $this->assign('template_url', '/Uploads/Directory/import/directoryTemplate.xls');
        $this->assign('template_name', '课文目录模板');
        $this->assign('example_url', '/Uploads/Directory/import/directoryExample.xls');
        $this->assign('example_name', '课文目录示例模板');
        $this->display('Public/import');
    }

    public function add(){
        set_time_limit(0);

        if ($_FILES['file']['size'] > 0) {
            $fields['file'] = '@'.realpath($_FILES['file']['tmp_name']).";type=".$_FILES['file']['type'].";filename=".$_FILES['file']['name'];
        }
        
        $_POST['re_id'] = session('re_id');
        $_POST['re_title'] = session('re_title');

        $result = $this->apiReturnDeal(getApi($_POST, 'Directory', 'import', 'json', $fields));

        $this->show($result);
    }
}