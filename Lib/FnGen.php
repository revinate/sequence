<?php

namespace Revinate\SequenceBundle\Lib;

use \ArrayAccess;
use \Closure;

/**
 * Class FnGen
 * @author jasondent
 * @package Revinate\SequenceBundle\Lib
 */
class FnGen {
    /**
     * @return callable
     */
    public static function fnKeepNotEmpty() {
        return function ($v) { return !empty($v); };
    }

    /**
     * @return callable
     */
    public static function fnKeepIsSet() {
        return function ($v) { return isset($v); };
    }

    /**
     * Alias for fnKeepIsSet
     *
     * Usage Sequence::make($values)->filter(FnGen::clean())->to_a();
     *
     * @return callable
     */
    public static function fnClean() {
        return static::fnKeepNotEmpty();
    }

    /**
     * @return callable
     */
    public static function fnIsEmpty() {
        return function ($v) { return empty($v); };
    }

    /**
     * Generates a function that returns true if $map has a key that matches the value.
     *
     * @param array|ArrayAccess $map
     * @return callable
     */
    public static function fnKeepInMap($map) {
        if (is_array($map)) {
            return function ($v) use ($map) { return array_key_exists($v, $map);};
        } else if (class_implements($map, 'ArrayAccess')) {
            return function ($v) use($map) { return $map->offsetExists($v); };
        }
        // just use isset
        return function ($v) use($map) { return isset($map[$v]); };
    }

    /**
     * Generates a function that returns false if $map has a key that matches the value.
     *
     * @param array|ArrayAccess $map
     * @return callable
     */
    public static function fnKeepNotInMap($map) {
        $fnInMap = static::fnKeepInMap($map);
        return function ($v) use($fnInMap) { return ! $fnInMap($v); };
    }

    /**
     * Generates a function that returns true if a value is equal
     *
     * @param $value
     * @return callable
     */
    public static function fnIsEqual($value) {
        return function ($v) use($value) { return $value == $v; };
    }

    /**
     * Generates a function that returns true if a value is equal
     *
     * @param $value
     * @return callable
     */
    public static function fnIsEqualEqual($value) {
        return function ($v) use($value) { return $value === $v; };
    }

    /**
     * Generates a function that returns true if a value is not equal
     *
     * @param $value
     * @return callable
     */
    public static function fnIsNotEqual($value) {
        return function ($v) use($value) { return $value != $v; };
    }


    /**
     * Generates a function that returns true if a value is not equal
     *
     * @param $value
     * @return callable
     */
    public static function fnIsNotEqualEqual($value) {
        return function ($v) use($value) { return $value !== $v; };
    }


    /**
     * Generates a function that returns true if a value is numeric
     *
     * @return callable
     */
    public static function fnIsNumeric() {
        return function ($v) { return is_numeric($v); };
    }

    /** Returns a function that trims the value.
     *
     * @return callable
     * @deprecated use FnString::fnTrim instead
     */
    public static function fnTrim() {
        return FnString::fnTrim();
    }

    /**
     * Generates a function that always returns true
     *
     * @return callable
     */
    public static function fnTrue() {
        return function () { return true; };
    }

    /**
     * Generates a function that always returns false
     *
     * @return callable
     */
    public static function fnFalse() {
        return function () { return false; };
    }

    /**
     * @param $array
     * @return callable
     */
    public static function fnKeepInArray($array) {
        return function ($v) use($array) { return in_array($v, $array);};
    }

    /**
     * @param $array
     * @return callable
     */
    public static function fnKeepNotInArray($array) {
        return function ($v) use ($array) { return ! in_array($v, $array); };
    }

    /**
     * Generate a function that returns true if an object implements an interface
     *
     * @param $className
     * @return callable
     */
    public static function fnKeepImplements($className) {
        return function ($v) use ($className) { return class_implements($v, $className); };
    }

    /**
     * Generate a function that returns true if a value is an object
     *
     * @return callable
     */
    public static function fnKeepIfIsObject() {
        return function ($v)  { return is_object($v); };
    }

    /**
     * @param ArrayAccess|array $map
     * @return callable
     */
    public static function fnMap($map) {
        return function ($v) use ($map) { return $map[$v]; };
    }

    /**
     * Generate a function that casts values to ints.
     * @return callable
     * @deprecated
     */
    public static function fnCastToInt() {
        return FnMap::fnCastToInt();
    }

    /**
     * Generate a function that swaps the order of the parameters and calls $fn
     *
     * @param callable $fn
     * @return callable
     */
    public static function fnSwapParamsPassThrough($fn) {
        return function ($a, $b) use ($fn) { return $fn($b, $a); };
    }

    /**
     * Generate a function that returns the key from a map call.
     *
     * @return callable
     */
    public static function fnMapToKey() {
        return function ($v, $k) { return $k; };
    }

    /**
     * Generate a function that combines the key and the value into a tuple.
     *
     * @return callable
     */
    public static function fnMapToKeyValuePair() {
        return function ($v, $k) { return array($k, $v); };
    }


