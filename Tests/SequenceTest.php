<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 20/03/15
 * Time: 14:01
 */

namespace Revinate\SequenceBundle\Lib;

use \ArrayObject;
use \PHPUnit_Framework_TestCase;
use Revinate\SequenceBundle\Lib\FancyArray;

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
        $values = TestData::$people;

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

        // Test that they can be sorted by age.
        $this->assertEquals(
            array('Sam', 'Terry', 'Bob', 'Robert'),
            Sequence::make(TestData::$people)->limit(4)->sort(FnGen::fnPluck('age'))->pluck('name')->to_a());
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
        $values = TestData::$fruit;

        $fnTest = FnGen::fnCallChain(FnGen::fnPluck('count'), FnGen::fnIsEqual(6));

        $this->assertEquals(FancyArray::make($values)->first($fnTest), Sequence::make($values)->first($fnTest));
        $this->assertEquals($values[6], Sequence::make($values)->first(FnGen::fnCallChain(FnGen::fnPluck('name'), FnGen::fnIsEqual('grape'))));

        // Test it without a function, it should return the first value in the list.
        $this->assertEquals($values[0], Sequence::make($values)->first());
    }


    public function testFirstKey() {
        $values = TestData::$fruit;

        $fnTest = FnGen::fnCallChain(FnGen::fnPluck('count'), FnGen::fnIsEqual(6));

        $this->assertEquals(FancyArray::make($values)->first_key($fnTest), Sequence::make($values)->firstKey($fnTest));
        $this->assertEquals(6, Sequence::make($values)->firstKey(FnGen::fnCallChain(FnGen::fnPluck('name'), FnGen::fnIsEqual('grape'))));

        // Test it without a function, it should return the first value in the list.
        $this->assertEquals(0, Sequence::make($values)->firstKey());

        $fnTest = FnGen::fnCallChain(FnGen::fnPluck('name'), FnGen::fnIsEqual('grape'));
        $this->assertEquals(FancyArray::make($values)->first_key($fnTest), Sequence::make($values)->firstKey($fnTest));

        $this->assertEquals('grape', Sequence::make($values)->keyBy(FnGen::fnPluck('name'))->firstKey($fnTest));

    }

    public function testMake() {
        $values = TestData::$fruit[0];

        $this->assertNotEmpty(Sequence::make($values)->to_a());
        $this->assertNotEmpty(Sequence::make((object)$values)->to_a());
        $this->assertNotEmpty(Sequence::make(new ArrayObject($values))->to_a());
        $this->assertEquals(Sequence::make($values)->to_a(), Sequence::make((object)$values)->to_a());
        $this->assertEquals(Sequence::make($values)->to_a(), Sequence::make(new ArrayObject($values))->to_a());
        $this->assertEquals(Sequence::make($values)->to_a(), Sequence::make(new ArrayObject((object)$values))->to_a());
    }


    public function testFlattenOnce() {
        $values = range(1,5);
        $flattened = Sequence::make($values)->flattenOnce()->to_a();
        $this->assertEquals($values, $flattened);
        $this->assertEquals(FancyArray::make($values)->flatten_once()->to_a(), $flattened);

        $values = TestData::$fruit;
        $flattened = Sequence::make($values)->flattenOnce()->to_a();
        $this->assertEquals(FancyArray::make($values)->flatten_once()->to_a(), $flattened);

        $values = array(
            TestData::$fruit,
            TestData::$fruit,
            TestData::$fruit,
            TestData::$fruit,
            TestData::$fruit,
            array('tropical'=>20, 'exotic'=>1),
            array('tropical'=>10, 'exotic'=>3),
        );
        $flattened = Sequence::make($values)->flattenOnce()->to_a();
        $this->assertEquals(FancyArray::make($values)->flatten_once()->to_a(), $flattened);

        $a = Sequence::make($values)->flattenOnceNow()->to_a();
        $this->assertEquals($a, $flattened);

        //if used with values or re-keying, then the flattenOnce and flattenOnceNow behaviour will differ
        $values = array(
            array('tropical' => 20, 'exotic' => 1),
            array('tropical' => 10, 'exotic' => 3),
        );
        $flattened = Sequence::make($values)->flattenOnce()->values()->to_a();
        $a = Sequence::make($values)->flattenOnceNow()->values()->to_a();

        $this->assertEquals(array(10,3), $a);
        $this->assertEquals(array(20,1,10,3), $flattened);
        $this->assertNotEquals($a, $flattened);

        $values1 = array(
            TestData::$fruit,
            TestData::$fruit,
        );
        $values2 = array(
            Sequence::make(TestData::$fruit),
            TestData::$fruit,
        );
        $flattened1 = Sequence::make($values1)->flattenOnce()->to_a();
        $flattened2 = Sequence::make($values2)->flattenOnce()->to_a();
        $this->assertEquals($flattened1, $flattened2);

        $this->assertEquals(array(), Sequence::make(null)->flattenOnce()->to_a());
    }

    public function testPluck() {
        $this->assertEquals(Sequence::make(TestData::$fruit)->map(FnGen::fnPluck('name'))->to_a(), Sequence::make(TestData::$fruit)->pluck('name')->to_a());
        $this->assertEquals(Sequence::make(TestData::$fruit)->map(FnGen::fnPluck('count'))->to_a(), Sequence::make(TestData::$fruit)->pluck('count')->to_a());
        $this->assertEquals(Sequence::make(TestData::$fruit)->map(FnGen::fnPluck('missing'))->to_a(), Sequence::make(TestData::$fruit)->pluck('missing')->to_a());
        $this->assertEquals(Sequence::make(TestData::$fruit)->map(FnGen::fnPluck('missing', 'hello'))->to_a(), Sequence::make(TestData::$fruit)->pluck('missing', 'hello')->to_a());
        $this->assertEquals(Sequence::make(TestData::$fruit)->map(FnGen::fnPluck('name','left'))->to_a(), Sequence::make(TestData::$fruit)->pluck('name','right')->to_a());
        $this->assertNotEquals(Sequence::make(TestData::$fruit)->map(FnGen::fnPluck('missing', 'hello'))->to_a(), Sequence::make(TestData::$fruit)->pluck('missing', 'bye')->to_a());
        $this->assertNotEquals(Sequence::make(TestData::$fruit)->map(FnGen::fnPluck('missing', 'hello'))->to_a(), Sequence::make(TestData::$fruit)->pluck('missing')->to_a());
        $this->assertNotEquals(Sequence::make(TestData::$fruit)->map(FnGen::fnPluck('name'))->to_a(), Sequence::make(TestData::$people)->pluck('name')->to_a());
    }

    public function testFlatten() {
        $values = array(
            TestData::$fruit,
            TestData::$fruit,
            TestData::$fruit,
            TestData::$fruit,
            TestData::$fruit,
            array('tropical'=>20, 'exotic'=>1),
            array('tropical'=>10, 'exotic'=>3),
        );

        // Should be the same for a single depth.
        $this->assertEquals(Sequence::make(TestData::$fruit)->flattenOnce()->to_a(), Sequence::make(TestData::$fruit)->flatten()->to_a());

        $flattened = Sequence::make($values)->flatten()->to_a();
        $this->assertCount(4, $flattened);
        $this->assertEquals(10, $flattened['tropical']);

        $data = TestData::$hotel;

        $seq = Sequence::make($data)->flatten(0)->to_a();
        $expectedResult = $data;
        $this->assertEquals($expectedResult, $seq);

        $seq = Sequence::make($data)->flatten(1)->to_a();
        $expectedResult = array(
            'id' => 1,
            'name' => 'Fancy Hotel',
            'rooms' => 200,
            'survey' => array(
                'qcount' => 3,
                'questions' => array(
                    'qid' => 1,
                    'Dont you like my hotel?',
                    'Why not?',
                    'Where would you rather go?'
                )
            ),
            'ranking' => 5
        );
        $this->assertEquals($expectedResult, $seq);

        $seq = Sequence::make($data)->flatten(2)->to_a();
        $expectedResult = array(
            'id' => 1,
            'name' => 'Fancy Hotel',
            'rooms' => 200,
            'qcount' => 3,
            'questions' => array(
                'qid' => 1,
                'Dont you like my hotel?',
                'Why not?',
                'Where would you rather go?'
            ),
            'ranking' => 5
        );
        $this->assertEquals($expectedResult, $seq);

        $fullyFlattened = array(
            'id' => 1,
            'name' => 'Fancy Hotel',
            'rooms' => 200,
            'qcount' => 3,
            'qid' => 1,
            '0' => 'Dont you like my hotel?',
            '1' => 'Why not?',
            '2' => 'Where would you rather go?',
            'ranking' => 5
        );

        $seq = Sequence::make($data)->flatten(3)->to_a();
        $this->assertEquals($fullyFlattened, $seq);

        $seq = Sequence::make($data)->flatten(4)->to_a();
        $this->assertEquals($fullyFlattened, $seq);

        $seq = Sequence::make($data)->flatten()->to_a();
        $this->assertEquals($fullyFlattened, $seq);

    }

    public function testTraverse() {
        $data = TestData::$hotel;

        $seq = Sequence::make($data)->traverse(0)->to_a();
        $expectedResult = $data;
        $this->assertEquals($expectedResult, $seq);

        $seq = Sequence::make($data)->traverse(1)->to_a();
        $expectedResult = array(
            'hotel.id' => 1,
            'hotel.name' => 'Fancy Hotel',
            'hotel.rooms' => 200,
            'hotel.survey' => array(
                'qcount' => 3,
                'questions' => array(
                    'qid' => 1,
                    'Dont you like my hotel?',
                    'Why not?',
                    'Where would you rather go?'
                )
            ),
            'hotel.ranking' => 5
        );
        $this->assertEquals($expectedResult, $seq);

        $seq = Sequence::make($data)->traverse(2)->to_a();
        $expectedResult = array(
            'hotel.id' => 1,
            'hotel.name' => 'Fancy Hotel',
            'hotel.rooms' => 200,
            'hotel.survey.qcount' => 3,
            'hotel.survey.questions' => array(
                'qid' => 1,
                'Dont you like my hotel?',
                'Why not?',
                'Where would you rather go?'
            ),
            'hotel.ranking' => 5
        );
        $this->assertEquals($expectedResult, $seq);

        $fullyTraversed = array(
            'hotel.id' => 1,
            'hotel.name' => 'Fancy Hotel',
            'hotel.rooms' => 200,
            'hotel.survey.qcount' => 3,
            'hotel.survey.questions.qid' => 1,
            'hotel.survey.questions.0' => 'Dont you like my hotel?',
            'hotel.survey.questions.1' => 'Why not?',
            'hotel.survey.questions.2' => 'Where would you rather go?',
            'hotel.ranking' => 5
        );
        $seq = Sequence::make($data)->traverse(3)->to_a();
        $this->assertEquals($fullyTraversed, $seq);

        $seq = Sequence::make($data)->traverse(4)->to_a();
        $this->assertEquals($fullyTraversed, $seq);

        $seq = Sequence::make($data)->traverse()->to_a();
        $this->assertEquals($fullyTraversed, $seq);
    }

    public function testGroupBy() {
        $fruitOrders = TestData::$fruit;

        $fnExtractFruitCountsForName = function($fruit, $name) {
            return Sequence::make($fruit)
                ->filter(FnGen::fnCallChain(FnGen::fnPluck('name'), FnGen::fnIsEqual($name)))
                ->values()
                ->to_a();
        };

        $fruitByName = Sequence::make($fruitOrders)->groupBy(FnGen::fnPluck('name'))->to_a();

        foreach ($fruitByName as $name => $orders) {
            $filteredOrders = $fnExtractFruitCountsForName($fruitOrders, $name);
            $this->assertEquals($filteredOrders, $orders);
        }

    }

}
