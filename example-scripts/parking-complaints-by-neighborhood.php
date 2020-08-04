#!/usr/bin/env php
<?php

/**
 * Example script to count the number of reports per neighborhood that complain about parking and write the data to a
 * CSV file.
 *
 * @see collected data and visualizations here;
 *   https://docs.google.com/spreadsheets/d/1QC-FOvrRgGRkb-_wrWM_s8x3zTKG_q1eR0M2aZTCACk/edit#gid=1770142492
 */

require_once 'vendor/autoload.php';

use Balsama\Client;
use Balsama\Helpers;

$neighborhoods = Helpers::getNeighborhoods();

$counts = [];

foreach ($neighborhoods as $neighborhood) {
    $client = new Client();

    $client->filterNeighborhood($neighborhood);
    $client->filterCategory('illegal parking');
    $client->dateFilterAfterDate('July 2020');
    $client->groupResultsByMonth();

    $client->fetchReports();
    $reports = $client->getReports();

    $reports = Helpers::fillMissingDateArrayKeys($reports);
    $reports = Helpers::arrayValuesToCounts($reports);

    $counts[$neighborhood] = $reports;

    echo "Done with $neighborhood\n";
}

$counts = array_filter($counts);
$headers = array_keys($counts);

$counts = Helpers::fillLowerLevelDates($counts);
$counts = Helpers::flattenMultitermDateCount($counts);


array_unshift($headers, 'date');

Helpers::csv($headers, $counts, 'parking-complaints-by-neighborhood--july2020-only.csv');
