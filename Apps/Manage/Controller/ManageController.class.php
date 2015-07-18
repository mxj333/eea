<?php
namespace Manage\Controller;
use Think\Controller;
class ManageController extends Controller {
    public function _initialize() {

        // 黑名单验证
        if (is_safe_ip()) {
            // 在黑名单内  跳转到404页
            header("HTTP/1.1 404 Not Found");  
            header("Status: 404 Not Found");  
            exit;
        }

        if (!file_exists('./install.log')) {
            redirect('/Install/Index');
        }

        // 用户权限检查
        if (C('USER_AUTH_ON') && !in_array(CONTROLLER_NAME, explode(',', C('NOT_CHECK_CONTROLLER')))) {
            // 加载RBAC
            if (!D('Rbac')->AccessDecision()) {

                // 检查认证识别号
                if (!$_SESSION[C('USER_AUTH_KEY')]) {

                    // 跳转到认证网关
                    redirect(C('USER_AUTH_GATEWAY'));
                }

                // 没有权限 抛出错误
                if (C('RBAC_ERROR_PAGE')) {

                    // 定义权限错误页面
                    redirect(C('RBAC_ERROR_PAGE'));
                } else {
                    if (C('GUEST_AUTH_ON')){
                        $this->assign('jumpUrl', PHP_FILE.C('USER_AUTH_GATEWAY'));
                    }
                }
            }
        }

        cacheData();
        C('DEFAULT_THEME', C('MANAGE_DEFAULT_THEME'));

        // 获取权限
        if (isset($_SESSION[C('USER_AUTH_KEY')])) {
            D('Rbac')->getAccessList();
        }

        // 重组数据
        $list = reloadCache('group');
        $node = reloadCache('node');

        foreach ($_SESSION['_ACCESS_LIST_DATA'.$_SESSION[C('USER_AUTH_KEY')]] as $nk => $nv) {
            $groups[$node[$nk]['g_id']]['title'] = $list[$node[$nk]['g_id']];
            $node[$nk]['n_action'] = $nv;
            $groups[$node[$nk]['g_id']]['list'][] = $node[$nk];
            if (strtoupper($node[$nk]['n_name']) == strtoupper(CONTROLLER_NAME)) {
                $bannerOn = $node[$nk]['g_id'];
                $_SESSION['_ACCESS_CONTROLLER_ID'.$_SESSION[C('USER_AUTH_KEY')]] = $node[$nk]['n_id'];
            }
        }

        $this->bannerOn = intval($bannerOn);
        $this->groups = $groups;

        // 默认选择分组第一个
        if (!$this->bannerOn) {
            $this->bannerOn = key($groups);
        }
    }

    public function upload($file, $config = array()) {

        $info = upload($file, $config);
        if (!is_array($info)) {
            $this->error($info);
        }
        return $info;
    }

    // 默认写入操作
    public function insert($table = '') {
        $this->show($this->data($table, $_POST));
    }

    // 默认写入数据
    public function data($table = '', $data = array()) {

        // 如果table有值则替换当前控制器mod
        $table = empty($table) ? CONTROLLER_NAME : $table;

        $model = D($table, 'Model');
        if ($data) {
            $_POST = $data;
        }

        $save_data = $model->create();
        if (false === $save_data) {
            $this->error($model->getError());
        }
        $pk = $model->getPk();

        if ($_POST[$pk]) {
            return $model->update($save_data);
        } else {
            unset($save_data[$pk]);
            $_POST[$pk] = $model->insert($save_data);
            return $_POST[$pk];
        }
    }

    // 默认显示操作
    public function show($result, $message = '', $jumpUrl = '') {

        $message = $message ? $message : L('OPERATION');
        $jumpUrl = $jumpUrl ? $jumpUrl : __CONTROLLER__ . '/index';
        //保存当前数据对象
        if (false !== $result) {
            //成功提示
            $this->assign('jumpUrl', $jumpUrl);
            $this->success($message . L('SUCCESS'));
        } else {
            //失败提示
            $this->error($message . L('FAILURE'));
        }
    }

