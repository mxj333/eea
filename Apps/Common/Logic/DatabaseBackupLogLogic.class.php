<?php
namespace Common\Logic;
class DatabaseBackupLogLogic extends Logic {

    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;

        $default = array(
            'order' => 'dbl_id ASC',
            'p' => intval($param['p']),
        );

        $config = array_merge($default, $config);
        // 分页获取数据
        $lists = D($this->name)->getListByPage($config);
        $user = D('User')->getAll(array('fields' => 'u_id,u_nickname'));

        foreach ($lists['list'] as $key => $value) {

            $lists['list'][$key]['dbl_title'] = date('YmdHis', $value['dbl_created']) . '_' . $value['dbl_title'] . '.sql';
            $lists['list'][$key]['u_id'] = $user[$value['u_id']];
            $lists['list'][$key]['dbl_created'] = date('Y-m-d', $value['dbl_created']);
            $lists['list'][$key]['dbl_type'] = $value['dbl_type'] == 0 ? '自动' : '手动';
        }

        return $lists;
    }

    public function backup($type = 0) {
        $db = new \Think\DBManage(C('DB_HOST'), C('DB_USER'), C('DB_PWD'), C('DB_NAME'), C('DB_CHARSET'));
        $time = time();
        $dir = C('UPLOADS_ROOT_PATH') . C('BACKUP_DATABASE_PATH') . date('Ymd', $time) . '/';
        mk_dir($dir);
        $tableName = '';
        $num = $db->backup($dir, date('YmdHis', $time), $tableName, array(C('DB_PREFIX') . 'database_backup_log'));

        $title = $tableName ? $tableName : 'all';
        if (file_exists($dir . date('YmdHis', $time) . '_' . $title . '_v1.sql')) {
            $data['u_id'] = $_SESSION[C('USER_AUTH_KEY')];
            $data['dbl_title'] = $title;
            $data['dbl_type'] = $type;
            $data['dbl_num'] = $num;
            $data['dbl_created'] = $time;
            return D('DatabaseBackupLog', 'Model')->insert($data);
        }

        return false;
    }

    public function resume($id) {
        $res = D('DatabaseBackupLog', 'Model')->getOne(array('where' => array('dbl_id' => $id)));
        $db = new \Think\DBManage(C('DB_HOST'), C('DB_USER'), C('DB_PWD'), C('DB_NAME'), C('DB_CHARSET'));
        $db->restore(C('UPLOADS_ROOT_PATH') . C('BACKUP_DATABASE_PATH') . date('Ymd', $res['dbl_created']) . '/' . date('YmdHis', $res['dbl_created']) . '_' . $res['dbl_title'] . '_v1.sql');
    }

    public function delete($id) {
        $res = D('DatabaseBackupLog', 'Model')->getOne(array('where' => array('dbl_id' => $id)));

        for ($i = 1; $i <= $res['dbl_num']; $i ++) {
            unlink(C('UPLOADS_ROOT_PATH') . C('BACKUP_DATABASE_PATH') . date('Ymd', $res['dbl_created']) . '/' . date('YmdHis', $res['dbl_created']) . '_' . $res['dbl_title'] . '_v' . $i . '.sql');
        }
        return D('DatabaseBackupLog', 'Model')->delete(array('where' => array('dbl_id' => $id)));
    }
}