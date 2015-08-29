<?php

namespace Revinate\Sequence;

use Revinate\Sequence\fn as fn;
use \ArrayAccess;
use \Closure;

/**
 * Class FnGen
 * @author jasondent
 * @package Revinate\Sequence
 */
class FnGen {
    /**
     * @return callable
     */
    public static function fnKeepNotEmpty() {
        return fn\fnKeepNotEmpty();
    }

    /**
     * @return callable
     */
    public static function fnKeepIsSet() {
        return fn\fnKeepIsSet();
    }

    /**
     * Alias for fnKeepIsSet
     *
     * Usage Sequence::make($values)->filter(FnGen::clean())->to_a();
     *
     * @return callable
     */
    public static function fnClean() {
        return fn\fnClean();
    }

    /**
     * @return callable
     */
    public static function fnIsEmpty() {
        return fn\fnIsEmpty();
    }

    /**
     * Generates a function that returns true if $map has a key that matches the value.
     *
     * @param array|ArrayAccess $map
     * @return callable
     */
    public static function fnKeepInMap($map) {
        return fn\fnKeepInMap($map);
    }

    /**
     * Generates a function that returns false if $map has a key that matches the value.
     *
     * @param array|ArrayAccess $map
     * @return callable
     */
    public static function fnKeepNotInMap($map) {
        return fn\fnKeepNotInMap($map);
    }

    /**
     * Generates a function that returns true if a value is equal
     *
     * @param $value
     * @return callable
     */
    public static function fnIsEqual($value) {
        return fn\fnIsEqual($value);
    }

    /**
     * Generates a function that returns true if a value is equal
     *
     * @param $value
     * @return callable
     */
    public static function fnIsEqualEqual($value) {
        return fn\fnIsEqualEqual($value);
    }

    /**
     * Generates a function that returns true if a value is not equal
     *
     * @param $value
     * @return callable
     */
    public static function fnIsNotEqual($value) {
        return fn\fnIsNotEqual($value);
    }


    /**
     * Generates a function that returns true if a value is not equal
     *
     * @param $value
     * @return callable
     */
    public static function fnIsNotEqualEqual($value) {
        return fn\fnIsNotEqualEqual($value);
    }


    /**
     * Generates a function that returns true if a value is numeric
     *
     * @return callable
     */
    public static function fnIsNumeric() {
        return fn\fnIsNumeric();
    }

    /** Returns a function that trims the value.
     *
     * @return callable
     */
    public static function fnTrim() {
        return fn\fnTrim();
    }

    /**
     * Generates a function that always returns true
     *
     * @return callable
     */
    public static function fnTrue() {
        return fn\fnTrue();
    }

    /**
     * Generates a function that always returns false
     *
     * @return callable
     */
    public static function fnFalse() {
        return fn\fnFalse();
    }

    /**
     * @param $array
     * @return callable
     */
    public static function fnKeepInArray($array) {
        return fn\fnKeepInArray($array);
    }

    /**
     * @param $array
     * @return callable
     */
    public static function fnKeepNotInArray($array) {
        return fn\fnKeepNotInArray($array);
    }

    /**
     * Generate a function that returns true if an object implements an interface
     *
     * @param $className
     * @return callable
     */
    public static function fnKeepImplements($className) {
        return fn\fnKeepImplements($className);
    }

    /**
     * Generate a function that returns true if a value is an object
     *
     * @return callable
     */
    public static function fnKeepIfIsObject() {
        return fn\fnKeepIfIsObject();
    }

    /**
     * @param ArrayAccess|array $map
     * @return callable
     */
    public static function fnMap($map) {
        return fn\fnMap($map);
    }

    /**
     * Generate a function that casts values to ints.
     * @return callable
     */
    public static function fnCastToInt() {
        return fn\fnCastToInt();
    }

    /**
     * Generate a function that swaps the order of the parameters and calls $fn
     *
     * @param callable $fn
     * @return callable
     */
    public static function fnSwapParamsPassThrough($fn) {
        return fn\fnSwapParamsPassThrough($fn);
    }

    /**
     * Generate a function that returns the key from a map call.
     *
     * @return callable
     */
    public static function fnMapToKey() {
        return fn\fnMapToKey();
    }

    /**
     * Generate a function that combines the key and the value into a tuple.
     *
     * @return callable
     */
    public static function fnMapToKeyValuePair() {
        return fn\fnMapToKeyValuePair();
    }


