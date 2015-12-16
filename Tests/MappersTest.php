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

    public function testFnCallGetterFunction() {
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

    public function testFnPluck() {
        $srcOf1DArrays = array(
            array('id'=>1),
            array('id'=>3),
            array('id'=>5)
        );
        $srcOf2DArrays = array(
            array('inner'=>array('id'=>21)),
            array('inner'=>array('id'=>23)),
            array('inner'=>array('id'=>25))
        );
        $srcOf1DObjects = array(
            new MappersTest_sampleObject(1),
            new MappersTest_sampleObject(3),
            new MappersTest_sampleObject(5)
        );
        $srcOf2DObjects = array(
            new MappersTest_sampleObject(new MappersTest_sampleObject(21)),
            new MappersTest_sampleObject(new MappersTest_sampleObject(23)),
            new MappersTest_sampleObject(new MappersTest_sampleObject(25))
        );
        $this->assertEquals(
            array(1, 3, 5),
            Sequence::make($srcOf1DArrays)
                ->map(fn\fnPluck('id'))
                ->to_a()
        );
        $this->assertEquals(
            array(21, 23, 25),
            Sequence::make($srcOf2DArrays)
                ->map(fn\fnPluck(array('inner', 'id')))
                ->to_a()
        );
        $this->assertEquals(
            array(22, 22, 22),
            Sequence::make($srcOf2DArrays)
                ->map(fn\fnPluck(array('inner', 'id', 'nonExistantKey'), 22))
                ->to_a()
        );
        $this->assertEquals(
            array(1, 3, 5),
            Sequence::make($srcOf1DObjects)
                ->map(fn\fnPluck('value'))
                ->to_a()
        );
        $this->assertEquals(
            array(21, 23, 25),
            Sequence::make($srcOf2DObjects)
                ->map(fn\fnPluck(array('value', 'value')))
                ->to_a()
        );
    }
}

class MappersTest_sampleObject{
    public $value;
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