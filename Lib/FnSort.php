<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 29/06/15
 * Time: 15:11
 */

namespace Revinate\SequenceBundle\Lib;
use \Closure;

class FnSort {

    /**
     * Generate a comparison function that uses an extractor to get the values for comparison
     *
     * @param callable $fnExtractValue -- Function that will extract the values to be compared.
     * @return callable -- returns a function to be used with sort
     */
    public static function fnComp(Closure $fnExtractValue) {
        return function ($lhs, $rhs) use ($fnExtractValue) {
            $lhsValue = $fnExtractValue($lhs);
            $rhsValue = $fnExtractValue($rhs);

            if ($lhsValue < $rhsValue) {
                return -1;
            } else if ($lhsValue > $rhsValue) {
                return 1;
            }
            return 0;
        };
    }

    /**
     * Generate a comparison function that uses an extractor to get the values for comparison
     * The order of the comparison is reversed.
     *
     * @param callable $fnExtractValue -- Function that will extract the values to be compared.
     * @return callable -- returns a function to be used with sort
     */
    public static function fnRevComp(Closure $fnExtractValue) {
        return FnGen::fnSwapParamsPassThrough(FnSort::fnComp($fnExtractValue));
    }


    /**
     * Generate a function that can sort an array
     *
     * @param callable $fnComp($lhs, $rhs) -- see PHP usort
     * @return callable
     */
    public static function fnSort(Closure $fnComp = null) {
        if ($fnComp) {
            return function ($array) use ($fnComp) {
                usort($array, $fnComp);
                return $array;
            };
        }

        return function ($array) {
            sort($array);
            return $array;
        };
    }

    /**
     * Generates a sort function that can sort an array by a given field.
     *
     * @param string $fieldName
     * @return callable
     */
    public static function fnSortByField($fieldName) {
        return FnSort::fnSort(FnSort::fnComp(FnGen::fnPluck($fieldName)));
    }
}
