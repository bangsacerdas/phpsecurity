<?php
class Test_Base
{
	public $test = 'BASE';
	public function getTest()
	{
		return $this->test;
	}
	public function setTest($test)
	{
		$this->test = $test;
	}
}