<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 05/09/15
 * Time: 11:19
 */

namespace Revinate\Sequence\Tutorial;

use Revinate\Sequence\fn as fn;

require_once __DIR__.'/../sequencePatterns.php';

class sequencePatternsTest extends \PHPUnit_Framework_TestCase {

    public function testExampleExtractField1() {
        $employees = SampleDataLoader::getEmployees(true);

        $employeeIds = exampleExtractField1($employees);
        $this->assertEquals(array_map(fn\fnPluck('employeeId'), $employees), $employeeIds);
    }

    public function testExampleExtractField2() {
        $employees = SampleDataLoader::getEmployees(true);

        $employeeIds = exampleExtractField2($employees);
        $this->assertEquals(array_map(fn\fnPluck('employeeId'), $employees), $employeeIds);
    }

    public function testExampleSortByEmployeeId() {
        $employees = SampleDataLoader::getEmployees(true);

        $employeesSortedByEmployeeId = exampleSortByEmployeeId($employees);

        $this->assertNotEmpty($employeesSortedByEmployeeId);
        $this->assertNotEquals($employees, $employeesSortedByEmployeeId);
        $employeeIdsById = exampleExtractField1($employeesSortedByEmployeeId);
        $employeeIds = exampleExtractField1($employees);
        $this->assertNotEquals($employeeIds, $employeeIdsById);
        sort($employeeIds);
        $this->assertEquals($employeeIds, $employeeIdsById);
    }

    public function testExampleSortByEmployeeIdRev() {
        $employees = SampleDataLoader::getEmployees(true);

        $employeesSortedByEmployeeId = exampleSortByEmployeeIdRev($employees);
        $this->assertNotEmpty($employeesSortedByEmployeeId);
        $this->assertNotEquals($employees, $employeesSortedByEmployeeId);
        $employeeIdsById = exampleExtractField1($employeesSortedByEmployeeId);
        $employeeIds = exampleExtractField1($employees);
        $this->assertNotEquals($employeeIds, $employeeIdsById);
        rsort($employeeIds);
        $this->assertEquals($employeeIds, $employeeIdsById);
    }

    public function testExampleKeyByIdSortByEmployeeLastName() {
        $employees = SampleDataLoader::getEmployees(true);

        $employeesSorted1 = exampleKeyByIdSortByEmployeeLastName1($employees);
        $employeesSorted2 = exampleKeyByIdSortByEmployeeLastName2($employees);
        $employeesSorted1Wrong = exampleKeyByIdSortByEmployeeLastName1Wrong($employees);
        $this->assertNotEquals($employees, $employeesSorted1);
        $this->assertEquals($employeesSorted1, $employeesSorted2);
        $this->assertNotEquals($employeesSorted1, $employeesSorted1Wrong);
    }


}
