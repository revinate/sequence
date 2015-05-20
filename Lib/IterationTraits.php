<?php

namespace Revinate\SequenceBundle\Lib;

/**
 * Class IterationTraits
 * @author jasondent
 * @package Revinate\SequenceBundle\Lib
 */
class IterationTraits {

    /**
     * @param Iterator $iterator
     * @param callable $fnValue($value, $key)
     * @param callable $fnKey($key, $value) [optional]
     * @return MappedSequence
     */
    public static function map(Iterator $iterator, Closure $fnValue, Closure $fnKey = null) {
        if (empty($fnKey)) {
            $fnKey = FnGen::fnIdentity();
        }
        return new MappedSequence($iterator, $fnValue, $fnKey);
    }

    /**
     * @param Iterator $iterator
     * @param callable $fn($key, $value)
     * @return MappedSequence
     */
    public static function mapKeys(Iterator $iterator, Closure $fn) {
        return new MappedSequence($iterator, FnGen::fnIdentity(), $fn);
    }

    /**
     * @param Iterator $iterator
     * @param callable $fn($value, $key)
     * @return Sequence
     */
    public static function filter(Iterator $iterator, Closure $fn) {
        return Sequence::make(new FilteredSequence($iterator, $fn));
    }

    /**
     * @param Iterator $iterator
     * @param callable $fn($key, $value)
     * @return Sequence
     */
    public static function filterKeys(Iterator $iterator, Closure $fn) {
        return Sequence::make(new FilteredSequence($iterator, FnGen::fnSwapParamsPassThrough($fn)));
    }

    /**
     * Limit the number of items.
     *
     * @param Iterator $iterator
     * @param int $limit
     * @return Sequence
     */
    public static function limit(Iterator $iterator, $limit) {
        return Sequence::make(new LimitIterator($iterator, 0, $limit));
    }

    /**
     * Skip a number of items.
     *
     * @param Iterator $iterator
     * @param int $offset
     * @return Sequence
     */
    public static function offset(Iterator $iterator, $offset) {
        return Sequence::make(new LimitIterator($iterator, $offset));
    }

    /**
     * @param Iterator $iterator
     * @param mixed $init - The first, initial, value of $reducedValue
     * @param callable $fn($reducedValue, $value, $key) - function that takes the following params ($reducedValue, $value, $key) where $reducedValue is the current
     * @return mixed
     */
    public static function reduce(Iterator $iterator, $init, Closure $fn) {
        $reducedValue = $init;
        foreach ($iterator as $key => $value) {
            $reducedValue = $fn($reducedValue, $value, $key);
        }

        return $reducedValue;
    }

    /**
     * @param Iterator $iterator
     * @return array
     */
    public static function to_a(Iterator $iterator) {
        return iterator_to_array($iterator);
    }

    /**
     * @param Iterator $iterator
     * @return MappedSequence
     */
    public static function keys(Iterator $iterator) {
        return new MappedSequence($iterator, FnGen::fnMapToKey(), FnGen::fnCounter());
    }

    /**
     * @param Iterator $iterator
     * @return MappedSequence
     */
    public static function values(Iterator $iterator) {
        return new MappedSequence($iterator, FnGen::fnIdentity(), FnGen::fnCounter());
    }

    /**
     * Call a function for all items available to the iterator.
     *
     * Note: it does a rewind on $iterator and walks ALL values.  It does NOT rewind the iterator a second time.
     *
     * @param Iterator $iterator
     * @param callable $fn($value, $key) -- the function to call for each item.
     * @return Iterator
     */
    public static function walk(Iterator $iterator, Closure $fn) {
        foreach ($iterator as $key => $value) {
            $fn($value, $key);
        }

        return $iterator;
    }

    /**
     * Collect all the values into an array, sort them and return the resulting Sequence.  Keys are NOT preserved.
     *
     * @param Iterator $iterator
     * @param callable $fn
     * @return static
     */
    public static function sort(Iterator $iterator, Closure $fn = null) {
        $array = iterator_to_array($iterator);
        if ($fn) {
            usort($array, $fn);
        } else {
            sort($array);
        }

        return Sequence::make($array);
    }

    /**
     * Collect all the values into an array, sort them and return the resulting Sequence.  Keys are preserved.
     *
     * @param Iterator $iterator
     * @param callable $fn
     * @return static
     */
    public static function asort(Iterator $iterator, Closure $fn = null) {
        $array = iterator_to_array($iterator);
        if ($fn) {
            uasort($array, $fn);
        } else {
            asort($array);
        }

        return Sequence::make($array);
    }

    /**
     * Collect all the values into an array, sort them and return the resulting Sequence.  Keys are preserved.
     *
     * @param Iterator $iterator
     * @param callable $fn
     * @return static
     */
    public static function sortKeys(Iterator $iterator, Closure $fn = null) {
        $array = iterator_to_array($iterator);
        if ($fn) {
            uksort($array, $fn);
        } else {
            ksort($array);
        }

        return Sequence::make($array);
    }
}