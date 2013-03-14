AltrEgo
=======

A tool to allow you access to an object protected / private properties in PHP 5.4 or above

This is an implementation of [Chris McMacken's "Friends in PHP"](https://github.com/chrismcmacken/phptools/tree/master/friend) tool built specifically for PHP 5.4.

Why bother?
-----------

Well, for a number of reasons;

Firstly It doesn't use reflection!
PHP 5.4 has a new "scope breaking" feature with the use of closures. Take a look at [Davey Shafik's closure puzzle blog post](http://daveyshafik.com/archives/32789-the-closure-puzzle.html) top get a good understanding of how this works. 
This method is far quicker than using PHP's built in Reflection tools, tests that I've performed "breaking scope" with this method have given a speed increase in speed of around 52%.

Secondly; AltrEgo allows you to completely maintain your object's state throughout any manipulations. 
If you decide you want the scope to come back into play, you simply fetch your object back. Any changes made during its time as an "AltrEgo" object will remain.


Usage
-----

```php
// Given the following class (nice and private all round)
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

// We first off create an alter ego
$alterEgo = AltrEgo::create(new Foo());

// Right, now lets get a hook on those private bits
echo $alterEgo->priv . PHP_EOL;
// Output: This is a private variable

// Woot, works, now lets set it to something else and see what happens
$alterEgo->priv = 'new private value';
echo $alterEgo->priv . PHP_EOL;
// Output: new private value

// This value remains on your object, even after retrieving it back with $alterEgo->getObject(), now lets try some method calls
echo $alterEgo->privFunc('Private call') . PHP_EOL;
//Output: Private call

// Now then, onto arrays; You can pass in an array of parameters like so:
var_dump($alterEgo->privFunc('Private', 'call'));
/**
Output: 
array(2) {
  [0]=>
  string(7) "Private"
  [1]=>
  string(4) "call"
}
*/

// You can push values straight into them using standard PHP array syntax. But be aware, the array will be converted (and maintained) as an ArrayObject
$alterEgo->privArray[] = 'new value';
var_dump($alterEgo->privArray);
/**
Output: 
object(ArrayObject)#5 (1) {
  ["storage":"ArrayObject":private]=>
  array(2) {
    [0]=>
    string(19) "private array entry"
    [1]=>
    string(9) "new value"
  }
}
*/

// You can add associative array values and unset them as you normally would in PHP
$alterEgo->privArray['assoc_key'] = 'private key value entry';
var_dump($alterEgo->privArray);
unset($alterEgo->privArray['assoc_key']);
var_dump($alterEgo->privArray);
/**
Output: 
object(ArrayObject)#5 (1) {
  ["storage":"ArrayObject":private]=>
  array(3) {
    [0]=>
    string(19) "private array entry"
    [1]=>
    string(9) "new value"
    ["assoc_key"]=>
    string(23) "private key value entry"
  }
}
object(ArrayObject)#5 (1) {
  ["storage":"ArrayObject":private]=>
  array(2) {
    [0]=>
    string(19) "private array entry"
    [1]=>
    string(9) "new value"
  }
}
*/

// We also have the facility to execute static function that have private/protected visibility.
// It's rare you'll ever come across these, they're typically set as public, and when they are its advised you just call them directly
echo AltrEgo::callStatic($alterEgo, 'privStatFunc', 'private static call') . PHP_EOL;
// Output: private static call

Also works with arrays
var_dump(AltrEgo::callStatic($alterEgo, 'privStatFunc', array('private', 'static', 'call')));   // doing the same thing with arrays
/**
Output: 
array(3) {
  [0]=>
  string(7) "private"
  [1]=>
  string(6) "static"
  [2]=>
  string(4) "call"
}
*/

// If at anytime you want to jump back into scope just fetch your object back, you can throw it back into AltrEgo::create() whenever you need
$backToScope = $alterEgo->getObject();
```

Limitations
-----------

#It's PHP 5.4 Only

#Whenever accessing an array property it will be converted (and maintained) as an ArrayObject. This is due to limitation on setting array values when using PHP overloading (__get).
