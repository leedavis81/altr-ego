<?php
namespace AltregoTests\Fixture;

class Foo
{
	private $priv = 'This is a private variable';

	private $privArray = array('private array entry');

	private function privFunc($value)
	{
		return $value;
	}

	private static function privStatFunc($value)
	{
		return $value;
	}

	public static function publStatFunc($value)
	{
		return self::privStatFunc($value);
	}
}