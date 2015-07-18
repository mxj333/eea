<?php
namespace Schoolback\Controller;
use Think\Controller;
class ArticleController extends SchoolbackController {
    public function index() {

        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/Js/model/' . strtolower(CONTROLLER_NAME) . '.js'));

        // 显示字段
        $tplList['id'] = array('id' => 'art_id', 'percent' => '8', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'art_title', 'percent' => '30', 'title' => L('TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'ca_title', 'percent' => '20', 'title' => L('COLUMN'), 'class' => 'showContent');
        $tplList[] = array('id' => 'art_status', 'percent' => '10', 'title' => L('WETHER_TO_PUBLISH'));
        $tplList['action'] = array('id' => 'action', 'percent' => '18', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'publish', 'percent' => '6', 'title' => L('PUBLISH'));
        $action[] = array('id' => 'edit', 'percent' => '6', 'title' => L('EDIT'));
        $action[] = array('id' => 'del', 'percent' => '6', 'title' => L('DELETE'));

        // 检索器
        $search[] = array('title' => L('TITLE'), 'name' => 'art_title');
        $search[] = array('title' => '', 'inline' => 1, 'display' => 'none', 'name' => 'ca_id');

        // 工具
        $tools = array('add');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        $this->display();
    } 
        // 新增操作
    public function add() {

        if (!$_POST) {
            $ca_id = intval(I('request.ca_id'));

            if (!$ca_id) {
                $this->redirect('index');
            }

            // 栏目类型
            $info = $this->apiReturnDeal(getApi(array('ca_id' => $ca_id), 'Category', 'shows'));
            $model = reloadCache('model');
            // 文章属性
            if ($model[$info['m_id']]['m_list']) {
                $attribute = $this->apiReturnDeal(getApi(array('at_id' => $model[$info['m_id']]['m_list']), 'Article', 'getRelation'));
            }

            $this->assign('everyModelJs', intval(file_exists('.' . MPUBLIC_NAME . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Add.js')));
            $this->assign('attribute', $attribute);
            $this->assign('ca_id', $ca_id);
            $this->assign('m_id', $info['m_id']);
            $this->assign('articlePosition', C('ARTICLE_POSITION'));
            $this->display($model[$info['m_id']]['m_name']);
        } else {
            $this->insert();
        }
    }

    public function insert() {

        // 封面图
        if ($_FILES['art_cover']['size'] > 0) {
            $fields['art_cover'] = '@'.realpath($_FILES['art_cover']['tmp_name']).";type=".$_FILES['art_cover']['type'].";filename=".$_FILES['art_cover']['name'];
        }

        // 图片
        if ($_FILES['pic']) {
            foreach ($_FILES['pic']['name'] as $pic_key => $pic_val) {
                if ($_FILES['pic']['size'][$pic_key] > 0) {
                    $fields['pic['.$pic_key.']'] = '@'.realpath($_FILES['pic']['tmp_name'][$pic_key]).";type=".$_FILES['pic']['type'][$pic_key].";filename=".$pic_val;
                }
            }

        }

        // 视频
        if ($_FILES['video']['size'] > 0) {
            $fields['video'] = '@'.realpath($_FILES['video']['tmp_name']).";type=".$_FILES['video']['type'].";filename=".$_FILES['video']['name'];
        }

        $_POST['s_id'] = session('s_id');

        $apiFunction = intval($_POST['art_id']) ? 'edit' : 'add';
    
        $result = $this->apiReturnDeal(getApi($_POST, 'Article', $apiFunction, 'json', $fields));
        if ($result['status']) {
            $this->redirect(__CONTROLLER__ . '/index/ca_id/' . $_POST['ca_id']);
        } else {
            $this->error($result['info']);
        }
    }
    
    // 编辑操作
    public function edit() {

        if (!$_POST) {
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
            $this->display($model[$res['article']['m_id']]['m_name']);
        } else {
            $this->insert();
        }
    }

    // 删除文件
    public function delFile($id) {
        
        $res = $this->apiReturnDeal(getApi(array('f_id' => intval($id)), 'Article', 'forbid'));
        echo json_encode($res);
    }
    
    public function delete() {

        $res = $this->apiReturnDeal(getApi(array('art_id' => strval(I('id'))), 'Article', 'del'));
        if (!$res['status']) {
            $this->error($res['info']);
        } else {
            $this->redirect(__CONTROLLER__ . '/index/ca_id/' . $res['res_value']);
        }
    }

    public function publish() {

        $res = $this->apiReturnDeal(getApi(array('art_id' => strval(I('id'))), 'Article', 'publish'));
        if (!$res['status']) {
            $this->error($res['info']);
        } else {
            $this->redirect(__CONTROLLER__ . '/index/ca_id/' . $res['res_value']);
        }
    }
   
}