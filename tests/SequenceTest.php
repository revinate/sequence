<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 20/03/15
 * Time: 14:01
 */

class SequenceTest extends PHPUnit_Framework_TestCase  {

    public function testMap() {

        $values = range(1,100);

        $fn = function($v){ return 2 * $v;};
        $results = Sequence::make($values)->map($fn)->to_a();

        $this->assertTrue($results == range(2,200,2));
        $this->assertTrue($results == FancyArray::make($values)->map($fn)->to_a());
    }

    public function testMapKeys() {
        $values = range(0,100);
        $fnKeyMap = function($k) { return $k * 2; };

        $results = Sequence::make($values)->mapKeys($fnKeyMap)->to_a();

        $this->assertTrue(array_keys($results) == range(0, 200, 2));
    }

    public function testKeyBy() {
        $values = array(
            array('name'=>'Terry', 'age'=> 22),
            array('name'=>'Bob', 'age' => 30),
            array('name'=>'Sam', 'age' => 19),
            array('name'=>'Robert', 'age' => 55),
            array('group'=>'student'),
        );

        $results = Sequence::make($values)->keyBy(FnGen::fnPluck('name'))->to_a();

        $this->assertArrayHasKey('Terry', $results);
        $this->assertTrue($results['Robert']['age'] == 55);
    }

    public function testFilter() {
        $values = range(1,100,1);

        $fn = function($v){ return $v % 2; };
        $results = Sequence::make($values)->filter($fn)->to_a();

        $this->assertTrue($results == FancyArray::make($values)->filter($fn)->to_a());
        $this->assertTrue($results == array_filter($values, $fn));
    }

    public function testFilterKeys() {
        $values = range(0,100);

        $fn = function($v){ return $v % 2; };
        $results = Sequence::make($values)->filterKeys($fn)->to_a();

        $this->assertTrue($results == FancyArray::make($values)->filter_k($fn)->to_a());
        $this->assertTrue($results == array_filter($values, $fn));
    }

    public function testChaining() {

        $fnMap1 = function($v, $k) { return $v * $k;};
        $fnMap2 = function($v) { return $v + 1;};
        $fnFilter = function($v) { return $v % 3 == 0; };

        $values = range(1,100);

        $results = Sequence::make($values)->map($fnMap1)->filter($fnFilter)->map($fnMap2)->to_a();
        $resultsFancy = FancyArray::make($values)->map($fnMap1)->filter($fnFilter)->map($fnMap2)->to_a();

        $this->assertTrue($results == $resultsFancy);
    }

    public function testTo_a() {
        $values = range(0,100,1);
        $results = Sequence::make($values)->to_a();

        $this->assertTrue($values == $results);
    }

    public function testKeys() {
        $keys = range(2,100,2);
        $values = range(1,100,2);
        $array = array_combine($keys, $values);

        $results = Sequence::make($array)->keys()->to_a();
        $this->assertTrue($results == $keys);

        $results = Sequence::make($array)->values()->keys()->to_a();
        $this->assertFalse($results == $keys);
    }

    public function testValues() {
        $keys = range(2,100,2);
        $values = range(1,100,2);
        $array = array_combine($keys, $values);

        $results = Sequence::make($array)->values()->to_a();

        $this->assertTrue($results == $values);
    }

    public function testReduce() {
        $fn = function($result, $v, $k) {
            return $result + $v;
        };

        $n = 100;

        $result = Sequence::make(range(1,$n))->reduce(0, $fn);

        $this->assertTrue($result == $n * ($n +1) / 2);
    }

    public function testWalk() {
        $sum = 0;
        $fn = function ($value) use (&$sum) { $sum += $value; };
        $fnReduceSum = function ($sum, $value) { return $sum + $value; };
        $values = range(1,100);

        Sequence::make($values)->walk($fn);

        $this->assertTrue($sum == Sequence::make($values)->reduce(0, $fnReduceSum));
    }

    public function testLimit() {
        $values = range(1, 100);

        $results = Sequence::make($values)->limit(50)->to_a();

        $this->assertTrue($results == range(1,50));
    }

    public function testOffset() {
        $values = range(1, 100);

        $results = Sequence::make($values)->offset(50)->values()->to_a();

        $this->assertTrue($results == range(51,100));
    }

    public function testNull() {
        $result = Sequence::make(null)->to_a();

        $this->assertTrue(empty($result));
    }
}
