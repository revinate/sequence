<?php

namespace Revinate\SequenceBundle\Lib;

/**
 * Class MappedSequence
 * @author jasondent
 * @package Revinate\SequenceBundle\Lib
 */
class MappedSequence extends Sequence {

    /** @var Closure $fnMapValueFunction */
    protected $fnMapValueFunction;

    /** @var Closure $fnMapKeyFunction */
    protected $fnMapKeyFunction;

    public function __construct(Iterator $iterator, Closure $fnMapValueFunction, Closure $fnMapKeyFunction) {
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
        /** @var Closure $fn */
        $fn = $this->fnMapValueFunction;
        return $fn(parent::current(), parent::key());
    }

    public function key() {
        /** @var Closure $fn */
        $fn = $this->fnMapKeyFunction;
        return $fn(parent::key(), parent::current());
    }
}