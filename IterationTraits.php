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
     * @param callable $fn
     * @return MappedSequence
     */
    public static function map(Iterator $iterator, Closure $fn) {
        return new MappedSequence($iterator, $fn, FnGen::fnIdentity());
    }

    /**
     * @param Iterator $iterator
     * @param callable $fn
     * @return FilteredSequence
     */
    public static function filter(Iterator $iterator, Closure $fn) {
        return new FilteredSequence($iterator, $fn);
    }

    /**
     * @param Iterator $iterator
     * @param $init - The first, initial, value of $reducedValue
     * @param callable $fn - function that takes the following params ($reducedValue, $value, $key) where $reducedValue is the current
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
}