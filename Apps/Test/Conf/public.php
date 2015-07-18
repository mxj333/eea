<?php
return array(
    // API接口类
    'ACTION' => array(
        'Public' => array(
            'title' => '公共类',
            'info' => '',
            'function'=> array(
                'init' => array(
                    'title' => '初始化',
                    'param'=> array(
                        'mtime' => array(
                            'title' => '时间',
                            'type' => 'int',
                            'required' => '1',
                            'value' => '0',
                        ),
                    ),
                ),
                 'login' => array(
                    'title' => '登录',
                    'param'=> array(
                        'username' => array(
                            'title' => '账号',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'password' => array(
                            'title' => '密码',
                            'type' => 'string',
                            'required' => '1',
                        ),
                    ),
                ),
                'logout' => array(
                    'title' => '退出',
                    'param'=> array(
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'skey' => array(
                            'title' => 'SKEY',
                            'type' => 'string',
                            'required' => '1',
                        ),
                    ),
                ),
            ),
        ),
        'Resource' => array(
            'title' => '资源类',
            'info' => '',
            'function'=> array(
                'lists' => array(
                    'title' => '列表',
                    'param'=> array(
                        'type' => array(
                            'title' => '列表类型',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        're_id' => array(
                            'title' => '地区ID',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        's_id' => array(
                            'title' => '学校ID',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'c_id' => array(
                            'title' => '班级ID',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'rc_id' => array(
                            'title' => '类型',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'res_title' => array(
                            'title' => '名称',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '',
                        ),
                        'res_is_published' => array(
                            'title' => '是否发布',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '9',
                        ),
                        'res_is_pass' => array(
                            'title' => '是否审核',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '9',
                        ),
                        'res_is_recommend' => array(
                            'title' => '是否推荐',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '9',
                        ),
                        'res_is_excellent' => array(
                            'title' => '是否推优',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '9',
                        ),
                        'res_is_pushed' => array(
                            'title' => '是否推送',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '9',
                        )
                    ),
                ),
                'shows' => array(
                    'title' => '信息',
                    'param'=> array(
                        'res_id' => array(
                            'title' => '标识ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                    ),
                ),
                'del' => array(
                    'title' => '删除',
                    'param'=> array(
                        'skey' => array(
                            'title' => 'key',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'res_id' => array(
                            'title' => '标识ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                    ),
                ),
                'addAll' => array(
                    'title' => '批量添加',
                    'param'=> array(
                        'skey' => array(
                            'title' => 'key',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'fields' => array(
                            'title' => '添加字段',
                            'type' => 'array',
                            'required' => '1',
                        ),
                        'values' => array(
                            'title' => '字段值',
                            'type' => 'array',
                            'required' => '1',
                        ),
                    ),
                ),
                'add' => array(
                    'title' => '新增',
                    'param'=> array(
                        'skey' => array(
                            'title' => 'key',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'rf_id' => array(
                            'title' => '资源文件ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'res_title' => array(
                            'title' => '标题',
                            'type' => 'string',
                            'required' => '1',
                            'value' => '',
                        ),
                        'rc_id' => array(
                            'title' => '使用类型',
                            'type' => 'int',
                            'required' => '1',
                            'value' => '1',
                        ),
                        're_id' => array(
                            'title' => '地区ID',
                            'type' => 'string',
                            'required' => '1',
                            'value' => '',
                        ),
                        're_title' => array(
                            'title' => '地区名称',
                            'type' => 'string',
                            'required' => '1',
                            'value' => '',
                        ),
                        's_id' => array(
                            'title' => '学校ID',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'c_id' => array(
                            'title' => '班级ID',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'res_is_recommend' => array(
                            'title' => '是否推荐',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '9',
                        ),
                        'res_is_excellent' => array(
                            'title' => '是否优质',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '9',
                        ),
                        'res_is_pushed' => array(
                            'title' => '是否推送',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '9',
                        ),
                        'res_is_published' => array(
                            'title' => '是否发布',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '9',
                        ),
                        'res_is_original' => array(
                            'title' => '是否原创',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '9',
                        ),
                        'res_author' => array(
                            'title' => '作者',
                            'type' => 'string',
                            'required' => '1',
                            'value' => '',
                        ),
                        'res_language' => array(
                            'title' => '语种',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '汉语',
                        ),
                        'res_metadata_language' => array(
                            'title' => '元数据语种',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '汉语',
                        ),
                        'res_audience_learner' => array(
                            'title' => '学习者',
                            'type' => 'int',
                            'required' => '1',
                            'value' => '1',
                        ),
                        'res_audience_educational_type' => array(
                            'title' => '教育类型',
                            'type' => 'int',
                            'required' => '1',
                            'value' => '8',
                        ),
                        'res_summary' => array(
                            'title' => '摘要',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        'res_published_name' => array(
                            'title' => '出版者姓名',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        'res_published_company' => array(
                            'title' => '出版者单位',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        'res_other_author' => array(
                            'title' => '其他作者',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        'res_version' => array(
                            'title' => '版本',
                            'type' => 'string',
                            'required' => '0',
                            'value' => 'V1.0',
                        ),
                        'res_permissions' => array(
                            'title' => '是否收费',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '9',
                        ),
                        'res_download_points' => array(
                            'title' => '所需智慧豆',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '0',
                        ),
                        'res_issused' => array(
                            'title' => '发行时间',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        'res_metadata_scheme' => array(
                            'title' => '元数据方案',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        'res_short_title' => array(
                            'title' => '短标题',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        'res_valid' => array(
                            'title' => '显示时间',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '',
                        ),
                        'res_avaliable' => array(
                            'title' => '有效期',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '',
                        ),
                    ),
                ),
                'edit' => array(
                    'title' => '编辑',
                    'param'=> array(
                        'skey' => array(
                            'title' => 'key',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'res_id' => array(
                            'title' => '资源标识ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'res_title' => array(
                            'title' => '标题',
                            'type' => 'string',
                            'required' => '1',
                            'value' => '',
                        ),
                        'rc_id' => array(
                            'title' => '使用类型',
                            'type' => 'int',
                            'required' => '1',
                            'value' => '1',
                        ),
                        're_id' => array(
                            'title' => '地区ID',
                            'type' => 'string',
                            'required' => '1',
                            'value' => '',
                        ),
                        're_title' => array(
                            'title' => '地区名称',
                            'type' => 'string',
                            'required' => '1',
                            'value' => '',
                        ),
                        's_id' => array(
                            'title' => '学校ID',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'c_id' => array(
                            'title' => '班级ID',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'res_is_recommend' => array(
                            'title' => '是否推荐',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '9',
                        ),
                        'res_is_excellent' => array(
                            'title' => '是否优质',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '9',
                        ),
                        'res_is_pushed' => array(
                            'title' => '是否推送',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '9',
                        ),
                        'res_is_published' => array(
                            'title' => '是否发布',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '9',
                        ),
                        'res_is_original' => array(
                            'title' => '是否原创',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '9',
                        ),
                        'res_author' => array(
                            'title' => '作者',
                            'type' => 'string',
                            'required' => '1',
                            'value' => '',
                        ),
                        'res_language' => array(
                            'title' => '语种',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '汉语',
                        ),
                        'res_metadata_language' => array(
                            'title' => '元数据语种',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '汉语',
                        ),
                        'res_audience_learner' => array(
                            'title' => '学习者',
                            'type' => 'int',
                            'required' => '1',
                            'value' => '1',
                        ),
                        'res_audience_educational_type' => array(
                            'title' => '教育类型',
                            'type' => 'int',
                            'required' => '1',
                            'value' => '8',
                        ),
                        'res_summary' => array(
                            'title' => '摘要',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        'res_published_name' => array(
                            'title' => '出版者姓名',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        'res_published_company' => array(
                            'title' => '出版者单位',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        'res_other_author' => array(
                            'title' => '其他作者',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        'res_version' => array(
                            'title' => '版本',
                            'type' => 'string',
                            'required' => '0',
                            'value' => 'V1.0',
                        ),
                        'res_permissions' => array(
                            'title' => '是否收费',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '9',
                        ),
                        'res_download_points' => array(
                            'title' => '所需智慧豆',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '0',
                        ),
                        'res_issused' => array(
                            'title' => '发行时间',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        'res_metadata_scheme' => array(
                            'title' => '元数据方案',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        'res_short_title' => array(
                            'title' => '短标题',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        'res_valid' => array(
                            'title' => '显示时间',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '',
                        ),
                        'res_avaliable' => array(
                            'title' => '有效期',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '',
                        ),
                    ),
                ),
                'upload' => array(
                    'title' => '资源文件上传',
                    'param'=> array(
                        'skey' => array(
                            'title' => 'key',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'file' => array(
                            'title' => '资源文件',
                            'type' => 'file',
                            'required' => '1',
                        ),
                    ),
                ),
                'getFile' => array(
                    'title' => '文件信息',
                    'param'=> array(
                        'rf_id' => array(
                            'title' => '文件ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                    ),
                ),
                'review' => array(
                    'title' => '资源审核',
                    'param'=> array(
                        'skey' => array(
                            'title' => 'key',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'res_id' => array(
                            'title' => '资源标识ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'res_is_pass' => array(
                            'title' => '是否通过',
                            'type' => 'int',
                            'required' => '1',
                        ),
                    ),
                ),
                'category' => array(
                    'title' => '使用类型',
                    'param'=> array(
                    ),
                ),
            ),
        ),
        'ResourceImport' => array(
            'title' => '资源导入',
            'info' => '',
            'function'=> array(
                'add' => array(
                    'title' => '资源文件处理',
                    'param'=> array(
                        'skey' => array(
                            'title' => 'key',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'res_path' => array(
                            'title' => '资源路径',
                            'type' => 'string',
                            'required' => '1',
                        ),
                    ),
                ),
            ),
        ),
        'ResourceRecycle' => array(
            'title' => '资源回收站',
            'info' => '',
            'function'=> array(
                'resume' => array(
                    'title' => '恢复',
                    'param'=> array(
                        'skey' => array(
                            'title' => 'key',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'res_id' => array(
                            'title' => '标识ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                    ),
                ),
                'del' => array(
                    'title' => '删除',
                    'param'=> array(
                        'skey' => array(
                            'title' => 'key',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'res_id' => array(
                            'title' => '标识ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                    ),
                ),
            ),
        ),
        'GradeSubjectRelation' => array(
            'title' => '年级学科关系类',
            'info' => '',
            'function'=> array(
                'lists' => array(
                    'title' => '列表',
                    'param'=> array(
                        're_id' => array(
                            'title' => '区域ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'gsr_school_type' => array(
                            'title' => '学制ID',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'gsr_grade' => array(
                            'title' => '年级ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                    ),
                ),
                'shows' => array(
                    'title' => '信息',
                    'param'=> array(
                        'gsr_id' => array(
                            'title' => '关系ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                    ),
                ),
            ),
        ),
        'Member' => array(
            'title' => '用户类',
            'info' => '',
            'function'=> array(
                'lists' => array(
                    'title' => '列表',
                    'param'=> array(
                        'me_nickname' => array(
                            'title' => '姓名',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        'me_account' => array(
                            'title' => '账号',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        'me_type' => array(
                            'title' => '用户类型',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        're_id' => array(
                            'title' => '地区ID',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        's_id' => array(
                            'title' => '学校ID',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'c_id' => array(
                            'title' => '班级ID',
                            'type' => 'int',
                            'required' => '0',
                        ),
                    ),
                ),
                'shows' => array(
                    'title' => '信息',
                    'param'=> array(
                        'auth_id' => array(
                            'title' => '标识ID',
                            'type' => 'int',
                            'required' => '1',
                            'value' => '0',
                        ),
                    ),
                ),
                'check' => array(
                    'title' => '账号检查',
                    'param'=> array(
                        'account' => array(
                            'title' => '账号',
                            'type' => 'int',
                            'required' => '1',
                        ),
                    ),
                ),
                'saveLoginStatus' => array(
                    'title' => '更新登录信息',
                    'param'=> array(
                        'skey' => array(
                            'title' => 'key',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'auth_id' => array(
                            'title' => '标识ID',
                            'type' => 'int',
                            'required' => '1',
                            'value' => '0',
                        ),
                        'login_ip' => array(
                            'title' => '登录ip',
                            'type' => 'int',
                            'required' => '1',
                            'value' => '0',
                        ),
                    ),
                ),
                'del' => array(
                    'title' => '删除',
                    'param'=> array(
                        'skey' => array(
                            'title' => 'key',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '标识ID',
                            'type' => 'string',
                            'required' => '1',
                            'value' => '',
                        ),
                    ),
                ),
                'add' => array(
                    'title' => '新增',
                    'param'=> array(
                        'skey' => array(
                            'title' => 'key',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_nickname' => array(
                            'title' => '姓名',
                            'type' => 'string',
                            'required' => '1',
                            'value' => '',
                        ),
                        'me_password' => array(
                            'title' => '密码',
                            'type' => 'string',
                            'required' => '1',
                            'value' => '',
                        ),
                        'me_validity' => array(
                            'title' => '有效期',
                            'type' => 'string',
                            'required' => '1',
                            'value' => '',
                        ),
                        'me_mobile' => array(
                            'title' => '手机',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        'me_phone' => array(
                            'title' => '电话',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        'me_email' => array(
                            'title' => '邮箱',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        'me_status' => array(
                            'title' => '状态',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '1',
                        ),
                        'md_sex' => array(
                            'title' => '性别',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '0',
                        ),
                        'md_birthday' => array(
                            'title' => '生日',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '0',
                        ),
                        'md_description' => array(
                            'title' => '描述',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        're_id' => array(
                            'title' => '地区ID',
                            'type' => 'string',
                            'required' => '1',
                            'value' => '',
                        ),
                        're_title' => array(
                            'title' => '地区名称',
                            'type' => 'string',
                            'required' => '1',
                            'value' => '',
                        ),
                        'md_chinese_name' => array(
                            'title' => '中文名',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        'md_english_name' => array(
                            'title' => '英文名',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        'md_native_place' => array(
                            'title' => '籍贯',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        'md_card_type' => array(
                            'title' => '证件类型',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '0',
                        ),
                        'md_card_num' => array(
                            'title' => '证件号',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        'md_political_type' => array(
                            'title' => '政治面貌',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '0',
                        ),
                        'md_blood_type' => array(
                            'title' => '血型',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '0',
                        ),
                        'me_type' => array(
                            'title' => '类型',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'me_note' => array(
                            'title' => '个人签名',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        's_id' => array(
                            'title' => '学校ID',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'c_id' => array(
                            'title' => '班级ID',
                            'type' => 'int',
                            'required' => '0',
                        ),
                    ),
                ),
                'edit' => array(
                    'title' => '编辑',
                    'param'=> array(
                        'skey' => array(
                            'title' => 'key',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '标识ID',
                            'type' => 'string',
                            'required' => '1',
                            'value' => '',
                        ),
                        'me_nickname' => array(
                            'title' => '姓名',
                            'type' => 'string',
                            'required' => '1',
                            'value' => '',
                        ),
                        'me_password' => array(
                            'title' => '密码',
                            'type' => 'string',
                            'required' => '1',
                            'value' => '',
                        ),
                        'me_validity' => array(
                            'title' => '有效期',
                            'type' => 'string',
                            'required' => '1',
                            'value' => '',
                        ),
                        'me_mobile' => array(
                            'title' => '手机',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        'me_phone' => array(
                            'title' => '电话',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        'me_email' => array(
                            'title' => '邮箱',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        'me_status' => array(
                            'title' => '状态',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '1',
                        ),
                        'md_sex' => array(
                            'title' => '性别',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '0',
                        ),
                        'md_birthday' => array(
                            'title' => '生日',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '0',
                        ),
                        'md_description' => array(
                            'title' => '描述',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        're_id' => array(
                            'title' => '地区ID',
                            'type' => 'string',
                            'required' => '1',
                            'value' => '',
                        ),
                        're_title' => array(
                            'title' => '地区名称',
                            'type' => 'string',
                            'required' => '1',
                            'value' => '',
                        ),
                        'md_chinese_name' => array(
                            'title' => '中文名',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        'md_english_name' => array(
                            'title' => '英文名',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        'md_native_place' => array(
                            'title' => '籍贯',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        'md_card_type' => array(
                            'title' => '证件类型',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '0',
                        ),
                        'md_card_num' => array(
                            'title' => '证件号',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        'md_political_type' => array(
                            'title' => '政治面貌',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '0',
                        ),
                        'md_blood_type' => array(
                            'title' => '血型',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '0',
                        ),
                        'me_type' => array(
                            'title' => '类型',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'me_note' => array(
                            'title' => '个人签名',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        's_id' => array(
                            'title' => '学校ID',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'c_id' => array(
                            'title' => '班级ID',
                            'type' => 'int',
                            'required' => '0',
                        ),
                    ),
                ),
                'getArchives' => array(
                    'title' => '档案列表',
                    'param'=> array(
                        'auth_id' => array(
                            'title' => '用户ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'mar_type' => array(
                            'title' => '类型',
                            'type' => 'int',
                            'required' => '1',
                        ),
                    ),
                ),
                'setArchives' => array(
                    'title' => '新增/班级档案',
                    'param'=> array(
                        'skey' => array(
                            'title' => 'key',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'auth_id' => array(
                            'title' => '标识ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'mar_id' => array(
                            'title' => '档案ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'mar_type' => array(
                            'title' => '类型',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'mar_starttime' => array(
                            'title' => '开始时间',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'mar_endtime' => array(
                            'title' => '结束时间',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'mar_school_type' => array(
                            'title' => '学制',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'mar_title' => array(
                            'title' => '标题',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'mar_subject' => array(
                            'title' => '学科',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'mar_score' => array(
                            'title' => '分数',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'mar_text' => array(
                            'title' => '文本',
                            'type' => 'int',
                            'required' => '0',
                        ),
                    ),
                ),
                'delArchives' => array(
                    'title' => '删除档案',
                    'param'=> array(
                        'skey' => array(
                            'title' => 'key',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'mar_id' => array(
                            'title' => '档案ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                    ),
                ),
                'getRelation' => array(
                    'title' => '亲属',
                    'param'=> array(
                        'auth_id' => array(
                            'title' => '学生ID',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'parent_id' => array(
                            'title' => '家长ID',
                            'type' => 'int',
                            'required' => '0',
                        ),
                    ),
                ),
                'setRelation' => array(
                    'title' => '新增/编辑亲属',
                    'param'=> array(
                        'auth_id' => array(
                            'title' => '学生ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'parent_id' => array(
                            'title' => '家长ID',
                            'type' => 'array',
                            'required' => '1',
                        ),
                    ),
                ),
            ),
        ),
        'School' => array(
            'title' => '学校类',
            'info' => '',
            'function'=> array(
                'lists' => array(
                    'title' => '列表',
                    'param'=> array(
                        're_id' => array(
                            'title' => '地区ID',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        's_type' => array(
                            'title' => '学制',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        's_divide' => array(
                            'title' => '划分',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        's_title' => array(
                            'title' => '名称',
                            'type' => 'string',
                            'required' => '0',
                        ),
                    ),
                ),
                'shows' => array(
                    'title' => '信息',
                    'param'=> array(
                        's_id' => array(
                            'title' => '标识ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                    ),
                ),
                'del' => array(
                    'title' => '删除',
                    'param'=> array(
                        'skey' => array(
                            'title' => 'key',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        's_id' => array(
                            'title' => '标识ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                    ),
                ),
                'add' => array(
                    'title' => '新增',
                    'param'=> array(
                        'skey' => array(
                            'title' => 'key',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        's_title' => array(
                            'title' => '校名',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        's_type' => array(
                            'title' => '学制',
                            'type' => 'int',
                            'required' => '1',
                            'value' => '15',
                        ),
                        's_divide' => array(
                            'title' => '划分',
                            'type' => 'int',
                            'required' => '1',
                            'value' => '1',
                        ),
                        's_status' => array(
                            'title' => '状态',
                            'type' => 'int',
                            'required' => '1',
                            'value' => '1',
                        ),
                        're_id' => array(
                            'title' => '地区ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        're_title' => array(
                            'title' => '地区名称',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        's_phone' => array(
                            'title' => '电话',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        's_description' => array(
                            'title' => '简介',
                            'type' => 'string',
                            'required' => '0',
                        ),
                    ),
                ),
                'edit' => array(
                    'title' => '编辑',
                    'param'=> array(
                        'skey' => array(
                            'title' => 'key',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        's_id' => array(
                            'title' => '标识ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        's_title' => array(
                            'title' => '校名',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        's_type' => array(
                            'title' => '学制',
                            'type' => 'int',
                            'required' => '1',
                            'value' => '15',
                        ),
                        's_divide' => array(
                            'title' => '划分',
                            'type' => 'int',
                            'required' => '1',
                            'value' => '1',
                        ),
                        's_status' => array(
                            'title' => '状态',
                            'type' => 'int',
                            'required' => '1',
                            'value' => '1',
                        ),
                        're_id' => array(
                            'title' => '地区ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        're_title' => array(
                            'title' => '地区名称',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        's_phone' => array(
                            'title' => '电话',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        's_description' => array(
                            'title' => '简介',
                            'type' => 'string',
                            'required' => '0',
                        ),
                    ),
                ),
            ),
        ),
        'AreaManager' => array(
            'title' => '区域管理员类',
            'info' => '',
            'function'=> array(
                'lists' => array(
                    'title' => '列表',
                    'param'=> array(
                    ),
                ),
                'info' => array(
                    'title' => '信息详情',
                    'param'=> array(
                        'id' => array(
                            'title' => '标识ID',
                            'type' => 'int',
                            'required' => '1',
                            'value' => '0',
                        ),
                    ),
                ),
                'check' => array(
                    'title' => '管理员检查',
                    'param'=> array(
                        'me_id' => array(
                            'title' => '管理员ID',
                            'type' => 'int',
                            'required' => '1',
                            'value' => '0',
                        ),
                        'ac_id' => array(
                            'title' => '应用类型ID',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '1',
                        ),
                    ),
                ),
                'delete' => array(
                    'title' => '删除',
                    'param'=> array(
                        'skey' => array(
                            'title' => 'key',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'am_id' => array(
                            'title' => '标识ID',
                            'type' => 'string',
                            'required' => '1',
                            'value' => '',
                        ),
                    ),
                ),
            ),
        ),
        'App' => array(
            'title' => '应用类',
            'function'=> array(
                'lists' => array(
                    'title' => '列表',
                    'param' => array(
                        'ac_id' => array(
                            'title' => '类型',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'a_title' => array(
                            'title' => '名称',
                            'type' => 'string',
                            'required' => '0',
                        ),
                    ),
                ),
                'category' => array(
                    'title' => '类型',
                    'param' => array(
                    ),
                ),
            ),
        ),
        'Article' => array(
            'title' => '资讯类',
            'info' => '',
            'function'=> array(
                'lists' => array(
                    'title' => '列表',
                    'param'=> array(
                        'type' => array(
                            'title' => '列表类型',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'art_title' => array(
                            'title' => '资讯标题',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        're_id' => array(
                            'title' => '地区ID',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '0',
                        ),
                        's_id' => array(
                            'title' => '学校ID',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '0',
                        ),
                        'm_id' => array(
                            'title' => '模型ID',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '0',
                        ),
                        'ca_id' => array(
                            'title' => '栏目ID',
                            'type' => 'int',
                            'required' => '',
                            'value' => '0',
                        ),
                    ),
                ),
                'shows' => array(
                    'title' => '信息详情',
                    'param'=> array(
                        'art_id' => array(
                            'title' => '标识ID',
                            'type' => 'int',
                            'required' => '1',
                            'value' => '0',
                        ),
                    ),
                ),
                'del' => array(
                    'title' => '删除',
                    'param'=> array(
                        'skey' => array(
                            'title' => 'key',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'art_id' => array(
                            'title' => '标识ID',
                            'type' => 'string',
                            'required' => '1',
                            'value' => '',
                        ),
                    ),
                ),
                'publish' => array(
                    'title' => '发布',
                    'param'=> array(
                        'skey' => array(
                            'title' => 'key',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'art_id' => array(
                            'title' => '标识ID',
                            'type' => 'strval',
                            'required' => '1',
                        ),
                    ),
                ),
                'forbid' => array(
                    'title' => '删除文件',
                    'param'=> array(
                        'skey' => array(
                            'title' => 'key',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'f_id' => array(
                            'title' => '文件标识ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                    ),
                ),
                'insert' => array(
                    'title' => '新增',
                    'param'=> array(
                        'skey' => array(
                            'title' => 'key',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'ca_id' => array(
                            'title' => '栏目ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'm_id' => array(
                            'title' => '类型ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'art_title' => array(
                            'title' => '标题',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'art_short_title' => array(
                            'title' => '短标题',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        'art_content' => array(
                            'title' => '内容',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        'art_keywords' => array(
                            'title' => '关键词',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        'art_summary' => array(
                            'title' => '摘要',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        'art_sort' => array(
                            'title' => '排序',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'art_position' => array(
                            'title' => '推荐位',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '0',
                        ),
                        'art_is_allow_comment' => array(
                            'title' => '是否允许评论',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '1',
                        ),
                        'art_designated_published' => array(
                            'title' => '发布时间',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'art_status' => array(
                            'title' => '状态',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '1',
                        ),
                        're_id' => array(
                            'title' => '地区ID',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        're_title' => array(
                            'title' => '地区名称',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        's_id' => array(
                            'title' => '学校ID',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '',
                        ),
                    ),
                ),
                'update' => array(
                    'title' => '编辑',
                    'param'=> array(
                        'skey' => array(
                            'title' => 'key',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'art_id' => array(
                            'title' => '标识ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'ca_id' => array(
                            'title' => '栏目ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'm_id' => array(
                            'title' => '类型ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'art_title' => array(
                            'title' => '标题',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'art_short_title' => array(
                            'title' => '短标题',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        'art_content' => array(
                            'title' => '内容',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        'art_keywords' => array(
                            'title' => '关键词',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        'art_summary' => array(
                            'title' => '摘要',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        'art_sort' => array(
                            'title' => '排序',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'art_position' => array(
                            'title' => '推荐位',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '0',
                        ),
                        'art_is_allow_comment' => array(
                            'title' => '是否允许评论',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '1',
                        ),
                        'art_designated_published' => array(
                            'title' => '发布时间',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'art_status' => array(
                            'title' => '状态',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '1',
                        ),
                        're_id' => array(
                            'title' => '地区ID',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        're_title' => array(
                            'title' => '地区名称',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        's_id' => array(
                            'title' => '学校ID',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '',
                        ),
                    ),
                ),
            ),
        ),
        'Category' => array(
            'title' => '资讯栏目类',
            'info' => '',
            'function'=> array(
                'lists' => array(
                    'title' => '列表',
                    'param'=> array(
                        're_id' => array(
                            'title' => '地区ID',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        's_id' => array(
                            'title' => '学校ID',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'ca_title' => array(
                            'title' => '栏目名称',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        'ca_status' => array(
                            'title' => '状态',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'ca_is_show' => array(
                            'title' => '是否显示',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'return_num' => array(
                            'title' => '返回记录数',
                            'type' => 'int',
                            'required' => '0',
                        ),
                    ),
                ),
                'shows' => array(
                    'title' => '信息',
                    'param'=> array(
                        'ca_id' => array(
                            'title' => '标识ID',
                            'type' => 'int',
                            'required' => '1',
                        )
                    ),
                ),
                'add' => array(
                    'title' => '新增',
                    'param'=> array(
                        'skey' => array(
                            'title' => 'key',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        're_id' => array(
                            'title' => '地区ID',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        're_title' => array(
                            'title' => '地区名称',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        's_id' => array(
                            'title' => '学校ID',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '',
                        ),
                        'ca_title' => array(
                            'title' => '标题',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'ca_name' => array(
                            'title' => '英文名',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'm_id' => array(
                            'title' => '类型ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'ca_keywords' => array(
                            'title' => '关键字',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        'ca_description' => array(
                            'title' => '描述',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        'ca_level' => array(
                            'title' => '层级',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '0',
                        ),
                        'ca_pid' => array(
                            'title' => '上级ID',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '0',
                        ),
                        'ca_sort' => array(
                            'title' => '排序',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '255',
                        ),
                        'ca_url' => array(
                            'title' => '跳转地址',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        'ca_tpl_index' => array(
                            'title' => '首页模板',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        'ca_tpl_detail' => array(
                            'title' => '详情模板',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        'ca_is_show' => array(
                            'title' => '是否展示',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '1',
                        ),
                        'ca_status' => array(
                            'title' => '状态',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '1',
                        ),
                    ),
                ),
                'edit' => array(
                    'title' => '编辑',
                    'param'=> array(
                        'skey' => array(
                            'title' => 'key',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'ca_id' => array(
                            'title' => '标识ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        're_id' => array(
                            'title' => '地区ID',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        're_title' => array(
                            'title' => '地区名称',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '',
                        ),
                        's_id' => array(
                            'title' => '学校ID',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '',
                        ),
                        'ca_title' => array(
                            'title' => '标题',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'ca_name' => array(
                            'title' => '英文名',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'm_id' => array(
                            'title' => '类型ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'ca_keywords' => array(
                            'title' => '关键字',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        'ca_description' => array(
                            'title' => '描述',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        'ca_level' => array(
                            'title' => '层级',
                            'type' => 'string',
                            'required' => '0',
                            'value' => '0',
                        ),
                        'ca_pid' => array(
                            'title' => '上级ID',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '0',
                        ),
                        'ca_sort' => array(
                            'title' => '排序',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '255',
                        ),
                        'ca_url' => array(
                            'title' => '跳转地址',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        'ca_tpl_index' => array(
                            'title' => '首页模板',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        'ca_tpl_detail' => array(
                            'title' => '详情模板',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        'ca_is_show' => array(
                            'title' => '是否展示',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '1',
                        ),
                        'ca_status' => array(
                            'title' => '状态',
                            'type' => 'int',
                            'required' => '0',
                            'value' => '1',
                        ),
                    ),
                ),
            ),
        ),
        'Friends' => array(
            'title' => '好友类',
            'info' => '',
            'function'=> array(
                'insert' => array(
                    'title' => '添加好友',
                    'param'=> array(
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'fr_friend_id' => array(
                            'title' => '好友ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'skey' => array(
                            'title' => 'SKEY',
                            'type' => 'string',
                            'required' => '1',
                        ),
                    ),
                ),
                'auditing' => array(
                    'title' => '好友审核/好友设置',
                    'param' =>array(
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'fr_friend_id' => array(
                            'title' => '被审核的好友ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'fr_status' => array(
                            'title' => '审核状态',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'skey' => array(
                            'title' => 'SKEY',
                            'type' => 'string',
                            'required' => '1',
                        ),
                    ),
                ),
                'lists' => array(
                    'title' => '好友列表',
                    'param' =>array(
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'fr_status' => array(
                            'title' => '好友状态(0：未审核的  1：审核通过的)',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'skey' => array(
                            'title' => 'SKEY',
                            'type' => 'string',
                            'required' => '1',
                        ),
                    ),
                ),
                'search' => array(
                    'title' => '好友查询',
                    'param' =>array(
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'me_account' => array(
                            'title' => '好友账号',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_nickname' => array(
                            'title' => '好友姓名',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'skey' => array(
                            'title' => 'SKEY',
                            'type' => 'string',
                            'required' => '1',
                        ),
                    ),
                ),
                'update' => array(
                    'title' => '好友更新',
                    'param' =>array(
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'fr_friend_id' => array(
                            'title' => '好友ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'fr_remark' => array(
                            'title' => '好友备注',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'skey' => array(
                            'title' => 'SKEY',
                            'type' => 'string',
                            'required' => '1',
                        ),
                    ),
                ),
                'groups' => array(
                    'title' => '好友分组',
                    'param' =>array(
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'fr_friend_id' => array(
                            'title' => '好友ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'fg_id' => array(
                            'title' => '好友分组',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'skey' => array(
                            'title' => 'SKEY',
                            'type' => 'string',
                            'required' => '1',
                        ),
                    ),
                ),
                'delete' => array(
                    'title' => '删除好友',
                    'param' =>array(
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'fr_friend_id' => array(
                            'title' => '好友ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'skey' => array(
                            'title' => 'SKEY',
                            'type' => 'string',
                            'required' => '1',
                        ),
                    ),
                ),
            ),
        ),
        'Crowd' => array(
            'title' => '群组类',
            'function'=> array(
                'insert' => array(
                    'title' => '新增/修改群组',
                    'param' => array(
                        'cro_title' => array(
                            'title' => '群组名称',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'skey' => array(
                            'title' => 'SKEY',
                            'type' => 'int',
                            'required' => '1',
                        ),
                    ),
                ),
                'lists' => array(
                    'title' => '群组列表',
                    'param' => array(
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'skey' => array(
                            'title' => 'SKEY',
                            'type' => 'int',
                            'required' => '1',
                        ),
                    ),
                ),
                'search' => array(
                    'title' => '群组查询',
                    'param' => array(
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'cro_num' => array(
                            'title' => '群号',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'cro_title' => array(
                            'title' => '群组名称',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'skey' => array(
                            'title' => 'SKEY',
                            'type' => 'int',
                            'required' => '1',
                        ),
                    ),
                ),
                'delete' => array(
                    'title' => '删除群组',
                    'param' => array(
                        'cro_id' => array(
                            'title' => '群组ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'int',
                            'required'=> '1',
                        ),
                        'skey' => array(
                            'title' => 'SKEY',
                            'type' => 'int',
                            'required'=> '1',
                        ),
                    ),
                ),
                'insertMembers' => array(
                    'title' => '添加群组成员/申请加入群组',
                    'param' => array(
                        'cro_id' => array(
                            'title' => '群组ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'skey' => array(
                            'title' => 'SKEY',
                            'type' => 'int',
                            'required' => '1',
                        ),
                    ),
                ),
                'listMembers' => array(
                    'title' => '群组成员列表',
                    'param' => array(
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'cro_id' => array(
                            'title' => '群组ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'cm_status' => array(
                            'title' => '成员状态',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'skey' => array(
                            'title' => 'SKEY',
                            'type' => 'string',
                            'required' => '1',
                        ),
                    ),
                ),
                'deleteMembers' => array(
                    'title' => '删除群组成员/用户退出群组',
                    'param' => array(
                        'cro_id' => array(
                            'title' => '群组ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'skey' => array(
                            'title' => 'SKEY',
                            'type' => 'int',
                            'required' => '1',
                        ),
                    ),
                ),
               'auditing' => array(
                    'title' => '审核群组成员/用户确认加群',
                    'param' => array(
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'cm_id' => array(
                            'title' => '群成员ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'cm_status' => array(
                            'title' => '审核状态',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'skey' => array(
                            'title' => 'SKEY',
                            'type' => 'int',
                            'required' => '1',
                        ),
                    ),
                ),
            ),
        ),
        'FriendsGroup' => array(
            'title' => '好友分组类',
            'function'=> array(
                'insert' => array(
                    'title' => '新增/修改分组',
                    'param' => array(
                        'fg_title' => array(
                            'title' => '名称',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'skey' => array(
                            'title' => 'SKEY',
                            'type' => 'int',
                            'required' => '1',
                        ),
                    ),
                ),
                'lists' => array(
                    'title' => '分组列表',
                    'param' => array(
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'skey' => array(
                            'title' => 'SKEY',
                            'type' => 'int',
                            'required' => '1',
                        ),
                    ),
                ),
                'sorts' => array(
                    'title' => '分组排序',
                    'param' => array(
                        'fg_id' => array(
                            'title' => '分组ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'skey' => array(
                            'title' => 'SKEY',
                            'type' => 'int',
                            'required' => '1',
                        ),
                    ),
                ),
                'delete' => array(
                    'title' => '删除分组',
                    'param' => array(
                        'fg_id' => array(
                            'title' => '分组ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'skey' => array(
                            'title' => 'SKEY',
                            'type' => 'int',
                            'required' => '1',
                        ),
                    ),
                ),
            ),
        ),
        'Tag' => array(
            'title' => '标签类',
            'function'=> array(
                'lists' => array(
                    'title' => '列表',
                    'param' => array(
                        't_type' => array(
                            'title' => '类型ID',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        't_title' => array(
                            'title' => '名称',
                            'type' => 'string',
                            'required' => '0',
                        ),
                    ),
                ),
                'category' => array(
                    'title' => '可用标签列',
                    'param' => array(
                    ),
                ),
            ),
        ),
        'Notice' => array(
            'title' => '通知类',
            'function'=> array(
                'lists' => array(
                    'title' => '列表',
                    'param' => array(
                        're_id' => array(
                            'title' => '地区ID',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        's_id' => array(
                            'title' => '学校ID',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'c_id' => array(
                            'title' => '班级ID',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'no_title' => array(
                            'title' => '标题',
                            'type' => 'string',
                            'required' => '0',
                        ),
                    ),
                ),
                'shows' => array(
                    'title' => '信息',
                    'param' => array(
                        'no_id' => array(
                            'title' => '标识ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                    ),
                ),
                'del' => array(
                    'title' => '删除',
                    'param' => array(
                        'no_id' => array(
                            'title' => '标识ID',
                            'type' => 'string',
                            'required' => '1',
                        ),
                    ),
                ),
                'add' => array(
                    'title' => '新增',
                    'param' => array(
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'skey' => array(
                            'title' => 'SKEY',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'no_title' => array(
                            'title' => '标题',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'no_url' => array(
                            'title' => '链接',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        'no_starttime' => array(
                            'title' => '发布时间',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'no_endtime' => array(
                            'title' => '到期时间',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'no_sort' => array(
                            'title' => '排序',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'no_content' => array(
                            'title' => '内容',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        're_id' => array(
                            'title' => '地区ID',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        're_title' => array(
                            'title' => '地区名称',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        's_id' => array(
                            'title' => '学校ID',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'c_id' => array(
                            'title' => '班级ID',
                            'type' => 'int',
                            'required' => '0',
                        ),
                    ),
                ),
                'edit' => array(
                    'title' => '编辑',
                    'param' => array(
                        'me_id' => array(
                            'title' => '用户ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'skey' => array(
                            'title' => 'SKEY',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'no_id' => array(
                            'title' => '标识ID',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'no_title' => array(
                            'title' => '标题',
                            'type' => 'string',
                            'required' => '1',
                        ),
                        'no_url' => array(
                            'title' => '链接',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        'no_starttime' => array(
                            'title' => '发布时间',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'no_endtime' => array(
                            'title' => '到期时间',
                            'type' => 'int',
                            'required' => '1',
                        ),
                        'no_sort' => array(
                            'title' => '排序',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'no_content' => array(
                            'title' => '内容',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        're_id' => array(
                            'title' => '地区ID',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        're_title' => array(
                            'title' => '地区名称',
                            'type' => 'string',
                            'required' => '0',
                        ),
                        's_id' => array(
                            'title' => '学校ID',
                            'type' => 'int',
                            'required' => '0',
                        ),
                        'c_id' => array(
                            'title' => '班级ID',
                            'type' => 'int',
                            'required' => '0',
                        ),
                    ),
                ),
            ),
        ),
    ),
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
        8 => array(
            'errCode' => 8,
            'errMessage' => '操作失败',
        ),
    ),

    'API_URL' => '/Api/',
);
?>