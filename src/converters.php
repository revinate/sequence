<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 29/08/15
 * Time: 10:19
 */

namespace Revinate\Sequence;


/**
 * Returns a function that will case a value to an int
 * @return \Closure
 */
function fnCastToInt() {
    return function ($value) { return (int)$value; };
}

/**
 * Returns a function that will case a value to an float
 * @return \Closure
 */
function fnCastToFloat() {
    return function ($value) { return (float)$value; };
}

/**
 * Returns a function that will case a value to an double
 * @return \Closure
 */
function fnCastToDouble() {
    return function ($value) { return (double)$value; };
}

/**
 * Returns a function that will case a value to an string
 * @return \Closure
 */
function fnCastToString() {
    return function ($value) { return (string)$value; };
}

/**
 * Returns a function that will case a value to an array
 * @return \Closure
 */
function fnCastToArray() {
    return function ($value) { return (array)$value; };
}

/**
 * Returns a function that will case a value to an object
 * @return \Closure
 */
function fnCastToObject() {
    return function ($value) { return (object)$value; };
}
