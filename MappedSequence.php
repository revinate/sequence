<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 20/03/15
 * Time: 13:09
 */

class MappedSequence extends Sequence {
    protected $fnMapValueFunction;
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
        $fn = $this->fnMapValueFunction;
        return $fn(parent::current(), parent::key());
    }

    public function key() {
        $fn = $this->fnMapKeyFunction;
        return $fn(parent::key(), parent::current());
    }
}