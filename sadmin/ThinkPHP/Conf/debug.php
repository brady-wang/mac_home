<?php

/**
 * 加载顺序：惯例配置 -> 应用配置 -> 模式配置 -> 调试配置 -> 状态配置 -> 模块配置 -> 扩展配置 -> 动态配置
 *
 * 惯例配置: ThinkPHP/Conf/convention.php
 * 应用配置: Applictaion/Common/Conf/config.php
 * 模式配置: Applictaion/Common/Conf/config_应用模式名称.php
 * 调试配置: ThinkPHP/Conf/debug.php
 * 状态配置: Applictaion/Common/Conf/xxx.php(xxx 为 APP_STATUS 值)
 * 模块配置: Applictaion/当前模块名/Conf/config.php
 * 扩展配置: 'LOAD_EXT_CONFIG' => 'config_file1, config_file2'
 * 动态配置: C('CONFIG_KEY_XXX', 'SET_VALUE');
 */

/**
 * ThinkPHP 默认的调试模式配置文件
 */
defined('THINK_PATH') or exit();
// 调试模式下面默认设置 可以在应用配置目录下重新定义 debug.php 覆盖
return  array(
    'LOG_RECORD'            =>  true, // 进行日志记录
    'LOG_EXCEPTION_RECORD'  =>  true, // 是否记录异常信息日志
    'LOG_LEVEL'             =>  'EMERG,ALERT,CRIT,ERR,WARN,NOTIC,INFO,DEBUG,SQL', // 允许记录的日志级别
    'DB_FIELDS_CACHE'       =>  false, // 字段缓存信息
    'DB_DEBUG'              =>  true, // 开启调试模式 记录SQL日志
    'TMPL_CACHE_ON'         =>  false, // 是否开启模板编译缓存,设为false则每次都会重新编译
    'TMPL_STRIP_SPACE'      =>  false, // 是否去除模板文件里面的html空格与换行
    'SHOW_ERROR_MSG'        =>  true, // 显示错误信息
    'URL_CASE_INSENSITIVE'  =>  false, // URL区分大小写
);
