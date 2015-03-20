<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 20/03/15
 * Time: 11:54
 */

class Sequence extends IteratorIterator implements IterationFunctions {

    public function map(Closure $fn) {
        return IterationTraits::map($this, $fn);
    }

    public function filter(Closure $fn) {
        return IterationTraits::filter($this, $fn);
    }

    public function reduce($init, Closure $fn) {
        return IterationTraits::reduce($this, $init, $fn);
    }

    public function keys() {
        return IterationTraits::keys($this);
    }

    public function values() {
        return IterationTraits::values($this);
    }

    public function to_a() {
        return IterationTraits::to_a($this);
    }

    public static function make($iterator) {
        if (is_array($iterator)) {
            $iterator = new ArrayIterator($iterator);
        }
        return new static($iterator);
    }
}


