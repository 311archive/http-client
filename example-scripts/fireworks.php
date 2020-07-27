#!/usr/bin/env php
<?php

/**
 * Example script to fetch 311 reports about fireworks and write the data to a CSV file.
 */

require_once 'vendor/autoload.php';

use Balsama\Client;

$client = new Client();

$client->filterDescription(['fireworks', 'fire works']);
$client->dateFilterAfterDate('Jan 2015');
$client->dateFilterBeforeDate('Jan 2021');

$client->groupResultsByYear();

$client->fetchReports();
$reports = $client->getReports();

\Balsama\Helpers::convertDateValArrayToCsv($reports, 'fireworks.csv');
