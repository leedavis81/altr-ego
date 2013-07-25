<?php
namespace AltrEgo\Adapter;

use AltrEgo\AltrEgo;

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
 * Adapter for PHP 5.3 routines (uses reflection)
 * @author Lee Davis (leedavis81)
 */
class Php53 extends AdapterAbstract
{

	/** (non-PHPdoc)
     * @see AltrEgo\Adapter.AdapterInterface::_call()
     */
    public function _call($name, $arguments)
    {
        $object = $this->getObject();
    	try
    	{
			$method = new \ReflectionMethod($object, $name);
		} catch (\Exception $e)
		{
		    if (method_exists($object, '__call'))
		    {
		        return call_user_func_array(array($object, '__call'), (array) $arguments);
		    } else
		    {
                throw new \Exception('Unable to invoke method ' . $name . ' on object of class ' . get_class($object));
		    }
		    return null;
		}

		$method->setAccessible(true);
		return $method->invokeArgs($object, $arguments);
    }

	/** (non-PHPdoc)
     * @see AltrEgo\Adapter.AdapterInterface::_get()
     */
    public function _get($name)
    {
        $object = $this->getObject();
        $property = $this->getReflectionProperty($name);

        $value = $property->getValue($this->getObject());
        if (is_array($value))
		{
		    $property->setValue($object, new \ArrayObject($value));
        }

        return $property->getValue($this->getObject());
    }

	/** (non-PHPdoc)
     * @see AltrEgo\Adapter.AdapterInterface::_set()
     */
    public function _set($name, $value)
    {
        $property = $this->getReflectionProperty($name);
        return $property->setValue($this->getObject(), $value);
    }

    public function _isset($name)
    {
        try
        {
            $property = $this->getReflectionProperty($name);
            if (!is_null($property)) {
                $value = $property->getValue($this->getObject());
                return isset($value);
            }
            return false;
        } catch (\Exception $e)
        {
            return false;
        }
    }

	/** (non-PHPdoc)
     * @see AltrEgo\Adapter.AdapterInterface::_callStatic()
     */
    public static function _callStatic($object, $name, $arguments)
    {
		if (is_object($object))
		{
			$object = ($object instanceof AltrEgo) ? $object->getObject() : $object;
		} elseif (!class_exists($object))
		{
			throw new \Exception('Static call (callStatic) to AltrEgo must be passed either an object or an accessible class name');
		}

        try
    	{
			$method = new \ReflectionMethod($object, $name);
			if ($method->isStatic())
			{
        		$method->setAccessible(true);
        		return $method->invokeArgs($object, (array) $arguments);
			}
		} catch (\Exception $e)
		{
		}
		throw new \Exception('Unable to call static method "' . $name . '" on class ' . get_class($object));
    }

    /**
     * Get a reflection property - may need to iterate through class parents
     * @param string $name
     */
    protected function getReflectionProperty($name)
    {
        do
        {
            $reflClass = (!isset($reflClass)) ? new \ReflectionClass($this->getObject()) : $reflClass;
            $property = $reflClass->getProperty($name);
            if (isset($property))
            {
                break;
            }
        } while (($reflClass = $reflClass->getParentClass()) !== null);

        if ($property instanceof \ReflectionProperty)
        {
            $property->setAccessible(true);
            return $property;
        } else
        {
            throw new \Exception('Property ' . $name . ' doesn\'t exist on object of class ' . get_class($this->getObject()));
        }
    }





}