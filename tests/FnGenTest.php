<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 23/03/15
 * Time: 12:16
 */

class FnGenTest extends PHPUnit_Framework_TestCase {

    public function testFnIdentity() {
        $fn = FnGen::fnIdentity();
        $this->assertTrue($fn(99) === 99);
        $this->assertTrue($fn("hello") === "hello");
    }

    public function testPluck() {
        $fn = FnGen::fnPluck('value');
        $doc = array('value'=>42, 'question'=>'What is the meaning of life?');

        $this->assertTrue($fn($doc) == $doc['value']);
        $this->assertTrue($fn(array('no-value'=>0)) === null);
    }

    public function testFnCounter() {
        $fn = FnGen::fnCounter(0);
        $this->assertTrue($fn() == 0);
        $this->assertTrue($fn() == 1);
        $this->assertTrue($fn() == 2);
        $this->assertTrue($fn() == 3);

        $fn = FnGen::fnCounter(10);
        $this->assertTrue($fn() == 10);
        $this->assertTrue($fn() == 11);
        $this->assertTrue($fn() == 12);
        $this->assertTrue($fn() == 13);
    }

    public function testFnCallChain() {
        $fn = function($v) { return $v + 1; };

        $fnChain = FnGen::fnCallChain($fn, $fn, $fn, $fn);

        $this->assertTrue($fnChain(5) == 9);

        // More useful example:

        $values = array(
            array('name'=>'Terry', 'age'=> 22),
            array('name'=>'Bob', 'age' => 30),
            array('name'=>'Sam', 'age' => 19),
            array('name'=>'Robert', 'age' => 55),
            array('group'=>'student'),
        );
        $fnLen = function($v) { return strlen($v); };

        // Extract only the elements with with name length of 3
        $results = Sequence::make($values)
            ->filter(FnGen::fnCallChain(
                FnGen::fnPluck('name'),     // get the name field
                $fnLen,                     // get the length
                FnGen::fnIsEqual(3)         // compare to 3
            ))->to_a();

        $this->assertTrue(count($results) == 2);

        // Same thing, but without the chain.
        $resultsNonChain = Sequence::make($values)
            ->filter(function ($v) {
                if (isset($v['name'])) {
                    $x = $v['name'];
                } else {
                    $x = '';
                }
                $len = strlen($x);
                return $len == 3;
            })->to_a();

        $this->assertTrue($results == $resultsNonChain);

        // The first function in the chain is allowed multiple params
        $results = Sequence::make($values)
            ->filterKeys(FnGen::fnCallChain(
                function($k, $v) { return $v; },  // get the value -- test multiple params
                FnGen::fnPluck('name'),     // get the name field
                $fnLen,                     // get the length
                FnGen::fnIsEqual(3)         // compare to 3
            ))->to_a();

        $this->assertTrue(count($results) == 2);
    }
}