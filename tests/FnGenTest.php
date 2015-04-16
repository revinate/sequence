<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 23/03/15
 * Time: 12:16
 */

class FnGenTest extends PHPUnit_Framework_TestCase {

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

        $data = array(
            array('value'=>1),
            array('value'=>2),
            array('value'=>3),
            array('name'=>'PHP'),
            array('value'=>4),
        );
        $this->assertEquals(
            array(1, 2, 3, 'Not Found', 4),
            Sequence::make($data)->map(FnGen::fnPluck('value', 'Not Found'))->to_a()
        );
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

    public function testFnSum() {
        $range = range(0, 100);
        $this->assertEquals(array_sum($range), Sequence::make($range)->reduce(0, FnGen::fnSum()));

        $fruit = array(
            array('name'=>'apple',  'count'=>1),
            array('name'=>'orange', 'count'=>5),
            array('name'=>'apple',  'count'=>3),
            array('name'=>'banana', 'count'=>9),
        );

        $this->assertEquals(18,
            Sequence::make($fruit)
            ->reduce(0, FnGen::fnSum(
                FnGen::fnPluck('count', 0)
            )));
    }

    public function testFnAvg() {
        $range = range(1, 10);
        $this->assertEquals(5.5, Sequence::make($range)->reduce(0, FnGen::fnAvg()));

        $fruit = array(
            array('name'=>'apple',  'count'=>1),
            array('name'=>'orange', 'count'=>5),
            array('name'=>'apple',  'count'=>3),
            array('name'=>'banana', 'count'=>9),
            array('name'=>'Out of Stock'),
            array('name'=>'orange', 'count'=>5),
        );

        $counts = Sequence::make($fruit)->map(FnGen::fnPluck('count'))->filter(FnGen::fnKeepIsSet())->to_a();
        $avg = array_sum($counts) / count($counts);

        $avg2 = Sequence::make($fruit)
            ->reduce(0, FnGen::fnAvg(
                FnGen::fnPluck('count')
            ));

        $this->assertEquals($avg, $avg2);
    }

    public function testFnNestedMap() {
        $fnMap = function ($v) { $v['mul'] = strlen($v['name']) * $v['count']; return $v; };
        $fnMap2 = function ($v) { $v['mul'] = -strlen($v['name']) * $v['count']; return $v; };

        $fruitBasket = array(
            self::$fruit,
            self::$fruit,
            self::$fruit,
            self::$fruit,
            self::$fruit,
        );

        $n1 = Sequence::make($fruitBasket)->map(FnGen::fnNestedMap($fnMap))->to_a();
        $n2 = Sequence::make($fruitBasket)->map(FnSequence::make()->map($fnMap)->to_a())->to_a();
        $n3 = Sequence::make($fruitBasket)
            ->map(function ($values) use ($fnMap) {
                return Sequence::make($values)->map($fnMap)->to_a();
            })
            ->to_a();
        $x1 = Sequence::make($fruitBasket)->map(FnGen::fnNestedMap($fnMap2))->to_a();

        $this->assertEquals($n3, $n1);
        $this->assertEquals($n3, $n2);
        $this->assertNotEquals($n1, $x1);
    }

