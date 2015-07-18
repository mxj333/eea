<?php
namespace Test\Controller;
use Think\Controller;
class IndexController extends Controller {

    // 初始化
    public function _initialize() {
        cacheData();
        C('DEFAULT_THEME', C('MANAGE_DEFAULT_THEME'));
    }

    public function index(){
        $this->action = C('ACTION');
        $this->display();
    }

    public function getFunction() {

        $action = C('ACTION');
        echo json_encode($action[$_POST['con']]['function']);
    }

    public function getParam() {

        $action = C('ACTION');
        $res = $action[$_POST['con']]['function'][$_POST['fun']]['param'];

        foreach ($res as $key => $value) {
            $res[$key]['value'] = strval($value['value']);
            $res[$key]['sign'] = strval($value['sign']);
            if ($key == 'skey') {
                $res['skey']['value'] = strval(Cookie('passport_id'));
            }
            if ($key == 'me_id') {
                $res['me_id']['value'] = getPassportId();
            }
        }
        echo json_encode($res);
    }

    public function api() {

        $params['method'] = $_POST['action'] . '.' . $_POST['function'];
        $params['args'] = $_POST['require'];
        $params['ts'] = time();
        $params['format'] = strtolower($_POST['format']) == 'json' ? 'JSON' : 'XML';

        if ($_POST['param']) {
            $_POST['param'] = explode('&', preg_replace('/\n|\r\n/', '&', $_POST['param']));
            foreach ($_POST['param'] as $key => $value) {
                $tmp = explode('=', $value);
                $params['args'][$tmp[0]] = $tmp[1];
            }
        }

        unset($params['args']['skey']);
        $params['skey'] = $_POST['require']['skey'];
        $skey = urldecode($params['skey']);

        $params = initParams($params['method'], $params['args'], $params['ts'], $skey, $params['format']);

        $res['param1'] = generateSign($params);
        $params['sign'] = md5($res['param1']);

        $res['param2'] = buildUrl($params);

        $data = doRequest('http://' . C('API_INTERFACE_IP_ADDRESS') . '/' . C('API_INTERFACE_URL_PATH'), $res['param2']);

        switch ($params['format']) {
            case 'JSON' :
                if (strpos($data[1], 'errCode') !== FALSE) {
                    $err = get_object_vars(json_decode($data[1]));
                    $errCode = C('ERROR_CODE');
                    $res['obj'] = $errCode[$err['errCode']]['errMessage'];
                } else {
                    $res['obj'] = $this->createTable(json_decode(trim($data[1], chr(239).chr(187).chr(191)), true));
                    if ($params['method'] == 'Public.login') {
                        $tmp = json_decode($data[1], TRUE);
                        Cookie('passport_id', $tmp['skey'], C('COOKIE_EXPIRE'));
                    }
                }
                $res['str'] = strval($data[1]);
                break;
            case 'XML' :
                $res['obj'] = $this->createTable($this->xml2array(simplexml_load_string($data[1])));
                $res['str'] = htmlspecialchars($data[1]);
                break;
        }
        if ($params['method'] == 'Public.logout') {
            Cookie('passport_id', NULL);
        }
        echo json_encode($res);
    }

    public function createTable($arr, $str = '') {

        $str .= '<table cellpadding="0" cellspacing="0" width="100%">';

        if (is_object($arr)) {
            $arr = get_object_vars($arr);
        }

        $str .= '<tr><td>总数</td><td>' . count($arr) . '</td></tr>';

        foreach ($arr as $key => $value) {
            $str .= '<tr><td>' .$key . '</td><td>';

            if (is_array($value) || is_object($value)) {
                $str .= $this->createTable($value);
            } else {

                if ($key === 're_cover' || (strpos($value, 'AuthAvatar') !== FALSE || strpos($value, 'image') !== FALSE || strpos($value, 'ResourceImg') !== FALSE) && strpos($value, '/apps/Uploads') !== FALSE && !in_array($key, array('re_savename', 'stu_notes', 'version'))) {
                    $str .= '<img src="' . $value . '" />';
                } else {
                    $str .= $value;
                }
            }
            $str .= '</td></tr>';
        }
        $str .= '</table>';
        return $str;
    }

    public function xml2array($xml){
        foreach ($xml as $k => $v) {
            $res[$k] = (string)$xml->$k;
            if ($xml->$k->children()) {
                $res[$k] = $this->xml2array($v);
            }
        }
        return $res;
    }
}