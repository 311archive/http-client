#!/usr/bin/env php
<?php

/**
 * Example script to fetch 311 reports about fireworks and write the data to a CSV file.
 *
 * @see data results in this spreadsheet:
 *   https://docs.google.com/spreadsheets/d/1PsDT169ZKQfYN3sxNZtBHLG6zsUDFD9mwjDXkheCnOI/edit#gid=0
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
