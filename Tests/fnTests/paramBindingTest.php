<?php

namespace Revinate\Sequence;

use \PHPUnit_Framework_TestCase;
use \Revinate\Sequence\fn as fn;

class ParamBindingTest extends PHPUnit_Framework_TestCase {

    public function testFnPipe() {
        $fn = function($v) { return $v + 1; };

        $fnChain = fn\fnPipe($fn, $fn, $fn, $fn);

        $this->assertEquals(9, $fnChain(5));
    }

    public function testFnPipeWithMultipleArgumentsInFirstFunction() {
        $fnAdd = function ($a, $b) { return $a + $b; };
        $fnIncrease = function($v) { return $v + 1; };

        $fnChain = fn\fnPipe($fnAdd, $fnIncrease, $fnIncrease, $fnIncrease);

        $this->assertEquals(8, $fnChain(2, 3));
    }
}
