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
function fnKeepNotEmpty() {
    return function ($v) { return ! empty($v); };
}

/**
 * @return callable
 */
function fnKeepIsSet() {
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
    return fnKeepNotEmpty();
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
function fnKeepInMap($map) {
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
function fnKeepNotInMap($map) {
    $fnInMap = fnKeepInMap($map);
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
 * Generates a function that always returns true
 *
 * @return callable
 */
function fnTrue() {
    return function () { return true; };
}

/**
 * Generates a function that always returns false
 *
 * @return callable
 */
function fnFalse() {
    return function () { return false; };
}

/**
 * @param $array
 * @return callable
 */
function fnKeepInArray($array) {
    return function ($v) use ($array) { return in_array($v, $array); };
}

/**
 * @param $array
 * @return callable
 */
function fnKeepNotInArray($array) {
    return function ($v) use ($array) { return ! in_array($v, $array); };
}

/**
 * Generate a function that returns true if an object implements an interface
 *
 * @param $className
 * @return callable
 */
function fnKeepImplements($className) {
    return function ($v) use ($className) { return class_implements($v, $className); };
}

/**
 * Generate a function that returns true if a value is an object
 *
 * @return callable
 */
function fnKeepIfIsObject() {
    return function ($v) { return is_object($v); };
}

