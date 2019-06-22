<?php

return array(
    // PATHINFO 映射表，用于根据 URL 匹配出本次访问所对应的末级访问权限代号
    'C_AUTH_ACCESS_MAP' => array(
        'Home' => array(
            // 【数据统计】
            'Stat' => array(
                // 数据汇总
                'staticsCount' => array('code' => AUTH_ACCESS_STAT_COUNT),
                'iframeStaticsCountDownFile' => array('code' => AUTH_ACCESS_STAT_COUNT),
                // 市场数据 - 代理概况
                'clubPromoter' => array('code' => AUTH_ACCESS_STATMK_PROFILE),
                'iframeClubPromoterDownFile' => array('code' => AUTH_ACCESS_STATMK_PROFILE),
                // 市场数据 - 收入趋势
                'clubIncome' => array('code' => AUTH_ACCESS_STATMK_INCOME),
                'iframeClubIncomeDownFile' => array('code' => AUTH_ACCESS_STATMK_INCOME),
                // 市场数据 - 经济分析
                'clubEconomic' => array('code' => AUTH_ACCESS_STATMK_ECONOMIC),
                'iframeClubEconomicDownFile' => array('code' => AUTH_ACCESS_STATMK_ECONOMIC),
                // 实时数据
                'realtimeOnline' => array('code' => AUTH_ACCESS_STATREALTIME_ONLINE),
                'realtimeRegister' => array('code' => AUTH_ACCESS_STATREALTIME_REGISTER),
                // 用户数据
                'user' => array(
                    'code' => AUTH_ACCESS_STAT_USER,
                    'third' => array(
                        'daily' => AUTH_ACCESS_STATUSER_DAILY, // 每日简报
                        'total' => AUTH_ACCESS_STATUSER_TOTAL, // 累计数据
                        'remain' => AUTH_ACCESS_STATUSER_REMAIN, // 用户留存
                        'behave' => AUTH_ACCESS_STATUSER_BEHAVE, // 行为统计
                        'register' => AUTH_ACCESS_STATUSER_REGISTER, // 注册来源
                        'rank' => AUTH_ACCESS_STATUSER_RANK, // 用户排行,
                        'channel' => AUTH_ACCESS_STATUSER_CHANNEL,  // 渠道统计
                    ),
                ),
                'iframeUserDailyDownFile' => array('code' => AUTH_ACCESS_STATUSER_DAILY),
                'iframeGetRegionInfo' => array('code' => AUTH_ACCESS_STATUSER_DAILY),
                // 用户数据 - 分享统计
                'userShare' => array('code' => AUTH_ACCESS_STATUSER_SHARE),
                // 游戏数据
                'game' => array(
                    'code' => AUTH_ACCESS_STAT_GAME,
                    'third' => array(
                        'consume' => AUTH_ACCESS_STATGAME_CONSUME, // 钻石消耗
                    ),
                ),
                // 游戏数据 - 对局统计
                'gameRound' => array('code' => AUTH_ACCESS_STATGAME_ROUND),
                'iframeGameRoundDownFile' => array('code' => AUTH_ACCESS_STATGAME_ROUND),
                // 游戏数据 - 玩法统计
                'gameRoom' => array('code' => AUTH_ACCESS_STATGAME_PLAY),
                'iframeGameRoomPlayDownFile' => array('code' => AUTH_ACCESS_STATGAME_PLAY),
                'iframeGameRoomNumDownFile' => array('code' => AUTH_ACCESS_STATGAME_PLAY),
                // 游戏数据 - 钻石产出
                'gameProduce' => array('code' => AUTH_ACCESS_STATGAME_PRODUCE),
                'iframeGameProduceDownFile' => array('code' => AUTH_ACCESS_STATGAME_PRODUCE),
            ),
            // 【游戏配置】
            'Gameconf' => array(
                // 运营配置
                'operate' => array(
                    'code' => AUTH_ACCESS_GF_OPERATE,
                    'third' => array(
                        'opeconf' => AUTH_ACCESS_GF_OPERATECONF, // 运营配置
                    ),
                ),
                'ajaxPasteOperate' => array('code' => AUTH_ACCESS_GF_OPERATECONF),
                'ajaxDelOperate' => array('code' => AUTH_ACCESS_GF_OPERATECONF),
                'ajaxSubmitHorse' => array('code' => AUTH_ACCESS_GF_OPERATECONF),
                'ajaxSubmitAdvImg' => array('code' => AUTH_ACCESS_GF_OPERATECONF),
                'ajaxSubmitCms' => array('code' => AUTH_ACCESS_GF_OPERATECONF),
                'ajaxSubmitCallAgent' => array('code' => AUTH_ACCESS_GF_OPERATECONF),
                'ajaxAdvUploadImages' => array('code' => AUTH_ACCESS_GF_OPERATECONF),
                // 运营配置 - 奖励配置
                'opDailyReward' => array('code' => AUTH_ACCESS_GF_DAILYREWARD),
                'ajaxSubmitDailyReward' => array('code' => AUTH_ACCESS_GF_DAILYREWARD),
                'ajaxDelDailyReward' => array('code' => AUTH_ACCESS_GF_DAILYREWARD),
                // 运营配置 - 登录广告配置
                'opLoginAd' => array('code' => AUTH_ACCESS_GF_LOGINAD),
                'ajaxSubmitLogAdv' => array('code' => AUTH_ACCESS_GF_LOGINAD),
                // 运营配置 - 朋友圈-分享配置
                'opShareCont' => array('code' => AUTH_ACCESS_GF_OPSHARECONT),
                'ajaxShareContSetCopyCookie' => array('code' => AUTH_ACCESS_GF_OPSHARECONT),
                'ajaxShareUploadThumbImg' => array('code' => AUTH_ACCESS_GF_OPSHARECONT),
                'ajaxShareUploadBgImg' => array('code' => AUTH_ACCESS_GF_OPSHARECONT),
                'ajaxPasteShareCont' => array('code' => AUTH_ACCESS_GF_OPSHARECONT),
                'ajaxAddShareCont' => array('code' => AUTH_ACCESS_GF_OPSHARECONT),
                'ajaxEditShareCont' => array('code' => AUTH_ACCESS_GF_OPSHARECONT),
                'ajaxDelShareCont' => array('code' => AUTH_ACCESS_GF_OPSHARECONT),
                // 运营配置 - 好友/群-分享配置
                'opFriendShare' => array('code' => AUTH_ACCESS_GF_OPFRIENDSHARE),
                'ajaxFriendShareSetCopyCookie' => array('code' => AUTH_ACCESS_GF_OPFRIENDSHARE),
                'ajaxPasteFriendShare' => array('code' => AUTH_ACCESS_GF_OPFRIENDSHARE),
                'ajaxAddFriendShare' => array('code' => AUTH_ACCESS_GF_OPFRIENDSHARE),
                'ajaxEditFriendShare' => array('code' => AUTH_ACCESS_GF_OPFRIENDSHARE),
                'ajaxDelFriendShare' => array('code' => AUTH_ACCESS_GF_OPFRIENDSHARE),
                // 运营配置 - 绑定手机奖励配置
                'opBindPhone' => array('code' => AUTH_ACCESS_GF_OPBINDPHONE),
                'ajaxEditBindPhone' => array('code' => AUTH_ACCESS_GF_OPBINDPHONE),
                // 活动配置
                'project' => array(
                    'code' => AUTH_ACCESS_GF_PROJECT,
                    'third' => array(
                        'proredpack' => AUTH_ACCESS_GF_PROREDPACK, // 兑换红包
                        'profreedio' => AUTH_ACCESS_GF_PROFREEDIO, // 限时免钻
                        'pronewredpack' => AUTH_ACCESS_GF_PRONEWREDPACK,//新手红包
                        'proactinvitefrd' => AUTH_ACCESS_GF_ACTINVITEFRD,//新年拉新
                    ),
                ),
                'ajaxSaveRedDateConf' => array('code' => AUTH_ACCESS_GF_PROREDPACK),
                'ajaxSaveRedGoldConf' => array('code' => AUTH_ACCESS_GF_PROREDPACK),
                'ajaxSaveProDio' => array('code' => AUTH_ACCESS_GF_PROFREEDIO),
                'ajaxSaveNewRedPack' => array('code' => AUTH_ACCESS_GF_PRONEWREDPACK),
                'ajaxSaveActInviteConf' => array('code' => AUTH_ACCESS_GF_ACTINVITEFRD),
                'ajaxResetRedGoldConf' => array('code' => AUTH_ACCESS_GF_PROREDPACK),
                // 商城配置
                'mall' => array(
                    'code' => AUTH_ACCESS_GF_MALL,
                    'third' => array(
                        'mallconf' => AUTH_ACCESS_GF_MALLCONF, // 兑换商城
                        'malloper' => AUTH_ACCESS_GF_MALLOPER, // 已下架
                        'editGoods' => AUTH_ACCESS_GF_MALLCONF,
                        'addGoods' => AUTH_ACCESS_GF_MALLCONF,
                        'malllist' => AUTH_ACCESS_GF_MALLCONF,
                    ),
                ),
                'mallUploadImages' => array('code' => AUTH_ACCESS_GF_MALL),
                'mallGetGoodsInfo' => array('code' => AUTH_ACCESS_GF_MALL),
                // 邀请好友
                'friend' => array(
                    'code' => AUTH_ACCESS_GF_FRIEND,
                    'third' => array(
                        'frdinvite' => AUTH_ACCESS_GF_FRDINVITE,
                    ),
                ),
                'ajaxsaveInviteId' => array('code'=>AUTH_ACCESS_GF_FRDINVITE),
                'ajaxsaveInviteConf' => array('code'=>AUTH_ACCESS_GF_FRDINVITE),
                /******************** 游戏配置 ********************/
                // 游戏配置
                'game' => array(
                    'code' => AUTH_ACCESS_GF_GAME,
                    'third' => array(
                        'gamefeed' => AUTH_ACCESS_GF_GAMEFEED, // 房费配置
                        'savegamefeed' => AUTH_ACCESS_GF_GAMEFEED,
                        'addgamefeed' => AUTH_ACCESS_GF_GAMEFEED,
                        'gameuser' => AUTH_ACCESS_GF_GAMEUSER, // 角色配置
                        'savegameuser' => AUTH_ACCESS_GF_GAMEUSER,
                    ),
                ),
                // 房间配置
                'confRoom' => array('code' => AUTH_ACCESS_GF_GAMEROOM),
                'ajaxRoomUploadThumbImg' => array('code' => AUTH_ACCESS_GF_GAMEROOM),
                'ajaxSaveRoomConf' => array('code' => AUTH_ACCESS_GF_GAMEROOM),
                /******************** 产品配置 ********************/
                // 维护控制
                'gameAppConf' => array('code' => AUTH_ACCESS_GF_GAPPCONF),
                'ajaxGameAppSaveConf' => array('code' => AUTH_ACCESS_GF_GAPPCONF),
                // 白名单
                'gameAppWhiteList' => array('code' => AUTH_ACCESS_GF_GAPPWHITELIST),
                'ajaxAddWhiteList' => array('code' => AUTH_ACCESS_GF_GAPPWHITELIST),
                'ajaxDelWhiteList' => array('code' => AUTH_ACCESS_GF_GAPPWHITELIST),
                //白名单（热更）
                'gameAppWhiteListHotUpdate' => array('code' => AUTH_ACCESS_GF_GAPPWHITELISTHP),
                'ajaxAddUpdateWhiteList' => array('code' => AUTH_ACCESS_GF_GAPPWHITELISTHP),
                'ajaxDelUpdateWhiteList' => array('code' => AUTH_ACCESS_GF_GAPPWHITELISTHP),
                'ajaxChangeUpdateWhiteConf' => array('code' => AUTH_ACCESS_GF_GAPPWHITELISTHP),
                'ajaxUpdatePlayVersion' => array('code' => AUTH_ACCESS_GF_GAPPWHITELISTHP),
                // 黑名单
                'gameAppBlackList' => array('code' => AUTH_ACCESS_GF_GAPPBLACKLIST),
                'ajaxAddBlackList' => array('code' => AUTH_ACCESS_GF_GAPPBLACKLIST),
                'ajaxDelBlackList' => array('code' => AUTH_ACCESS_GF_GAPPBLACKLIST),
                // 版本管理
                'gameAppVersion' => array('code' => AUTH_ACCESS_GF_GAPPVERSION),
                'ajaxGetVersionList' => array('code' => AUTH_ACCESS_GF_GAPPVERSION),
                'iframeGetVersionInfo' => array('code' => AUTH_ACCESS_GF_GAPPVERSION),
                'ajaxVersionUploadResource' => array('code' => AUTH_ACCESS_GF_GAPPVERSION),
                'ajaxAddVersion' => array('code' => AUTH_ACCESS_GF_GAPPVERSION),
                'ajaxEdtVersion' => array('code' => AUTH_ACCESS_GF_GAPPVERSION),
                'ajaxPublishVersion' => array('code' => AUTH_ACCESS_GF_GAPPVERSION),
                'ajaxCancelVersion' => array('code' => AUTH_ACCESS_GF_GAPPVERSION),
                // 落地页配置
                'gameAppLandpage'  => array('code' => AUTH_ACCESS_GF_GAPPLANDPAGE),
                'ajaxLandpageUploadImages' => array('code' => AUTH_ACCESS_GF_GAPPLANDPAGE),
                'ajaxSaveLandpage' => array('code' => AUTH_ACCESS_GF_GAPPLANDPAGE),
                // AppID配置
                'gameAppAppid' => array('code' => AUTH_ACCESS_GF_GAPPAPPID),
                'ajaxConfAddAppid' => array('code' => AUTH_ACCESS_GF_GAPPAPPID),
                'ajaxConfEditAppid' => array('code' => AUTH_ACCESS_GF_GAPPAPPID),
                'ajaxConfDeleteAppid' => array('code' => AUTH_ACCESS_GF_GAPPAPPID),
                // 域名配置
                'gameAppDomain' => array('code' => AUTH_ACCESS_GF_GAPPDOMAIN),
                'ajaxConfAddDomain' => array('code' => AUTH_ACCESS_GF_GAPPDOMAIN),
                'ajaxConfEditDomain' => array('code' => AUTH_ACCESS_GF_GAPPDOMAIN),
                'ajaxConfDeleteDomain' => array('code' => AUTH_ACCESS_GF_GAPPDOMAIN),
                // 人工排查
                'gameAppManual' => array('code' => AUTH_ACCESS_GF_GAPPMANUAL),
                'ajaxUpdateManualShareConf' => array('code' => AUTH_ACCESS_GF_GAPPMANUAL),
            ),
            // 【游戏管理】
            'Gamemgr' => array(
                // 日志查询
                'log' => array(
                    'code' => AUTH_ACCESS_GAME_LOG,
                    'third' => array(
                        'round' => AUTH_ACCESS_GLOG_ROUND, // 牌局日志
                        'showroundlog' => AUTH_ACCESS_GLOG_ROUND ,
                        'room' => AUTH_ACCESS_GLOG_ROOM, // 房间日志
                    ),
                ),
                // 玩家管理
                'user' => array(
                    'code' => AUTH_ACCESS_GAME_USER,
                    'third' => array(
                        'info' => AUTH_ACCESS_GUSER_INFO, // 用户信息
                        'terminal' => AUTH_ACCESS_GUSER_TERMINAL, // 解散房间
                    ),
                ),
                'ajaxQueryUser' => array('code' => AUTH_ACCESS_GUSER_INFO),
                'ajaxRemoveRoom' => array('code' => AUTH_ACCESS_GUSER_TERMINAL),
                'ajaxResetUser' => array('code' => AUTH_ACCESS_GUSER_TERMINAL),
                'ajaxGiveGold' => array('code' => AUTH_ACCESS_GUSER_INFO),
                // 邮件&公告
                'notify' => array(
                    'code' => AUTH_ACCESS_GAME_NOTIFY,
                    'third' => array(
                        'mail' => AUTH_ACCESS_GNOTIFY_MAIL, // 邮件信息
                    ),
                ),
                'ajaxSendTimerMail' => array('code' => AUTH_ACCESS_GNOTIFY_MAIL),
                'ajaxDelTimerMail' => array('code' => AUTH_ACCESS_GNOTIFY_MAIL),
                'ajaxVerifyMail' => array('code' => AUTH_ACCESS_GNOTIFY_MAIL),
                'ajaxBatchUserUpload' => array('code' => AUTH_ACCESS_GNOTIFY_MAIL),
                'downloadTemplate' => array('code' => AUTH_ACCESS_GNOTIFY_MAIL),
                'iframeGetMailInfo' => array('code' => AUTH_ACCESS_GNOTIFY_MAIL),
                'iframeExportMail' => array('code' => AUTH_ACCESS_GNOTIFY_MAIL),
            ),
            // 【游戏活动配置】
            'Activity' => array(
                'actList' => array('code' => AUTH_ACCESS_ACT_LIST),
                'actSet' => array('code' => AUTH_ACCESS_ACT_LIST),
                'actDetail' => array('code' => AUTH_ACCESS_ACT_LIST),
                'actAnalysis' => array('code' => AUTH_ACCESS_ACT_LIST),
                'actAnalysisByUid' => array('code' => AUTH_ACCESS_ACT_LIST),
                'actSave' => array('code' => AUTH_ACCESS_ACT_LIST),
                'ajaxShareUploadBgImg' => array('code' => AUTH_ACCESS_ACT_LIST),
                'setTestParams' => array('code' => AUTH_ACCESS_ACT_LIST),
                'bagList' => array('code' => AUTH_ACCESS_BAG_LIST),
                'bagSave' => array('code' => AUTH_ACCESS_BAG_LIST),
                'getBagInfoById' => array('code' => AUTH_ACCESS_BAG_LIST),
                'delBagById' => array('code' => AUTH_ACCESS_BAG_LIST),
                'rewardList' => array('code' => AUTH_ACCESS_REWARD_LIST),
                'ajaxEdtActConf' => array('code' => AUTH_ACCESS_ACT_LIST),
            ),
            // 【系统管理】
            'System' => array(
                // 角色管理
                'role' => array('code' => AUTH_ACCESS_SYSTEM_ROLE),
                'addRole' => array('code' => AUTH_ACCESS_SYSTEM_ROLE),
                'ajaxAddRole' => array('code' => AUTH_ACCESS_SYSTEM_ROLE),
                'editRole' => array('code' => AUTH_ACCESS_SYSTEM_ROLE),
                'ajaxEditRole' => array('code' => AUTH_ACCESS_SYSTEM_ROLE),
                'ajaxDelRole' => array('code' => AUTH_ACCESS_SYSTEM_ROLE),
                'viewRole' => array('code' => AUTH_ACCESS_SYSTEM_ROLE),
                // 用户管理
                'user' => array('code' => AUTH_ACCESS_SYSTEM_USER),
                'ajaxAddUser' => array('code' => AUTH_ACCESS_SYSTEM_USER),
                'ajaxEdtUser' => array('code' => AUTH_ACCESS_SYSTEM_USER),
                'ajaxDelUser' => array('code' => AUTH_ACCESS_SYSTEM_USER),
                'ajaxRecoverUser' => array('code' => AUTH_ACCESS_SYSTEM_USER),
                // 权限查询
                'showRole' => array('code' => AUTH_ACCESS_ROLE_SHOW),
                // 功能兼容管理
                'edition' => array('code' => AUTH_ACCESS_SYSTEM_EDITION),
                'ajaxAddEdition' => array('code' => AUTH_ACCESS_SYSTEM_EDITION),
                'ajaxEdtEdition' => array('code' => AUTH_ACCESS_SYSTEM_EDITION),
                'ajaxDelEdition' => array('code' => AUTH_ACCESS_SYSTEM_EDITION),
                // 数据管理
                'data' => array(
                    'code' => AUTH_ACCESS_SYSTEM_DATA,
                    'third' => array(
                        'gameconf' => AUTH_ACCESS_SYSDATA_GAMECONF,
                        'dbconf' => AUTH_ACCESS_SYSDATA_DBCONF,
                        'dbshow' => AUTH_ACCESS_SYSDATA_DBSHOW,
                        'dbalter' => AUTH_ACCESS_SYSDATA_DBALTER,
                    ),
                ),
                // 游戏配置
                'ajaxAddGameconf' => array('code' => AUTH_ACCESS_SYSDATA_GAMECONF),
                'ajaxEdtGameconf' => array('code' => AUTH_ACCESS_SYSDATA_GAMECONF),
                'ajaxDelGameconf' => array('code' => AUTH_ACCESS_SYSDATA_GAMECONF),
                // 外库配置
                'ajaxAddDbconf' => array('code' => AUTH_ACCESS_SYSDATA_DBCONF),
                'ajaxEdtDbconf' => array('code' => AUTH_ACCESS_SYSDATA_DBCONF),
                'ajaxDelDbconf' => array('code' => AUTH_ACCESS_SYSDATA_DBCONF),
                // 数据库修改
                'iframeGetSqlInfo' => array('code' => AUTH_ACCESS_SYSDATA_DBSHOW),
                'doApplySqlStatement' => array('code' => AUTH_ACCESS_SYSDATA_DBSHOW),
                'doUpdateSqlStatement' => array('code' => AUTH_ACCESS_SYSDATA_DBSHOW),
                'doCancelSqlStatement' => array('code' => AUTH_ACCESS_SYSDATA_DBSHOW),
                'doExecuteSqlStatement' => array('code' => AUTH_ACCESS_SYSDATA_DBSHOW),
                'doRejectSqlStatement' => array('code' => AUTH_ACCESS_SYSDATA_DBSHOW),
                // 流水查询
                'log' => array(
                    'code' => AUTH_ACCESS_SYSTEM_LOG,
                    'third' => array(
                        'operation' => AUTH_ACCESS_SYSLOG_OPER,
                        'error' => AUTH_ACCESS_SYSLOG_ERR,
                        'crontab' => AUTH_ACCESS_SYSLOG_CRON,
                    ),
                ),
                // 错误流水
                'iframeGetErrorInfo' => array('code' => AUTH_ACCESS_SYSLOG_ERR),
                'ajaxGetBatchData' => array('code' => AUTH_ACCESS_SYSLOG_ERR),
                'ajaxEditException' => array('code' => AUTH_ACCESS_SYSLOG_ERR),
                'ajaxEditExceptionBatch' => array('code' => AUTH_ACCESS_SYSLOG_ERR),
                //系统接口流水
                'logApi' => array('code' => AUTH_ACCESS_SYSLOG_API),
            ),
        ),
    ),

    // 导航栏信息 map
    'C_ACCESS_NAV_MAP' => array(
        AUTH_ACCESS_STAT_MAIN => array(
            'name' => '数据统计',
            'url' => '/Stat/realtimeOnline',
            'icon' => 'fa-chart-bar',
            'sublevel' => array(
                AUTH_ACCESS_STAT_COUNT => array(
                    'name' => '数据汇总',
                    'url' => '/Stat/staticsCount',
                ),
                AUTH_ACCESS_STAT_MARKET => array(
                    'name' => '市场数据',
                    'url' => '/Stat/clubPromoter',
                    'third' => array(
                        AUTH_ACCESS_STATMK_PROFILE => array(
                            'name' => '代理概况',
                            'url' => '/Stat/clubPromoter',
                        ),
                        AUTH_ACCESS_STATMK_INCOME => array(
                            'name' => '收入趋势',
                            'url' => '/Stat/clubIncome',
                        ),
                        AUTH_ACCESS_STATMK_ECONOMIC => array(
                            'name' => '经济分析',
                            'url' => '/Stat/clubEconomic',
                        ),
                    ),
                ),
                AUTH_ACCESS_STAT_REALTIME => array(
                    'name' => '实时数据',
                    'url' => '/Stat/realtimeOnline',
                    'third' => array(
                        AUTH_ACCESS_STATREALTIME_ONLINE => array(
                            'name' => '在线人数',
                            'url' => '/Stat/realtimeOnline',
                        ),
                        AUTH_ACCESS_STATREALTIME_REGISTER => array(
                            'name' => '新增注册',
                            'url' => '/Stat/realtimeRegister',
                        ),
                    ),
                ),
                AUTH_ACCESS_STAT_USER => array(
                    'name' => '用户数据',
                    'url' => '/Stat/user',
                    'third' => array(
                        AUTH_ACCESS_STATUSER_DAILY => array(
                            'name' => '每日简报',
                            'url' => '/Stat/user/third/daily',
                        ),
                        AUTH_ACCESS_STATUSER_TOTAL => array(
                            'name' => '累计数据',
                            'url' => '/Stat/user/third/total',
                        ),
                        AUTH_ACCESS_STATUSER_REMAIN => array(
                            'name' => '用户留存',
                            'url' => '/Stat/user/third/remain',
                        ),
                        AUTH_ACCESS_STATUSER_BEHAVE => array(
                            'name' => '行为统计',
                            'url' => '/Stat/user/third/behave',
                        ),
                        AUTH_ACCESS_STATUSER_SHARE => array(
                            'name' => '分享统计',
                            'url' => '/Stat/userShare',
                        ),
                        AUTH_ACCESS_STATUSER_REGISTER => array(
                            'name' => '注册来源',
                            'url' => '/Stat/user/third/register',
                        ),
                        AUTH_ACCESS_STATUSER_RANK => array(
                            'name' => '用户排行',
                            'url' => '/Stat/user/third/rank',
                        ),
                        AUTH_ACCESS_STATUSER_CHANNEL => array(
                            'name' => '渠道统计',
                            'url' => '/Stat/user/third/channel',
                        ),
                    ),
                ),
                AUTH_ACCESS_STAT_GAME => array(
                    'name' => '游戏数据',
                    'url' => '/Stat/game',
                    'third' => array(
                        AUTH_ACCESS_STATGAME_ROUND => array(
                            'name' => '对局统计',
                            'url' => '/Stat/gameRound',
                        ),
                        AUTH_ACCESS_STATGAME_PLAY => array(
                            'name' => '玩法统计',
                            'url' => '/Stat/gameRoom',
                        ),
                        AUTH_ACCESS_STATGAME_CONSUME => array(
                            'name' => '钻石消耗',
                            'url' => '/Stat/game/third/consume',
                        ),
                        AUTH_ACCESS_STATGAME_PRODUCE => array(
                            'name' => '钻石产出',
                            'url' => '/Stat/gameProduce',
                        ),
                    ),
                ),
            ),
        ),
        AUTH_ACCESS_GF_MAIN => array(
            'name' => '游戏配置',
            'url' => '/Gameconf/operate',
            'icon' => 'fa-wrench',
            'sublevel' => array(
                AUTH_ACCESS_GF_OPERATE => array(
                    'name' => '运营配置',
                    'url' => '/Gameconf/operate',
                    'third' => array(
                        AUTH_ACCESS_GF_OPERATECONF => array(
                            'name'=>'运营配置',
                            'url'=>'/Gameconf/operate/third/opeconf',
                            'oper' => array(
                                AUTH_OPER_GF_OPECONF_PASTE => '粘贴配置',
                                AUTH_OPER_GF_OPECONF_DELETE => '删除配置',
                                AUTH_OPER_GF_OPECONF_HORSE => '跑马灯修改',
                                AUTH_OPER_GF_OPECONF_ADV => '广告修改',
                                AUTH_OPER_GF_OPECONF_CMS => '客服修改',
                                AUTH_OPER_GF_OPECONF_LOGADV => '登录广告修改',
                                AUTH_OPER_GF_OPECONF_CALLAGENT => '招募代理修改',
                            ),
                        ),
                        AUTH_ACCESS_GF_DAILYREWARD => array(
                            'name' => '奖励配置',
                            'url'=>'/Gameconf/opDailyReward',
                            'oper' => array(
                                AUTH_OPER_GF_DAILYREWARD_MGR => '添加、修改、删除 ',
                            ),
                        ),
                        AUTH_ACCESS_GF_LOGINAD => array(
                            'name' => '登录广告配置',
                            'url'=>'/Gameconf/opLoginAd',
                            'oper' => array(
                                AUTH_OPER_GF_DAILYREWARD_MGR => '添加、修改、删除 ',
                            ),
                        ),
                        AUTH_ACCESS_GF_OPSHARECONT => array(
                            'name' => '朋友圈-分享配置',
                            'url'=>'/Gameconf/opShareCont',
                            'oper' => array(
                                AUTH_OPER_GF_SHARECONT_EDIT => '添加、修改、复制、粘贴、删除',
                            ),
                        ),
                        AUTH_ACCESS_GF_OPFRIENDSHARE => array(
                            'name' => '好友/群-分享配置',
                            'url'=>'/Gameconf/opFriendShare',
                            'oper' => array(
                                AUTH_OPER_GF_FRIENDSHARE_EDIT => '添加、修改、复制、粘贴、删除',
                            ),
                        ),
                        AUTH_ACCESS_GF_OPBINDPHONE => array(
                            'name' => '绑定手机奖励配置',
                            'url' => '/Gameconf/opBindPhone',
                            'oper' => array(
                                AUTH_OPER_GF_BINDPHONE_EDIT => '修改',
                            )
                        )
                    ),
                ),
                AUTH_ACCESS_GF_PROJECT => array(
                    'name' => '活动配置',
                    'url' => '/Gameconf/project',
                    'third' => array(
                        AUTH_ACCESS_GF_PROREDPACK => array(
                            'name'=>'兑换红包',
                            'url'=>'/Gameconf/project/third/proredpack',
                            'oper' => array(
                                AUTH_OPER_GF_PROREDPACK_DATE => '活动日期修改',
                                AUTH_OPER_GF_PROREDPACK_DROPRATE => '元宝掉落修改',
                            ),
                        ),
                        /*AUTH_ACCESS_GF_PROFREEDIO => array(
                            'name'=>'限时免钻',
                            'url'=>'/Gameconf/project/third/profreedio',
                            'oper' => array(
                                AUTH_OPER_GF_PROFREEDIO_MGR => '修改',
                            ),
                        ),*/
                        /*AUTH_ACCESS_GF_PRONEWREDPACK => array(
                            'name'=>'新手红包',
                            'url'=>'/Gameconf/project/third/pronewredpack',
                            'oper' => array(
                                AUTH_OPER_GF_PRONEWREDPACK_MGR => '修改',
                            ),
                        ),*/
                        AUTH_ACCESS_GF_ACTINVITEFRD => array(
                            'name'=>'新年拉新',
                            'url'=>'/Gameconf/project/third/proactinvitefrd',
                            'oper'=>array(
                                AUTH_OPER_GF_ACTINVITEFRD_MGR => '修改',
                            ),
                        ),
                    ),
                ),
                /*AUTH_ACCESS_GF_MALL => array(
                    'name' => '商城配置',
                    'url' => '/Gameconf/mall',
                    'third' => array(
                        AUTH_ACCESS_GF_MALLCONF => array(
                            'name'=>'兑换商城',
                            'url'=>'/Gameconf/mall/third/mallconf',
                            'oper' => array(
                                AUTH_OPER_GF_MALLCONF_MGR => '增删改',
                            ),
                        ),
                    ),
                ),*/
                /*AUTH_ACCESS_GF_FRIEND => array(
                    'name' => '好友配置',
                    'url' => '/Gameconf/friend',
                    'third' => array(
                        AUTH_ACCESS_GF_FRDINVITE => array(
                            'name'=>'邀请好友',
                            'url'=>'/Gameconf/friend/third/frdinvite',
                            'oper' => array(
                                AUTH_OPER_GF_FRDINVITE_INVITEID => '填写邀请人ID修改',
                                AUTH_OPER_GF_FRDINVITE_INVITEFRD => '邀请好友修改',
                            ),
                        ),
                    ),
                ),*/
                AUTH_ACCESS_GF_GAME => array(
                    'name' => '游戏配置',
                    'url' => '/Gameconf/game',
                    'third' => array(
                        AUTH_ACCESS_GF_GAMEFEED => array(
                            'name'=>'房费配置',
                            'url'=>'/Gameconf/game/third/gamefeed',
                            'oper' => array(
                                AUTH_OPER_GF_GAMEFEED_MGR => '修改',
                            ),
                        ),
                        AUTH_ACCESS_GF_GAMEUSER => array(
                            'name'=>'角色配置',
                            'url'=>'/Gameconf/game/third/gameuser',
                        ),
                        AUTH_ACCESS_GF_GAMEROOM => array(
                            'name' => '房间配置',
                            'url' => '/Gameconf/confRoom',
                            'oper' => array(
                                AUTH_OPER_GF_GAMEROOM_MGR => '修改',
                            ),
                        ),
                    ),
                ),
                AUTH_ACCESS_GF_GAMEAPP => array(
                    'name' => '产品配置',
                    'url' => '/Gameconf/gameAppConf',
                    'third' => array(
                        AUTH_ACCESS_GF_GAPPCONF => array(
                            'name' => '维护控制',
                            'url' => '/Gameconf/gameAppConf',
                        ),
                        AUTH_ACCESS_GF_GAPPWHITELIST => array(
                            'name' => '白名单设置',
                            'url' => '/Gameconf/gameAppWhiteList',
                            'oper' => array(
                                AUTH_OPER_GF_GAPPWHITELIST_MGR => '增删改',
                            ),
                        ),
                        AUTH_ACCESS_GF_GAPPWHITELISTHP => array(
                            'name' => '热更白名单设置',
                            'url' => '/Gameconf/gameAppWhiteListHotUpdate',
                            'oper' => array(
                                AUTH_OPER_GF_GAPPWHITELISTHP_MGR => '增删改',
                            ),
                        ),
                        AUTH_ACCESS_GF_GAPPBLACKLIST => array(
                            'name' => '黑名单设置',
                            'url' => '/Gameconf/gameAppBlackList',
                            'oper' => array(
                                AUTH_OPER_GF_GAPPBLACKLIST_MGR => '增删改',
                            ),
                        ),
                        AUTH_ACCESS_GF_GAPPVERSION => array(
                            'name' => '版本管理',
                            'url' => '/Gameconf/gameAppVersion',
                            'oper' => array(
                                AUTH_OPER_GF_GAPPVERSION_MGR => '提交版本、修改、取消',
                                AUTH_OPER_GF_GAPPVERSION_PUBLISH => '发布',
                            ),
                        ),
                        AUTH_ACCESS_GF_GAPPLANDPAGE => array(
                            'name'=>'落地页配置',
                            'url'=>'/Gameconf/gameAppLandpage',
                            'oper'=>array(
                                AUTH_OPER_GF_GAPP_IOS_DOWNLINK => 'IOS下载地址修改',
                                AUTH_OPER_GF_GAPP_ANDROID_DOWNLINK => '安卓下载地址修改',
                            ),
                        ),
                        AUTH_ACCESS_GF_GAPPAPPID => array(
                            'name'=>'AppID配置',
                            'url'=>'/Gameconf/gameAppAppid',
                            'oper' => array(
                                AUTH_OPER_GF_GAPPAPPID_ADD => '添加',
                                AUTH_OPER_GF_GAPPAPPID_EDT => '修改',
                                AUTH_OPER_GF_GAPPAPPID_DEL => '删除',
                            ),
                        ),
                        AUTH_ACCESS_GF_GAPPDOMAIN => array(
                            'name'=>'域名配置',
                            'url'=>'/Gameconf/gameAppDomain',
                            'oper' => array(
                                AUTH_OPER_GF_GAPPDOMAIN_ADD => '添加',
                                AUTH_OPER_GF_GAPPDOMAIN_EDT => '修改',
                                AUTH_OPER_GF_GAPPDOMAIN_DEL => '删除',
                            ),
                        ),
                        AUTH_ACCESS_GF_GAPPMANUAL => array(
                            'name'=>'人工排查',
                            'url'=>'/Gameconf/gameAppManual',
                        ),
                    ),
                ),
            ),
        ),
        AUTH_ACCESS_GAME_MAIN => array(
            'name' => '游戏管理',
            'url' => '/Gamemgr/log',
            'icon' => 'fa-sitemap',
            'sublevel' => array(
                AUTH_ACCESS_GAME_LOG => array(
                    'name' => '日志查询',
                    'url' => '/Gamemgr/log',
                    'third' => array(
                        AUTH_ACCESS_GLOG_ROUND => array(
                            'name' => '牌局日志',
                            'url' => '/Gamemgr/log/third/round',
                        ),
                        AUTH_ACCESS_GLOG_ROOM => array(
                            'name' => '房间日志',
                            'url' => '/Gamemgr/log/third/room',
                        ),
                    ),
                ),
                AUTH_ACCESS_GAME_USER => array(
                    'name' => '玩家管理',
                    'url' => '/Gamemgr/user',
                    'third' => array(
                        AUTH_ACCESS_GUSER_INFO => array(
                            'name' => '用户信息',
                            'url' => '/Gamemgr/user/third/info',
                            'oper' => array(
                                AUTH_OPER_MN_GUSER_GIVE_YUANBAO => '赠送元宝'
                            )
                        ),
                        AUTH_ACCESS_GUSER_TERMINAL => array(
                            'name' => '解散房间',
                            'url' => '/Gamemgr/user/third/terminal',
                            'oper' => array(
                                AUTH_OPER_MN_GUSER_TERMINAL => '解散房间、重置玩家房间信息',
                            )
                        ),
                    ),
                ),
                AUTH_ACCESS_GAME_NOTIFY => array(
                    'name' => '邮件信息',
                    'url' => '/Gamemgr/notify',
                    'third' => array(
                        AUTH_ACCESS_GNOTIFY_MAIL => array(
                            'name' => '邮件信息',
                            'url' => '/Gamemgr/notify/third/mail',
                            'oper' => array(
                                AUTH_OPER_MN_MAILNOTICE_SEND => '发送邮件',
                                AUTH_OPER_MN_MAILNOTICE_DEL => '删除邮件',
                                AUTH_OPER_MN_MAILNOTICE_ATTREWARD => '添加补偿物品',
                                AUTH_OPER_MN_MAILNOTICE_VERIFY => '审核邮件',
                            ),
                        ),
                    ),
                ),
            ),
        ),
        AUTH_ACCESS_ACTIVITY_MAIN => array(
            'name' => '活动配置',
            'url' => '/Activity/actList',
            'icon' => 'fa-suitcase',
            'sublevel' => array(
                AUTH_ACCESS_ACT_LIST => array(
                    'name' => '活动管理',
                    'url' => '/Activity/actList',
                    'nodeType' => 0,
                    'oper' => array(
                        AUTH_OPER_ACT_AVTIVITY_DETAIL => '活动详情',
                        AUTH_OPER_ACT_AVTIVITY_DATA => '活动数据分析',
                        AUTH_OPER_ACT_AVTIVITY_OPER => '活动配置保存',
                        AUTH_OPER_ACT_AVTIVITY_TEST => '测试参数配置',
                    ),
                ),
                AUTH_ACCESS_BAG_LIST => array(
                    'name' => '礼包管理',
                    'url' => '/Activity/bagList',
                    'nodeType' => 0,
                    'oper' => array(
                        AUTH_OPER_ACT_GAPPAPPID_ADD => '添加',
                        AUTH_OPER_ACT_GAPPAPPID_EDT => '修改',
                        AUTH_OPER_ACT_GAPPAPPID_DEL => '删除',
                    ),
                ),
                AUTH_ACCESS_REWARD_LIST => array(
                     'name' => '实物奖励管理',
                    'url' => '/Activity/rewardList',
                    'nodeType' => 0,
                    'oper' => array(
                        AUTH_OPER_ACT_GAPPAPPID_ADD => '添加',
                        AUTH_OPER_ACT_GAPPAPPID_EDT => '修改',
                        AUTH_OPER_ACT_GAPPAPPID_DEL => '删除',
                    ),
                ),
            ),
        ),
        AUTH_ACCESS_SYSTEM_MAIN => array(
            'name' => '系统管理',
            'url' => '/System/role',
            'icon' => 'fa-server',
            'sublevel' => array(
                AUTH_ACCESS_SYSTEM_ROLE => array(
                    'name' => '角色管理',
                    'url' => '/System/role',
                    'oper' => array(
                        AUTH_OPER_SYS_ROLE_ADD => '添加',
                        AUTH_OPER_SYS_ROLE_UPDATE => '修改',
                        AUTH_OPER_SYS_ROLE_DELETE => '删除',
                    ),
                ),
                AUTH_ACCESS_SYSTEM_USER => array(
                    'name' => '用户管理',
                    'url' => '/System/user',
                    'oper' => array(
                        AUTH_OPER_SYS_USER_ADD => '添加',
                        AUTH_OPER_SYS_USER_UPDATE => '修改',
                        AUTH_OPER_SYS_USER_DELETE => '删除',
                    ),
                ),
                AUTH_ACCESS_ROLE_SHOW => array(
                    'name' => '权限查询',
                    'url' => '/System/showRole',
                ),
                AUTH_ACCESS_SYSTEM_EDITION => array (
                    'name' => '功能兼容管理',
                    'url' => '/System/edition',
                ),
                AUTH_ACCESS_SYSTEM_DATA => array(
                    'name' => '数据管理',
                    'url' => '/System/data',
                    'third' => array(
                        AUTH_ACCESS_SYSDATA_GAMECONF => array(
                            'name' => '游戏配置',
                            'url' => '/System/data/third/gameconf',
                            'oper' => array(
                                AUTH_OPER_SYS_DATA_GAMECONF_MGR => '增删改',
                            ),
                        ),
                        AUTH_ACCESS_SYSDATA_DBCONF => array(
                            'name' => '外库配置',
                            'url' => '/System/data/third/dbconf',
                            'oper' => array(
                                AUTH_OPER_SYS_DATA_DBCONF_MGR => '增删改',
                            ),
                        ),
                        AUTH_ACCESS_SYSDATA_DBSHOW => array(
                            'name' => '数据库结构',
                            'url' => '/System/data/third/dbshow',
                        ),
                        AUTH_ACCESS_SYSDATA_DBALTER => array(
                            'name' => '数据库修改',
                            'url' => '/System/data/third/dbalter',
                            'oper' => array(
                                AUTH_OPER_SYS_DATA_DBALT_CHECK => '审核',
                            ),
                        ),
                    ),
                ),
                AUTH_ACCESS_SYSTEM_LOG => array(
                    'name' => '流水查询',
                    'url' => '/System/log',
                    'third' => array(
                        AUTH_ACCESS_SYSLOG_OPER => array(
                            'name' => '操作流水',
                            'url' => '/System/log/third/operation',
                        ),
                        AUTH_ACCESS_SYSLOG_ERR => array(
                            'name' => '系统错误',
                            'url' => '/System/log/third/error',
                        ),
                        AUTH_ACCESS_SYSLOG_API => array(
                            'name' => '接口流水',
                            'url' => '/System/logApi',
                        ),
                        AUTH_ACCESS_SYSLOG_CRON => array(
                            'name' => '定时器日志',
                            'url' => '/System/log/third/crontab',
                        ),
                    ),
                ),
            ),
        ),
    ),
);
