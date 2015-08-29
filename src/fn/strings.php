<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 29/08/15
 * Time: 11:22
 */

namespace Revinate\Sequence\fn;

/**
 * Returns a function that trims the value.
 *
 * @return callable
 */
function fnTrim() {
    return function ($v) {
        return trim($v);
    };
}

/**
 * Returns a function that removes a suffix from a string if it exists
 *
 * @param   string $suffix
 * @return  callable
 */
function fnRemoveSuffix($suffix) {
    return function ($val) use ($suffix) {
        return preg_replace('/' . preg_quote($suffix) . '$/', '', $val);
    };
}

/**
 * Returns a function that removes a prefix from a string if it exists
 *
 * @param   string $prefix
 * @return  callable
 */
function fnRemovePrefix($prefix) {
    return function ($val) use ($prefix) {
        return preg_replace('/^' . preg_quote($prefix) . '/', '', $val);
    };
}

/**
 * Returns a function that prefixes a string
 *
 * @param $prefix
 * @return callable
 */
function fnAddPrefix($prefix) {
    return function ($val) use ($prefix) {
        return $prefix . $val;
    };
}

/**
 * Returns a function that postfixes a string
 *
 * @param $postfix
 * @return callable
 */
function fnAddPostfix($postfix) {
    return function ($val) use ($postfix) {
        return $val . $postfix;
    };
}

/**
 * @param string|null $encoding
 * @return \Closure
 */
function fnToUpper($encoding = null) {
    if ($encoding) {
        return function ($val) use ($encoding) {
            return mb_strtoupper($val, $encoding);
        };
    }

    return function ($val) {
        return mb_strtoupper($val);
    };
}

/**
 * @param string|null $encoding
 * @return \Closure
 */
function fnToLower($encoding = null) {
    if ($encoding) {
        return function ($val) use ($encoding) {
            return mb_strtolower($val, $encoding);
        };
    }

    return function ($val) {
        return mb_strtolower($val);
    };
}

