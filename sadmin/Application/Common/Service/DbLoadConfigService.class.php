<?php

namespace Common\Service;

use Home\Model\DatabasesConfModel;

/**
 * 本服务用于加载外库配置
 */
class DbLoadConfigService
{
    /**
     * 通过库类型加载配置
     * @param string $confName 配置参数名称
     * @param int $mode 模式： 0 不判断主从；1 取主库；2 取从库
     * @return 加载成功返回 true; 否则返回 errmsg
     */
    public function load($gameId, $confName, $mode)
    {
        $confMod = new DatabasesConfModel();

        // 通过要加载的配置参数名称，确定数据库对应的库类型
        switch ($confName) {
            // 游戏主库
            case 'GAME_DEV_DB':
                $dbType = $confMod::DB_TYPE_GAME_DEV;
                break;
            // 游戏日志库
            case 'CONF_DBTYPE_GAME_LOG_DEV':
                $dbType = $confMod::DB_TYPE_GAME_LOG_DEV;
                break;
            // 游戏字典库
            case 'GAME_DICT_DB':
                $dbType = $confMod::DB_TYPE_DSQP_DICT;
                break;
            // 活动库
            case 'GAME_ACTIVITY_DB':
                $dbType = $confMod::DB_TYPE_DSQP_ACTIVITY;
                break;
            // 活动日志库
            case 'GAME_ACTIVITY_LOG_DB':
                $dbType = $confMod::DB_TYPE_DSQP_LOG_ACTIVITY;
                break;
            // 亲友圈代理商库
            case 'CONF_DBTYPE_CLUB':
                $dbType = $confMod::DB_TYPE_CLUB;
                break;
            // 亲友圈字典库
            case 'AGENT_ALL_DICT_DB':
                $dbType = $confMod::DB_TYPE_ALL_CLUB_DICT;
                break;
            // 亲友圈日志库
            case 'CONF_DBTYPE_CLUB_LOG':
                $dbType = $confMod::DB_TYPE_CLUB_LOG;
                break;
            default:
                return "未知配置参数名称：{$confName}";
        }

        // 获取配置列表
        $attr = array(
            'game_id' => $gameId,
            'db_type' => $dbType,
        );
        $modRet = $confMod->queryDbConfAllList($attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            return $modRet['msg'];
        }
        $confList = $modRet['data'];

        // 指定游戏 id 获取不到配置，那么就获取通用配置
        if (empty($confList)) {
            $attr = array(
                'game_id' => 0,
                'db_type' => $dbType,
            );
            $modRet = $confMod->queryDbConfAllList($attr);
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                return $modRet['msg'];
            }
            $confList = $modRet['data'];
        }

        /*
         * $connection
         *
         * db_type 数据库类型
         * db_host 服务器地址
         * db_port 端口
         * db_user 用户名
         * db_pwd 密码
         * db_name 数据库名
         * db_dsn 数据库连接DSN，用于PDO方式
         * db_params 数据库连接参数
         * db_charset 数据库编码默认采用utf8
         * db_deploy_type 数据库部署方式:0 集中式(单一服务器)；1 分布式(主从服务器)
         * db_rw_separate 数据库读写是否分离，主从式有效
         * db_master_num 读写分离后，主服务器数量
         * db_slave_no 指定从服务器序号
         * db_debug 数据库调试模式
         * db_lite 数据库Lite模式
         */
        $connection = array();

        switch ($mode) {
            // 不判断主库或从库
            case 0:
                foreach ($confList as $v) {
                    $connection = array(
                        'db_type' => 'mysql',
                        'db_host' => $v['host'],
                        'db_port' => $v['port'],
                        'db_user' => $v['user'],
                        'db_pwd' => $v['pwd'],
                        'db_name' => $v['db_name'],
                        'db_charset' => $v['charset'],
                        'db_params' => array(\PDO::ATTR_CASE => \PDO::CASE_NATURAL),
                    );
                    break;
                }
                break;

            // 主从模式，主库
            case 1:
                foreach ($confList as $v) {
                    if ('1' == $v['is_master']) {
                        $connection = array(
                            'db_type' => 'mysql',
                            'db_host' => $v['host'],
                            'db_port' => $v['port'],
                            'db_user' => $v['user'],
                            'db_pwd' => $v['pwd'],
                            'db_name' => $v['db_name'],
                            'db_charset' => $v['charset'],
                            'db_params' => array(\PDO::ATTR_CASE => \PDO::CASE_NATURAL),
                        );
                        break;
                    }
                }
                break;

            // 主从模式，从库
            case 2:
                foreach ($confList as $v) {
                    if ('0' == $v['is_master']) {
                        $connection = array(
                            'db_type' => 'mysql',
                            'db_host' => $v['host'],
                            'db_port' => $v['port'],
                            'db_user' => $v['user'],
                            'db_pwd' => $v['pwd'],
                            'db_name' => $v['db_name'],
                            'db_charset' => $v['charset'],
                            'db_params' => array(\PDO::ATTR_CASE => \PDO::CASE_NATURAL),
                        );
                        break;
                    }
                }
                break;

            default:
                return "unknow mode {$mode}";
        }
        if (empty($connection)) {
            return "找不到相关数据库配置，请到系统数据管理 - 外库配置页面确认配置信息";
        }

        // 加载配置
        C($confName, $connection);

        return true;
    }
}
