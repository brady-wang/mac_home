<?php

// 检测 PHP 环境
if (version_compare(PHP_VERSION, '5.3.0', '<')) die('require PHP > 5.3.0 !');

// 根目录
define('ROOT_PATH', __DIR__.'/');

// 是否调试模式，生产环境需要设为 false
define('APP_DEBUG', false);

// 根据环境状态加载配置文件
//   develop    开发环境加载 Apps/Common/Conf/develop.php
//   testing    测试环境加载 Apps/Common/Conf/testing.php
//   release    预发布环境加载 Apps/Common/Conf/release.php
//   production 生产环境加载 Apps/Common/Conf/production.php
define('APP_STATUS', '{{ tmpl_param_app_status }}');

// 引入ThinkPHP入口文件
require ROOT_PATH.'ThinkPHP/ThinkPHP.php';
