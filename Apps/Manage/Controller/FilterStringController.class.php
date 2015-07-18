<?php
namespace Manage\Controller;
class FilterStringController extends ManageController {
    public function index() {
        
        if ($_POST) {

            // 过滤方式
            $data['con_value'] = I('type', 1, 'intval');
            $config['where']['con_name'] = 'BLACK_THESAURUS_FILTER_TYPE';
            $res_type = D('Config')->update($data, $config);

            // 词库黑名单
            $content = strip_tags(I('content', '', 'strval'));
            $data['con_value'] = str_replace("\r\n", '', $content);
            $config['where']['con_name'] = 'THESAURUS_BLACK_LIST';
            $res_val = D('Config')->update($data, $config);
            if ($res_type !== false && $res_val !== false) {
                // 更新缓存
                reloadCache('config', true);
                $this->success(L('SUCCESS'), 'index');
            } else {

                $this->error(L('FAILURE'));
            }
        } else {

            $this->assign('type', C('BLACK_THESAURUS_FILTER_TYPE'));
            $this->assign('content', str_replace(',', ",\r\n", C('THESAURUS_BLACK_LIST')));
            $this->display();
        }
    }
}