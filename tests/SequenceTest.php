<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 20/03/15
 * Time: 14:01
 */

class SequenceTest  {

    public function testMap() {

        $values = range(1,100,1);

        $results = Sequence::make($values)->map(function($v){ return 2 * $v;})->to_a();

        $x = $results;
    }

    public function testFilter() {
        $values = range(1,100,1);

        $results = Sequence::make($values)->filter(function($v){ return $v % 2; })->to_a();

        $x = $results;
    }

    public function testChaining() {

    }

    public function testTo_a() {

    }

    public function testKeys() {

    }

    public function testValues() {

    }

    public function testReduce() {

    }

}
