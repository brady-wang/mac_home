<?php

/************************************************** Auth Access Code **************************************************/

define('AUTH_ACCESS_STAT_MAIN',             20000); // 数据统计
define('AUTH_ACCESS_STAT_COUNT',            20050); // 数据统计 - 数据汇总
define('AUTH_ACCESS_STAT_MARKET',           20060); // 数据统计 - 市场数据
define('AUTH_ACCESS_STATMK_PROFILE',        20061); // 数据统计 - 市场数据 - 代理概况
define('AUTH_ACCESS_STATMK_INCOME',         20062); // 数据统计 - 市场数据 - 收入趋势
define('AUTH_ACCESS_STATMK_ECONOMIC',       20063); // 数据统计 - 市场数据 - 经济分析
define('AUTH_ACCESS_STAT_REALTIME',         20100); // 数据统计 - 实时数据
define('AUTH_ACCESS_STATREALTIME_ONLINE',   20101); // 数据统计 - 实时数据 - 在线人数
define('AUTH_ACCESS_STATREALTIME_REGISTER', 20102); // 数据统计 - 实时数据 - 新增注册
define('AUTH_ACCESS_STAT_USER',             20200); // 数据统计 - 用户数据
define('AUTH_ACCESS_STATUSER_DAILY',        20201); // 数据统计 - 用户数据 - 每日简报
define('AUTH_ACCESS_STATUSER_TOTAL',        20202); // 数据统计 - 用户数据 - 累计数据
define('AUTH_ACCESS_STATUSER_REMAIN',       20203); // 数据统计 - 用户数据 - 用户留存
define('AUTH_ACCESS_STATUSER_BEHAVE',       20204); // 数据统计 - 用户数据 - 行为统计
define('AUTH_ACCESS_STATUSER_SHARE',        20209); // 数据统计 - 用户数据 - 分享统计
define('AUTH_ACCESS_STATUSER_REGISTER',     20205); // 数据统计 - 用户数据 - 注册来源
define('AUTH_ACCESS_STATUSER_RANK',         20207); // 数据统计 - 用户数据 - 用户排行
define('AUTH_ACCESS_STATUSER_CHANNEL',      20208); // 数据统计 - 用户数据 - 渠道统计
define('AUTH_ACCESS_STAT_GAME',             20300); // 数据统计 - 游戏数据
define('AUTH_ACCESS_STATGAME_ROUND',        20301); // 数据统计 - 游戏数据 - 对局统计
define('AUTH_ACCESS_STATGAME_PLAY',         20302); // 数据统计 - 游戏数据 - 玩法统计
define('AUTH_ACCESS_STATGAME_CONSUME',      20303); // 数据统计 - 游戏数据 - 钻石消耗
define('AUTH_ACCESS_STATGAME_PRODUCE',      20304); // 数据统计 - 游戏数据 - 钻石产出

define('AUTH_ACCESS_GAME_MAIN',         40000); // 游戏管理
define('AUTH_ACCESS_GAME_LOG',          40100); // 游戏管理 - 日志查询
define('AUTH_ACCESS_GLOG_ROUND',        40102); // 游戏管理 - 日志查询 - 牌局日志
define('AUTH_ACCESS_GLOG_ROOM',         40103); // 游戏管理 - 日志查询 - 房间日志
define('AUTH_ACCESS_GAME_USER',         40200); // 游戏管理 - 玩家管理
define('AUTH_ACCESS_GUSER_INFO',        40201); // 游戏管理 - 玩家管理 - 用户信息
define('AUTH_ACCESS_GUSER_TERMINAL',    40203); // 游戏管理 - 玩家管理 - 解散房间
define('AUTH_ACCESS_GAME_NOTIFY',       40300); // 游戏管理 - 邮件信息
define('AUTH_ACCESS_GNOTIFY_MAIL',      40302); // 游戏管理 - 用户通知 - 邮件信息

