<?php

/**
 * postRequest
 *
 * @param  mixed $url
 * @param  mixed $data
 * @return void
 */
function doRequest($url, $data) {

    if (function_exists('curl_init')) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Meishi API PHP Client 0.1 (curl) ' . phpversion());
        $result = curl_exec($ch);
        $errno = curl_errno($ch);
        curl_close($ch);

        return array($errno, $result);

    } else {

        $context =
        array('http' =>
                array('method' => 'POST',
                    'header' => 'Content-type: application/x-www-form-urlencoded'."\r\n".
                                'User-Agent: Meishi API PHP Client 0.1 (non-curl) '.phpversion()."\r\n".
                                'Content-length: ' . strlen($data),
                    'content' => $data));
        $contextid = stream_context_create($context);
        $sock = fopen($url, 'r', false, $contextid);
        if ($sock) {
            $result = '';
            while (!feof($sock)) {
                $result .= fgets($sock, 4096);
            }
            fclose($sock);
        }

        return array(0, $result);
    }
}

/**
 * initParams
 * 必要参数初始化
 *
 * @param  mixed $method
 * @return void
 */
function initParams($method, $args, $ts, $skey = '', $format = '') {

    $params = array();
    $params['format'] = $format ? $format : strtoupper(C('API_DATA_FORMAT'));
    $params['ts'] = $ts;
    $params['method'] = $method;
    ksort($args);
    $params['args'] = $args;

    if ($skey) {
        $params['skey'] = $skey;
    }
    return $params;
}

/**
 * generateSign
 * sign生成方法
 *
 * @param  mixed $params
 * @param  mixed $method
 * @param  mixed $args
 * @return void
 */
function generateSign($params) {

    $fields = array('method', 'ts', 'args', 'format', 'skey');
    sort($fields);

    $request = array();
    foreach ($fields as $k) {
        if ($k != 'skey' || $params['skey']){
            $request[$k] = $params[$k];
        }
    }

    return buildUrl($request);
}

/**
 * buildUrl
 * 组装 url
 *
 * @param  array $request
 * @return string
    array(
        'method' => 'Public.init',
        'format' => 'JSON',
        'ts' => 123123123
    )
    method=Public.init&format=JSON&ts=123123123

 **/
function buildUrl($request) {

    return http_build_query($request, '', '&');
}

/**
 * buildUrlArray
 * 组装 url 数组参数
 *
 * @param  array $request
 * @return string
    array(
        'a' => array( 1 => 'red'),
        'b' => array( 2 => 'red'),
        'c' => array( 3 => 'red'),
    );
    array(
        'a[1]' => 'red',
        'b[2]' => 'red',
        'c[3]' => 'red',
    );
 **/
function buildUrlArray($request, $prev_key = 'args') {
        
    static $return = array();

    if (!is_array($request)) {
        return $request;
    }

    foreach ($request as $key => $value) {
        if (is_array($value)) {
            $return = buildUrlArray($value, $prev_key . '['.$key.']');
        } else {
            $return[$prev_key . '['.$key.']'] = $value;
        }
    }
    
    return $return;
}

function getApiData($data = array()) {

    $data = initParams($data['method'], $data['args'], $data['ts'], $data['skey']);
    $data['sign'] = md5(generateSign($data));
    $res = doRequest('http://' . C('API_INTERFACE_IP_ADDRESS') . '/' . C('API_INTERFACE_URL_PATH'), buildUrl($data));
    return json_decode($res[1], true);
}

function getApi($args = array(), $action = '', $function = '', $format = 'json', $file = '') {
    
    $action = $action ? $action : lcfirst(CONTROLLER_NAME);
    $function = $function ? $function : lcfirst(ACTION_NAME);

    $params['method'] = $action . '.' . $function;
    $params['args'] = $args;
    $params['ts'] = time();
    $params['format'] = strtolower($format) == 'json' ? 'JSON' : 'XML';
    
    if (in_array($function, array('add', 'addAll', 'edit', 'del', 'upload', 'review', 'resume', 'sync', 'addRelation', 'download', 'forbid', 'publish', 'user', 'import', 'saveLoginStatus'))) {
        // 加密字符
        $skey = setPassportId($_SESSION[C('USER_AUTH_KEY')]);
        $params['args']['me_id'] = $_SESSION[C('USER_AUTH_KEY')];
    }
    
    $params = initParams($params['method'], $params['args'], $params['ts'], $skey, $params['format']);

    $res['param1'] = generateSign($params);
    $params['sign'] = md5($res['param1']);
    $res['param2'] = buildUrl($params);

    if ($file) {
        // 文件上传  args 参数 数组 转换发送形式
        $paramsArgs = buildUrlArray($params['args']);
        unset($params['args']);
        // 文件上传  已数组的形式 发送数据
        $postData = array_merge($params, $paramsArgs, $file);
    } else {
        $postData = $res['param2'];
    }
    
    $data = doRequest('http://' . C('API_INTERFACE_IP_ADDRESS') . '/' . C('API_INTERFACE_URL_PATH'), $postData);

    if (in_array($function, array('add', 'addAll', 'edit', 'del', 'upload', 'review', 'resume', 'sync', 'addRelation', 'download', 'forbid', 'publish', 'user', 'import', 'saveLoginStatus'))) {
        // 释放加密串
        setPassportId(NULL);
    }

    switch ($params['format']) {
        case 'JSON' :
            return json_decode($data[1], true);
            break;
        case 'XML' :
            return xml2array(simplexml_load_string($data[1]));
            break;
    }
}

