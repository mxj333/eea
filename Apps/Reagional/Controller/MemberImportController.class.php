<?php
namespace Reagional\Controller;
class MemberImportController extends ReagionalController {
    public function index() {
        $this->assign('template_url', '/Uploads/Member/import/memberTemplate.xls');
        $this->assign('template_name', '用户模板');
        $this->assign('example_url', '/Uploads/Member/import/memberExample.xls');
        $this->assign('example_name', '用户示例模板');
        $this->display('Public/import');
    }

    public function add(){

        set_time_limit(0);

        if ($_FILES['file']['size'] > 0) {
            $fields['file'] = '@'.realpath($_FILES['file']['tmp_name']).";type=".$_FILES['file']['type'].";filename=".$_FILES['file']['name'];
        }
        
        // 默认值
        $_POST['md_register_ip'] = get_client_ip();

        $result = $this->apiReturnDeal(getApi($_POST, 'Member', 'import', 'json', $fields));

        $this->show($result);
    }
}