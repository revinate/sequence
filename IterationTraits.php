<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 20/03/15
 * Time: 12:52
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
    public static function mapKey(Iterator $iterator, Closure $fn) {
        return new MappedSequence($iterator, FnGen::fnIdentity(), $fn);
    }

    /**
     * @param Iterator $iterator
     * @param callable $fn($value, $key)
     * @return FilteredSequence
     */
    public static function filter(Iterator $iterator, Closure $fn) {
        return Sequence::make(new FilteredSequence($iterator, $fn));
    }

    /**
     * Limit the number of items.
     *
     * @param Iterator $iterator
     * @param $limit
     * @return Sequence
     */
    public static function limit(Iterator $iterator, $limit) {
        return Sequence::make(new LimitIterator($iterator, 0, $limit));
    }

    /**
     * Skip a number of items.
     *
     * @param Iterator $iterator
     * @param $offset
     * @return Sequence
     */
    public static function offset(Iterator $iterator, $offset) {
        return Sequence::make(new LimitIterator($iterator, $offset));
    }

    /**
     * @param Iterator $iterator
     * @param $init - The first, initial, value of $reducedValue
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
}