define('AUTH_ACCESS_GF_MAIN',           50000); // 游戏配置
define('AUTH_ACCESS_GF_OPERATE',        50100); // 游戏配置 - 运营配置
define('AUTH_ACCESS_GF_OPERATECONF',    50101); // 游戏配置 - 运营配置 - 运营配置
define('AUTH_ACCESS_GF_HORSELAMP',      50102); // 游戏配置 - 运营配置 - 跑马灯配置
define('AUTH_ACCESS_GF_ADVERT',         50103); // 游戏配置 - 运营配置 - 广告配置
define('AUTH_ACCESS_GF_CSINFO',         50104); // 游戏配置 - 运营配置 - 客服界面信息配置
define('AUTH_ACCESS_GF_LOGINAD',        50105); // 游戏配置 - 运营配置 - 登录广告配置
define('AUTH_ACCESS_GF_RECRUIT',        50106); // 游戏配置 - 运营配置 - 招募代理配置
define('AUTH_ACCESS_GF_OPSHARECONT',    50107); // 游戏配置 - 运营配置 - 朋友圈-分享配置
define('AUTH_ACCESS_GF_OPFRIENDSHARE',  50108); // 游戏配置 - 运营配置 - 好友/群-分享配置
define('AUTH_ACCESS_GF_DAILYREWARD',    50109); // 游戏配置 - 运营配置 - 奖励配置
define('AUTH_ACCESS_GF_OPBINDPHONE',    50110); // 游戏配置 - 运营配置 - 绑定手机奖励配置
define('AUTH_ACCESS_GF_PROJECT',        50200); // 游戏配置 - 活动配置
define('AUTH_ACCESS_GF_PROREDPACK',     50201); // 游戏配置 - 活动配置 - 兑换红包
define('AUTH_ACCESS_GF_PROFREEDIO',     50202); // 游戏配置 - 活动配置 - 限时免钻
define('AUTH_ACCESS_GF_PRONEWREDPACK',  50203); // 游戏配置 - 活动配置 - 新手红包
define('AUTH_ACCESS_GF_ACTINVITEFRD',   50204); // 游戏配置 - 活动配置 - 新年拉新
define('AUTH_ACCESS_GF_MALL',           50300); // 游戏配置 - 商城配置
define('AUTH_ACCESS_GF_MALLCONF',       50301); // 游戏配置 - 商城配置 - 兑换商城
define('AUTH_ACCESS_GF_MALLOPER',       50303); // 游戏配置 - 商城配置 - 已下架
define('AUTH_ACCESS_GF_FRIEND',         50400); // 游戏配置 - 好友配置
define('AUTH_ACCESS_GF_FRDINVITE',      50401); // 游戏配置 - 好友配置 - 邀请好友
define('AUTH_ACCESS_GF_GAME',           50500); // 游戏配置 - 游戏配置
define('AUTH_ACCESS_GF_GAMEFEED',       50501); // 游戏配置 - 游戏配置 - 房费配置
define('AUTH_ACCESS_GF_GAMEUSER',       50502); // 游戏配置 - 游戏配置 - 角色配置
define('AUTH_ACCESS_GF_GAMEROOM',       50503); // 游戏配置 - 游戏配置 - 房间配置
define('AUTH_ACCESS_GF_GAMEAPP',        50700); // 游戏配置 - 产品设置
define('AUTH_ACCESS_GF_GAPPCONF',       50701); // 游戏配置 - 产品设置 - 维护控制
define('AUTH_ACCESS_GF_GAPPWHITELIST',  50702); // 游戏配置 - 产品设置 - 白名单设置
define('AUTH_ACCESS_GF_GAPPVERSION',    50703); // 游戏配置 - 产品设置 - 版本管理
define('AUTH_ACCESS_GF_GAPPLANDPAGE',   50704); // 游戏配置 - 产品设置 - 落地页设置
define('AUTH_ACCESS_GF_GAPPBLACKLIST',  50705); // 游戏配置 - 产品设置 - 黑名单设置
define('AUTH_ACCESS_GF_GAPPWHITELISTHP',50706); // 游戏配置 - 产品设置 - 白名单设置(热更)
define('AUTH_ACCESS_GF_GAPPAPPID',      50601); // 游戏配置 - 产品设置 - AppID配置
define('AUTH_ACCESS_GF_GAPPDOMAIN',     50602); // 游戏配置 - 产品设置 - 域名配置
define('AUTH_ACCESS_GF_GAPPMANUAL',     50609); // 游戏配置 - 产品设置 - 人工排查

