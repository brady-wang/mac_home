<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2019/4/13
 * Time: 10:02
 */

$arr = [
	1=>'a',
	2=>'b',
	3=>'c',
	4=>'d',
	5=>'e',
	6=>'f',
	7=>'g',
	8=>'h',
	9=>'i',

];

echo "<pre>";

$res = array_chunk($arr,4,false);

print_r($res);