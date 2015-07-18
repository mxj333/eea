<?php
return array(
    'CMS_INSTALLED'         => 1,
    'URL_MODEL'             => 2,
    'LOAD_EXT_CONFIG' => 'db,public,linux,tags',
    'LOAD_EXT_FILE' => 'api,html',

    'DEFAULT_M_LAYER' => 'Logic',

    'LANG_SWITCH_ON' => true,
    'LANG_AUTO_DETECT' => true, // 自动侦测语言 开启多语言功能后有效
    'LANG_LIST'        => 'zh-cn', // 允许切换的语言列表 用逗号分隔
    'VAR_LANGUAGE'     => 'l', // 默认语言切换变量

    //Linux ffmepg 视频转码工具路径
    'FFMPEG_BIN_PATH' => '/usr/local/bin/ffmpeg',
    //Linux SWF文档转换工具路径
    'SWF_PATH' => '/usr/local/bin/pdf2swf',
    //Linux word转pdf
    'OPEN_OFFICE_SERVER' => '/usr/bin/unoconv --server localhost --port 8100',
    'ABIWORD' => 'abiword',
);