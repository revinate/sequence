<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 29/06/15
 * Time: 15:35
 */

namespace Revinate\SequenceBundle\Lib;


class FnString {
    /**
     * Returns a function that trims the value.
     *
     * @return callable
     */
    public static function fnTrim() {
        return function ($v) {
            return trim($v);
        };
    }

    /**
     * Returns a function that removes a suffix from a string if it exists
     *
     * @param   string  $suffix
     * @return  callable
     */
    public static function fnRemoveSuffix($suffix) {
        return function ($val) use ($suffix) {
            return preg_replace('/'.preg_quote($suffix).'$/', '', $val);
        };
    }

    /**
     * Returns a function that removes a prefix from a string if it exists
     *
     * @param   string  $prefix
     * @return  callable
     */
    public static function fnRemovePrefix($prefix) {
        return function ($val) use ($prefix) {
            return preg_replace('/^'.preg_quote($prefix).'/', '', $val);
        };
    }

    /**
     * Returns a function that prefixes a string
     *
     * @param $prefix
     * @return callable
     */
    public static function fnAddPrefix($prefix) {
        return function ($val) use ($prefix) {
            return $prefix.$val;
        };
    }

    /**
     * Returns a function that postfixes a string
     *
     * @param $postfix
     * @return callable
     */
    public static function fnAddPostfix($postfix) {
        return function ($val) use ($postfix) {
            return $val.$postfix;
        };
    }
}