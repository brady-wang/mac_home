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
 * 状态配置: 每个应用都可以在不同的情况下设置自己的状态（或者称之为应用场景），并且加载不同的配置文件。
 * 配置不同环境的参数，例如数据库配置等
 * 'APP_STATUS' 为 'production' 加载本配置(正式环境)
 */
return array(

    // 数据库配置信息
    'DB_TYPE' => 'mysql', // 数据库类型
    'DB_HOST' => '{{ tmpl_param_db_host }}', // 服务器地址
    'DB_NAME' => '{{ tmpl_param_db_name }}', // 数据库名
    'DB_USER' => '{{ tmpl_param_db_user }}', // 用户名
    'DB_PWD' => '{{ tmpl_param_db_pwd }}', // 密码
    'DB_PORT' => '{{ tmpl_param_db_port }}', // 端口
    'DB_PREFIX' => "sad_", // 数据库表前缀
    'DB_PARAMS' => array(PDO::ATTR_CASE=>PDO::CASE_NATURAL), // 数据库连接参数
    'DB_DEBUG' => true, // 数据库调试模式 开启后可以记录SQL日志
    'DB_CHARSET' => 'utf8', // 数据库编码默认采用utf8
    'DB_FIELDS_CACHE' => false, // 启用字段缓存

    // 系统加解密接口密钥，32位随机字符串
    'THINK_ENCRYPT_KEY' => '{{ tmpl_param_encrypt_key }}',

    // 资源服务器访问地址
    'RESOURCE_SERVER_IPHOST' => '{{ tmpl_param_resource_server_host }}',
    'RESOURCE_SERVER_PORT' => '{{ tmpl_param_resource_server_port }}',
    // 资源服文件传输地址
    'RESOURCE_API_IPHOST' => '{{ tmpl_param_resource_api_host }}',
    'RESOURCE_API_PORT' => '{{ tmpl_param_resource_api_port }}',

    // 分享后台地址
    'SHARE_SERVER_IPHOST' => '{{ tmpl_param_share_server_host }}',
    'SHARE_SERVER_PORT' => '{{ tmpl_param_share_server_port }}',

    // 白名单服务器地址
    'WHITELIST_SERVER_IPHOST' => '{{ tmpl_param_whitelist_server_host }}',
    'WHITELIST_SERVER_PORT' => '{{ tmpl_param_whitelist_server_port }}',

    // 落地页服务器地址
    'LANDPAGE_SERVER_IPHOST' => '{{ tmpl_param_landpage_server_host }}',
    'LANDPAGE_SERVER_PORT' => '{{ tmpl_param_landpage_server_port }}',

    // 阿里云CDN AccessKeyId
    'ALIYUN_CDN_ACCESSKEYID' => '{{ tmpl_param_ali_cdn_keyid }}',
    // 阿里云CDN AccessSecret
    'ALIYUN_CDN_ACCESSSECRET' => '{{ tmpl_param_ali_cdn_secret }}',
    // 阿里云CDN domain
    'ALIYUN_CDN_CACHE_DOMAIN' => '{{ tmpl_param_ali_cdn_domain }}',

    //系统跟JAVA系统通讯接口使用的签名私钥
    'GAME_API_PRIVATEKEY' => '{{ tmpl_param_game_api_privatekey }}',

    //活动服配置接口
    'ACT_API1_CONFURL' => '',
    'ACT_API1_CONFPORT' => '',

    'ACT_API2_CONFURL' => '',
    'ACT_API2_CONFPORT' => '',
    'ACT_API_CONFKEY' => '',
);
