<?php

if(!function_exists("dump")) {
	function dump($arr)
	{
		echo "<pre>";

		print_r($arr);

		echo "</pre>";
	}
}