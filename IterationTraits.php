<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 20/03/15
 * Time: 12:52
 */

class IterationTraits {
    public static function map(Iterator $iterator, Closure $fn) {
        return new MappedSequence($iterator, $fn, FnGen::fnIdentity());
    }

    public static function filter(Iterator $iterator, Closure $fn) {
        return new FilteredSequence($iterator, $fn);
    }

    public static function reduce(Iterator $iterator, $init, Closure $fn) {
        $r = $init;
        foreach ($iterator as $key => $value) {
            $r = $fn($r, $value, $key);
        }

        return $r;
    }

    public static function to_a(Iterator $iterator) {
        return iterator_to_array($iterator);
    }

    public static function keys(Iterator $iterator) {
        return new MappedSequence($iterator, FnGen::fnMapToKey(), FnGen::fnCounter());
    }

    public static function values(Iterator $iterator) {
        return new MappedSequence($iterator, FnGen::fnIdentity(), FnGen::fnCounter());
    }
}