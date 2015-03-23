<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 20/03/15
 * Time: 13:08
 */

class FilteredSequence extends FilterIterator implements  IterationFunctions {
    protected $fnFilterFunction = null;

    /**
     * @param Iterator $iterator
     * @param callable $fnFilterFunction($value, $key) - returns bool - true to keep, false to throw away.
     */
    public function __construct(Iterator $iterator, Closure $fnFilterFunction) {
        parent::__construct($iterator);
        $this->fnFilterFunction = $fnFilterFunction;
    }

    /**
     * Necessary to support FilterIterator - true = keep, false = skip
     * @return bool
     */
    public function accept() {
        /** @var Closure $fn */
        $fn = $this->fnFilterFunction;
        return $fn($this->current(), $this->key());
    }

    /**
     * @param callable $fn
     * @return MappedSequence
     */
    public function map(Closure $fn) {
        return IterationTraits::map($this, $fn);
    }

    /**
     * @param callable $fn
     * @return FilteredSequence
     */
    public function filter(Closure $fn) {
        return IterationTraits::filter($this, $fn);
    }

    /**
     * @param $init
     * @param callable $fn($reducedValue, $value, $key)
     * @return mixed
     */
    public function reduce($init, Closure $fn) {
        return IterationTraits::reduce($this, $init, $fn);
    }

    /**
     * Get the keys
     * @return MappedSequence
     */
    public function keys() {
        return IterationTraits::keys($this);
    }

    /**
     * Get the values
     *
     * @return MappedSequence
     */
    public function values() {
        return IterationTraits::values($this);
    }

    /**
     * Convert to an array.
     * @return array
     */
    public function to_a() {
        return IterationTraits::to_a($this);
    }
}
