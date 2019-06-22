<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2019/4/13
 * Time: 10:10
 */

$arr1 = ['a','b','c'];
$arr2 = [1,2,3];

$res = array_combine($arr2,$arr1);
echo "<pre>";
print_r($res);