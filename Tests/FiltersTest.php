<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 30/08/15
 * Time: 22:38
 */

namespace Revinate\Sequence\fn;

use Revinate\Sequence\Test\FancyArray;
use Revinate\Sequence\TestAccessClass;

require_once 'TestAccessClass.php';
require_once 'FancyArray.php';

class FiltersTest extends \PHPUnit_Framework_TestCase {
    public function testNot() {
        $fnNot = fnNot();

        $this->assertInternalType('bool', $fnNot(true));
        $this->assertFalse($fnNot(true));
        $this->assertFalse($fnNot(1));
        $this->assertFalse($fnNot(100));
        $this->assertFalse($fnNot('Hello'));
        $this->assertTrue($fnNot(false));
        $this->assertTrue($fnNot(0));
        $this->assertTrue($fnNot(null));
        $this->assertTrue($fnNot(''));
        $this->assertTrue($fnNot('0'));
    }

    public function testFnInstanceOf() {
        $fnIsInstanceOfTestAccessClass = fnInstanceOf('\Revinate\Sequence\TestAccessClass');

        $fancyArray = new FancyArray();
        $testAccessClass = new TestAccessClass();

        $this->assertTrue($fnIsInstanceOfTestAccessClass($testAccessClass));
        $this->assertFalse($fnIsInstanceOfTestAccessClass($fancyArray));
        $this->assertFalse($fnIsInstanceOfTestAccessClass(5));
        $this->assertFalse($fnIsInstanceOfTestAccessClass('\Revinate\Sequence\TestAccessClass'));

    }


}
