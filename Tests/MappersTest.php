<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 17/10/15
 * Time: 22:12
 */

namespace Revinate\Sequence;

use Revinate\Sequence\fn as fn;


class MappersTest extends \PHPUnit_Framework_TestCase {


    public function testFnPairFunctions() {
        $values = range(10,20);
        $keys = range(20,30);
        $combined = array_combine($keys, $values);
        $fnPair = fn\fnPair();
        $fnPairKey = fn\fnPairKey();
        $fnPairValue = fn\fnPairValue();
        foreach ($combined as $key => $value) {
            $pair = $fnPair($value, $key);
            $this->assertEquals(array($key, $value), $pair);
            $this->assertEquals($key, $fnPairKey($pair));
            $this->assertEquals($value, $fnPairValue($pair));
        }
    }

}
