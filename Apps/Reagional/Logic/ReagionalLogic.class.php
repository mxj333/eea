<?php
namespace Reagional\Logic;
class ReagionalLogic extends Logic {
    
    protected function getApi($action = '', $function = '', $args = array(), $format = 'json') {

        $params['method'] = $action . '.' . $function;
        $params['args'] = $args;
        $params['ts'] = time();
        $params['format'] = strtolower($format) == 'json' ? 'JSON' : 'XML';
        unset($params['args']['skey']);
        $skey = urldecode($args['skey']);

        $params = initParams($params['method'], $params['args'], $params['ts'], $skey, $params['format']);

        $res['param1'] = generateSign($params);
        $params['sign'] = md5($res['param1']);
        $res['param2'] = buildUrl($params);

        $data = doRequest('http://' . C('API_INTERFACE_IP_ADDRESS') . '/' . C('API_INTERFACE_URL_PATH'), $res['param2']);

        switch ($params['format']) {
            case 'JSON' :
                if (strpos($data[1], 'errCode') !== FALSE) {
                    $err = get_object_vars(json_decode($data[1]));
                    $errCode = $err['errCode'];
                    $this->error = $err['errMessage'];
                    return false;
                } else {
                    return json_decode($data[1], true);
                }
                break;
            case 'XML' :
                return xml2array(simplexml_load_string($data[1]));
                break;
        }
    }

    // 获取状态
    function getStatus($status, $imageShow = true) {
        switch($status) {
            case 1:
                $showText = '正常';
                $showImg  = '<img src="/Public/Images/ok.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="正常">';
                break;
            default:
                $showText = '禁用';
                $showImg  = '<img src="/Public/Images/locked.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="禁用">';

        }
        return ($imageShow===true)? ($showImg) : $showText;
    }
}