<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 06/09/15
 * Time: 23:55
 */

namespace Revinate\Sequence\Tutorial\util;

use Revinate\Sequence\Sequence;
use Revinate\Sequence\Tutorial\SampleDataLoader;

class StreamReaderIteratorTest extends \PHPUnit_Framework_TestCase {

    public function testStreamReaderIterator() {
        $handle = SampleDataLoader::getEmployeesCsvStream();

        $expected = SampleDataLoader::getEmployeesCsv();

        $fromIterator = Sequence::make(new StreamReaderIterator($handle))
            ->reduce('', function($content, $line){
                return $content . $line;
            });

        $this->assertEquals($expected, $fromIterator);
    }
}
