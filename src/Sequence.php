<?php

namespace Revinate\Sequence;

use \ArrayIterator;
use \Closure;
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
     * @return MappedSequence
     */
    public function map(Closure $fnValueMap, Closure $fnKeyMap = null) {
        return IterationTraits::map($this, $fnValueMap, $fnKeyMap);
    }

    /**
     * Map the keys of a sequence
     *
     * @param callable $fnKeyMap($key, $value) -- function that returns the new key
     * @return MappedSequence
     */
    public function mapKeys(Closure $fnKeyMap) {
        return IterationTraits::mapKeys($this, $fnKeyMap);
    }

    /**
     * @param callable $fnMap($value, $key) -- function that returns the new key
     * @return MappedSequence
     */
    public function keyBy(Closure $fnMap) {
        return IterationTraits::mapKeys($this, FnGen::fnSwapParamsPassThrough($fnMap));
    }

    /**
     * @param callable $fn
     * @return Sequence
     */
    public function filter(Closure $fn) {
        return IterationTraits::filter($this, $fn);
    }

    /**
     * @param callable $fn($key, $value)
     * @return Sequence
     */
    public function filterKeys(Closure $fn) {
        return IterationTraits::filterKeys($this, $fn);
    }

    /**
     * @param $init
     * @param callable $fn($reducedValue, $value, $key)
     * @return mixed
     */
    public function reduce($init, Closure $fn) {
        return IterationTraits::reduce($this, $init, $fn);
    }

    /**
     * This is a reduce function that results in a Sequence
     * The return value of the reduce function can be anything that can
     * be iterated over.
     *
     * @param mixed $init
     * @param callable $fn($reducedValue, $value, $key)
     * @return Sequence
     */
    public function reduceToSequence($init, Closure $fn) {
        return Sequence::make($this->reduce($init, $fn));
    }

    /**
     * Get the keys
     * @return MappedSequence
     */
    public function keys() {
        return IterationTraits::keys($this);
    }

    /**
     * Get the values
     *
     * @return MappedSequence
     */
    public function values() {
        return IterationTraits::values($this);
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
    public function walk(Closure $fn) {
        return IterationTraits::walk($this, $fn);
    }

    /**
     * Limit the number of values returned
     *
     * @param int $limit
     * @return Sequence
     */
    public function limit($limit) {
        return IterationTraits::limit($this, $limit);
    }

    /**
     * Skip $offset number of values
     *
     * @param int $offset
     * @return Sequence
     */
    public function offset($offset) {
        return IterationTraits::offset($this, $offset);
    }

    /**
     * Sort ALL the values in the sequence.  Keys are NOT preserved.
     *
     * @param null|Closure $fn($a, $b) [optional] -- function to use to sort the values, needs to return an int see usort
     * @return Sequence
     */
    public function sort($fn = null) {
        return IterationTraits::sort($this, $fn);
    }

    /**
     * Sort ALL the values in the sequence.  Keys ARE preserved.
     *
     * @param callable $fn($a, $b) [optional] -- function to use to sort the values, needs to return an int see uasort
     * @return Sequence
     */
    public function asort(Closure $fn = null) {
        return IterationTraits::asort($this, $fn);
    }

    /**
     * Sort ALL the values by the keys in the sequence.  Keys ARE preserved.
     *
     * @param callable $fn($a, $b) [optional] -- function to use to sort the values, needs to return an int see uksort
     * @return Sequence
     */
    public function sortKeys(Closure $fn = null) {
        return IterationTraits::sortKeys($this, $fn);
    }

    /**
     * Group A Sequence based upon the result of $fnMapValueToGroup($value, $key) and return the result as a Sequence
     *
     * @param Closure $fnMapValueToGroup($value, $key) -- return the field name to group the values under.
     * @return Sequence
     */
    public function groupBy(Closure $fnMapValueToGroup) {
        return IterationTraits::groupBy($this, $fnMapValueToGroup);
    }

    /**
     * Map -- extracts a given field from the values.
     *
     * This is a alias for ->map(FnGen::fnPluck())
     *
     * @param string $fieldName -- name of the field to extract
     * @param mixed $default = null
     * @return MappedSequence
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
    public function first(Closure $fnTest = null) {
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
    public function firstKey(Closure $fnTest = null) {
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
     * @return Sequence
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
        return Sequence::make($result);
    }

    /**
     * Flatten a Sequence by one level into a new Sequence.
     *
     * @return Sequence
     */
    public function flattenOnce() {
        return $this->flatten(1);
    }

    /**
     * Flatten a Sequence into a new Sequence.
     *
     * @param int $depth
     * @return Sequence
     */
    public function flatten($depth = -1) {
        $recursiveIterator = new RecursiveIteratorIterator(RecursiveSequence::make($this)->setMaxDepth($depth));
        // Simulate array_merge by sequencing numeric keys but do not touch string keys.
        return IterationTraits::sequenceNumericKeys(Sequence::make($recursiveIterator));
    }

    /**
     * Traverses a sequence storing the path as keys
     *
     * @param int $depth
     * @return Sequence
     */
    public function traverse($depth = -1) {
        $recursiveIterator = new RecursiveIteratorIterator(TraverseSequence::make($this)->setMaxDepth($depth));
        return Sequence::make($recursiveIterator);
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