    // 默认新增操作
    public function add($html = '') {
        if ($_POST) {
            $this->insert();
        } else {
            $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Add.js'));
            $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . 'Add.css'));

            if ($html) {
                $this->display($html);
            } else {
                $this->addTpl = $this->addTpl(L('ADD'));
                $this->display('Public/add');
            }
        }
    }

    // 默认编辑操作
    public function edit($html = '') {
        if ($_POST) {
            $this->insert();
        } else {
            // 获取数据
            $vo = D(CONTROLLER_NAME)->getById(intval(I('request.id')));

            foreach ($vo as &$value) {
                $value = stripFilter($value);
            }

            $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Add.js'));
            $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . 'Add.css'));
            $this->assign('vo', $vo);

            if ($html) {
                $this->display($html);
            } else {
                $this->addTpl = $this->addTpl(L('EDIT'), $vo);
                $this->display('Public/add');
            }
        }
    }

    // 默认删除操作
    public function deleteData($table = '') {

        $table = empty($table) ? ucfirst(CONTROLLER_NAME) : ucfirst($table);
        $model = D($table);

        // 获取主键
        $pk = $model->getPk();

        if (!empty($model)) {
            // 条件
            $config['where'][$pk] = array('IN', strval(I('request.id')));
            return $model->delete($config);
        }
    }

    public function resume($table = '', $default = 1) {
        $table = empty($table) ? ucfirst(CONTROLLER_NAME) : ucfirst($table);
        $model = D($table, 'Model');

        // 获取主键
        $pk = $model->getPk();

        if (!empty($model)) {

            // 接收参数
            $id = strval(I('request.id'));
            // 条件
            $pre = substr($pk, 0, strpos($pk, '_')) . '_';

            // 条件
            $data[$pre . 'status'] = $default;
            $config['where'][$pk] = array('in', $id);

            $model->update($data, $config);
        }

        $this->show(1, L('RECOVER'));
    }

    // 默认删除操作
    public function delete($table = '') {
        $this->show($this->deleteData($table), L('DELETE'));
    }

    // 默认排序操作
    public function sort() {
        // 模板赋值
        $this->assign("sortList", D(CONTROLLER_NAME)->getSortList($_GET['sortId']));
        $this->display();
    }

    // 默认排序保存操作
    // TO DO 要改成 pdo
    public function saveSort() {

        $seqNoList = $_POST['seqNoList'];

        if (!empty($seqNoList)) {

            $model = M(CONTROLLER_NAME);

            // 获取主键
            $pk = $model->getPk();
            $pre = substr($pk, 0, strpos($pk, '_')) . '_';
            $sort = $pre . 'sort';

            $col = explode(',', $seqNoList);
            //启动事务
            $model->startTrans();
            foreach ($col as $val) {

                $val = explode(':',$val);

                $model->$pk = $val[0];
                $model->$sort = $val[1];

                $result = $model->save();
                if (false === $result) {
                    break;
                }
            }

            //提交事务
            $model->commit();
            if (false !== $result) {

                //采用普通方式跳转刷新页面
                $this->success(L('UPDATE_SUCCESS'));
            }else {

                $this->error($model->getError());
            }
        }
    }

    public function status($model = '', $field = 'status', $status = 1) {
        $model = $model ? $model : CONTROLLER_NAME;
        $pk = $model->getPk();

        // 接收参数
        $id = strval($_GET['id']);
        // 条件
        $pre = substr($pk, 0, strpos($pk, '_')) . '_';

        // 条件
        $config[$pre . 'status'] = $status;
        $data['where'][$pk] = array('IN', $id);
        if (D($model)->update($config, $data) !== false) {
            $this->success(L('OPERATION_SUCCESS'));
        } else {
            $this->error(L('OPERATION_FAILURE'));
        }
    }

    // 首页
    public function index() {
        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . '.js'));
        $this->display('Public/index');
    }

    // 列表
    public function lists() {
        echo json_encode(D(CONTROLLER_NAME)->lists());
    }

    // 富文本编辑器
    public function ueditor() {
        echo D('Public')->ueditor();
    }
}