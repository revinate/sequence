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
}