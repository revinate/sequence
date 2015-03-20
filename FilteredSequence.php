<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 20/03/15
 * Time: 13:08
 */

class FilteredSequence extends FilterIterator implements  IterationFunctions {
    protected $fnFilterFunction = null;

    public function __construct(Iterator $iterator, Closure $fnFilterFunction) {
        parent::__construct($iterator);
        $this->fnFilterFunction = $fnFilterFunction;
    }

    public function accept() {
        $fn = $this->fnFilterFunction;
        return $fn($this->current(), $this->key());
    }

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
}
