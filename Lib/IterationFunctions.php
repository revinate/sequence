<?php

namespace Revinate\SequenceBundle\Lib;

use \Closure;

/**
 * Interface IterationFunctions
 * @author jasondent
 * @package Revinate\SequenceBundle\Lib
 */
interface IterationFunctions {
    public function map(Closure $fnValueMap, Closure $fnKeyMap = null);
    public function mapKeys(Closure $fnKeyMap);
    public function filter(Closure $fn);
    public function filterKeys(Closure $fn);
    public function reduce($init, Closure $fn);
    public function to_a();
    public function keys();
    public function values();
    public function limit($limit);
    public function offset($offset);
    public function walk(Closure $fn);
}