<?php
namespace Common\Logic;
class PublicLogic extends Logic {
    /**
     * 验证码
     * expire 验证码的有效期(秒)
     * useImgBg 是否使用背景图片 默认为false
     * fontSize 验证码字体大小（像素） 默认为25 
     * useCurve 是否使用混淆曲线 默认为true 
     * useNoise 是否添加杂点 默认为true 
     * length 验证码位数 
     * fontttf 指定验证码字体 默认为随机获取 
     * useZh 是否使用中文验证码 
     * bg 验证码背景颜色 rgb数组设置，例如 array(243, 251, 254) 
     * 
     */
    public function verify($id = '', $data = array()) {

        $config = array(
            'useImgBg'  => false,
            'fontSize'  => 20,
            'useCurve'  => false,
            'useNoise'  => false,
            'length'    => C('VERIFY_LENGTH'),
            'fontttf'   => '5.ttf',
            'useZh'     => (C('VERIFY_TYPE') == 2),
        );

        $config = array_merge($config, (array)$data);
        $Verify = new \Think\Verify($config);
        $Verify->entry($id);
    }

    /**
     * 验证码验证
     *
     * return boolean
     */
    public function checkVerify($code, $id = '') {
        $verify = new \Think\Verify(array('reset' => false));
        return $verify->check($code, $id);
    }

    public function ueditor(){
        $data = new \Org\Util\Ueditor();
        return $data->output();
    }
}
?>