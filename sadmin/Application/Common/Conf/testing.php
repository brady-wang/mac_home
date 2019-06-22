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
 * 'APP_STATUS' 为 'testing' 加载本配置（测试环境）
 */
return array(

    // 数据库配置信息
    'DB_TYPE' => 'mysql', // 数据库类型
    'DB_HOST' => '192.168.7.105', // 服务器地址
    'DB_NAME' => 'sadmin_test', // 数据库名
    'DB_USER' => 'root', // 用户名
    'DB_PWD' => 'steve201718', // 密码
    'DB_PORT' => '3306', // 端口
    'DB_PREFIX' => "sad_", // 数据库表前缀
    'DB_PARAMS' => array(PDO::ATTR_CASE=>PDO::CASE_NATURAL), // 数据库连接参数
    'DB_DEBUG' => true, // 数据库调试模式 开启后可以记录SQL日志
    'DB_CHARSET' => 'utf8', // 数据库编码默认采用utf8
    'DB_FIELDS_CACHE' => false, // 启用字段缓存

    // 系统加解密接口密钥，32位随机字符串
    'THINK_ENCRYPT_KEY' => 'NoFaSId7REe2la2v8u8M1I80Wu1407e6',

    // 资源服务器访问地址
    'RESOURCE_SERVER_IPHOST' => 'http://test-resource.stevengame.com',
    'RESOURCE_SERVER_PORT' => 85,
    // 资源服文件传输地址
    'RESOURCE_API_IPHOST' => 'http://test-resource.stevengame.com',
    'RESOURCE_API_PORT' => 85,

    // 分享后台地址
    'SHARE_SERVER_IPHOST' => 'http://192.168.7.105',
    'SHARE_SERVER_PORT' => 8099,

    // 白名单服务器地址
    'WHITELIST_SERVER_IPHOST' => '192.168.7.105',
    'WHITELIST_SERVER_PORT' => 8089,

    // 落地页服务器地址
    'LANDPAGE_SERVER_IPHOST' => 'http://192.168.7.105',
    'LANDPAGE_SERVER_PORT' => 8088,

    // 阿里云CDN AccessKeyId
    'ALIYUN_CDN_ACCESSKEYID' => 'LTAIW5ggeJHc1hk1',
    // 阿里云CDN AccessSecret
    'ALIYUN_CDN_ACCESSSECRET' => 'dN8yXDn6H0bMFBV1Ci45eyBibL3pS3',
    // 阿里云CDN domain
    'ALIYUN_CDN_CACHE_DOMAIN' => 'client-update.stevengame.com',

    // 系统跟JAVA系统通讯接口使用的签名私钥
    'GAME_API_PRIVATEKEY' => 'nJ4pEN31ZAaAktVhhNfySU9QMvcrigyD',

    //活动服配置接口
    'ACT_API_CONFURL' => 'http://192.168.7.26',
    'ACT_API_CONFPORT' => '18482',

    'ACT_API1_CONFURL' => 'http://192.168.7.26',
    'ACT_API1_CONFPORT' => '18482',

    'ACT_API2_CONFURL' => 'http://192.168.7.26',
    'ACT_API2_CONFPORT' => '18482',

    'ACT_API_CONFKEY' => 'er3jiE$73S^$%',
);
