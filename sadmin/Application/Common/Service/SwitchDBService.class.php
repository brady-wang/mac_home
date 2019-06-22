<?php

namespace Common\Service;

/**
 * 切换多数据库实例
 * 通过校验返回 true; 否则返回 errmsg
 */
class SwitchDBService
{
    private $db; //数据库连接对象
    private $cfgFix = 'GAMEID';
    private $config;

    public function __construct($param)
    {
    }

    /**
     * 获取游戏数据库配置
     * @param int $gameId 游戏ID
     * @param int $dbType 游戏数据库配置
     * @return object 返回游戏数据库配置
     */
    private function _getGameConfig($gameId, $dbType)
    {
        $data = D('Home/DatabasesConf')->queryGameConfigInfo($gameId, $dbType);
        if (empty($data)) {
            return false;
        }
        $config = array(
            'DB_TYPE' => 'mysql', // 数据库类型
            'DB_HOST' => $data['host'], // 服务器地址
            'DB_NAME' => $data['db_name'], // 数据库名
            'DB_USER' => $data['user'], // 用户名
            'DB_PWD' =>  $data['pwd'], // 密码
            'DB_PORT' => $data['port'], // 端口
            'DB_PREFIX' => "", // 数据库表前缀
            'DB_CHARSET' => $data['charset'], // 数据库编码默认采用utf8
        );

        C($this->cfgFix.$gameId ,$config) ; //放入淋湿配置，供数据库DB驱动使用

        return $config;
    }

    /**
     * 实例化游戏数据库句柄
     * @param int $gameId 游戏ID
     * @param array $config 游戏数据库配置
     * @return object 返回游戏数据库连接对象
     */
    public function getGameDb($gameId, $dbType)
    {
        if (empty($gameId) || empty($dbType)) {
            redirect('/System/data/third/dbconf');
            //E("游戏ID:{$gameId}-{$dbType}不存在或者");
        }
        //获取游戏配置信息
        $this->config = $config = $this->_getGameConfig($gameId, $dbType);

        if (empty($this->config)) {
            redirect('/System/data/third/dbconf');
            //E('配置不存在');
        }

        // 静态化存储数据库对象
        static $_model = array();
        $class = 'Think\\Model';

        // model标识
        $guid = !empty($config['DB_NAME']) ? $config['DB_NAME']: $this->cfgFix.$gameId;
        $connection = $this->cfgFix.$gameId ;//游戏数据库连接/配置标识

        if (!isset($_model[$guid])) {
            $_model[$guid] = new $class($tablename,$tablePrefix, $connection);
        }

        return $_model[$guid];
    }

    /**
     * 获取游戏基本数据
     * @param int $gameId 游戏ID
     * @param int $dbType 游戏数据库配置
     * @return array 返回游戏基本信息
     */
    private function _getGameInfo($gameId, $dbType)
    {
        $gameInfo = array();

        //$gameInfo =  @TODO 待补充

        return $gameInfo ;
    }
}