    /**
     * Generates a function that will apply a mapping function to a sub field of a record
     *
     * @param string $fieldName
     * @param callable $fnMap($fieldValue, $fieldName, $parentRecord, $parentKey)
     * @return callable
     */
    public static function fnMapField($fieldName, $fnMap) {
        return fn\fnMapField($fieldName, $fnMap);
    }


    /**
     * Generate a pluck function that returns the value of a field, or null if the field does not exist.
     *
     * @param string $key - the name / key, of the field to get the value from.
     * @param mixed $default - the default value to assign if the field does not exist.
     * @return callable
     */
    public static function fnPluck($key, $default = null) {
        return fn\fnPluck($key, $default);
    }

    /**
     * returns a function that given a key returns a value from $from.
     *
     * @param array|ArrayAccess $from
     * @param null|mixed $default
     * @return callable
     */
    public static function fnPluckFrom($from, $default = null) {
        return fn\fnPluckFrom($from, $default);
    }

    /**
     * Generate a function that returns the value given.
     *
     * @return callable
     */
    public static function fnIdentity() {
        return fn\fnIdentity();
    }

    /**
     * @description Generate a function that will return the result of calling count()
     *
     * @return callable
     */
    public static function fnCount() {
        return fn\fnCount();
    }

    /**
     * Generate a function that returns a counter.
     *
     * @param int $startingValue
     * @return callable
     */
    public static function fnCounter($startingValue = 0) {
        return fn\fnCounter($startingValue);
    }

    /**
     * Generate a function that when called, will call a set of functions passing the result as input to the next function.
     *
     * @param Callable[]|Callable $fn
     * @return callable
     */
    public static function fnCallChain($fn) {
        return call_user_func_array('\Revinate\Sequence\fn\fnCallChain', func_get_args());
    }

    /**
     * Generate a function that will return the specified parameter
     *
     * @param int $num
     * @return callable
     */
    public static function fnParam($num) {
        return call_user_func_array('\Revinate\Sequence\fn\fnParam', func_get_args());
    }

    /**
     * Returns a function that applies a function to a nested array and returns the results.
     *
     * @return callable
     */
    public static function fnNestedSort() {
        return fn\fnNestedSort();
    }

    /**
     * Returns a function that applies a function to a nested array and returns the results.
     *
     * @param $fn
     * @return callable
     */
    public static function fnNestedMap($fn) {
        return fn\fnNestedMap($fn);
    }

    /**
     * Returns a function that applies a function to a nested array and returns the results.
     *
     * @param $fn
     * @return callable
     */
    public static function fnNestedUKeyBy($fn) {
        return fn\fnNestedUKeyBy($fn);
    }

    /**
     * Returns a function that removes a suffix from a string if it exists
     *
     * @param   string  $suffix
     * @return  callable
     * @deprecated use FnString::fnRemoveSuffix
     */
    public static function fnRemoveSuffix($suffix) {
        return fn\fnRemoveSuffix($suffix);
    }

    /**
     * Returns a function that removes a prefix from a string if it exists
     *
     * @param   string  $prefix
     * @return  callable
     * @deprecated  use FnString::fnRemovePrefix
     */
    public static function fnRemovePrefix($prefix) {
        return fn\fnRemoveSuffix($prefix);
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
        return fn\fnSum($fnMapValue);
    }

    /**
     * @description Generate a function that can be used with reduce to get the max value
     * @return callable
     * @deprecated
     */
    public static function fnMax() {
        return fn\fnMax();
    }

    /**
     * @description Generate a function that can be used with reduce to get the min value
     * @return callable
     * @deprecated
     */
    public static function fnMin() {
        return fn\fnMin();
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
        return fn\fnAvg($fnMapValue);
    }

    /**
     * @description Alias for fnSum -- usage is to do a union between arrays.
     * @return callable
     * @deprecated
     */
    public static function fnUnion() {
        return fn\fnUnion();
    }

    /**
     * Generate a function that will:
     *
     * @param callable $fnReduce(mixed, $value)
     * @return callable
     */
    public static function fnReduce(Closure $fnReduce) {
        return $fnReduce($fnReduce);
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
        return fn\fnIfMap($fnTest, $fnMapTrue, $fnMapFalse);
    }

    /**
     * Create a function that will cache the results of another function based upon the
     *
     * @param callable $fnMap($value,...) - any invariant map function
     * @param callable|null $fnHash  - Converts the arguments into a hash value
     * @return callable
     */
    public static function fnCacheResult(Closure $fnMap, Closure $fnHash = null) {
        return fn\fnCacheResult($fnMap, $fnHash);
    }
}