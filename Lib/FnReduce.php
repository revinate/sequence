<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 23/07/2015
 * Time: 20:51
 */

namespace Revinate\SequenceBundle\Lib;

use \Closure;

class FnReduce {
    /**
     * Used in Sequence::Reduce to sum all values.
     *
     * @param Closure $fnMapValue [optional] - a function to get the needed value
     * @return callable
     *
     * @example:
     * Get the total number of fruit.
     * Sequence::make([['count'=>5, 'name'=>'apple'], ['count'=>2, 'name'=>'orange']])->reduce(FnGen::fnSum(FnGen::fnPluck('count'))
     */
    public static function fnSum(Closure $fnMapValue = null) {
        if ($fnMapValue) {
            return function ($sum, $v) use ($fnMapValue) { return $sum + $fnMapValue($v); };
        }
        return function ($sum, $v) { return $sum + $v; };
    }

    /**
     * @description Generate a function that can be used with reduce to get the max value
     * @return callable
     */
    public static function fnMax() {
        return function ($max, $v) { return max(array($max, $v)); };
    }

    /**
     * @description Generate a function that can be used with reduce to get the min value
     * @return callable
     */
    public static function fnMin() {
        return function ($min, $v) { return is_null($min) ? $v : (is_null($v) ? $min : min($min, $v)); };
    }

    /**
     * Generate a function that will:
     * Calculate the average of a set of values.  Null values are skipped.
     *
     * @param callable $fnMapValue [optional] - maps the value before it is computed.
     * @return callable
     */
    public static function fnAvg(Closure $fnMapValue = null) {
        $count = 0;
        $sum = 0;

        if (!$fnMapValue) {
            $fnMapValue = FnGen::fnIdentity();
        }

        return function ($avg, $v) use (&$count, &$sum, $fnMapValue) {
            $v = $fnMapValue($v);
            if (is_null($v)) {
                if (!$count) {
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
     * @return callable
     */
    public static function fnUnion() {
        return FnReduce::fnSum();
    }


}