<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 20/03/15
 * Time: 11:53
 */

interface IterationFunctions {
    public function map(Closure $fnValueMap, Closure $fnKeyMap = null);
    public function filter(Closure $fn);
    public function reduce($init, Closure $fn);
    public function to_a();
    public function keys();
    public function values();
    public function walk(Closure $fn);
}