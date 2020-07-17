#!/usr/bin/env php
<?php

/**
 * Example script to fetch 311 reports about fireworks and write the data to a CSV file.
 */

require_once 'vendor/autoload.php';

use Balsama\Client;

$terms = [
    'mask',
    'distanc',
    'social',
];


$termsCounts = [];
foreach ($terms as $term) {
    $client = new Client();

    $client->filterDescription([$term]);

    $client->dateFilterAfterDate('March 2020');
    $client->dateFilterBeforeDate('April 2020');

    $client->groupResultsByDay();

    $client->fetchReports();

    $reports = $client->getReports();
    $reports = \Balsama\Helpers::fillMissingDateArrayKeys($reports);
    $reports = \Balsama\Helpers::arrayValuesToCounts($reports);

    $termsCounts[$term] = $reports;
}

$foo = 21;


