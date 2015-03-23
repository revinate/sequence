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
}