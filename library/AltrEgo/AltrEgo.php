<?php
namespace AltrEgo;

/**
 * Copyright (c) 2013 individual committers of the code
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * Except as contained in this notice, the name(s) of the above copyright holders
 * shall not be used in advertising or otherwise to promote the sale, use or other
 * dealings in this Software without prior written authorization.
 *
 * The end-user documentation included with the redistribution, if any, must
 * include the following acknowledgment: "This product includes software developed
 * by contributors", in the same place and form as other third-party
 * acknowledgments. Alternately, this acknowledgment may appear in the software
 * itself, in the same form and location as other such third-party
 * acknowledgments.
 *
 *
 * A tool to allow you access to an object protected / private properties in PHP
 * This is loosly based on Chris McMacken's "Friends in PHP" tool but uses closures to break scope instead of reflection when using PHP 5.4
 * Otherwise falls back to using Reflection on older versions of PHP (must be >= 5)
 * @author Lee Davis (leedavis81)
 */
class AltrEgo
{

	/**
	 * The object we want to access priv/prot properties of
	 * @var object
	 */
	protected $object;

	/**
	 * Routine calls are sent to a PHP version specific adapter
	 * @var unknown_type
	 */
	protected $adapter;

	/**
	 * @param mixed $object - Can be either an object instance or the class name you want instantiated
	 */
	public function __construct($object)
	{
		if (is_object($object))
		{
			$this->object = $object;
		} elseif (class_exists($object))
		{
			$this->object = new $object;
		} else
		{
			throw new \Exception('AltrEgo must be constructed with either an object or a class name');
		}
	    $adapterClass = self::getAdapterClassName();
        $this->adapter = new $adapterClass($this->object);
	}

	/**
	 * Static call to fetch the adapter class name to use
	 * @throws \Exception if version isn't high enough
	 */
	public static function getAdapterClassName()
	{
        if (version_compare(phpversion(), '5.4', '>='))
    	{
            return 'AltrEgo\Adapter\Php54';
        } elseif (version_compare(phpversion(), '5.3.3', '>='))
        {
            return 'AltrEgo\Adapter\Php53';
        } else
        {
            throw new \Exception('PHP Version must be a minimum of PHP 5');
        }
	}

	/**
	 * Factory call to create an alter ego
	 * @param unknown_type $obj
	 */
	public static function create($obj)
	{
		return new self($obj);
	}

	/**
	 * Returns the object originally passed (or one that was instatiated) in the constructor
	 * @return object
	 */
	public function getObject()
	{
		return $this->object;
	}

	public function __call($name, $arguments)
	{
	    return $this->adapter->_call($name, $arguments);
	}

	public function __get($name)
	{
	    return $this->adapter->_get($name);
	}

	public function __set($name, $value)
	{
        return $this->adapter->_set($name, $value);
	}

	/**
	 * Allows exposure to privately defined static calls
	 * @param mixed $object - Either a AltrEgo object, you own object or the class name the static function resides
	 * @param string $name - the static function name you want to call
	 * @param mixed $arguments - Can be a single argument, or an array of them that you wish to pass to the function call
	 * @throws \Exception
	 */
	public static function callStatic($object, $name, $arguments)
	{
	    $className = self::getAdapterClassName();
	    return $className::_callStatic($object, $name, $arguments);
	}

}