<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 23/07/2015
 * Time: 16:41
 */

namespace Revinate\SequenceBundle\Lib;

use \Closure;

class FnMap {

    public static function fnCastToInt() {
        return function ($value) { return (int)$value; };
    }

    public static function fnCastToFloat() {
        return function ($value) { return (float)$value; };
    }

    public static function fnCastToDouble() {
        return function ($value) { return (double)$value; };
    }

    public static function fnCastToString() {
        return function ($value) { return (string)$value; };
    }

    public static function fnCastToArray() {
        return function ($value) { return (array)$value; };
    }

    public static function fnCastToObject() {
        return function ($value) { return (object)$value; };
    }

}
