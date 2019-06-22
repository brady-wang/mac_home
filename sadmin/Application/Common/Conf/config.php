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
 * 应用配置: 调用所有模块之前都会首先加载的公共配置文件
 */
return array(

    // 启用模板布局
    'LAYOUT_ON' => true,
    'LAYOUT_PATH' => "./Template/Common/",
    'LAYOUT_NAME' => "layout",

    // 自定义配置文件
    'LOAD_EXT_CONFIG' => 'auth',

    // 自定义扩展文件，逗号之后不能空格
    'LOAD_EXT_FILE' => 'SAdminDefined',

    // 分页每张页面的记录数
    'PAGE_SIZE' => 20,

    // 图片上传最大数 MB
    'IMG_MAX_UPLOAD_SIZE' => 2,

    //用户排序名次数目
    'RANK_SIZE' => 200,
);
