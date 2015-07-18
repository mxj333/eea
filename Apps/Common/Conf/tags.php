<?php
return array(
    'app_begin' => array('Behavior\CheckLangBehavior'),
    'app_init' => array('Behavior\CheckLangBehavior'),
    'view_filter' => array('Behavior\TokenBuildBehavior'),
);