define('AUTH_ACCESS_SYSTEM_MAIN',       90000); // 系统管理
define('AUTH_ACCESS_SYSTEM_ROLE',       90200); // 系统管理 - 角色管理
define('AUTH_ACCESS_ROLE_SHOW',         90201); // 系统管理 - 权限查询
define('AUTH_ACCESS_SYSTEM_USER',       90300); // 系统管理 - 用户管理
define('AUTH_ACCESS_SYSTEM_EDITION',    90400); // 系统管理 - 功能兼容管理
define('AUTH_ACCESS_SYSTEM_DATA',       90700); // 系统管理 - 数据管理
define('AUTH_ACCESS_SYSDATA_GAMECONF',  90701); // 系统管理 - 数据管理 - 游戏配置
define('AUTH_ACCESS_SYSDATA_DBCONF',    90702); // 系统管理 - 数据管理 - 外库配置
define('AUTH_ACCESS_SYSDATA_DBSHOW',    90703); // 系统管理 - 数据管理 - 数据库结构
define('AUTH_ACCESS_SYSDATA_DBALTER',   90704); // 系统管理 - 数据管理 - 数据库修改
define('AUTH_ACCESS_SYSTEM_LOG',        90800); // 系统管理 - 流水查询
define('AUTH_ACCESS_SYSLOG_OPER',       90801); // 系统管理 - 流水查询 - 操作流水
define('AUTH_ACCESS_SYSLOG_ERR',        90802); // 系统管理 - 流水查询 - 错误流水
define('AUTH_ACCESS_SYSLOG_API',        90803); // 系统管理 - 流水查询 - 接口流水
define('AUTH_ACCESS_SYSLOG_CRON',       90804); // 系统管理 - 流水查询 - 定时器流水
define('AUTH_ACCESS_ACTIVITY_MAIN',     100000); // 活动管理
define('AUTH_ACCESS_ACT_LIST',          100100); // 活动列表
define('AUTH_ACCESS_BAG_LIST',          100200); // 活动礼包 - 礼包管理
define('AUTH_ACCESS_REWARD_LIST',       100300); // 实物奖励列表 - 奖品列表

/************************************************** Auth Operate Code **************************************************/

