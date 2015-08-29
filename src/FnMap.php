<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 23/07/2015
 * Time: 16:41
 */

namespace Revinate\Sequence;

use Revinate\Sequence\fn as fn;

/**
 * Class FnMap
 * @package Revinate\Sequence
 * @description This is a static wrapper class for backwards compatibility
 */
class FnMap {

    public static function fnCastToInt() {
        return fn\fnCastToInt();
    }

    public static function fnCastToFloat() {
        return fn\fnCastToFloat();
    }

    public static function fnCastToDouble() {
        return fn\fnCastToDouble();
    }

    public static function fnCastToString() {
        return fn\fnCastToString();
    }

    public static function fnCastToArray() {
        return fn\fnCastToArray();
    }

    public static function fnCastToObject() {
        return fn\fnCastToObject();
    }

}
