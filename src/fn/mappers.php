<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 29/08/15
 * Time: 10:17
 */

namespace Revinate\Sequence\fn;

use \ArrayAccess;
use \Closure;
use Revinate\Sequence\ArrayUtil;
use Revinate\Sequence\Sequence;
use Revinate\Sequence\FnSequence;

/**
 * @param ArrayAccess|array $map
 * @return callable
 */
function fnMap($map) {
    return function ($v) use ($map) { return $map[$v]; };
}

/**
 * Generate a function that swaps the order of the parameters and calls $fn
 *
 * @param callable $fn
 * @return callable
 */
function fnSwapParamsPassThrough($fn) {
    return function ($a, $b) use ($fn) { return $fn($b, $a); };
}

/**
 * Generate a function that returns the key from a map call.
 *
 * @return callable
 */
function fnMapToKey() {
    return function ($v, $k) { return $k; };
}

/**
 * Generate a function that combines the key and the value into a tuple.
 *
 * @return callable
 */
function fnMapToKeyValuePair() {
    return function ($v, $k) { return array($k, $v); };
}


/**
 * Generates a function that will apply a mapping function to a sub field of a record
 *
 * @param string $fieldName
 * @param callable $fnMap($fieldValue, $fieldName, $parentRecord, $parentKey)
 * @return callable
 */
function fnMapField($fieldName, $fnMap) {
    return function ($record, $key = null) use ($fieldName, $fnMap) {
        if ($record instanceof ArrayAccess || is_array($record)) {
            $fieldValue = isset($record[$fieldName]) ? $record[$fieldName] : null;
            $record[$fieldName] = $fnMap($fieldValue, $fieldName, $record, $key);
        } elseif (is_object($record) && property_exists($record, $fieldName)) {
            $record->{$fieldName} = $fnMap($record->{$fieldName}, $fieldName, $record, $key);
        }

        return $record;
    };
}


/**
 * Generate a pluck function that returns the value of a field, or null if the field does not exist.
 *
 * @param string $key - the name / key, of the field to get the value from.
 * @param mixed $default - the default value to assign if the field does not exist.
 * @return callable
 */
function fnPluck($key, $default = null) {
    return function ($v) use ($key, $default) {
        return ArrayUtil::getField($v, $key, $default);
    };
}

/**
 * returns a function that given a key returns a value from $from.
 *
 * @param array|ArrayAccess $from
 * @param null|mixed $default
 * @return callable
 */
function fnPluckFrom($from, $default = null) {
    return function ($key) use ($from, $default) {
        return ArrayUtil::getField($from, $key, $default);
    };
}

/**
 * Generate a function that returns the value given.
 *
 * @return callable
 */
function fnIdentity() {
    return function ($v) { return $v; };
}

/**
 * @description Generate a function that will return the result of calling count()
 *
 * @return callable
 */
function fnCount() {
    return function ($v) { return count($v); };
}

/**
 * Generate a function that returns a counter.
 *
 * @param int $startingValue
 * @return callable
 */
function fnCounter($startingValue = 0) {
    $count = $startingValue;
    return function () use (&$count) { return $count++; };
}

/**
 * Generate a function that when called, will call a set of functions passing the result as input to the next function.
 *
 * @param Callable[]|Callable $fn
 * @return callable
 */
function fnCallChain($fn) {
    if (is_array($fn)) {
        $args = $fn;
    } else {
        $args = func_get_args();
    }
    return function ($v) use ($args) {
        $fn = array_shift($args);
        $v = call_user_func_array($fn, func_get_args());
        foreach ($args as $fn) {
            $v = $fn($v);
        }
        return $v;
    };
}

/**
 * Generate a function that will return the specified parameter
 *
 * @param int $num
 * @return callable
 */
function fnParam($num) {
    return function () use ($num) {
        $args = func_get_args();
        return isset($args[$num]) ? $args[$num] : null;
    };
}

/**
 * Returns a function that applies a function to a nested array and returns the results.
 *
 * @return callable
 */
function fnNestedSort() {
    return function ($array) {
        return Sequence::make($array)->sort()->to_a();
    };
}

/**
 * Returns a function that applies a function to a nested array and returns the results.
 *
 * @param $fn
 * @return callable
 */
function fnNestedMap($fn) {
    return FnSequence::make()->map($fn)->to_a();
}

/**
 * Returns a function that applies a function to a nested array and returns the results.
 *
 * @param $fn
 * @return callable
 */
function fnNestedUKeyBy($fn) {
    return function ($array) use ($fn) {
        return Sequence::make($array)->keyBy($fn)->to_a();
    };
}

/**
 * Returns a map function that will allow different map functions to be called based upon the result of a test function.
 *
 * @param Closure $fnTest($value, $key)        -- the test function
 * @param Closure $fnMapTrue($value, $key)     -- the map function to use if the test is true
 * @param Closure $fnMapFalse($value, $key)    -- the map function to use if the test is false
 * @return callable
 */
function fnIfMap(Closure $fnTest, Closure $fnMapTrue, Closure $fnMapFalse = null) {
    if (is_null($fnMapFalse)) {
        $fnMapFalse = fnIdentity();
    }

    return function ($value, $key) use ($fnTest, $fnMapTrue, $fnMapFalse) {
        if ($fnTest($value, $key)) {
            return $fnMapTrue($value, $key);
        } else {
            return $fnMapFalse($value, $key);
        }
    };
}

/**
 * Create a function that will cache the results of another function based upon the
 *
 * @param Closure $fnMap($value,...) - any invariant map function
 * @param Closure|null $fnHash  - Converts the arguments into a hash value
 * @return callable
 */
function fnCacheResult(Closure $fnMap, Closure $fnHash = null) {
    $fnHash = $fnHash ?: fnIdentity();
    $cache = array();
    return function($value) use ($fnMap, $fnHash, &$cache) {
        $args = func_get_args();
        $hashKey = call_user_func_array($fnHash, $args);
        if (! array_key_exists($hashKey, $cache)) {
            $cache[$hashKey] = call_user_func_array($fnMap, $args);
        }

        return $cache[$hashKey];
    };
}
