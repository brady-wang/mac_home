<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2019/4/13
 * Time: 09:51
 */

$arr = ['NAMe'=>'hello','Age'=>18,'hobbY'=>'internet'];

echo "<pre>";
$res = array_change_key_case($arr,CASE_LOWER);
print_r($res);
print_r($arr);
