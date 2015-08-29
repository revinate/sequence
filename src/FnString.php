<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 29/06/15
 * Time: 15:35
 */

namespace Revinate\Sequence;

use Revinate\Sequence\fn as fn;

class FnString {
    /**
     * Returns a function that trims the value.
     *
     * @return callable
     */
    public static function fnTrim() {
        return fn\fnTrim();
    }

    /**
     * Returns a function that removes a suffix from a string if it exists
     *
     * @param   string  $suffix
     * @return  callable
     */
    public static function fnRemoveSuffix($suffix) {
        return fn\fnRemoveSuffix($suffix);
    }

    /**
     * Returns a function that removes a prefix from a string if it exists
     *
     * @param   string  $prefix
     * @return  callable
     */
    public static function fnRemovePrefix($prefix) {
        return fn\fnRemovePrefix($prefix);
    }

    /**
     * Returns a function that prefixes a string
     *
     * @param $prefix
     * @return callable
     */
    public static function fnAddPrefix($prefix) {
        return fn\fnAddPrefix($prefix);
    }

    /**
     * Returns a function that postfixes a string
     *
     * @param $postfix
     * @return callable
     */
    public static function fnAddPostfix($postfix) {
        return fn\fnAddPostfix($postfix);
    }

    /**
     * @param string|null $encoding
     * @return \Closure
     */
    public static function fnToUpper($encoding = null) {
        return fn\fnToUpper($encoding);
    }

    /**
     * @param string|null $encoding
     * @return \Closure
     */
    public static function fnToLower($encoding = null) {
        return fn\fnToLower($encoding);
    }
}