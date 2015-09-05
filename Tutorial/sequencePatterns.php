<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 05/09/15
 * Time: 10:35
 */
namespace Revinate\Sequence\Tutorial;

require_once __DIR__.'/../vendor/autoload.php';

use Revinate\Sequence\Sequence;
use Revinate\Sequence\fn as fn;
use Revinate\Sequence\Tutorial\SampleDataLoader;


function exampleExtractField1() {
    $employees = SampleDataLoader::getEmployees(true);

    $employeeIds = Sequence::make($employees)  // make the array into a Sequence
        ->pluck('employeeId')
        ->to_a();

    print_r($employeeIds);
}