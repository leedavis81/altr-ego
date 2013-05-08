<?php
namespace Altrego\Adapter;

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
 * An adapter interface for PHP version specific adapters to implement
 * @author Lee Davis (leedavis81)
 */
interface AdapterInterface
{
    /**
     * Method overloading call
     * @param string $name
     * @param array $arguments
     */
    public function _call($name, $arguments);

    /**
     * Variable overloading get call
     * @param string $name - Name of variable called
     */
    public function _get($name);

    /**
     * Variable overloading set call
     * @param string $name Name of variable being set
     * @param mixed $value - Values being used to set
     */
    public function _set($name, $value);

	/**
	 * Allows exposure to privately defined static calls
	 * @param mixed $object - Either a Altrego object, you own object or the class name the static function resides
	 * @param string $name - the static function name you want to call
	 * @param mixed $arguments - Can be a single argument, or an array of them that you wish to pass to the function call
	 * @throws \Exception if not passed an object or an accessible class name
	 */
	public static function _callStatic($object, $name, $arguments);

}