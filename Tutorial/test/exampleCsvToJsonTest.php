<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 06/09/15
 * Time: 23:58
 */

namespace Revinate\Sequence\Tutorial;

require_once __DIR__.'/../exampleCsvToJson.php';


class exampleCsvToJsonTest extends \PHPUnit_Framework_TestCase {

    public function testExampleCsvToJson() {
        $input = SampleDataLoader::getEmployeesCsvStream();
        $output = fopen('php://memory', 'r+');

        exampleCsvToJson($input, $output);

        $length = ftell($output);
        rewind($output);
        $json = fread($output, $length);

        $this->assertNotEmpty($json);

        $employeesFromJson = json_decode($json, true);
        $employees = SampleDataLoader::getEmployees(true);

        $this->assertEquals($employees, $employeesFromJson);
    }
}
