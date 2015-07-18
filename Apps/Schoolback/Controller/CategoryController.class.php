<?php
namespace Schoolback\Controller;
use Think\Controller;
class CategoryController extends SchoolbackController {
    public function shows() {

        $s_id = session('s_id');
        $type = strtolower(I('type'));
        if ($type == 'articleshows') {
            // 展示上级
            $config['re_id'] = session('s_re_id');
            $config['s_id'] = 0;
        } else {
            // 展示自身
            $config['s_id'] = $s_id;
        }
        $config['fields'] = 'ca_id,ca_pid,ca_level,ca_title';
        $config['order'] = 'ca_sort ASC';
        $config['is_page'] = false;

        $cate = $this->apiReturnDeal(getApi($config, 'Category', 'lists'));

        header("Content-Type:text/xml; charset=utf-8");
        $xml  = '<?xml version="1.0" encoding="utf-8" ?>'."\n";
        $xml .= '<tree caption="栏目" id="0">'."\n";
        $xml .= html_entity_decode(toTree(tree($cate['list'], 'ca_id', 'ca_pid', 'list'), 'ca_id', 'ca_pid', 'ca_level', 'ca_title', 'list'));
        $xml .= '</tree>';
        exit($xml);
    }

    public function index() {
            // 显示字段
            $tplList['id'] = array('id' => 'ca_id', 'percent' => '8', 'title' => L('NUMBER'));
            $tplList[] = array('id' => 'ca_title', 'percent' => '20', 'title' => L('CHINESE_NAME'), 'class' => 'showContent');
            $tplList[] = array('id' => 'ca_name', 'percent' => '20', 'title' => L('ENGLISH_NAME'), 'class' => 'showContent');
            $tplList[] = array('id' => 'm_title', 'percent' => '10', 'title' => L('MODEL'), 'class' => 'showContent', 'data' => reloadCache('model'));
            $tplList[] = array('id' => 'ca_level', 'percent' => '6', 'title' => L('HIERARCHY'));
            $tplList[] = array('id' => 'ca_status', 'percent' => '12', 'title' => L('STATUS'));
            $tplList['action'] = array('id' => 'action', 'percent' => '16', 'title' => L('OPERATION'));

            // 操作
            $action[] = array('id' => 'edit', 'percent' => '8', 'title' => L('EDIT'));
            $action[] = array('id' => 'child', 'percent' => '8', 'title' => L('SUB_SECTION'));

            // 检索器
            $search[] = array('title' => L('CHINESE_NAME'), 'name' => 'ca_title');
            $search[] = array('title' => '', 'inline' => 1, 'display' => 'none', 'name' => 'ca_pid');

            // 工具
            $tools = array('add', 'edit', 'return');

            $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
            parent::index();
    }

    public function addTpl($title = '', $result = array()) {

        $id = intval($_GET['ca_id']);
        if (isset($_GET['ca_id'])) {
            $result['ca_pid'] = $id;
            $result['ca_level'] = 0;
        }
        if ($id) {
            $categoryConfig['ca_id'] = $id;
            $categoryConfig['fields'] = 'ca_level';
            $category = $this->apiReturnDeal(getApi($categoryConfig, 'Category', 'shows'));
            $result['ca_level'] = intval($category['ca_level']) + 1;
        }

        $model = reloadCache('model');
        foreach ($model as $value) {
            $modelData[$value['m_id']] = $value['m_title'];
        }

        // 默认值
        $result['s_id'] = session('s_id');

        // 显示字段
        $addList[] = array('title' => L('CHINESE_NAME'), 'name' => 'ca_title', 'class' => 'w460', 'require' => true);
        $addList[] = array('title' => L('ENGLISH_NAME'), 'name' => 'ca_name', 'class' => 'w460', 'require' => true);
        $addList[] = array('title' => L('SORT'), 'name' => 'ca_sort', 'class' => 'w460');
        $addList[] = array('title' => L('MODEL'), 'name' => 'm_id', 'label' => 'select', 'data' => $modelData);
        $addList[] = array('title' => L('HOME_PAGE_TEMPLATE'), 'name' => 'ca_tpl_index', 'class' => 'w460');
        $addList[] = array('title' => L('DETAILS_TEMPLATE'), 'name' => 'ca_tpl_detail', 'class' => 'w460');
        $addList[] = array('title' => L('JUMP_AND_LINK'), 'name' => 'ca_url', 'class' => 'w460');
        $addList[] = array('title' => L('KEYWORD'), 'name' => 'ca_keywords', 'label' => 'textarea');
        $addList[] = array('title' => L('DESCRIPTION'), 'name' => 'ca_description', 'label' => 'textarea');
        $addList[] = array('title' => L('SHOW'), 'name' => 'ca_is_show', 'label' => 'select', 'data' => array(1 => L('YES'), 9 => L('NO')));
        $addList[] = array('title' => L('STATUS'), 'name' => 'ca_status', 'label' => 'select', 'data' => array(1 => L('ENABLE'), 9 => L('DISABLE')));
        $addList[] = array('title' => '', 'name' => 'ca_pid', 'type' => 'hidden');
        $addList[] = array('title' => '', 'name' => 'ca_level', 'type' => 'hidden');
        $addList[] = array('title' => '', 'name' => 's_id', 'type' => 'hidden');

        return generationAddTpl($addList, 'ca_id', $title, $result);
    }

    public function edit() {

        parent::edit('ca_id');
    }

    public function insert() {

        $apiFunction = intval($_POST['ca_id']) ? 'edit' : 'add';

        $result['s_id'] = session('s_id');

        // 处理接口返回信息
        $res = $this->apiReturnDeal(getApi($_POST, 'Category', $apiFunction));

        $this->show($res, '', __CONTROLLER__ . '/index/ca_pid/' . $_POST['ca_pid']);
    }

}
?>