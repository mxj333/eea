<?php
return array(
    'USER_AUTH_ON'          => true,
    'NOT_AUTH_MODULE'       => 'Public',
    'REQUIRE_AUTH_MODULE'   => '',
    'REQUIRE_AUTH_ACTION'   => '',
    'RBAC_ROLE_TABLE'       => 'cms_role',
    'RBAC_USER_TABLE'       => 'cms_role_user',
    'RBAC_ACCESS_TABLE'     => 'cms_access',
    'RBAC_NODE_TABLE'       => 'cms_node',
    'USER_AUTH_KEY'         => 'reagionalId',
    'USER_AUTH_TYPE'        => 1,
    'ADMIN_AUTH_KEY'        => 'reagional_admin',
    'AUTH_PWD_ENCODER'      => 'md5',
    'USER_AUTH_GATEWAY'     => '/' . MODULE_NAME . '/Public/login',
    'USER_AUTH_MODEL'       => 'Member',
    'REQUIRE_AUTH_MODULE'   => '',
    'GUEST_AUTH_ON'         => FALSE,
    'GUEST_AUTH_ID'         => 0,
    'BACKGROUND_TYPE'       => 1, // 区域后台
);