<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 20/03/15
 * Time: 14:01
 */

class SequenceTest extends PHPUnit_Framework_TestCase  {

    protected static $fruit = array(
        array('name'=> 'apple', 'count' => 5 ),
        array('name'=> 'orange', 'count' => 15 ),
        array('name'=> 'banana', 'count' => 25 ),
        array('name'=> 'orange', 'count' => 6 ),
        array('name'=> 'pear', 'count' => 2 ),
        array('name'=> 'apple', 'count' => 6 ),
        array('name'=> 'grape', 'count' => 53 ),
        array('name'=> 'apple', 'count' => 10 ),
    );

    protected static $people = array(
        array('name'=>'Terry', 'age'=> 22),
        array('name'=>'Bob', 'age' => 30),
        array('name'=>'Sam', 'age' => 19),
        array('name'=>'Robert', 'age' => 55),
        array('group'=>'student'),
    );

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
        $values = self::$people;

        $results = Sequence::make($values)->filter(FnGen::fnPluck('name'))->keyBy(FnGen::fnPluck('name'))->to_a();
        $results2 = FancyArray::make($values)->filter(FnGen::fnPluck('name'))->ukey_by(FnGen::fnPluck('name'))->to_a();

        $this->assertTrue($results == $results2);
        $this->assertEquals($results2, $results);

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

    public function testSort() {
        $range = range(1, 100);
        $rangeReverse = array_reverse($range);

        // Check the values are sorted.
        $result = Sequence::make($rangeReverse)->sort()->to_a();
        $this->assertEquals(range(1, 100), $result);

        // Check the keys are in numeric order
        $result = Sequence::make($rangeReverse)->sort()->keys()->to_a();
        $this->assertEquals(range(0, 99), $result);
    }

    public function testASort() {
        $range = range(1, 100);
        $rangeReverse = array_reverse($range);

        // Check the values are sorted.
        $result = Sequence::make($rangeReverse)->asort()->values()->to_a();
        $this->assertEquals(range(1, 100), $result);

        // Check the keys are preserved
        $result = Sequence::make($rangeReverse)->asort()->keys()->to_a();
        $this->assertEquals(array_reverse(range(0, 99)), $result);
    }

    public function testSortKeys() {
        $range = range(1, 100, 2);
        $rangeReverse = array_reverse($range);

        // Check the values are reversed and the keys are in the right order.
        $result = Sequence::make(array_combine($rangeReverse, $range))->sortKeys()->to_a();
        $this->assertEquals(array_combine($range, $rangeReverse), $result);
    }

    public function testFirst() {
        $values = self::$fruit;

        $fnTest = FnGen::fnCallChain(FnGen::fnPluck('count'), FnGen::fnIsEqual(6));

        $this->assertEquals(FancyArray::make($values)->first($fnTest), Sequence::make($values)->first($fnTest));
        $this->assertEquals($values[6], Sequence::make($values)->first(FnGen::fnCallChain(FnGen::fnPluck('name'), FnGen::fnIsEqual('grape'))));

        // Test it without a function, it should return the first value in the list.
        $this->assertEquals($values[0], Sequence::make($values)->first());
    }


    public function testFlattenOnce() {
        $values = range(1,5);
        $flattened = Sequence::make($values)->flattenOnce()->to_a();
        $this->assertEquals($values, $flattened);
        $this->assertEquals(FancyArray::make($values)->flatten_once()->to_a(), $flattened);

        $values = self::$fruit;
        $flattened = Sequence::make($values)->flattenOnce()->to_a();
        $this->assertEquals(FancyArray::make($values)->flatten_once()->to_a(), $flattened);

        $values = array(
            self::$fruit,
            self::$fruit,
            self::$fruit,
            self::$fruit,
            self::$fruit,
        );
        $flattened = Sequence::make($values)->flattenOnce()->to_a();
        $this->assertEquals(FancyArray::make($values)->flatten_once()->to_a(), $flattened);

        $values1 = array(
            self::$fruit,
            self::$fruit,
        );
        $values2 = array(
            Sequence::make(self::$fruit),
            self::$fruit,
        );
        $flattened1 = Sequence::make($values1)->flattenOnce()->to_a();
        $flattened2 = Sequence::make($values2)->flattenOnce()->to_a();
        $this->assertEquals($flattened1, $flattened2);

        $this->assertEquals(array(), Sequence::make(null)->flattenOnce()->to_a());
    }

}
