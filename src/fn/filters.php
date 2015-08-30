<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 29/08/15
 * Time: 10:18
 */

namespace Revinate\Sequence\fn;

use \ArrayAccess;

/**
 * @return callable
 */
function fnIsNotEmpty() {
    return function ($v) { return ! empty($v); };
}

/**
 * @return callable
 */
function fnIsSet() {
    return function ($v) { return isset($v); };
}

/**
 * Alias for fnKeepIsSet
 *
 * Usage Sequence::make($values)->filter(FnGen::clean())->to_a();
 *
 * @return callable
 */
function fnClean() {
    return fnIsNotEmpty();
}

/**
 * @return callable
 */
function fnIsEmpty() {
    return function ($v) { return empty($v); };
}

/**
 * Generates a function that returns true if $map has a key that matches the value.
 *
 * @param array|ArrayAccess $map
 * @return callable
 */
function fnIsInMap($map) {
    if (is_array($map)) {
        return function ($v) use ($map) { return array_key_exists($v, $map); };
    } else {
        if (class_implements($map, 'ArrayAccess')) {
            return function ($v) use ($map) { return $map->offsetExists($v); };
        }
    }
    // just use isset
    return function ($v) use ($map) { return isset($map[$v]); };
}

/**
 * Generates a function that returns false if $map has a key that matches the value.
 *
 * @param array|ArrayAccess $map
 * @return callable
 */
function fnIsNotInMap($map) {
    $fnInMap = fnIsInMap($map);
    return function ($v) use ($fnInMap) { return ! $fnInMap($v); };
}

/**
 * Generates a function that returns true if a value is equal
 *
 * @param $value
 * @return callable
 */
function fnIsEqual($value) {
    return function ($v) use ($value) { return $value == $v; };
}

/**
 * Generates a function that returns true if a value is equal
 *
 * @param $value
 * @return callable
 */
function fnIsEqualEqual($value) {
    return function ($v) use ($value) { return $value === $v; };
}

/**
 * Generates a function that returns true if a value is not equal
 *
 * @param $value
 * @return callable
 */
function fnIsNotEqual($value) {
    return function ($v) use ($value) { return $value != $v; };
}


/**
 * Generates a function that returns true if a value is not equal
 *
 * @param $value
 * @return callable
 */
function fnIsNotEqualEqual($value) {
    return function ($v) use ($value) { return $value !== $v; };
}


/**
 * Generates a function that returns true if a value is numeric
 *
 * @return callable
 */
function fnIsNumeric() {
    return function ($v) { return is_numeric($v); };
}

/**
 * @param $array
 * @return callable
 */
function fnIsInArray($array) {
    return function ($v) use ($array) { return in_array($v, $array); };
}

/**
 * @param $array
 * @return callable
 */
function fnIsNotInArray($array) {
    return function ($v) use ($array) { return ! in_array($v, $array); };
}

/**
 * Generate a function that returns true if an object implements an interface
 *
 * @param $className
 * @return callable
 */
function fnImplements($className) {
    return function ($v) use ($className) { return class_implements($v, $className); };
}

/**
 * Generate a function that returns true if a value is an object
 *
 * @return callable
 */
function fnIsObject() {
    return function ($v) { return is_object($v); };
}