    public function testFnEqual() {
        $fn = FnGen::fnIsEqual(0);

        $this->assertTrue($fn(0));
        $this->assertTrue($fn('0'));
        $this->assertTrue($fn(false));
        $this->assertTrue($fn(null));
        $this->assertTrue($fn('hello'));  // <-- sad truth about PHP.
        $this->assertTrue($fn(0.0));

        $this->assertFalse($fn(1));
        $this->assertFalse($fn(true));
        $this->assertFalse($fn('100'));

        $fn = FnGen::fnIsEqual('hello');
        $this->assertTrue($fn('hello'));
        $this->assertTrue($fn(true));   // <-- also true.
        $this->assertTrue($fn(0));      // <-- sad truth about PHP.
        $this->assertTrue($fn(0.0));    // <-- again, sadly this is true

        $this->assertFalse($fn('Hello'));
        $this->assertFalse($fn('0'));
        $this->assertFalse($fn(null));
        $this->assertFalse($fn(false));

        $fn = FnGen::fnIsEqual('0');  // Making it a string changes everything.
        $this->assertTrue($fn(0));
        $this->assertTrue($fn('0'));
        $this->assertTrue($fn(false));
        $this->assertTrue($fn(0.0));

        $this->assertFalse($fn(null));
        $this->assertFalse($fn('hello'));
        $this->assertFalse($fn(1));
        $this->assertFalse($fn(true));
        $this->assertFalse($fn('100'));

        $fn1 = FnGen::fnIsEqual(0);
        $fn2 = FnGen::fnIsEqual(1);
        $this->assertEquals($fn1, $fn2);  // <-- closure function == closure function --- should not be relied on.
    }


    public function testFnNotEqual() {
        $fn = FnGen::fnIsNotEqual(0);

        $this->assertFalse($fn(0));
        $this->assertFalse($fn('0'));
        $this->assertFalse($fn(false));
        $this->assertFalse($fn(null));
        $this->assertFalse($fn('hello'));  // <-- sad truth about PHP.
        $this->assertFalse($fn(0.0));

        $this->assertTrue($fn(1));
        $this->assertTrue($fn(true));
        $this->assertTrue($fn('100'));

        $fn = FnGen::fnIsNotEqual('hello');
        $this->assertFalse($fn('hello'));
        $this->assertFalse($fn(true));   // <-- also true.
        $this->assertFalse($fn(0));      // <-- sad truth about PHP.
        $this->assertFalse($fn(0.0));    // <-- again, sadly this is true

        $this->assertTrue($fn('Hello'));
        $this->assertTrue($fn('0'));
        $this->assertTrue($fn(null));
        $this->assertTrue($fn(false));

        $fn1 = FnGen::fnIsNotEqual(0);
        $fn2 = FnGen::fnIsEqual(1);
        $this->assertEquals($fn1, $fn2);  // <-- closure function == closure function --- should not be relied on.
    }

    public function testFnEqualEqual() {
        $fn = FnGen::fnIsEqualEqual(0);

        $this->assertTrue($fn(0));

        $this->assertFalse($fn('0'));
        $this->assertFalse($fn(false));
        $this->assertFalse($fn(null));
        $this->assertFalse($fn('hello'));
        $this->assertFalse($fn(0.0));
        $this->assertFalse($fn(1));
        $this->assertFalse($fn(true));
        $this->assertFalse($fn('100'));

        $fn = FnGen::fnIsEqualEqual('hello');
        $this->assertTrue($fn('hello'));

        $this->assertFalse($fn(true));
        $this->assertFalse($fn(0));
        $this->assertFalse($fn(0.0));
        $this->assertFalse($fn('Hello'));
        $this->assertFalse($fn('0'));
        $this->assertFalse($fn(null));
        $this->assertFalse($fn(false));
    }

    public function testFnNotEqualEqual() {
        $fn = FnGen::fnIsNotEqualEqual(0);

        $this->assertFalse($fn(0));

        $this->assertTrue($fn('0'));
        $this->assertTrue($fn(false));
        $this->assertTrue($fn(null));
        $this->assertTrue($fn('hello'));
        $this->assertTrue($fn(0.0));
        $this->assertTrue($fn(1));
        $this->assertTrue($fn(true));
        $this->assertTrue($fn('100'));

        $fn = FnGen::fnIsNotEqualEqual('hello');
        $this->assertFalse($fn('hello'));

        $this->assertTrue($fn(true));
        $this->assertTrue($fn(0));
        $this->assertTrue($fn(0.0));
        $this->assertTrue($fn('Hello'));
        $this->assertTrue($fn('0'));
        $this->assertTrue($fn(null));
        $this->assertTrue($fn(false));
    }



}