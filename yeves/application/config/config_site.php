
<?php
/* Set 环境类型：production,development*/
@define('ENVIRONMENT', 'development');

/* Set timezone. */
date_default_timezone_set('PRC');

//$config['db'] = [
//    'host'=>'192.168.33.30',
//    'db'=>'yeves',
//    'port'=>'3306',
//    'user'=>'root',
//    'password'=>'root'
//];

$config['db'] = [
    'host'=>'120.79.172.45',
    'db'=>'blog',
    'user'=>'root',
    'port'=>'3306',
    'password'=>'aa5421010',
    'db_debug'=>true
];

$config['front_verify'] = false;