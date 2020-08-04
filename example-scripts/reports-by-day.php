#!/usr/bin/env php
<?php

/**
 * Example script to count the number of 311 submission per day.
 * This takes a looooong time to run.
 *
 * @see the data in this spreadsheet:
 *   https://docs.google.com/spreadsheets/d/1jPRc6Z4FfuQ20fy0SDHYZdDznPORzIerMRMCfni4cMg/edit?usp=sharing
 */

require_once 'vendor/autoload.php';

use Balsama\Client;
use Balsama\Helpers;

$start = $month = strtotime('2015-01-01');
$end = strtotime('2020-08-01');
$filename = 'reports-by-day-ALL.csv';

Helpers::csv(['day', 'number of reports'], [], $filename);

while ($month < $end) {
    $startTime = time();
    echo 'Starting month ' . date('F Y', $month);
    $client = new Client();

    $client->groupResultsByDay();

    $client->dateFilterAfterDate(date('F Y', $month));
    $month = strtotime("+1 month", $month);
    $client->dateFilterBeforeDate(date('F Y', $month));

    $client->fetchReports();
    $reports = $client->getReports();

    $reports = Helpers::arrayValuesToCounts($reports);
    $reports = Helpers::fillMissingDateArrayKeys($reports);
    $reports = Helpers::includeArrayKeysInArray($reports);

    Helpers::csv([], $reports, $filename, true);

    echo ". Took " . (time() - $startTime) . " seconds.", PHP_EOL;
}
