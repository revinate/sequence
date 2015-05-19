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

    public function testInterviewQuestionA() {
        /*
            The sum of all natural numbers below 10 that are multiples of 3 or 5 are 23 (3 + 5 + 6 + 9)
            Write a php script that will find the sum of all the multiples of 3 or 5 below 1000. The script
            should run from command line and put the result on screen. We will judge this task based on
            simplicity, efficiency and cleverness of the code.
         */
        $limit = 1000;
        $values = range(0, $limit);
        $a = 3;
        $b = 5;

        $fnFilterMaker = function($a, $b) { return function($v) use ($a, $b) { return ($v % $a == 0) || ($v % $b == 0); }; };

        // test: sum of multiples of 3 or 5 below 10 is 23 (3 + 5 + 6 + 9)
        $this->assertTrue(
            Sequence::make(range(0, 9))
                ->filter($fnFilterMaker(3, 5))
                ->reduce(0, FnGen::fnSum()) == 23);

        $filteredValues = Sequence::make($values)->filter($fnFilterMaker($a, $b))->to_a();
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