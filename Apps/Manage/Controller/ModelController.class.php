<?php
namespace Manage\Controller;
class ModelController extends ManageController {

    public function insert() {

        $_POST['m_list'] = implode(',', $_POST['m_list']);
        parent::insert();
        reloadCache('model', true);
    }

    public function cache() {

        $id = strval(I('request.id'));

        if (!$id) {
            $this->error('请选择要生成缓存表的类型');
        }

        D('Model')->cache($id);
        $this->success('模型表缓存成功！');
    }

    // 同步数据
    public function sync() {

        // 记录时间
        G('1');

        // 接收参数
        $id = strval(I('request.id'));
        if (!$id) {
            $this->error(L('PlEASE_SELECT_THE_TYPE_OF_SYNCHRONOUS_DATA'));
        }

        $articleConfig['where']['art_status'] = 1;
        $count = D('Article')->total($articleConfig);
        // 每次同步条数
        $maxRow = 100;

        // 同步次数
        $times = ceil($count / $maxRow);

        // 数据同步是否完成
        if (empty($_GET['t'])) {
            $_GET['t'] = 1;
        }

        if ($_GET['t'] == 1) {
            // 类型条件
            $model = reloadCache('model');

            // 清空要同步的缓存表
            $ids = explode(',', $id);
            $sql = '';
            foreach ($ids as $value) {
                $sql .= 'truncate ' . C('DB_PREFIX') . 'cache_' . $model[$value]['m_name'] . ';';
            }
            D('Model', 'Model')->querySql($sql);
        }

        if($times == 0 || $_GET['t'] > $times) {
            $this->success(L('DATA_SYNCHRONIZATION_IS_COMPLETE'), __CONTROLLER__);
            exit;
        }

        $config['limit'] = $_GET['t'].','.$maxRow;
        $list = D('Article')->getAll($config);;

        if ($list) {
            D('Article')->publishData($list);
        }

        $url = __CONTROLLER__ . '/sync/id/' . $id . '/t/' . ($_GET['t'] + 1);
        $this->success(L('DATA_SYNCHRONIZATION') . $_GET['t'] . '/' . $times . '！' . L('TIME_CONSUMING') . '：' . G('1','2') . 's', $url);
    }

    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'm_id', 'percent' => '8', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'm_title', 'percent' => '20', 'title' => L('CHINESE_NAME'), 'class' => 'showContent');
        $tplList[] = array('id' => 'm_name', 'percent' => '20', 'title' => L('ENGLISH_NAME'), 'class' => 'showContent');
        $tplList[] = array('id' => 'm_list', 'percent' => '25', 'title' => L('ATTRIBUTE_LIST'), 'class' => 'showContent');
        $tplList['action'] = array('id' => 'action', 'percent' => '18', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '6', 'title' => L('EDIT'));
        $action[] = array('id' => 'cache', 'percent' => '6', 'title' => L('CACHE'));
        $action[] = array('id' => 'sync', 'percent' => '6', 'title' => L('SYNC'));

        // 检索器
        $search[] = array('title' => L('CHINESE_NAME'), 'type' => 'input', 'name' => 'm_title');

        // 工具
        $tools = array('add', 'edit', 'del');

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {

        $attributeConfig['fields'] = 'at_id,at_title';
        $attribute = D('Attribute')->getAll($attributeConfig);
        // 显示字段
        $addList[] = array('title' => L('CHINESE_NAME'), 'name' => 'm_title', 'class' => 'w460');
        $addList[] = array('title' => L('ENGLISH_NAME'), 'name' => 'm_name', 'class' => 'w460');
        $addList[] = array('title' => L('ATTRIBUTE_LIST'), 'name' => 'm_list[]', 'type' => 'checkbox', 'data' => $attribute);

        return generationAddTpl($addList, 'm_id', $title, $result);
    }
}