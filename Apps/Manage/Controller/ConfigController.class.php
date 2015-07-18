<?php
namespace Manage\Controller;
class ConfigController extends ManageController {

    // 插入
    public function insert() {

        // 是否有上传
        if ($_FILES['file']['size'] > 0) {

            // 获取后缀
            $pathInfo = getPathInfo($_FILES['file']['name']);
            $_POST['con_ext'] = $pathInfo['ext'];

            // 文件上传
            $config['exts'] = array('apk', 'ipa', C('DEFAULT_IMAGE_EXT'));
            $config['savePath'] = C('CONFIG_FILE_PATH');
            $file = parent::upload($_FILES['file'], $config);
        }

        if ($file && $pathInfo['ext'] == C('DEFAULT_IMAGE_EXT')) {
            // 文件路径
            $config = (array)json_decode($_POST['con_value'], true);
            $path = C('UPLOADS_ROOT_PATH') . C('CONFIG_FILE_PATH');
            image($path.$file['savename'], $path . strtolower($_POST['con_name']) . '.' . $_POST['con_ext'], $config, false);
            unlink($path.$file['savename']);
        }

        parent::insert();
        reloadCache('config', true);
    }

    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'con_id', 'percent' => '8', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'con_title', 'percent' => '25', 'title' => L('CHINESE_NAME'), 'class' => 'showContent');
        $tplList[] = array('id' => 'con_name', 'percent' => '25', 'title' => L('ENGLISH_NAME'), 'class' => 'showContent');
        $tplList[] = array('id' => 'con_value', 'percent' => '20', 'title' => L('DEFAULT_VALUE'), 'class' => 'showContent');
        $tplList['action'] = array('id' => 'action', 'percent' => '10', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'edit', 'percent' => '10', 'title' => L('EDIT'));

        // 检索器
        $search[] = array('title' => L('CHINESE_NAME'), 'type' => 'input', 'name' => 'con_title');
        $search[] = array('title' => '类型', 'label' => 'select', 'name' => 'con_type', 'data' => array('1' => '基本配置', '2' => '技术配置'));

        // 工具
        $tools = array('add', 'edit');

        // 标题栏
        $titleFlag = 1;

        $this->indexTpl = generationTpl($tplList, $action, $search, $tools);
        parent::index();
    }

    public function addTpl($title = '', $result = array()) {
        // 显示字段
        $addList[] = array('title' => L('CHINESE_NAME'), 'name' => 'con_title', 'class' => 'w460');
        $addList[] = array('title' => L('ENGLISH_NAME'), 'name' => 'con_name', 'class' => 'w460');
        $addList[] = array('title' => L('DEFAULT_VALUE'), 'name' => 'con_value', 'label' => 'textarea', 'class' => 'h80');
        $addList[] = array('title' => '类型', 'name' => 'con_type', 'label' => 'select', 'data' => array('1' => '基本配置', '2' => '技术配置'));
        $addList[] = array('title' => L('ANNEX'), 'name' => 'file', 'type' => 'file', 'class' => 'w460');
        $addList[] = array('title' => L('REMARKS'), 'name' => 'con_note', 'label' => 'textarea', 'class' => 'h80');

        if ($result['con_ext']) {
            $file = C('UPLOADS_ROOT_PATH') . C('CONFIG_FILE_PATH') . strtolower($result['con_name']) . '.' . $result['con_ext'];
            if (file_exists($file)) {
                $result['file'] = turnTpl($file);
                $result['ext'] = $result['con_ext'];
            }
        }
        return generationAddTpl($addList, 'con_id', $title, $result);
    }
}
