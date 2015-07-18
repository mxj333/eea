<?php
namespace Common\Model;
use Think\Model;
class ResourceModel extends ManageModel {

    protected $_validate = array(
        array('res_title', 'require', '请输入标题'),
        array('res_title', 'checkLength', '标题长度2-50个字', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 50)),
        array('rf_id', 'checkIsUploadFile', '请上传资源', self::MUST_VALIDATE, 'callback', self::MODEL_INSERT),
        array('res_author', 'checkLength', '作者长度2-15个字', self::VALUE_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 15)),
        array('res_language', 'require', '请填写语种'),
        array('res_language', 'checkLength', '语种长度2-20个字', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 20)),
        array('res_metadata_language', 'require', '请填写元数据语种'),
        array('res_metadata_language', 'checkLength', '元数据语种长度2-20个字', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 20)),
        array('res_published_name', 'checkLength', '出版者姓名长度2-15个字', self::VALUE_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 15)),
        array('res_published_company', 'checkLength', '出版者公司长度2-30个字', self::VALUE_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 30)),
        array('res_other_author', 'checkLength', '其他作者长度2-15个字', self::VALUE_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 15)),
        array('res_short_title', 'checkLength', '短标题长度2-15个字', self::VALUE_VALIDATE, 'callback', self::MODEL_BOTH, array(2, 15)),
    );

    protected $_auto = array (
        array('res_created', 'time', self::MODEL_INSERT, 'function'),
        array('res_updated', 'time', self::MODEL_UPDATE, 'function'),
        array('res_issused', 'strtotime', self::MODEL_BOTH, 'function'),
        array('res_valid', 'strtotime', self::MODEL_BOTH, 'function'),
        array('res_avaliable', 'strtotime', self::MODEL_BOTH, 'function'),
        array('re_id', 'dealValueDelimiter', self::MODEL_BOTH, 'callback'),
        array('re_title', 'dealTitleDelimiter', self::MODEL_BOTH, 'callback'),
        array('res_download_points', 'intval', self::MODEL_BOTH, 'function'),
    );

    public function checkIsUploadFile($data) {
        if ($data) {
            return true;
        }
        return false;
    }
}