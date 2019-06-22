<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2019/4/13
 * Time: 09:37
 */


/**
 * array_diff ( array $array1 , array $array2 [, array $... ] ) : array
对比 array1 和其他一个或者多个数组，返回在 array1 中但是不在其他 array 里的值。
 */

$arr1 = [1,3,4,6,9];  //比如修改用户权限 新加的权限
$arr2 = [2,3,6,8,10]; // 旧有的权限
$arr3 = [1,2,3,4,5];

echo "<pre>";
print_r(array_diff($arr1,$arr2,$arr3)); //最新的需要插入的权限
print_r(array_diff($arr2,$arr1,$arr3)); //需要删除的旧有的，比当前提交过来的权限多的


$arr4 = array_unique(array_merge($arr2,$arr3));
print_r($arr4);

print_r(array_diff($arr1,$arr4));

//当有第三个参数的时候 是指第一个和 后面所有的数组进行对比