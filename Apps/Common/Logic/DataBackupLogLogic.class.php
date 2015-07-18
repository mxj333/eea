<?php
namespace Common\Logic;
class DataBackupLogLogic extends Logic {

    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;

        $default = array(
            'order' => 'dblo_id ASC',
            'p' => intval($param['p']),
            'where' => array('dblo_is_deleted' => 9),
        );

        $config = array_merge($default, $config);
        // 分页获取数据
        $lists = D('DataBackupLog')->getListByPage($config);

        foreach ($lists['list'] as $key => $value) {
            
            $userConfig['where']['u_id'] = $value['u_id'];
            $userInfo = D('User')->getOne($userConfig);
            $lists['list'][$key]['dblo_title'] = date('YmdHis', $value['dblo_created']);
            $lists['list'][$key]['u_id'] = $userInfo['u_nickname'];
            $lists['list'][$key]['dblo_created'] = date('Y-m-d', $value['dblo_created']);
            $lists['list'][$key]['dblo_type'] = $value['dblo_type'] == 0 ? '自动' : '手动';
        }

        return $lists;
    }

    // 检查备份频率
    public function check() {
        // 限制手动备份频率
        $config['where']['dblo_type'] = 1;
        $config['where']['dblo_created'] = array('GT', intval($time - 86400*7)); // 一星期
        $info = D('DataBackupLog', 'Model')->getOne($config);
        if ($info) {
            $this->error = '备份太频繁，请稍后再试!';
            return false;
        }

        return true;
    }

    public function backup($type = 0) {

        $time = time();

        // 执行备份命令
        exec(C('DATA_BACKUP_COMMAND') . ' ' . date('YmdHis', $time), $out, $status);

        if ($status) {
            // 成功 入库记录
            $data['u_id'] = $_SESSION[C('USER_AUTH_KEY')];
            $data['dblo_title'] = date('YmdHis', $time);
            $data['dblo_type'] = $type;
            $data['dblo_created'] = $time;
            return D('DataBackupLog', 'Model')->insert($data);
        }

        return false;
    }

    public function resume($id) {

        $info = D('DataBackupLog')->getById($id);
        if (!$info) {
            $this->error = '备份文件不存在';
            return false;
        }

        $time = time();
        // 执行恢复命令
        exec(C('DATA_RESUME_COMMAND') . ' ' . date('YmdHis', $info['dblo_created']), $out, $status);

        if ($status) {
            // 成功 入库记录
            $data['u_id'] = $_SESSION[C('USER_AUTH_KEY')];
            $data['dblo_id'] = $id;
            $data['drl_created'] = time();
            $data['drl_runtime'] = $time;
            return D('DataRecoveryLog', 'Model')->insert($data);
        }

        return false;
    }

    public function delete($id) {
        $info = D('DataBackupLog')->getById($id);
        if (!$info) {
            $this->error = '备份文件不存在';
            return false;
        }

        // 删除备份文件
        if (file_exists(C('DATA_BACKUP_PATH') . date('YmdHis', $info['dblo_created']))) {
            del_dir(C('DATA_BACKUP_PATH') . date('YmdHis', $info['dblo_created']));
        }
        
        // 成功 删除记录
        $config['dblo_id'] = $id;
        $config['dblo_is_deleted'] = 1;
        $config['dblo_delete_time'] = time();
        $config['dblo_delete_u_id'] = $_SESSION[C('USER_AUTH_KEY')];
        return D('DataBackupLog', 'Model')->update($config);
    }
}