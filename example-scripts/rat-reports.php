#!/usr/bin/env php
<?php

/**
 * Example script to count the number of reports in a single category and print the results to a CSV file.
 * Is it getting better or worse!? Use this to find out!!! 😅 (answer, probably better honestly)
 *
 * @see data in this spreadsheet:
 *   https://docs.google.com/spreadsheets/d/13Akm7TLhVVvJucfSFLr65VTilqzBNr1eO-EXoKqRNTA/edit#gid=923115819
 */

require_once 'vendor/autoload.php';

use Balsama\Client;
use Balsama\Helpers;

$foo = 21;
$client = new Client();
$category = 'Rodent Sighting';
$filename = str_replace(' ', '-', $category . '-' . date('c', time())). '.csv';

$client->filterCategory($category);
$client->groupResultsByMonth();
$client->fetchReports();
$reports = $client->getReports();

Helpers::convertDateValArrayToCsv($reports, $filename);
