<?php


require "Human.php";

class HumanTest extends \PHPUnit\Framework\TestCase
{
	public function testEat()
	{
		$human = new Human();
		$this->assertEquals('eat animal',$human->eat());
	}
}