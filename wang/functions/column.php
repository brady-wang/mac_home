<?php

$arr = [
	[
		'id'=>1,
		'name'=>'wang',
		'age'=>10
	],
	[
		'id'=>2,
		'name'=>'yong',
		'age'=>28
	],
	[
	'id'=>3,
	'name'=>'shun',
	'age'=>33
]
];


$arr1 = array_column($arr,'name','age');
$arr2 = array_column($arr,'name');

echo '<pre>';
print_r($arr1);

echo '<pre>';
print_r($arr2);