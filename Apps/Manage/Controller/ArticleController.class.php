<?php
namespace Manage\Controller;
class ArticleController extends ManageController {

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
        $position = C('ARTICLE_POSITION');
        $position['first'] = $position[0];
        unset($position[0]);
        $search[] = array('title' => L('TITLE'), 'name' => 'art_title');
        $search[] = array('title' => L('POSITION'), 'name' => 'art_position', 'label' => 'select', 'data' => $position, 'inline' => true);
        $search[] = array('title' => L('STARTTIME'), 'name' => 'starttime', 'event' => 'onClick', 'eventValue' => "WdatePicker();");
        $search[] = array('title' => L('ENDTIME'), 'name' => 'endtime', 'event' => 'onClick', 'eventValue' => "WdatePicker();", 'inline' => true);
        $search[] = array('title' => '', 'inline' => 1, 'display' => 'none', 'name' => 'ca_id');

        // 工具
        $tools = array('add');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        $this->display();
    }

    // 列表
    public function lists() {
        if ($_POST['starttime']) {
            $_POST['starttime'] = strtotime($_POST['starttime']);
        }
        if ($_POST['endtime']) {
            $_POST['endtime'] = strtotime($_POST['endtime']);
        }
        echo json_encode(D(CONTROLLER_NAME)->lists($_POST));
    }

    // 新增操作
    public function add() {
        if (!$_POST) {
            $ca_id = intval(I('request.ca_id'));
            if (!$ca_id) {
                $this->redirect('index');
            }

            $m_id = D('Category')->getOne(array('where' => array('ca_id' => $ca_id), 'fields' => 'm_id'));
            $model = reloadCache('model');
            $attribute_config['where']['at_id'] = array('in', $model[$m_id]['m_list']);
            $attribute_config['fields'] = 'at_id,at_name,at_title,at_type,at_value';
            $attribute = D('Attribute')->getAll($attribute_config);

            $this->assign('everyModelJs', intval(file_exists('.' . MPUBLIC_NAME . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Add.js')));
            $this->assign('attribute', $attribute);
            $this->assign('ca_id', $ca_id);
            $this->assign('m_id', $m_id);
            $this->assign('articlePosition', C('ARTICLE_POSITION'));
            $this->display($model[$m_id]['m_name']);
        } else {
            $this->insert();
        }
    }

    public function insert() {

        // 是否有上传
        if ($_FILES['art_cover']['size'] > 0) {

            // 文件上传
            $config['exts'] = explode(',', strtolower(C('ALLOW_IMAGE_TYPE')));
            $config['savePath'] = C('ARTICLE_COVER_PATH');
            $config['autoSub'] = true;
            $config['subName'] = array('date', C('ARTICLE_SUBNAME_RULE'));

            $art_cover = parent::upload($_FILES['art_cover'], $config);
            $savepath = explode('/', $art_cover['savepath']);
            $_POST['art_cover_path'] = $savepath[1];
            $_POST['art_cover_name'] = $art_cover['savename'];
            $_POST['art_cover_ext'] = $art_cover['ext'];
        }

        $_POST['art_creator_table'] = 'User';
        $_POST['art_creator_id'] = $_POST['art_creator_id'] ? $_POST['art_creator_id'] : intval($_SESSION[C('USER_AUTH_KEY')]);

        $result = D('Article')->insert($_POST, 'User');
        if ($result === false) {
            $this->error(D('Article')->getError());
        }

        if (!$_POST['art_id']) {
            $_POST['art_id'] = $result;
        }

        $res_pic = D('Article')->uploadPic($_FILES['pic'], $_POST);
        if ($res_pic === false) {
            $this->error(D('Article')->getError());
        }
        $res_video = D('Article')->uploadVideo($_FILES['video'], $_POST);
        if ($res_video === false) {
            $this->error(D('Article')->getError());
        }

        D('AttributeRecord')->insert($_POST);
        
        if (C('ARTICLE_IS_DEFAULT_PUBLISHED')) {
            $publishConfig['where']['art_id'] = $_POST['art_id'];
            D('Article')->publishData(D('Article')->getOne($publishConfig));
        }

        $this->redirect(__CONTROLLER__ . '/index/ca_id/' . $_POST['ca_id']);
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
            $res = D('Article')->detail($id);
            $res['article']['art_content'] = stripFilter(htmlspecialchars_decode($res['article']['art_content']));
            $res['article']['art_cover'] = D('Article')->getCover($res['article']);

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

    public function delete() {
        $articleConfig['where']['art_id'] = array('in', $_REQUEST['id']);
        $articleConfig['fields'] = 'ca_id';
        $ca_id = D('Article')->getOne($articleConfig);

        D('Article')->statusUpdate($_REQUEST['id']);

        $this->redirect(__CONTROLLER__ . '/index/ca_id/' . $ca_id);
    }

    public function publish() {
        $config['art_status'] = 1;
        $config['art_published'] = time();
        $config['art_published_table'] = 'User';
        $config['art_publisher_id'] = intval($_SESSION[C('USER_AUTH_KEY')]);
        D('Article')->update($config, array('where' => array('art_id' => array('in', $_REQUEST['id']))));
        $articleConfig['where']['art_id'] = array('in', $_REQUEST['id']);
        $data = D('Article')->getAll($articleConfig);
        D('Article')->publishData($data);
        $this->redirect(__CONTROLLER__ . '/index/ca_id/' . $data[0]['ca_id']);
    }

    public function delFile($id) {
        $res = D('Article')->delFile(intval($id));
        if ($res !== false) {
            echo json_encode(array('status' => 1));
        } else {
            echo json_encode(array('status' => 0));
        }
    }
}