    /**
     * Generates a function that will apply a mapping function to a sub field of a record
     *
     * @param string $fieldName
     * @param callable $fnMap($fieldValue, $fieldName, $parentRecord, $parentKey)
     * @return callable
     */
    public static function fnMapField($fieldName, $fnMap) {
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
    public static function fnPluck($key, $default = null) {
        return function ($v) use ($key, $default) {
            if (isset($v[$key])) {
                return $v[$key];
            } elseif (is_object($v) && property_exists($v, $key)) {
                return $v->{$key};
            }
            return $default;
        };
    }

    /**
     * returns a function that given a key returns a value from $from.
     *
     * @param array|ArrayAccess $from
     * @param null|mixed $default
     * @return callable
     */
    public static function fnPluckFrom($from, $default = null) {
        return function ($key) use ($from, $default) {
            if (isset($from[$key])) {
                return $from[$key];
            }
            return $default;
        };
    }

    /**
     * Generate a function that returns the value given.
     *
     * @return callable
     */
    public static function fnIdentity() {
        return function ($v) { return $v; };
    }

    /**
     * @description Generate a function that will return the result of calling count()
     *
     * @return callable
     */
    public static function fnCount() {
        return function ($v) { return count($v); };
    }

    /**
     * Generate a function that returns a counter.
     *
     * @param int $startingValue
     * @return callable
     */
    public static function fnCounter($startingValue = 0) {
        $count = $startingValue;
        return function () use (&$count) { return $count++; };
    }

    /**
     * Generate a function that when called, will call a set of functions passing the result as input to the next function.
     *
     * @param Callable[]|Callable $fn
     * @return callable
     */
    public static function fnCallChain($fn) {
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
    public static function fnParam($num) {
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
    public static function fnNestedSort() {
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
    public static function fnNestedMap($fn) {
        return FnSequence::make()->map($fn)->to_a();
    }

    /**
     * Returns a function that applies a function to a nested array and returns the results.
     *
     * @param $fn
     * @return callable
     */
    public static function fnNestedUKeyBy($fn) {
        return function ($array) use ($fn) {
            return Sequence::make($array)->keyBy($fn)->to_a();
        };
    }

    /**
     * Returns a function that removes a suffix from a string if it exists
     *
     * @param   string  $suffix
     * @return  callable
     * @deprecated use FnString::fnRemoveSuffix
     */
    public static function fnRemoveSuffix($suffix) {
        return FnString::fnRemoveSuffix($suffix);
    }

    /**
     * Returns a function that removes a prefix from a string if it exists
     *
     * @param   string  $prefix
     * @return  callable
     * @deprecated  use FnString::fnRemovePrefix
     */
    public static function fnRemovePrefix($prefix) {
        return FnString::fnRemovePrefix($prefix);
    }


    /********************************************************************************
     * Reduce functions
     * have been moved to FnReduce
     */


    /**
     * Used in Sequence::Reduce to sum all values.
     *
     * @param Closure $fnMapValue [optional] - a function to get the needed value
     * @return callable
     * @deprecated
     *
     * @example:
     * Get the total number of fruit.
     * Sequence::make([['count'=>5, 'name'=>'apple'], ['count'=>2, 'name'=>'orange']])->reduce(FnGen::fnSum(FnGen::fnPluck('count'))
     */
    public static function fnSum(Closure $fnMapValue = null) {
        return $fnMapValue ? FnReduce::fnSum($fnMapValue) : FnReduce::fnSum();
    }

    /**
     * @description Generate a function that can be used with reduce to get the max value
     * @return callable
     * @deprecated
     */
    public static function fnMax() {
        return FnReduce::fnMax();
    }

    /**
     * @description Generate a function that can be used with reduce to get the min value
     * @return callable
     * @deprecated
     */
    public static function fnMin() {
        return FnReduce::fnMin();
    }

    /**
     * Generate a function that will:
     * Calculate the average of a set of values.  Null values are skipped.
     *
     * @param callable $fnMapValue [optional] - maps the value before it is computed.
     * @return callable
     * @deprecated
     */
    public static function fnAvg(Closure $fnMapValue = null) {
        return $fnMapValue ? FnReduce::fnAvg($fnMapValue) : FnReduce::fnAvg($fnMapValue);
    }

    /**
     * @description Alias for fnSum -- usage is to do a union between arrays.
     * @return callable
     * @deprecated
     */
    public static function fnUnion() {
        return FnReduce::fnUnion();
    }

    /**
     * Generate a function that will:
     *
     * @param callable $fnReduce(mixed, $value)
     * @return callable
     */
    public static function fnReduce(Closure $fnReduce) {
        return function ($current, $values) use ($fnReduce) {
            return Sequence::make($values)->reduce($current, $fnReduce);
        };
    }

    /**
     * Returns a map function that will allow different map functions to be called based upon the result of a test function.
     *
     * @param callable $fnTest($value, $key)        -- the test function
     * @param callable $fnMapTrue($value, $key)     -- the map function to use if the test is true
     * @param callable $fnMapFalse($value, $key)    -- the map function to use if the test is false
     * @return callable
     */
    public static function fnIfMap(Closure $fnTest, Closure $fnMapTrue, Closure $fnMapFalse = null) {
        if (is_null($fnMapFalse)) {
            $fnMapFalse = FnGen::fnIdentity();
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
     * @param callable $fnMap($value, $key) - any map function
     * @param callable|null $fnHash  - Converts the arguments into a hash value
     * @return callable
     */
    public static function fnCacheResult(Closure $fnMap, Closure $fnHash = null) {
        $fnHash = $fnHash ?: FnGen::fnIdentity();
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
}