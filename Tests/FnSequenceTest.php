<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 31/03/15
 * Time: 17:56
 */

class FnSequenceTest extends PHPUnit_Framework_TestCase {

    public static $fruit = array(
        array('name'=> 'apple', 'count' => 5 ),
        array('name'=> 'orange', 'count' => 15 ),
        array('name'=> 'banana', 'count' => 25 ),
        array('name'=> 'orange', 'count' => 6 ),
        array('name'=> 'pear', 'count' => 2 ),
        array('name'=> 'apple', 'count' => 6 ),
        array('name'=> 'grape', 'count' => 53 ),
        array('name'=> 'apple', 'count' => 10 ),
    );

    public function testIdentity() {
        $values = range(0, 100);

        $fnIdentity = FnSequence::make()->to_a();
        $this->assertEquals($values, $fnIdentity($values));

        $values = self::$fruit;

        $this->assertEquals($values, $fnIdentity($values));

        $this->assertEquals(array(), $fnIdentity(array()));
    }

    public function testMap() {
        $fnMap = function($v) { return str_repeat($v['name'], $v['count']); };
        $fnSequence = FnSequence::make()->map($fnMap)->to_a();
        $this->assertEquals(Sequence::make(self::$fruit)->map($fnMap)->to_a(), $fnSequence(self::$fruit));
        $this->assertNotEquals(Sequence::make(self::$fruit)->map($fnMap)->limit(4)->to_a(), $fnSequence(self::$fruit));
    }

    public function testFilter() {
        // Only pass on the even ones.
        $fnFilter = FnGen::fnCallChain(FnGen::fnPluck('count'), function($v) { return $v % 2 == 0; });
        $fnSequence = FnSequence::make()->filter($fnFilter)->to_a();
        $this->assertEquals(Sequence::make(self::$fruit)->filter($fnFilter)->to_a(), $fnSequence(self::$fruit));
        $this->assertNotEquals(Sequence::make(self::$fruit)->to_a(), $fnSequence(self::$fruit));
    }

    public function testLimit(){
        $fnSequence = FnSequence::make()->limit(3)->to_a();
        $this->assertEquals(Sequence::make(self::$fruit)->limit(3)->to_a(), $fnSequence(self::$fruit));
        $this->assertNotEquals(Sequence::make(self::$fruit)->to_a(), $fnSequence(self::$fruit));

        $fnSequence = FnSequence::make()->limit(count(self::$fruit) + 10)->to_a();
        $this->assertEquals(self::$fruit, $fnSequence(self::$fruit));
    }

    public function testOffset() {
        $fnSequence = FnSequence::make()->offset(3)->limit(2)->to_a();
        $this->assertEquals(Sequence::make(self::$fruit)->offset(3)->limit(2)->to_a(), $fnSequence(self::$fruit));
        $this->assertNotEquals(Sequence::make(self::$fruit)->to_a(), $fnSequence(self::$fruit));
    }

    public function testWalk() {
        $sum = 0;

        // use walk to sum the values.
        $fnSequence = FnSequence::make()->walk(function($v) use (&$sum) { $sum += $v['count'];})->to_fn();

        $fnSequence(self::$fruit);

        $this->assertEquals(Sequence::make(self::$fruit)->reduce(0, FnGen::fnSum(FnGen::fnPluck('count'))), $sum);
        $this->assertNotEquals(0, $sum);
    }

    public function testReduceBounded() {
        $fnSum = function($base, $value) { return $base + $value; };
        $fnReduce = FnSequence::make()->map(FnGen::fnPluck('count'))->reduceBounded(0, $fnSum);

        $this->assertEquals(Sequence::make(self::$fruit)->map(FnGen::fnPluck('count'))->reduce(0, $fnSum), $fnReduce(self::$fruit));

        // Let's try and count the number of apples
        $fnAppleCounter = FnSequence::make()
            ->filter(FnGen::fnCallChain(FnGen::fnPluck('name'), FnGen::fnIsEqual('apple'))) // filter out everything but apples
            ->map(FnGen::fnPluck('count')) // Extract the count
            ->reduceBounded(0, FnGen::fnSum());

        $this->assertEquals(21, $fnAppleCounter(self::$fruit));
    }

    public function testReduce() {
        $fnSum = function($base, $value) { return $base + $value; };
        $fnReduce = FnSequence::make()->map(FnGen::fnPluck('count'))->reduce($fnSum);

        $this->assertEquals(Sequence::make(self::$fruit)->map(FnGen::fnPluck('count'))->reduce(0, $fnSum), $fnReduce(0, self::$fruit));
    }

    public function testInject() {
        $fsFilterApples = FnSequence::make()->filter(FnGen::fnCallChain(FnGen::fnPluck('name'), FnGen::fnIsEqual('apple')));
        $fsFilterBananas = FnSequence::make()->filter(FnGen::fnCallChain(FnGen::fnPluck('name'), FnGen::fnIsEqual('banana')));
        $fsExtractCounts = FnSequence::make()->map(FnGen::fnPluck('count'));
        $fnSum = FnSequence::make()->reduceBounded(0, FnGen::fnSum());

        $fnAppleCounter = FnSequence::make()->inject($fsFilterApples)->inject($fsExtractCounts)->append($fnSum);
        $fnBananaCounter = FnSequence::make()->inject($fsFilterBananas)->inject($fsExtractCounts)->append($fnSum);

        $this->assertEquals(21, $fnAppleCounter(self::$fruit));
        $this->assertEquals(25, $fnBananaCounter(self::$fruit));
    }

    public function test_to_fn() {
        $field = 'name';

        // Make a function that will pluck the names from the set of fruit.
        $fnMap = FnSequence::make()->map(FnGen::fnPluck($field))->to_fn();

        $this->assertEquals(Sequence::make(self::$fruit)->map(FnGen::fnPluck($field))->to_a(), Sequence::make($fnMap(self::$fruit))->to_a());

        // Nest them.
        $fnLimit = FnSequence::make($fnMap)->limit(1)->to_fn();
        $this->assertEquals(Sequence::make(self::$fruit)->map(FnGen::fnPluck($field))->limit(1)->to_a(), Sequence::make($fnLimit(self::$fruit))->to_a());

        // Test the Not equal to make sure we are not just getting back a bunch of nulls.
        $this->assertNotEquals(Sequence::make(self::$fruit)->map(FnGen::fnPluck($field))->limit(2)->to_a(), Sequence::make($fnLimit(self::$fruit))->to_a());
    }
}