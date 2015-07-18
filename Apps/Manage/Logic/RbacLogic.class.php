<?php
namespace Manage\Logic;
class RbacLogic extends Logic {
    public function saveAccessList() {

        if(C('USER_AUTH_TYPE') !=2 && !$_SESSION[C('ADMIN_AUTH_KEY')] ) {
            $_SESSION['_ACCESS_LIST'.$_SESSION[C('USER_AUTH_KEY')]] = self::getAccessList();
        }
        return;
    }

    public function getAccessList() {

        $node = reloadCache('node');
        $action = C('ACTION_LIST');
        $access = array();
        if (!$_SESSION[C('ADMIN_AUTH_KEY')]) {
            $roleUser['where']['u_id'] = intval($_SESSION[C('USER_AUTH_KEY')]);
            $roleUser['fields'] = 'r_id';
            $r_ids = D('RoleUser', 'Model')->getAll($roleUser);

            $accessWhere['where']['r_id'] = array('IN', $r_ids);
            $acts = D('Access', 'Model')->getAll($accessWhere);
            $act = array();
            foreach($acts as $act_info) {
                if (!$act[$act_info['n_id']]) {
                    // 不存在  直接追加
                    $act[$act_info['n_id']] = $act_info['n_action'];
                    continue;
                }
                // 存在  计算权限
                $act_res = 0;
                foreach ($action as $action_key => $action_info) {
                    if (($action_key & $act[$act_info['n_id']]) || ($action_key & $act_info['n_action'])) {
                        $act_res += $action_key;
                    }
                }
                $act[$act_info['n_id']] = $act_res;
            }
        }

        foreach ($node as $key => $value) {
            if ($act[$key]) {
                foreach ($action as $aKey => $aValue){
                    if (intval($act[$value['n_id']]) & intval($aValue['value'])) {
                         $res[strtoupper($value['n_name'])][strtoupper($aValue['name'])] = 1;
                         $tmpAction[$value['n_id']] += intval($aValue['value']);
                    }
                }
                $access[strtoupper(MODULE_NAME)] = $res;
            }

            if ($_SESSION[C('ADMIN_AUTH_KEY')]) {
                $tmpAction[$value['n_id']] = $value['n_action'];
            }
        }

        $_SESSION['_ACCESS_LIST_DATA'.$_SESSION[C('USER_AUTH_KEY')]] = $tmpAction;
        return $access;
    }

    public function AccessDecision($appName = MODULE_NAME) {

        $no = explode(',',strtoupper(C('NOT_AUTH_MODULE')));
        if ((!empty($no) && !in_array(strtoupper(CONTROLLER_NAME), $no))) {

            if (empty($_SESSION[C('ADMIN_AUTH_KEY')])) {
                if (C('USER_AUTH_TYPE')==2) {
                    //加强验证和即时验证模式 更加安全 后台权限修改可以即时生效
                    //通过数据库进行访问检查
                    $accessList = self::getAccessList($_SESSION[C('USER_AUTH_KEY')]);
                } else {
                    // 如果是管理员或者当前操作已经认证过，无需再次认证
                    if($_SESSION[$accessGuid]) {
                        return true;
                    }
                    //登录验证模式，比较登录后保存的权限访问列表
                    $accessList = $_SESSION['_ACCESS_LIST'.$_SESSION[C('USER_AUTH_KEY')]];
                }

                if(!isset($accessList[strtoupper($appName)][strtoupper(CONTROLLER_NAME)][strtoupper(ACTION_NAME)])) {
                    return false;
                }
            } else{
                //管理员无需认证
                return true;
            }
        }

        return true;
    }

}