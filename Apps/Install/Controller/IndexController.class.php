<?php
namespace Install\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function _initialize() {
        if (file_exists('./install.log')) {
            redirect('/');
            exit;
        }
    }

    public function index() {
        header("Content-Type:text/html;charset=utf-8");
        echo '安装过程';
        file_put_contents('./install.log', '');
    }
}