<?php
namespace Reagional\Controller;
class ResourceImportController extends ReagionalController {
    public function index() {
        $this->assign('template_url', '/Uploads/Resource/import/resourceTemplate.xls');
        $this->assign('template_name', '资源文件模板');
        $this->assign('example_url', '/Uploads/Resource/import/resourceExample.xls');
        $this->assign('example_name', '资源示例模板');
        $this->display('Public/import');
    }

    public function add(){

        set_time_limit(0);

        if ($_FILES['file']['size'] > 0) {
            $fields['file'] = '@'.realpath($_FILES['file']['tmp_name']).";type=".$_FILES['file']['type'].";filename=".$_FILES['file']['name'];
        }
        
        $result = $this->apiReturnDeal(getApi($_POST, 'Resource', 'import', 'json', $fields));

        $this->show($result);
    }
}