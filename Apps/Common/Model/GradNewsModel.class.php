<?php
namespace Common\Model;
class GradNewsModel extends ManageModel {

    protected $_auto = array(
        array('gn_created', 'time', self::MODEL_INSERT, 'function'),
        array('gn_updated', 'time', self::MODEL_UPDATE, 'function'),
    );

    protected $_validate = array(
        array('gn_title', 'require', '请输入名称'),
        array('gn_url', 'require', '请输入地址'),
        array('gn_urlReg', 'require', '请输入地址正则'),
        array('gn_titleReg', 'require', '请输入标题正则'),
        array('gn_remarkReg', 'require', '请输入摘要正则'),
        array('gn_contentReg', 'require', '请输入内容正则'),
        array('gn_check', 'require', '请输入验证正则'),
    );
}