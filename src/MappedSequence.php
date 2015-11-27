<?php

namespace Revinate\Sequence;

use \Closure;
use \Iterator;

/**
 * Class MappedSequence
 * @author jasondent
 * @package Revinate\Sequence
 */
class MappedSequence extends Sequence {

    /** @var Closure|callable $fnMapValueFunction */
    protected $fnMapValueFunction;

    /** @var Closure|callable $fnMapKeyFunction */
    protected $fnMapKeyFunction;

    /**
     * @param Iterator      $iterator
     * @param callable|null $fnMapValueFunction($value, $key)
     * @param callable|null $fnMapKeyFunction($key, $value)
     */
    public function __construct(Iterator $iterator, $fnMapValueFunction, $fnMapKeyFunction) {
        parent::__construct($iterator);
        if (!$fnMapKeyFunction) {
            $fnMapKeyFunction = FnGen::fnIdentity();
        }

        if (!$fnMapValueFunction) {
            $fnMapValueFunction = FnGen::fnIdentity();
        }

        $this->fnMapValueFunction = $fnMapValueFunction;
        $this->fnMapKeyFunction = $fnMapKeyFunction;
    }

    public function current() {
        $fn = $this->fnMapValueFunction;
        return $fn(parent::current(), parent::key());
    }

    public function key() {
        $fn = $this->fnMapKeyFunction;
        return $fn(parent::key(), parent::current());
    }

    /**
     * @inheritDoc
     */
    public static function make($iterator) {
        // Force it to be a Sequence because the constructor for MappedSequence isn't identical
        return Sequence::make($iterator);
    }

}