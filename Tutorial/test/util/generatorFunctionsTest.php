<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 06/09/15
 * Time: 23:40
 */

namespace Revinate\Sequence\Tutorial\util;

use Revinate\Sequence\Sequence;
use Revinate\Sequence\Tutorial\SampleDataLoader;

if (phpversion() >= '5.5') {
    require_once __DIR__.'/../../util/generatorFunctions.php';
}

class generatorFunctionsTest extends \PHPUnit_Framework_TestCase {

    public function testFileToIterator() {
        if (phpversion() < '5.5') {
            $this->assertTrue(true);
            return;
        }

        $handle = SampleDataLoader::getEmployeesCsvStream();

        $expected = SampleDataLoader::getEmployeesCsv();

        $fromIterator = Sequence::make(fileToIterator($handle))
            ->reduce('', function($content, $line){
                return $content . $line;
            });

        $this->assertEquals($expected, $fromIterator);
    }
}
