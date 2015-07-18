<?php
return array(
    'MODULE_LIST' => array('Home', 'Manage', 'Api', 'Test', 'Install', 'Reagional', 'Area', 'Schoolback', 'School', 'Classback', 'Class', 'Member'),

    'HTML_CACHE_ON' => false,
    'HTML_CACHE_TIME' => 0,
    'HTML_FILE_SUFFIX' => '.shtml',
    'API_DATA_FORMAT' => 'JSON',
    'CURL_AGENT_IP' => '111.13.109.54:80',

    // 操作列表
    'ACTION_LIST' => array(
        1 => array(
            'name' => 'index',
            'value' => '1',
            'title' => L('HOME'),
        ),
        2 => array(
            'name' => 'add',
            'value' => '2',
            'title' => L('ADD'),
        ),
        4 => array(
            'name' => 'edit',
            'value' => '4',
            'title' => L('EDIT'),
        ),
        8 => array(
            'name' => 'shows',
            'value' => '8',
            'title' => L('SHOWS'),
        ),
        16 => array(
            'name' => 'lists',
            'value' => '16',
            'title' => L('QUERY'),
        ),
        32 => array(
            'name' => 'del',
            'value' => '32',
            'title' => L('DELETE'),
        ),
        64 => array(
            'name' => 'forbid',
            'value' => '64',
            'title' => L('DISABLE'),
        ),
        128 => array(
            'name' => 'resume',
            'value' => '128',
            'title' => L('RESUME'),
        ),
        256 => array(
            'name' => 'sort',
            'value' => '256',
            'title' => L('SORT'),
        ),
        512 => array(
            'name' => 'download',
            'value' => '512',
            'title' => L('DOWNLOAD'),
        ),
        1024 => array(
            'name' => 'return',
            'value' => '1024',
            'title' => L('RETURN'),
        ),
        2048 => array(
            'name' => 'publish',
            'value' => '2048',
            'title' => L('PUBLISH'),
        ),
        4096 => array(
            'name' => 'cache',
            'value' => '4096',
            'title' => L('CACHE'),
        ),
        8192 => array(
            'name' => 'sync',
            'value' => '8192',
            'title' => L('SYNCHRONOUS'),
        ),
        16384 => array(
            'name' => 'user',
            'value' => '16384',
            'title' => L('USER'),
        ),
        32768 => array(
            'name' => 'authorization',
            'value' => '32768',
            'title' => L('AUTHORIZATION'),
        ),
    ),

    'ARTICLE_POSITION' => array(
        0 => L('GENERAL'),
        1 => L('RECOMMEND'),
        2 => L('PROGRAM_RECOMMEND'),
        9 => L('HOME_RECOMMEND')
    ),

    // OpenFireIP
    'OPENFIRE_IP' => '192.168.1.3',

    // OpenFire端口
    'OPENFIRE_PORT' => '9090',

    // OpenFire开关 1:开 0:关
    'OPENFIRE_STATUS' => 1,

    // 聊天中上传的图片
    'OPENFIRE_FILES' => '../Uploads/OpenFire/',

);
?>