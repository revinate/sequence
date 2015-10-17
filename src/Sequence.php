<?php

namespace Revinate\Sequence;

use \ArrayIterator;
use \EmptyIterator;
use \Iterator;
use \IteratorIterator;
use \RecursiveIterator;
use \RecursiveIteratorIterator;
use \Traversable;

/**
 * Class Sequence
 * @author jasondent
 * @package Revinate\Sequence
 */
class Sequence extends IteratorIterator implements IterationFunctions, RecursiveIterator {
    /**
     * @param callable $fnValueMap($value, $key) -- function that returns the new value.
     * @param callable $fnKeyMap($key, $value) [optional] -- function that returns the new key
     * @return static
     */
    public function map($fnValueMap, $fnKeyMap = null) {
        return static::make(IterationTraits::map($this, $fnValueMap, $fnKeyMap));
    }

    /**
     * Map the keys of a sequence
     *
     * @param callable $fnKeyMap($key, $value) -- function that returns the new key
     * @return static
     */
    public function mapKeys($fnKeyMap) {
        return static::make(IterationTraits::mapKeys($this, $fnKeyMap));
    }

    /**
     * @param callable $fnMap($value, $key) -- function that returns the new key
     * @return static
     */
    public function keyBy($fnMap) {
        return static::make(IterationTraits::mapKeys($this, FnGen::fnSwapParamsPassThrough($fnMap)));
    }

    /**
     * @param callable $fn
     * @return static
     */
    public function filter($fn) {
        return static::make(IterationTraits::filter($this, $fn));
    }

    /**
     * @param callable $fn($key, $value)
     * @return static
     */
    public function filterKeys($fn) {
        return static::make(IterationTraits::filterKeys($this, $fn));
    }

    /**
     * @param $init
     * @param callable $fn($reducedValue, $value, $key)
     * @return mixed
     */
    public function reduce($init, $fn) {
        return IterationTraits::reduce($this, $init, $fn);
    }

    /**
     * This is a reduce function that results in a Sequence
     * The return value of the reduce function can be anything that can
     * be iterated over.
     *
     * @param mixed $init
     * @param callable $fn($reducedValue, $value, $key)
     * @return static
     */
    public function reduceToSequence($init, $fn) {
        return static::make($this->reduce($init, $fn));
    }

    /**
     * Get the keys
     * @return static
     */
    public function keys() {
        return static::make(IterationTraits::keys($this));
    }

    /**
     * Get the values
     *
     * @return static
     */
    public function values() {
        return static::make(IterationTraits::values($this));
    }

    /**
     * Calls $fnTap for each element.  This function is like walk, but does not consume the iterator.
     * Example: Sequence::make($values)->tap($fnLogValue)->map(...)
     *
     * @param callable $fnTap($value, $key) -- called for each element.  The return value is ignored.
     * @return static
     */
    public function tap($fnTap) {
        return static::make(IterationTraits::tap($this, $fnTap));
    }

    /**
     * Convert to an array.
     * @return array
     */
    public function to_a() {
        return IterationTraits::to_a($this);
    }

    /**
     * calls $fn for every value,key pair
     *
     * @param callable $fn($value, $key)
     * @return Iterator
     */
    public function walk($fn) {
        return IterationTraits::walk($this, $fn);
    }

    /**
     * Limit the number of values returned
     *
     * @param int $limit
     * @return static
     */
    public function limit($limit) {
        return static::make(IterationTraits::limit($this, $limit));
    }

    /**
     * Skip $offset number of values
     *
     * @param int $offset
     * @return static
     */
    public function offset($offset) {
        return static::make(IterationTraits::offset($this, $offset));
    }

    /**
     * Sort ALL the values in the sequence.  Keys are NOT preserved.
     *
     * @param null|$fn($a, $b) [optional] -- function to use to sort the values, needs to return an int see usort
     * @return static
     */
    public function sort($fn = null) {
        return static::make(IterationTraits::sort($this, $fn));
    }

    /**
     * Sort ALL the values in the sequence.  Keys ARE preserved.
     *
     * @param callable $fn($a, $b) [optional] -- function to use to sort the values, needs to return an int see uasort
     * @return static
     */
    public function asort($fn = null) {
        return static::make(IterationTraits::asort($this, $fn));
    }

