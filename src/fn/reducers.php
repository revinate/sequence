<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 29/08/15
 * Time: 11:32
 */

namespace Revinate\Sequence\fn;

use Revinate\Sequence\Sequence;
use \Closure;

/********************************************************************************
 * Reduce functions
 */


/**
 * Generate a function that will:
 * Reduce the current value.
 * This allows for sum of sums
 *
 * @param callable $fnReduce (mixed, $value)
 * @return callable
 */
function fnReduce($fnReduce) {
    return function ($current, $values) use ($fnReduce) {
        return Sequence::make($values)->reduce($current, $fnReduce);
    };
}

/**
 * Used in Sequence::fold to sum all values.
 *
 * @param callable|null $fnMapValue [optional] - a function to get the needed value
 * @return callable
 *
 * @example:
 * Get the total number of fruit.
 * Sequence::make([['count'=>5, 'name'=>'apple'], ['count'=>2, 'name'=>'orange']])->fold(FnGen::fnSum(FnGen::fnPluck('count'))
 */
function fnSum($fnMapValue = null) {
    if ($fnMapValue) {
        return function ($sum, $v) use ($fnMapValue) { return $sum + $fnMapValue($v); };
    }
    return function ($sum, $v) { return $sum + $v; };
}

/**
 * @description Generate a function that can be used with reduce or fold to get the max value
 * @return Closure
 */
function fnMax() {
    return function ($max, $v) { return max(array($max, $v)); };
}

/**
 * @description Generate a function that can be used with reduce or fold to get the min value
 * @return Closure
 */
function fnMin() {
    return function ($min, $v) { return is_null($min) ? $v : (is_null($v) ? $min : min($min, $v)); };
}

/**
 * Generate a function that will:
 * Calculate the average of a set of values.  Null values are skipped.
 *
 * @param callable|null $fnMapValue [optional] - maps the value before it is computed.
 * @return Closure
 */
function fnAvg($fnMapValue = null) {
    $count = 0;
    $sum   = 0;

    if (! $fnMapValue) {
        $fnMapValue = fnIdentity();
    }

    /** @noinspection PhpUnusedParameterInspection */
    /**
     * @param float|int $avg -- Ignored because the average will be recalculated.
     * @param float|int|null $v -- the value
     * @return float|int|null
     */
    return function ($avg, $v) use (&$count, &$sum, $fnMapValue) {
        $v = $fnMapValue($v);
        if (is_null($v)) {
            if (! $count) {
                return null;
            }
        } else {
            $count += 1;
            $sum += $v;
        }
        return $sum / $count;
    };
}

/**
 * @description Alias for fnSum -- usage is to do a union between arrays.
 * @return Closure
 */
function fnUnion() {
    return fnSum();
}


