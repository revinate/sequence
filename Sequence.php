<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 20/03/15
 * Time: 11:54
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
     * Sort ALL the values in the sequence.  Keys ARE preserved.
     *
     * @param callable $fn($a, $b) [optional] -- function to use to sort the values, needs to return an int see uasort
     * @return Sequence
     */
    public function sort(Closure $fn = null) {
        return IterationTraits::sort($this, $fn);
    }


    /**
     * Sort ALL the values by the keys in the sequence.  Keys ARE preserved.
     *
     * @param callable $fn($a, $b) [optional] -- function to use to sort the values, needs to return an int see uasort
     * @return Sequence
     */
    public function sortKeys(Closure $fn = null) {
        return IterationTraits::sortKeys($this, $fn);
    }


    /**
     * Make a sequence from an Traversable object (array or any other iterator).
     *
     * @param $iterator
     * @return static
     */
    public static function make($iterator) {
        if (is_array($iterator)) {
            $iterator = new ArrayIterator($iterator);
        } else if (is_null($iterator)) {
            $iterator = new EmptyIterator();
        }
        return new static($iterator);
    }
}