function parseRule($rule, $data, $page = '1') {
    $replace = array(
        '{ca_id}' => $data['ca_id'],
        '{ca_name}' => $data['ca_name'],
        '{page}' => $page,
        '{id}' => $data['art_id'],
        '{year}' => date('y', $data['art_published']),
        '{month}' => date('m', $data['art_published']),
        '{day}' => date('d', $data['art_published']),
        '{hour}' => date('H', $data['art_published']),
        '{p}' => $page,
        '{m}' => $data['m_id'],

    );
    return str_replace(array_keys($replace), array_values($replace), $rule).C('HTML_FILE_SUFFIX');
}

// 获取静态URL地址
function url($data = array(), $type = 0, $page = 1) {
    switch($type) {
        case 0:// 首页
            $rule = 'index';
            break;
        case 1:// 频道页
            if ($data['ca_url']) {
                return $data['ca_url'];
            }
            $rule = C('CHANNEL_URL_RULE');
            break;
        case 2:// 内容页
            $rule  = C('CONTENT_URL_RULE');
            break;
    }
    return parseRule($rule, $data, $page);
}

/**
 * 通过curl方式获取url地址的html内容
 * $url 目标网站
 * return string 返回HTML内容
 */
function getHtmlByCurl($url) {
    // curl 初始化
    $ch = curl_init();

    // 需要抓取的页面路径
    curl_setopt($ch, CURLOPT_URL, $url);

    // 伪造火狐浏览器
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);

    // 将获取的信息以文件流的形式返回，而不是直接输出
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // 超时时间
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);

    // 伪造ip
    curl_setopt($ch, CURLOPT_PROXY, C('CURL_AGENT_IP'));

    // 抓取的内容放在变量中
    $file_contents = curl_exec($ch);
    $curl_info = curl_getinfo($ch);

    // 状态码
    $http_code = $curl_info['http_code'];

    // 关闭 curl 资源
    curl_close($ch);
    if ($http_code == '200') {
        return $file_contents;
    } else {
        return '';
    }
}

// 获取PassportID
function getPassportId() {
    if ($_COOKIE['passport_id']) {
        return intval(passport_decrypt($_COOKIE['passport_id'], ILC_ENCRYPT_KEY));
    } else {
        return 0;
    }
}

// 设置PassportID
function setPassportId($id) {
    if (is_null($id)) { // 销毁Passport
        Cookie('passport_id', NULL);
    } elseif (!empty($id)) { // 设置Passport
        $value = passport_encrypt($id, ILC_ENCRYPT_KEY);
        Cookie('passport_id', $value);
        return $value;
    }
}

/**
 * 加密
 * @param mixed $data 内容
 * @param string $key 加密标识
 * @return mixed
 */
function passport_encrypt($data, $key) {
    $key = md5($key);
    $data = base64_encode($data);
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    for ($i = 0; $i < $len; $i ++) {
        if ($x == $l) $x = 0;
        $char .=substr($key, $x, 1);
        $x++;
    }
    for ($i = 0;$i < $len; $i ++) {
        $str .=chr(ord(substr($data,$i,1))+(ord(substr($char,$i,1)))%256);
    }
    return base64_encode($str);
}

/**
 * 解密
 * @param mixed $data 内容
 * @param string $key 解密标识
 * @return mixed
 */
function passport_decrypt($data, $key) {
    $key = md5($key);
    $x = 0;
    $data = base64_decode($data);
    $len = strlen($data);
    $l = strlen($key);

    for ($i = 0; $i < $len; $i ++) {
        if ($x == $l) $x = 0;
        $char .=substr($key, $x, 1);
        $x++;
    }

    for ($i = 0;$i < $len; $i ++) {

        if (ord(substr($data,$i,1))<ord(substr($char,$i,1))) {

            $str .=chr((ord(substr($data,$i,1))+256)-ord(substr($char,$i,1)));
        } else {
            $str .=chr(ord(substr($data,$i,1))-ord(substr($char,$i,1)));
        }
    }
    return base64_decode($str);
}

function xml2array($xml){
    foreach ($xml as $k => $v) {
        $res[$k] = (string)$xml->$k;
        if ($xml->$k->children()) {
            $res[$k] = xml2array($v);
        }
    }
    return $res;
}

function OpenFireFun($type, $data, $times = 1) {
    $OpenFire = D('OpenFire');
    switch ($type) {
        case 1:
            $res = $OpenFire->addCrowd($data);
            break;
        case 2:
            $res = $OpenFire->delCrowd($data);
            break;
        case 3:
            $res = $OpenFire->addUser($data);
            break;
        case 4:
            $res = $OpenFire->push($data);
            break;
    }

    if ($res['code'] != 200) {
        if ($times != 5) {
            OpenFireFun($type, $data, $times + 1);
        }
    }
}

?>