// 游戏配置
define("AUTH_OPER_GF_DAILYREWARD_MGR",       501); // 游戏配置 - 奖励配置 - 添加、修改、删除
define('AUTH_OPER_GF_OPECONF_PASTE',         511);  // 游戏配置 - 运营配置 - 黏贴配置
define('AUTH_OPER_GF_OPECONF_DELETE',        512);  // 游戏配置 - 运营配置 - 删除配置
define('AUTH_OPER_GF_OPECONF_SHARE',         513);  // 游戏配置 - 运营配置 - 分享信息配置配置
define('AUTH_OPER_GF_OPECONF_HORSE',         514);  // 游戏配置 - 运营配置 - 跑马灯配置
define('AUTH_OPER_GF_OPECONF_ADV',           515);  // 游戏配置 - 运营配置 - 广告配置
define('AUTH_OPER_GF_OPECONF_CMS',           516);  // 游戏配置 - 运营配置 - 客服界面信息配置
define('AUTH_OPER_GF_OPECONF_LOGADV',        517);  // 游戏配置 - 运营配置 - 登录广告配置
define('AUTH_OPER_GF_OPECONF_CALLAGENT',     518);  // 游戏配置 - 运营配置 - 招募代理配置
define('AUTH_OPER_GF_SHARECONT_EDIT',        519);  // 游戏配置 - 运营配置 - 朋友圈-分享配置 - 添加、修改、复制、粘贴、删除
define("AUTH_OPER_GF_FRIENDSHARE_EDIT",      5100); // 游戏配置 - 运营配置 - 好友/群-分享配置 - 添加、修改、复制、粘贴、删除
define("AUTH_OPER_GF_BINDPHONE_EDIT",        5100); // 游戏配置 - 运营配置 - 绑定手机奖励配置 - 修改
define("AUTH_OPER_GF_OPECONF_DAILYREWARD",   5110); // 游戏配置 - 运营配置 - 每日分享奖励配置
define('AUTH_OPER_GF_PROREDPACK_DATE',       521);  // 游戏配置 - 兑换红包 - 活动日期
define('AUTH_OPER_GF_PROREDPACK_DROPRATE',   522);  // 游戏配置 - 兑换红包 - 元宝掉落
define('AUTH_OPER_GF_PROFREEDIO_MGR',        523);  // 游戏配置 - 限时免钻 - 修改
define('AUTH_OPER_GF_PRONEWREDPACK_MGR',     524);  // 游戏配置 - 新手红包 - 修改
define('AUTH_OPER_GF_ACTINVITEFRD_MGR',      525);  // 游戏配置 - 新年拉新 - 修改
define('AUTH_OPER_GF_MALLCONF_MGR',          531);  // 游戏配置 - 商城配置 - 添加、修改、删除
define('AUTH_OPER_GF_FRDINVITE_INVITEID',    541);  // 游戏配置 - 邀请好友 - 填写邀请人id修改
define('AUTH_OPER_GF_FRDINVITE_INVITEFRD',   542);  // 游戏配置 - 邀请好友 - 邀请好友修改
define('AUTH_OPER_GF_GAMEFEED_MGR',          551);  // 游戏配置 - 房费配置 - 修改
define('AUTH_OPER_GF_GAMEUSER_MGR',          552);  // 游戏配置 - 角色配置 - 修改
define('AUTH_OPER_GF_GAMEROOM_MGR',          554);  // 游戏配置 - 房间配置 - 修改
define('AUTH_OPER_GF_GAPPWHITELIST_MGR',     560);  // 游戏配置 - 产品配置 - 白名单 - 增删改
define('AUTH_OPER_GF_GAPPWHITELISTHP_MGR',   568);  // 游戏配置 - 产品配置 - 白名单(热更新) - 增删改
define('AUTH_OPER_GF_GAPPBLACKLIST_MGR',     564);  // 游戏配置 - 产品配置 - 黑名单 - 增删改
define('AUTH_OPER_GF_GAPPAPPID_ADD',         561);  // 游戏配置 - 产品配置 - AppID配置 - 添加
define('AUTH_OPER_GF_GAPPAPPID_EDT',         562);  // 游戏配置 - 产品配置 - AppID配置 - 修改
define('AUTH_OPER_GF_GAPPAPPID_DEL',         563);  // 游戏配置 - 产品配置 - AppID配置 - 删除
define('AUTH_OPER_GF_GAPPDOMAIN_ADD',        565);  // 游戏配置 - 产品配置 - 域名配置 - 添加
define('AUTH_OPER_GF_GAPPDOMAIN_EDT',        566);  // 游戏配置 - 产品配置 - 域名配置 - 修改
define('AUTH_OPER_GF_GAPPDOMAIN_DEL',        567);  // 游戏配置 - 产品配置 - 域名配置 - 删除
define('AUTH_OPER_GF_GAPP_IOS_DOWNLINK',     571);  // 游戏配置 - 产品配置 - 落地页配置 - ios下载地址
define('AUTH_OPER_GF_GAPP_ANDROID_DOWNLINK', 572);  // 游戏配置 - 产品配置 - 落地页配置 - 安卓下载地址
define('AUTH_OPER_GF_GAPPVERSION_MGR',       575);  // 游戏配置 - 产品配置 - 版本管理 - 提交版本、修改、取消
define('AUTH_OPER_GF_GAPPVERSION_PUBLISH',   576);  // 游戏配置 - 产品配置 - 版本管理 - 发布

