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
     * Returns a function that applies a function to a nested array and returns the results.
     *
     * @param $fn
     * @return callable
     */
    public static function fnNestedMap($fn) {
        return function ($array) use ($fn) {
            return FancyArray::make($array)->map($fn)->to_a();
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
            return FancyArray::make($array)->ukey_by($fn)->to_a();
        };
    }
}