    /**
     * Sort ALL the values by the keys in the sequence.  Keys ARE preserved.
     *
     * @param callable $fn($a, $b) [optional] -- function to use to sort the values, needs to return an int see uksort
     * @return static
     */
    public function sortKeys($fn = null) {
        return static::make(IterationTraits::sortKeys($this, $fn));
    }

    /**
     * Group A Sequence based upon the result of $fnMapValueToGroup($value, $key) and return the result as a Sequence
     *
     * @param $fnMapValueToGroup($value, $key) -- return the field name to group the values under.
     * @return static
     */
    public function groupBy($fnMapValueToGroup) {
        return static::make(IterationTraits::groupBy($this, $fnMapValueToGroup));
    }

    /**
     * Map -- extracts a given field from the values.
     *
     * This is a alias for ->map(FnGen::fnPluck())
     *
     * @param string $fieldName -- name of the field to extract
     * @param mixed $default = null
     * @return static
     */
    public function pluck($fieldName, $default = null) {
        return $this->map(FnGen::fnPluck($fieldName, $default));
    }

    /**
     * Returns the first element where $fnTest returns true.
     *
     * @param callable|null $fnTest($value, $key)
     * @return null|mixed
     */
    public function first($fnTest = null) {
        if ($fnTest) {
            return $this->filter($fnTest)->limit(1)->reduce(null, FnGen::fnSwapParamsPassThrough(FnGen::fnIdentity()));
        }
        return $this->limit(1)->reduce(null, FnGen::fnSwapParamsPassThrough(FnGen::fnIdentity()));
    }

    /**
     * Returns the key of the first element where $fnTest returns true.
     *
     * @param callable|null $fnTest($value, $key)
     * @return mixed
     */
    public function firstKey($fnTest = null) {
        if ($fnTest) {
            return $this->filter($fnTest)->limit(1)->keys()->reduce(null, FnGen::fnSwapParamsPassThrough(FnGen::fnIdentity()));
        }
        return $this->limit(1)->keys()->reduce(null, FnGen::fnSwapParamsPassThrough(FnGen::fnIdentity()));
    }

    /**
     * Flatten a Sequence by one level into a new Sequence.
     *
     * In its current implementation it forces the evaluation of ALL the items in the Sequence.
     *
     * @return static
     */
    public function flattenOnceNow() {
        $result = $this->reduce(array(), function($result, $value) {
            if ($value instanceof Traversable) {
                $value = iterator_to_array($value);
            }
            if (is_array($value)) {
                return array_merge($result, $value);
            }
            $result[] = $value;
            return $result;
        });
        return static::make($result);
    }

    /**
     * Flatten a Sequence by one level into a new Sequence.
     *
     * @return static
     */
    public function flattenOnce() {
        return $this->flatten(1);
    }

    /**
     * Flatten a Sequence into a new Sequence.
     *
     * @param int $depth
     * @return static
     */
    public function flatten($depth = -1) {
        $recursiveIterator = new RecursiveIteratorIterator(RecursiveSequence::make($this)->setMaxDepth($depth));
        // Simulate array_merge by sequencing numeric keys but do not touch string keys.
        return static::make(IterationTraits::sequenceNumericKeys(Sequence::make($recursiveIterator)));
    }

    /**
     * Traverses a sequence storing the path as keys
     *
     * @param int $depth
     * @return static
     */
    public function traverse($depth = -1) {
        $recursiveIterator = new RecursiveIteratorIterator(TraverseSequence::make($this)->setMaxDepth($depth));
        return static::make($recursiveIterator);
    }

    /**
     * @param mixed $thing
     * @return bool - return true if we can iterate over it.
     */
    public static function canBeSequence($thing) {
        return $thing instanceof Traversable
        || is_array($thing)
        || is_object($thing);
    }

    /**
     * Make a sequence from an Traversable object (array or any other iterator).
     *
     * @param $iterator
     * @return static
     */
    public static function make($iterator) {
        if (! $iterator instanceof Traversable) {
            if (is_array($iterator) || is_object($iterator)) {
                $iterator = new ArrayIterator($iterator);
            } else if (is_null($iterator)) {
                $iterator = new EmptyIterator();
            }
        }
        return new static($iterator);
    }

    /**
     * @return null
     */
    public function getChildren() {
        return null;
    }

    /**
     * @return false
     */
    public function hasChildren() {
        return false;
    }
}


