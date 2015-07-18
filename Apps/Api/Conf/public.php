<?php
return array(
    'ERROR_CODE' => array(

        1 => array(
            'errCode' => 1,
            'errMessage' => 'sign 校验出错',
        ),
        2 => array(
            'errCode' => 2,
            'errMessage' => '参数未传递',
        ),
        3 => array(
            'errCode' => 3,
            'errMessage' => '系统繁忙',
        ),
        4 => array(
            'errCode' => 4,
            'errMessage' => '没有权限操作',
        ),
        5 => array(
            'errCode' => 5,
            'errMessage' => '账号密码错误',
        ),
        6 => array(
            'errCode' => 6,
            'errMessage' => '非法数据操作',
        ),
        7 => array(
            'errCode' => 7,
            'errMessage' => '无符合条件的数据',
        ),
    ),

    // 不需审核的操作
    'VERIFY_METHOD' => array(
        //'Public.test',
    ),

    // 需审核的方法
    'VERIFY_CONTROLLER' => array(
        'add',  // 增
        'addAll',
        'edit',  // 改
        'del',  // 删
        'upload', // 上传
        'review', // 审核
        'resume', // 恢复
        'sync',
        'addRelation',
        'download',
        'forbid',
        'publish',
        'user',
        'import',
        'saveLoginStatus'
    ),
    // 需要反解的参数名
    'HAVE_URL_DECODE' => array(
        'cro_title',
    ),

    // 需要加魔术引号的参数
    'HAVE_MAGIC_QUOTES' => array(
        'hd_answer',
        'cd_answer',
    ),

);
?>