// 游戏管理
define('AUTH_OPER_MN_GUSER_GIVE_YUANBAO',  411); // 游戏管理 - 玩家管理 - 用户信息 - 赠送元宝
define('AUTH_OPER_MN_GUSER_TERMINAL',      421); // 游戏管理 - 玩家管理 - 解散房间、重置玩家房间信息
define('AUTH_OPER_MN_MAILNOTICE_SEND',     431); // 游戏管理 - 邮件信息 - 发送邮件
define('AUTH_OPER_MN_MAILNOTICE_DEL',      432); // 游戏管理 - 邮件信息 - 删除定时邮件
define('AUTH_OPER_MN_MAILNOTICE_ATTREWARD',433); // 游戏管理 - 邮件信息 - 添加补偿物品
define('AUTH_OPER_MN_MAILNOTICE_VERIFY',   434); // 游戏管理 - 邮件信息 - 审核邮件

// 活动管理
define('AUTH_OPER_ACT_AVTIVITY_DETAIL',      601); // 活动管理 - 活动列表 - 添加活动
define('AUTH_OPER_ACT_AVTIVITY_OPER',     602); // 活动管理 - 活动列表 - 配置活动
define('AUTH_OPER_ACT_AVTIVITY_DATA',      603); // 活动管理 - 活动列表 - 活动数据
define('AUTH_OPER_ACT_AVTIVITY_TEST',      604); // 活动管理 - 活动列表 - 活动数据

// 系统管理
define('AUTH_OPER_SYS_ROLE_ADD',          901); // 系统管理 - 角色管理 - 新建
define('AUTH_OPER_SYS_ROLE_UPDATE',       902); // 系统管理 - 角色管理 - 修改
define('AUTH_OPER_SYS_ROLE_DELETE',       903); // 系统管理 - 角色管理 - 删除
define('AUTH_OPER_SYS_USER_ADD',          906); // 系统管理 - 用户管理 - 新建
define('AUTH_OPER_SYS_USER_UPDATE',       907); // 系统管理 - 用户管理 - 修改
define('AUTH_OPER_SYS_USER_DELETE',       908); // 系统管理 - 用户管理 - 删除
define('AUTH_OPER_SYS_DATA_GAMECONF_MGR', 971); // 系统管理 - 数据管理 - 游戏配置 - 增删改
define('AUTH_OPER_SYS_DATA_DBCONF_MGR',   973); // 系统管理 - 数据管理 - 外库配置 - 增删改
define('AUTH_OPER_SYS_DATA_DBALT_CHECK',  975); // 系统管理 - 数据管理 - 数据库修改 - 审核

/************************************************** Error Code **************************************************/

define('ERRCODE_SUCCESS',           0);    // 成功
define('ERRCODE_SYSTEM',            1000); // 系统错误
define('ERRCODE_PARAM_INVALID',     1001); // 无效参数
define('ERRCODE_PARAM_NULL',        1002); // 参数为空
define('ERRCODE_VALIDATE_FAILED',   1003); // 数据校验失败
define('ERRCODE_UPDATE_NONE',       1004); // 无修改
define('ERRCODE_OPER_UNAUTH',       1005); // 操作未授权
define('ERRCODE_API_ERR',           1006); // 接口出错
define('ERRCODE_DATA_OVERLAP',      1007); // 数据重复
define('ERRCODE_DATA_ERR',          1008); // 数据错误
define('ERRCODE_UPLOAD_FAILED',     1009); // 文件上传失败
define('ERRCODE_DB_DATA_EMPTY',     1100); // 数据库数据不存在
define('ERRCODE_DB_SELECT_ERR',     1101); // 数据库查询失败
define('ERRCODE_DB_ADD_ERR',        1102); // 数据库数据插入失败
define('ERRCODE_DB_UPDATE_ERR',     1103); // 数据库数据更新失败
define('ERRCODE_DB_DELETE_ERR',     1104); // 数据库数据删除失败
