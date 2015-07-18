<?php
namespace Reagional\Controller;
class ArticleShowsController extends ReagionalController {

    public function index() {
        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/Js/model/' . strtolower(CONTROLLER_NAME) . '.js'));

        // 显示字段
        $tplList['id'] = array('id' => 'art_id', 'percent' => '8', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'art_title', 'percent' => '30', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'ca_title', 'percent' => '30', 'title' => L('COLUMN'), 'class' => 'showContent');
        $tplList[] = array('id' => 'art_status', 'percent' => '20', 'title' => L('WETHER_TO_PUBLISH'));
        $tplList['action'] = array('id' => 'action', 'percent' => '6', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'shows', 'percent' => '6', 'title' => L('SHOWS'));

        // 检索器
        $search[] = array('title' => L('TITLE'), 'name' => 'art_title');
        $search[] = array('title' => '', 'inline' => 1, 'display' => 'none', 'name' => 'ca_id');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        $this->display('Article/index');
    }

    // 列表
    public function lists() {
        // 用户所属区域id
        //$_POST['re_id'] = session('re_id');
        // 结果自动处理
        $_POST['is_deal_result'] = true;
        $_POST['type'] = 4;

        // api 请求数据
        $data = $this->apiReturnDeal(getApi($_POST, 'Article', 'lists'));
        
        echo json_encode($data);
    }

    // 编辑操作
    public function shows() {

        // 验证
        $id = I('request.id');
        if (!$id) {
            $this->redirect('index');
        }

        $model = reloadCache('model');
        $res = $this->apiReturnDeal(getApi(array('art_id' => intval($id)), 'Article', 'shows'));
        $res['article']['art_content'] = stripFilter(htmlspecialchars_decode($res['article']['art_content']));

        // 赋值
        $this->assign('everyModelJs', intval(file_exists('.' . MPUBLIC_NAME . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Add.js')));
        $this->assign('attribute', $res['attribute']);
        $this->assign('article', $res['article']);
        $this->assign('articlePosition', C('ARTICLE_POSITION'));
        $this->assign('ca_id', $res['article']['ca_id']);
        $this->assign('p', intval($_REQUEST['p']));
        $this->assign('pic', $res['pic']);
        $this->assign('m_id', $res['article']['m_id']);
        $this->display('Article/' . $model[$res['article']['m_id']]['m_name']);
    }
}