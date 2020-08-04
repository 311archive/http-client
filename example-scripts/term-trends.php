#!/usr/bin/env php
<?php

/**
 * Example script to count the number of reports per day that include the terms mask, social, or distance, and write the
 * results to a CSV file.
 *
 * @see data in this spreadsheet:
 *   https://docs.google.com/spreadsheets/d/1r-HHo1RZSaw3h4HckbmNWemAPolnX1lyFibUWd6vnq4/edit#gid=0
 */

require_once 'vendor/autoload.php';

use Balsama\Client;
use Balsama\Helpers;

$terms = [
    'mask',
    'distanc',
    'social',
];


$termsCounts = [];
foreach ($terms as $term) {
    $client = new Client();

    // Filter for reports that contain the term...
    $client->filterDescription([$term]);

    // ...that were submitted after or on 1 February 2020.
    $client->dateFilterAfterDate('February 1st 2020');

    // Group the reports into an array keyed by the day the reports were submitted.
    $client->groupResultsByDay();

    // Fetch the reports from the API (this can take a long time) and store them locally as $reports.
    $client->fetchReports();
    $reports = $client->getReports();

    // Process the reports so that they have keys for all days, even those with no reports...
    $reports = Helpers::fillMissingDateArrayKeys($reports);
    // ...and store the number of total reports as the value for each day key (rather than the reports themselves).
    $reports = Helpers::arrayValuesToCounts($reports);

    $termsCounts[$term] = $reports;
}

// Right now, the $termCounts arrays have results from within the same timeframe, but the exact starts and end dates
// might be different. The `fillLowerLevelDates()` method makes sure that all the arrays start and end on the same date
// by filling in missing ones with zero values.
$termsCounts = Helpers::fillLowerLevelDates($termsCounts);

// Now the containing arrays all start and end at the same date, but each term has its own array. The
//`flattenMultitermDateCount()` method will collapse those into an array of values per day.
$termsCounts = Helpers::flattenMultitermDateCount($termsCounts);

// Add the word 'date' to the term names so we can use the results as the headers for the CSV file.
array_unshift($terms, 'date');

// Write the data to a CSV file.
Helpers::csv($terms, $termsCounts, 'term-counts.csv');
