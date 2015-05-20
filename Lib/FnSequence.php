<?php

namespace Revinate\SequenceBundle\Lib;

/**
 * Class FnSequence
 * @author jasondent
 * @package Revinate\SequenceBundle\Lib
 */
class FnSequence {
    /** @var Closure|null  */
    protected $fnToApply = null;

    public function __construct(Closure $fnPrev = null) {
        if (! $fnPrev) {
            $fnPrev = FnGen::fnIdentity();
        }

        $this->fnToApply = function ($values) use ($fnPrev) {
            return Sequence::make($fnPrev($values));
        };
    }

    /**
     * Adds a map function to the call chain.
     *
     * @param callable $fnMap
     * @return FnSequence
     */
    public function map(Closure $fnMap, Closure $fnMapKey = null) {
        $self = $this;
        $fnApply = function($values) use ($self, $fnMap, $fnMapKey) {
            return $self->apply($values)->map($fnMap, $fnMapKey);
        };
        return new FnSequence($fnApply);
    }

    /**
     * Adds a key map function to the call chain
     *
     * @param callable $fnKeyMap($key, $value) -- function that returns the new key
     * @return FnSequence
     */
    public function mapKey(Closure $fnMapKey) {
        $self = $this;
        $fnApply = function($values) use ($self, $fnMapKey) {
            return $self->apply($values)->mapKeys($fnMapKey);
        };
        return new FnSequence($fnApply);
    }

    /**
     * Adds a key by function to the call chain
     *
     * @param callable $fnMap($value, $key) -- function that returns the new key
     * @return FnSequence
     */
    public function keyBy(Closure $fnMap) {
        $self = $this;
        $fnApply = function($values) use ($self, $fnMap) {
            return $self->apply($values)->keyBy($fnMap);
        };
        return new FnSequence($fnApply);
    }

    /**
     * Adds a filter function to the call chain
     *
     * @param callable $fnFilter($value, $key)
     * @return FnSequence
     */
    public function filter(Closure $fnFilter) {
        $self = $this;
        $fnApply = function($values) use ($self, $fnFilter) {
            return $self->apply($values)->filter($fnFilter);
        };
        return new FnSequence($fnApply);
    }

    /**
     * @param callable $fn($key, $value)
     * @return Sequence
     */
    public function filterKeys(Closure $fn) {
        $self = $this;
        $fnApply = function($values) use ($self, $fn) {
            return $self->apply($values)->filterKeys($fn);
        };
        return new FnSequence($fnApply);
    }

    /**
     * Sets the limit on the sequence
     *
     * @param int $limit
     * @return FnSequence
     */
    public function limit($limit){
        $self = $this;
        $fnApply = function($values) use ($self, $limit) {
            return $self->apply($values)->limit($limit);
        };
        return new FnSequence($fnApply);
    }

    /**
     * Sets the offset into the sequence
     *
     * @param int $offset
     * @return FnSequence
     */
    public function offset($offset) {
        $self = $this;
        $fnApply = function($values) use ($self, $offset) {
            return $self->apply($values)->offset($offset);
        };
        return new FnSequence($fnApply);
    }

    /**
     * Inject another FnSequence
     *
     * @param FnSequence $fnSequence
     * @return FnSequence
     */
    public function inject(FnSequence $fnSequence) {
        return new FnSequence($this->append($fnSequence->to_fn()));
    }

    /**
     * END OF FnSequence
     *
     * Append a function to the end of the call chain.  The results of all earlier functions in the chain will be passed
     * to the appended function $fn when the return function is called.
     *
     * @param callable $fn($values)
     * @return callable - a function
     */
    public function append(Closure $fn) {
        $self = $this;
        return function($values) use ($self, $fn) {
            return $fn($self->apply($values));
        };
    }

    /**
     * @param callable $fnToApplyToEachElement
     * @return FnSequence
     */
    public function walk(Closure $fnToApplyToEachElement) {
        $self = $this;
        $fnApply = function($values) use ($self, $fnToApplyToEachElement) {
            return $self->apply($values)->walk($fnToApplyToEachElement);
        };
        return new FnSequence($fnApply);
    }

    /**
     * END OF FnSequence
     *
     * Finalizes a FnSequence with a reduce and a bounded initial value.
     *
     * @param $initialValue
     * @param callable $fn($current, $value, $key)
     * @return callable ($values)
     */
    public function reduceBounded($initialValue, Closure $fn) {
        $self = $this;
        $fnApply = function($values) use ($self, $fn, $initialValue) {
            return $self->apply($values)->reduce($initialValue, $fn);
        };
        return $fnApply;
    }

    /**
     * END OF FnSequence
     *
     * Finalizes a FnSequence with a reduce and a un-bound initial value.
     *
     * @param callable $fn($current, $value, $key)
     * @return callable ($init, $values)
     */
    public function reduce(Closure $fn) {
        $self = $this;
        $fnApply = function($initialValue, $values) use ($self, $fn) {
            return $self->apply($values)->reduce($initialValue, $fn);
        };
        return $fnApply;
    }

    /**
     * END OF FnSequence
     *
     * Converts the FnSequence into a callable function that will return a Sequence
     *
     * @return callable - calling the resulting function will return an array
     */
    public function to_fn() {
        $self = $this;
        return function ($values) use ($self) {
            return $self->apply($values);
        };
    }

    /**
     * END OF FnSequence
     *
     * Converts the FnSequence into a callable function that will apply all the sequence transforms and return an array.
     *
     * @return callable -- calling the resulting function will return an array
     */
    public function to_a() {
        $self = $this;
        return function ($values) use ($self) {
            return $self->apply($values)->to_a();
        };
    }

    /**
     * Applies FnSequences's function to the values and returns the resulting Sequence
     *
     * @param mixed $values
     * @return Sequence
     */
    public function apply($values) {
        $fnToApply = $this->fnToApply;
        return $fnToApply($values);
    }

    /**
     * @param callable $fnBaseMap($values) [optional] -- this the first function applied.
     * @return FnSequence
     */
    public static function make(Closure $fnBaseMap = null) {
        return new FnSequence($fnBaseMap);
    }
}