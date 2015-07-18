<?php
namespace Common\Model;
class FeedBackModel extends ManageModel {

    protected $_auto = array(
        array('fb_created', 'time', 1, 'function'),
    );

    protected $_validate = array(
        array('fb_content', 'require', '请输入反馈'),
    );
}