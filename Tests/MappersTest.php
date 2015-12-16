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

    public function testFnCallGetterFunction(){
        $src = array(
            new MappersTest_sampleObject(1),
            new MappersTest_sampleObject(3),
            new MappersTest_sampleObject(5)
        );
        $this->assertEquals(
            array(1,3,5),
            Sequence::make($src)
                ->map(fn\fnCallGetter('getValue'))
                ->to_a()
        );
        $this->assertEquals(
            array(12, 14, 16),
            Sequence::make($src)
                ->map(fn\fnCallGetter('getValuePlusSomething', null, 11))
                ->to_a()
        );
        $this->assertEquals(
            array(22, 22, 22),
            Sequence::make($src)
                ->map(fn\fnCallGetter('getterThatDoesNotExist', 22))
                ->to_a()
        );
    }
}

class MappersTest_sampleObject{
    protected $value;
    public function __construct($value) {
        $this->value = $value;
    }
    public function getValue(){
        return $this->value;
    }
    public function getValuePlusSomething($something) {
        return $this->value + $something;
    }
}