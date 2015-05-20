<?php

namespace Revinate\SequenceBundle\Lib;

use \ArrayIterator;
use \Closure;
use \EmptyIterator;
use \Iterator;
use \IteratorIterator;
use \Traversable;

/**
 * Class Sequence
 * @author jasondent
 * @package Revinate\SequenceBundle\Lib
 */
class Sequence extends IteratorIterator implements IterationFunctions {
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
     * @param callable $fn($a, $b) [optional] -- function to use to sort the values, needs to return an int see usort
     * @return Sequence
     */
    public function sort(Closure $fn = null) {
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
    public function flattenOnce() {
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
}


