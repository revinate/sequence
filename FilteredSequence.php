<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 20/03/15
 * Time: 13:08
 */

class FilteredSequence extends FilterIterator {
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
}
