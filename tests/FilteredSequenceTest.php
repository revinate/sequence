<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 23/03/15
 * Time: 13:00
 */

class FilteredSequenceTest extends PHPUnit_Framework_TestCase {


    /**
     * These functions are to make sure the basic IterationTraits are passed through.
     */
    public function testIterationTraits() {
        $values = range(1,100,1);

        $fn = function($v){ return $v % 2; };

        $filteredValues = Sequence::make($values)->filter($fn)->to_a();

        $this->assertTrue(Sequence::make($values)->filter($fn)->to_a() == Sequence::make($filteredValues)->to_a());


        // Test map
        $fnMap = function($v) { return $v * 2; };
        $this->assertTrue(Sequence::make($values)->filter($fn)->map($fnMap)->to_a() == Sequence::make($filteredValues)->map($fnMap)->to_a());

        // Test Filter
        $fnFilter = function($v) { return ($v-1) % 4; };
        $this->assertTrue(Sequence::make($values)->filter($fn)->filter($fnFilter)->to_a() == Sequence::make($filteredValues)->filter($fnFilter)->to_a());

        // Test Values
        $this->assertTrue(Sequence::make($values)->filter($fn)->values()->to_a() == Sequence::make($filteredValues)->values()->to_a());

        // Test Walk and reduce
        $sumWalk = 0;
        $fnWalk = function($v) use (&$sumWalk) { $sumWalk += $v; };
        Sequence::make($values)->filter($fn)->walk($fnWalk);

        $fnReduce = function($sum, $v) { return $sum + $v; };
        $this->assertTrue($sumWalk == Sequence::make($filteredValues)->reduce(0, $fnReduce));
        $this->assertTrue($sumWalk == Sequence::make($values)->filter($fn)->reduce(0, $fnReduce));

        // Test Keys
        $this->assertTrue(Sequence::make($values)->filter($fn)->keys()->to_a() == Sequence::make($filteredValues)->keys()->to_a());
    }

    public function testInterviewQuestion() {
        $limit = 100;
        $values = range(0, $limit);
        $a = 3;
        $b = 5;

        $filteredValues = Sequence::make($values)->filter(function($v) use ($a, $b) { return ($v % $a == 0) || ($v % $b == 0); } )->to_a();
        $this->assertArrayHasKey($a, $filteredValues);
        $this->assertArrayHasKey($b, $filteredValues);
        $this->assertArrayNotHasKey($a * $b + 1, $filteredValues);

        $valuesOnly = array_values($filteredValues);

        $subsetA = range(0, $limit, $a);
        $subsetB = range(0, $limit, $b);

        $this->assertTrue($subsetA == array_values(array_intersect($valuesOnly, $subsetA)));
        $this->assertTrue($subsetB == array_values(array_intersect($valuesOnly, $subsetB)));
        $this->assertTrue(! count(array_diff($valuesOnly, $subsetA, $subsetB)));
    }
}