<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 24/07/2015
 * Time: 18:32
 */

namespace Revinate\SequenceBundle\Lib;

use \ArrayAccess;

class ArrayUtil {

    /**
     * @param array|ArrayAccess|object $doc
     * @param string $fieldName
     * @param null|mixed $default
     * @return mixed
     */
    public static function getField($doc, $fieldName, $default = null) {
        if (is_array($doc) && array_key_exists($fieldName, $doc)) {
            return $doc[$fieldName];
        } elseif ($doc instanceof ArrayAccess) {
            if ($doc->offsetExists($fieldName)) {
                return $doc[$fieldName];
            }
        } elseif (is_object($doc)) {
            // Check getter
            $getMethod = 'get' . $fieldName;
            if (method_exists($doc, $getMethod)) {
                return call_user_func(array($doc, $getMethod));
            }

            if (property_exists($doc, $fieldName)) {
                $values = get_object_vars($doc);
                if (array_key_exists($fieldName, $values)) {
                    return $values[$fieldName];
                }
            }
        }

        return $default;
    }

    /**
     * @param array|ArrayAccess|object $doc
     * @param string $fieldName
     * @param mixed $value
     * @return array|ArrayAccess|object
     */
    public static function setField($doc, $fieldName, $value) {
        if ($doc instanceof ArrayAccess || is_array($doc)) {
            $doc[$fieldName] = $value;
        } elseif (is_object($doc)) {
            // Check setter
            $setMethod = 'set' . $fieldName;
            if (method_exists($doc, $setMethod)) {
                call_user_func(array($doc, $setMethod));
            } else {
                $doc->{$fieldName} = $value;
            }
        }

        return $doc;
    }

    /**
     * @param array|ArrayAccess|object $doc
     * @param string[] $path
     * @param null|mixed $default
     * @return mixed
     */
    public static function getPath($doc, $path, $default = null) {
        $notFound = (object)array(-1 => 'not found');

        $subDoc = $doc;
        foreach ($path as $field) {
            $subDoc = ArrayUtil::getField($subDoc, $field, $notFound);
            if ($subDoc === $notFound) {
                break;
            }
        }

        return $subDoc !== $notFound ? $subDoc : $default;
    }

    /**
     * @param array|ArrayAccess|object $doc
     * @param string[]      $path   -- array of field names
     * @param mixed         $value
     * @return array|ArrayAccess|object
     */
    public static function setPath($doc, $path, $value) {
        if (empty($path)) {
            return $value;
        }
        $field = array_shift($path);
        return ArrayUtil::setField($doc, $field, ArrayUtil::setPath(ArrayUtil::getField($doc, $field, array()), $path, $value));
    }
}
