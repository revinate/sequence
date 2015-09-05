<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 05/09/15
 * Time: 16:02
 */

namespace Revinate\Sequence\fn;

use \Closure;

/**
 * Generate a comparison function that uses an extractor to get the values for comparison
 *
 * @param Closure $fnExtractValue -- Function that will extract the values to be compared.
 * @return Closure -- returns a function to be used with sort
 */
function fnComp(Closure $fnExtractValue) {
    return function ($lhs, $rhs) use ($fnExtractValue) {
        $lhsValue = $fnExtractValue($lhs);
        $rhsValue = $fnExtractValue($rhs);

        if ($lhsValue < $rhsValue) {
            return -1;
        } else {
            if ($lhsValue > $rhsValue) {
                return 1;
            }
        }
        return 0;
    };
}

/**
 * Generate a comparison function that uses an extractor to get the values for comparison
 * The order of the comparison is reversed.
 *
 * @param Closure $fnExtractValue -- Function that will extract the values to be compared.
 * @return Closure -- returns a function to be used with sort
 */
function fnRevComp(Closure $fnExtractValue) {
    return fnSwapParamsPassThrough(fnComp($fnExtractValue));
}


/**
 * Generates a comparison function that can be used to sort an array by a given field.
 *
 * @param string $fieldName
 * @return Closure
 */
function fnByField($fieldName) {
    return fnComp(fnPluck($fieldName));
}

/**
 * Generates a comparison function that can be used to sort an array by a given field in reverse order.
 *
 * @param string $fieldName
 * @return Closure
 */
function fnByFieldRev($fieldName) {
    return fnRevComp(fnPluck($fieldName));
}

/**
 * Generate a function that can sort an array
 *
 * @param Closure|null $fnComp ($lhs, $rhs) -- see PHP usort
 * @return Closure
 */
function fnSort($fnComp = null) {
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
 * @return Closure
 */
function fnSortByField($fieldName) {
    return fnSort(fnByField($fieldName));
}
