<?php
namespace Reagional\Controller;
use Think\Controller;
class ReagionalController extends Controller {
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

                    // 提示错误信息
                    $this->error(L('NOT_HAVE_PERMISSION'));
                }
            }
        }

        cacheData();
        C('DEFAULT_THEME', C('REAGIONAL_DEFAULT_THEME'));

        // 获取权限
        if (isset($_SESSION[C('USER_AUTH_KEY')])) {
            D('Rbac')->getAccessList();
        }

        // 重组数据
        $allNode = reloadCache('permissionsNode');
        $allGroup = reloadCache('permissionsGroup');
        $node = $allNode[C('BACKGROUND_TYPE')];
        $list = $allGroup[C('BACKGROUND_TYPE')];

        foreach ($_SESSION['_ACCESS_LIST_DATA'.$_SESSION[C('USER_AUTH_KEY')]] as $nk => $nv) {
            $groups[$node[$nk]['pe_pid']]['title'] = $list[$node[$nk]['pe_pid']]['pe_title'];
            $groups[$node[$nk]['pe_pid']]['name'] = $list[$node[$nk]['pe_pid']]['pe_name'];
            $node[$nk]['pe_action'] = $nv;
            $groups[$node[$nk]['pe_pid']]['list'][] = $node[$nk];
            if (strtoupper($node[$nk]['pe_name']) == strtoupper(CONTROLLER_NAME)) {
                $bannerOn = $node[$nk]['pe_pid'];
                $bannerTitle = $list[$bannerOn]['pe_title'];
                $bannerName = $list[$bannerOn]['pe_name'];
                $_SESSION['_ACCESS_CONTROLLER_ID'.$_SESSION[C('USER_AUTH_KEY')]] = $node[$nk]['pe_id'];
            }
        }

        $this->bannerOn = intval($bannerOn);
        $this->bannerTitle = $bannerTitle;
        $this->bannerName = $bannerName;
        $this->groups = $groups;
        $currentRegionTitle = explode('-', session('re_title'));
        $this->currentRegionTitle = array_pop($currentRegionTitle);
    }

    // api 接口返回值处理
    public function apiReturnDeal($data) {

        // 接口请求错误
        if (isset($data['errCode'])) {
            $this->error($data['errMessage']);
            exit;
        }

        // 接口内容返回错误
        if (isset($data['status']) && !$data['status']) {
            $this->error($data['info']);
            exit;
        }

        // 接口内容正确
        return $data;
    }

    // 默认新增操作
    public function add($html = '') {
        if ($_POST) {
            $this->insert();
        } else {
            $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Add.js'));
            $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . 'Add.css'));

            // 添加时 默认选择当前管理员所属区域
            $vo['re_title'] = session('re_title');
            $this->assign('vo', $vo);

            if ($html) {
                $this->display($html);
            } else {
                $this->addTpl = $this->addTpl(L('ADD'));
                $this->display('Public/add');
            }
        }
    }

    // 默认编辑操作
    public function edit($pk, $html = '') {
        if ($_POST) {
            $this->insert();
        } else {
            // 获取数据
            $config['is_deal_result'] = true;
            $config[$pk] = intval(I('request.id'));
            $vo = $this->apiReturnDeal(getApi($config, CONTROLLER_NAME, 'shows'));
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

    // 首页
    public function index($html = '') {
        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . '.css'));
        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . '.js'));

        $html = $html ? $html : 'Public/index';
        $this->display($html);
    }

    // 列表
    public function lists() {
        // 用户所属区域id
        $_POST['re_id'] = $_SESSION['re_id'];
        // 结果自动处理
        $_POST['is_deal_result'] = true;
        // 所属平台 （区域后台）
        $_POST['belong'] = 2;

        // api 请求数据
        $data = $this->apiReturnDeal(getApi($_POST, CONTROLLER_NAME, 'lists'));
        
        echo json_encode($data);
    }

    // 默认显示操作
    public function show($result, $message = '', $jumpUrl = '') {

        //保存当前数据对象
        if (isset($result['status']) && !$result['status']) {
            //失败提示
            $message = $message ? $message . L('FAILURE') : $result['info'];
            $this->error($message);
        } else {
            //成功提示
            $message = $message ? $message . L('SUCCESS') : $result['info'];
            $jumpUrl = $jumpUrl ? $jumpUrl : __CONTROLLER__ . '/index';
            $this->assign('jumpUrl', $jumpUrl);
            $this->success($message);
        }
    }

    // 默认写入操作
    public function insert($pk, $table = '') {

        $apiFunction = intval($_POST[$pk]) ? 'edit' : 'add';

        // 如果table有值则替换当前控制器mod
        $table = empty($table) ? CONTROLLER_NAME : $table;

        // 处理接口返回信息
        $data = $this->apiReturnDeal(getApi($_POST, $table, $apiFunction));

        // 提示跳转
        $this->show($data);
    }

    // 默认删除操作
    public function delete($pk, $table = '') {

        $table = empty($table) ? ucfirst(CONTROLLER_NAME) : ucfirst($table);

        // api 请求数据
        $data = $this->apiReturnDeal(getApi(array($pk => I('id', 0, 'strval'), 'belong' => 2), $table, 'del'));

        // 提示跳转页面
        $this->show($data);
    }

    // 上传
    public function upload($file, $config = array(), $table = '') {

        $fields['file'] = '@'.realpath($file['tmp_name']).";type=".$file['type'].";filename=".$file['name'];

        $table = empty($table) ? ucfirst(CONTROLLER_NAME) : ucfirst($table);
        
        return $this->apiReturnDeal(getApi($config, $table, 'upload', 'json', $fields));
    }


    // TO DO 下面的还没有用到  未调试
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
        // 组织条件
        $config[$field] = $status;
        $data['where'][$pk] = array('IN', $id);
        if (D($model)->update($config, $data) !== false) {
            $this->success(L('OPERATION_SUCCESS'));
        } else {
            $this->error(L('OPERATION_FAILURE'));
        }
    }

    // 富文本编辑器
    public function ueditor() {
        echo D('Public')->ueditor();
    }
}