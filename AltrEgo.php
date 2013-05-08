<?php
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
 * A tool to allow you access to an object protected / private properties in PHP 5.4 or above
 * This is loosly based on Chris McMacken's "Friends in PHP" tool but uses closures to break scope instead of reflection
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
	 * @param mixed $object - Can be either an object instance or the class name you want instantiated
	 * @throws \Exception - if an object is not passed and class of $object name cannot be found
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

	/**
	 * Allows exposure to privately defined static calls
	 * @param mixed $object - Either a AltrEgo object, you own object or the class name the static function resides
	 * @param string $name - the static function name you want to call
	 * @param mixed $arguments - Can be a single argument, or an array of them that you wish to pass to the function call
	 * @throws \Exception
	 */
	public static function callStatic($object, $name, $arguments)
	{
		if (is_object($object))
		{
			$class = ($object instanceof AltrEgo) ? get_class($object->getObject()) : get_class($object);
		} elseif (!class_exists($object))
		{
			throw new \Exception('Static call (callStatic) to AltrEgo must be passed either an object or an accessible class name');
		}
		$callable = Closure::bind(function() use ($name, $arguments, $class){
			return call_user_func_array(array($class, $name), array($arguments));
		}, $this, $class);
		return $callable();
	}

	public function __call($name, $arguments)
	{
		$object = $this->getObject();
		$callable = function() use ($name, $arguments, $object){
			if (!method_exists($object, $name)) {
				throw new \Exception('Unable to invoke method ' . $name . ' on object of class ' . get_class($object));
			}
			return call_user_func_array(array($object, $name), (sizeof($arguments) > 1) ? array($arguments) : $arguments);
		};
		return $this->breakScopeAndExecute($callable);
	}

	public function __get($name)
	{
		$object = $this->getObject();
		$callable = function() use ($name, $object) {
			if (!property_exists($object, $name) && !method_exists($object, '__get'))
			{
				throw new \Exception('Property ' . $name . ' doesn\'t exist on object of class ' . get_class($object));
			}
			if (is_array($object->$name))
			{
				$object->$name = new ArrayObject($object->$name);
			}
			return $object->$name;
		};
		return $this->breakScopeAndExecute($callable);
	}

	public function __set($name, $value)
	{
		$object = $this->getObject();
		$callable = function() use ($name, $value, $object) {
			if (!property_exists($object, $name) && !method_exists($object, '__set'))
			{
				throw new \Exception('Property ' . $name . ' doesn\'t exist on object of class ' . get_class($object));
			}
			$object->$name = $value;
		};
		$this->breakScopeAndExecute($callable);
	}

	/**
	 * Break the scope of a callble and execute it
	 * @param Callable $closure
	 */
	protected function breakScopeAndExecute(Callable $closure)
	{
		$callable = Closure::bind($closure, $this, $this->getObject());
		return $callable();
	}
}