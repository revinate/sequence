<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 12/03/15
 * Time: 16:34
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
        return static::fnKeepIsSet();
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
     * Generates a function that returns true if a value is numeric
     *
     * @return callable
     */
    public static function fnIsNumeric() {
        return function ($v) { return is_numeric($v); };
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
     * @param $key - the name, key, of the field to get the value from.
     * @return callable
     */
    public static function fnPluck($key) {
        return function ($v) use ($key) {
            if (isset($v[$key])) {
                return $v[$key];
            }
            return null;
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
        return function ($array) use ($fn) {
            return Sequence::make($array)->map($fn)->to_a();
        };
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
     * @return callable
     */
    public static function fnSum() {
        return function ($sum, $v) { return $sum + $v; };
    }
}