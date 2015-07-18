<?php
namespace Manage\Controller;
class IndexController extends ManageController {
    public function index(){
/*
        $cpufree = get_cpufree();
        while ($cpufree < 10) {
            // wait for 0.1 seconds
            usleep(0.1*1000000);
            $cpufree = get_cpufree ();
        };*/
        // 剩余空间
        $space = round((disk_free_space(".") / (1024 * 1024 * 1024)), 2);
        if ($space < 5) {
            // 空间不足 5G  报警  变红色
            $space = '<font color="red">' . $space . 'G</font>';
        } else {
            $space = $space . 'G';
        }

        $info = array(
            L('OS') => PHP_OS,
            L('SERVER_SOFTWARE') => $_SERVER["SERVER_SOFTWARE"],
            L('APACHE_PROCESSES_NUMBER') => get_proc_count('httpd'),
            L('RUNNING_MODE') => php_sapi_name(),
            L('UPLOAD_MAX_FILE_SIZE') => ini_get('upload_max_filesize'),
            L('MAX_EXECUTION_TIME') => ini_get('max_execution_time') . L('SECOND'),
            L('SERVER_TIME') => date("Y年n月j日 H:i:s"),
            L('BEIJING_TIME') => gmdate("Y年n月j日 H:i:s", time() + 8 * 3600),
            L('DOMAIN_NAME') => $_SERVER['SERVER_NAME'] . ' [ ' . gethostbyname($_SERVER['SERVER_NAME']) . ' ]',
            L('SURPLUS') => $space,
            //'register_globals' => get_cfg_var("register_globals") == "1" ? "ON" : "OFF",
            L('MAGIC_QUOTE') => (1 === get_magic_quotes_gpc()) ? L('ON') : L('OFF'),
            //'magic_quotes_runtime' => (1 === get_magic_quotes_runtime()) ? 'YES' : 'NO',
        );

        $this->assign('info', $info);
        $this->display();
    }
}