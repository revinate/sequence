<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 29/06/15
 * Time: 15:37
 */

namespace Revinate\SequenceBundle\Lib;


class FnStringTest extends \PHPUnit_Framework_TestCase {

    public function testFnTrim() {
        $fn = FnString::fnTrim();
        $this->assertEquals('test', $fn('  test '));
        $array = array(' 352', '354 ', '333', ' 12 34 ', "\n  Cool Stuff  \n", "\r", "CRLF\r\n");
        $expectedTrimmedArray = array('352', '354', '333', '12 34', 'Cool Stuff', '', 'CRLF');
        $this->assertNotEquals($array, $expectedTrimmedArray);
        $trimmedArray = Sequence::make($array)
            ->map(FnString::fnTrim())
            ->to_a();
        $this->assertEquals($trimmedArray, $expectedTrimmedArray);
    }
}
