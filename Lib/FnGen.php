<?php

namespace Revinate\SequenceBundle\Lib;

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
     */
    public static function fnCastToInt() {
        return function ($v) { return (int)$v; };
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
    public static function fnMayToKeyValuePair() {
        return function ($v, $k) { return array($k, $v); };
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
     * Returns a function that applies a function to a nested array and returns the results.
     *
     * @return callable
     */
    public static function fnNestedSort() {
        return function ($array) {
            return FancyArray::make($array)->sort()->to_a();
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
    public static function fnNestedUkeyBy($fn) {
        return function ($array) use ($fn) {
            return Sequence::make($array)->keyBy($fn)->to_a();
        };
    }


    /********************************************************************************
     * Reduce functions
     */


    /**
     * Used in Sequence::Reduce to sum all values.
     *
     * @param Closure $fnMapValue [optional] - a function to get the needed value
     * @return callable
     *
     * @example:
     * Get the total number of fruit.
     * Sequence::make([['count'=>5, 'name'=>'apple'], ['count'=>2, 'name'=>'orange']])->reduce(FnGen::fnSum(FnGen::fnPluck('count'))
     */
    public static function fnSum(Closure $fnMapValue = null) {
        if ($fnMapValue) {
            return function ($sum, $v) use ($fnMapValue) { return $sum + $fnMapValue($v); };
        }
        return function ($sum, $v) { return $sum + $v; };
    }

    /**
     * Generate a function that will:
     * Calculate the average of a set of values.  Null values are skipped.
     *
     * @param callable $fnMapValue [optional] - maps the value before it is computed.
     * @return callable
     */
    public static function fnAvg(Closure $fnMapValue = null) {
        $count = 0;
        $sum = 0;

        if (!$fnMapValue) {
            $fnMapValue = FnGen::fnIdentity();
        }

        return function ($avg, $v) use (&$count, &$sum, $fnMapValue) {
            $v = $fnMapValue($v);
            if (is_null($v)) {
                if (!$count) {
                    return null;
                }
            } else {
                $count += 1;
                $sum += $v;
            }
            return $sum / $count;
        };
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
}