<?php
return array(
    'DB_TYPE'   => 'mysql',
    'DB_USER'   => 'admin',
    'DB_PWD'    => 'mysql@jsqtech.com@)!$',
    'DB_HOST'   => 'localhost',
    'DB_PORT'   => '3306',
    'DB_NAME'   => 'dkt4',
    'DB_PREFIX' => 'dkt_',

    // 数据库调试模式 开启后可以记录SQL日志
    'DB_DEBUG' => TRUE,

    'DB_CONFIG1' => array(
        'DB_TYPE'   => 'mysql',
        'DB_USER'   => 'admin',
        'DB_PWD'    => 'mysql@jsqtech.com@)!$',
        'DB_HOST'   => 'localhost',
        'DB_PORT'   => '3306',
        'DB_NAME'   => 'cms',
        'DB_PREFIX' => 'cms_',
    ),

    'DB_CONFIG2' => 'mysql://root:root@jsqtech.com@localhost:3306/cms',
    'CACHE_TABLE_ENGINE' => 'MyISAM',
);