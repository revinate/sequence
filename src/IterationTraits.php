<?php

namespace Revinate\Sequence;

use \Iterator;
use \LimitIterator;

/**
 * Class IterationTraits
 * @author jasondent
 * @package Revinate\Sequence
 */
class IterationTraits {

    /**
     * @param Iterator $iterator
     * @param callable $fnValue($value, $key)
     * @param callable $fnKey($key, $value) [optional]
     * @return MappedSequence
     */
    public static function map(Iterator $iterator, $fnValue, $fnKey = null) {
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
    public static function mapKeys(Iterator $iterator, $fn) {
        return new MappedSequence($iterator, FnGen::fnIdentity(), $fn);
    }

    /**
     * @param Iterator $iterator
     * @param callable $fn($value, $key)
     * @return Sequence
     */
    public static function filter(Iterator $iterator, $fn) {
        return Sequence::make(new FilteredSequence($iterator, $fn));
    }

    /**
     * @param Iterator $iterator
     * @param callable $fn($key, $value)
     * @return Sequence
     */
    public static function filterKeys(Iterator $iterator, $fn) {
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
    public static function reduce(Iterator $iterator, $init, $fn) {
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
     * @param Iterator $iterator
     * @return MappedSequence
     */
    public static function sequenceNumericKeys(Iterator $iterator) {
        return new MappedSequence(
            $iterator,
            FnGen::fnIdentity(),
            FnGen::fnIfMap(FnGen::fnIsNumeric(), FnGen::fnCounter(), FnGen::fnIdentity())
        );
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
    public static function walk(Iterator $iterator, $fn) {
        foreach ($iterator as $key => $value) {
            $fn($value, $key);
        }

        return $iterator;
    }


    /**
     * Allow for a function to be called for each element.
     *
     * @param Iterator $iterator
     * @param callable $fnTap($value, $key) - called with each $key/$value pair, the return value is ignored.
     * @return MappedSequence
     */
    public static function tap(Iterator $iterator, $fnTap) {
        $fnValue = function ($v, $k) use ($fnTap) { $fnTap($v, $k); return $v; };
        return new MappedSequence($iterator, $fnValue, null);
    }


    /**
     * @param callable $fn
     * @return Sequence
     */
    public static function wrapFunctionIntoSequenceOnDemand($fn) {
        return Sequence::make(new OnDemandIterator($fn));
    }

    /**
     * Collect all the values into an array, sort them and return the resulting Sequence.  Keys are NOT preserved.
     *
     * @param Iterator $iterator
     * @param callable $fn
     * @return Sequence
     */
    public static function sort(Iterator $iterator, $fn = null) {
        return self::wrapFunctionIntoSequenceOnDemand(function() use ($iterator, $fn) {
            $array = iterator_to_array($iterator);
            if ($fn) {
                usort($array, $fn);
            } else {
                sort($array);
            }
            return new \ArrayIterator($array);
        });
    }

    /**
     * Collect all the values into an array, sort them and return the resulting Sequence.  Keys are preserved.
     *
     * @param Iterator $iterator
     * @param callable $fn
     * @return Sequence
     */
    public static function asort(Iterator $iterator, $fn = null) {
        return self::wrapFunctionIntoSequenceOnDemand(function() use ($iterator, $fn) {
            $array = iterator_to_array($iterator);
            if ($fn) {
                uasort($array, $fn);
            } else {
                asort($array);
            }
            return new \ArrayIterator($array);
        });
    }

    /**
     * Collect all the values into an array, sort them and return the resulting Sequence.  Keys are preserved.
     *
     * @param Iterator $iterator
     * @param callable $fn
     * @return Sequence
     */
    public static function sortKeys(Iterator $iterator, $fn = null) {
        return self::wrapFunctionIntoSequenceOnDemand(function() use ($iterator, $fn) {
            $array = iterator_to_array($iterator);
            if ($fn) {
                uksort($array, $fn);
            } else {
                ksort($array);
            }
            return new \ArrayIterator($array);
        });
    }

    /**
     * Group all the the values into an array and return the result as a Sequence.
     *
     * @param Iterator $iterator
     * @param callable $fnToGroup
     * @param null $keys
     * @return Sequence
     */
    public static function groupBy(Iterator $iterator, $fnToGroup, $keys = null) {
        $init = self::initKeysForGroupBy($keys);
        return self::wrapFunctionIntoSequenceOnDemand(function() use ($iterator, $fnToGroup, $init) {
            return Sequence::make($iterator)
                ->reduceToSequence($init, function ($collection, $value, $key) use ($fnToGroup) {
                    $collection[$fnToGroup($value, $key)][] = $value;
                    return $collection;
                });
        });
    }

    /**
     * Initializes an array of arrays keyed based on the input. The behavior depends on the input type, as follows:
     *
     * If $keys is...
     *  null  -> empty array; no nested arrays (default)
     *  int   -> indexed array with length = $keys
     *  array -> associative array keyed by the values of $keys
     *
     * @param null|int|array $keys
     * @return array
     */
    protected static function initKeysForGroupBy($keys = null)
    {
        if (is_null($keys)) {
            return array();
        }

        if (is_numeric($keys)) {
            $keys = range(0, $keys-1);
        }
        return Sequence::make($keys)
            ->map(function() { return array(); }, FnGen::fnMapToKey())
            ->to_a();
    }
}
