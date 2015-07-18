<?php
namespace Home\Controller;
use Think\Controller;
class HomeController extends Controller {

    public function _initialize()
    {
        cacheData();
        C('DEFAULT_THEME', C('HOME_DEFAULT_THEME'));
    }

}