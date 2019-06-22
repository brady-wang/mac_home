<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2019/4/13
 * Time: 10:16
 */

$arr = [1,2,3,4,5,2,3,4,2,3,4,'a','a','c','age','age'];

echo "<pre>";
$res = array_count_values($arr);
//print_r($res);


$arr = range(0,100);

$res = array_fill_keys($arr,100);
//print_r($res);


$res = array_fill(0,100,'apple');

//print_r($res);


function add($a,$b)
{
	if($a > 5 || $b > 1){
		return true;
	} else{
		return false;
	}
}

$arr = [1=>'3',2=>6,3,4,5,6,7,8,9];
$res = array_filter($arr,'add',ARRAY_FILTER_USE_BOTH);

//print_r($res);

$arr = ['id'=>1,'test'=>1,'name'=>"wang",'age'=>18];

print_r(array_flip(array_flip($arr)));

print_r(array_unique($arr));


$arr = ['id'=>1,'name'=>"wang",'age'=>33];
//print_r(array_key_exists('id',$arr));

print_r(array_rand($arr,2));
