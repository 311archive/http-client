#!/usr/bin/env php
<?php

/**
 * This was a handy script I used to convert the thousands of rows of day data into months.
 */

require_once 'vendor/autoload.php';

use Balsama\Client;
use Balsama\Helpers;

$csv = file_get_contents('./data/rbd.csv');
$dates = array_map("str_getcsv", explode("\n", $csv));
array_shift($dates);
foreach ($dates as $date) {

    $newDates[date('Y-m', strtotime($date[0]))][] = $date[1];
}

foreach ($newDates as $month => $counts) {
    $monthValues[$month] = array_sum($counts);
}


$values = Helpers::includeArrayKeysInArray($monthValues);
Helpers::csv(['month', 'count'], $values, 'reports-by-month.csv');
