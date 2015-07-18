<?php
namespace Schoolback\Controller;
class RecycleController extends SchoolbackController {

    public function resume() {
        $res = $this->apiReturnDeal(getApi(array('art_id' => strval(I('id'))), 'Recycle', 'resume'));
        $this->show($res);
    }

    public function delete() {
        $res = $this->apiReturnDeal(getApi(array('art_id' => strval(I('id'))), 'Recycle', 'del'));
        $this->show($res);
    }

    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'art_id', 'percent' => '8', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'art_title', 'percent' => '40', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'ca_title', 'percent' => '20', 'title' => L('COLUMN'), 'class' => 'showContent');
        $tplList['action'] = array('id' => 'action', 'percent' => '18', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '6', 'title' => L('SHOWS'));
        $action[] = array('id' => 'resume', 'percent' => '6', 'title' => L('RECOVER'));
        $action[] = array('id' => 'del', 'percent' => '6', 'title' => L('DELETE'));

        // 检索器
        $search[] = array('title' => L('TITLE'), 'name' => 'art_title');
        //$search[] = array('title' => '', 'inline' => 1, 'display' => 'none', 'name' => 'ca_id');

        // 工具
        $tools = array('resume', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    // 列表
    public function lists() {
        // 用户所属学校id
        $_POST['s_id'] = $_SESSION['s_id'];
        // 结果自动处理
        $_POST['is_deal_result'] = true;
        // 回收站
        $_POST['type'] = 3;

        // api 请求数据
        $data = $this->apiReturnDeal(getApi($_POST, 'Article', 'lists'));
        
        echo json_encode($data);
    }

    // 预览
    public function edit() {
        
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