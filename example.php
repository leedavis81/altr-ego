<?php

require_once 'AltrEgo.php';

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


// First off; create an alter ego
$alterEgo = AltrEgo::create(new Foo());

// Right, now lets get a hook on those private bits
echo $alterEgo->priv . PHP_EOL;

// Woot, works, now lets set it to something else and see what happens
$alterEgo->priv = 'new private value';
echo $alterEgo->priv . PHP_EOL;

// This value remains on your object, even after retrieving it back with $alterEgo->getObject(), now lets try some method calls
echo $alterEgo->privFunc('Private call') . PHP_EOL;

// You can pass in an array of parameters if you wish
var_dump($alterEgo->privFunc('Private', 'call'));

// Now then, onto arrays; You can push values straight into them using standard PHP array syntax. But be aware, the array will be converted (and maintained) as an ArrayObject
$alterEgo->privArray[] = 'new value';
var_dump($alterEgo->privArray);

// You can add associative array values and unset them as you normally would in PHP
$alterEgo->privArray['assoc_key'] = 'private key value entry';
var_dump($alterEgo->privArray);
unset($alterEgo->privArray['assoc_key']);
var_dump($alterEgo->privArray);

// We also have the facility to execute static function that have private/protected visibility.
// It's rare you'll ever come across these, they're typically set as public, and when they are its advised you just call them directly
echo AltrEgo::callStatic($alterEgo, 'privStatFunc', 'private static call') . PHP_EOL;

// Also works with arrays
var_dump(AltrEgo::callStatic($alterEgo, 'privStatFunc', array('private', 'static', 'call')));   // doing the same thing with arrays

// If at anytime you want to jump back into scope just fetch your object back, you can throw it back into AltrEgo::create() whenever you need
$backToScope = $alterEgo->getObject();