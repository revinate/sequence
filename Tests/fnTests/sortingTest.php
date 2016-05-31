<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 31/05/2016
 * Time: 11:33
 */

namespace Revinate\Sequence;

use \Revinate\Sequence\fn;


class sortingTest extends \PHPUnit_Framework_TestCase {

    public function testCompareMulti() {
        $values = TestData::$fruit;

        usort($values, fn\fnCompareMulti(array(fn\fnCompareField('name'))));

        $this->assertNotEquals(TestData::$fruit, $values);
        $this->assertEquals(
            Sequence::make(TestData::$fruit)->pluck('name')->sort()->toValues(),
            array_map(fn\fnPluck('name'), $values)
        );

        $values = array(
            array('name' => 'Terry', 'age' => 22),
            array('name' => 'Bob', 'age' => 30),
            array('name' => 'Ann', 'age' => 30),
            array('name' => 'Sam', 'age' => 19),
            array('name' => 'Rob', 'age' => 30),
            array('name' => 'Robert', 'age' => 55),
        );

        $expected =  array(
            array('name' => 'Robert', 'age' => 55),
            array('name' => 'Ann', 'age' => 30),
            array('name' => 'Bob', 'age' => 30),
            array('name' => 'Rob', 'age' => 30),
            array('name' => 'Terry', 'age' => 22),
            array('name' => 'Sam', 'age' => 19),
        );

        usort($values, fn\fnCompareMulti(array(fn\fnCompareFieldRev('age'), fn\fnCompareField('name'))));
        $this->assertEquals($expected, $values);
    }
}
