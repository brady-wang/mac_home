<?php



define("BASE_PATH",realpath("./"));
define("APP_PATH",BASE_PATH."App/");

require BASE_PATH."/Core/Loader.php"; //

spl_autoload_register("\Core\Loader"."::loadClass");


require BASE_PATH."/vendor/autoload.php";

dump("dddd"); //使用公共函数'
$db = new \Libraries\Db(); //使用db类 已经通过composer引入了
echo $db->getInstance();

$redis = new \Libraries\Redis();//已经通过composer 的classmap引入了
$redis->getInstance();


echo "<hr>";//自动加载本项目的类 通过psr
use Wang\Test;

$a = new Test();
$a->tt();

echo "<hr>";
//通过psr加载第三方包
use Brady\Tool;

$db = new Tool\Db\DB();

$db->hello();
Tool\Redis::getInstance();

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// create a log channel
$log = new Logger('name');
$log->pushHandler(new StreamHandler('logs/monolog.log', Logger::WARNING));

// add records to the log
$log->warning('Foo');
$log->error('Bar');