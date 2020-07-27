# Boston 311 PHP Client
PHP client to aid in fetching and processing Boston 311 reports from [Boston 311 Archive](https://311.report) API.

## Example usage

See the `example-scripts` folder for examples.

### Create a CSV of the number of 311 Reports about fireworks per day over the last few years.
1. Clone this repo or include it in your PHP project.
2. Instantiate a new Client:

```$php
// Instantiate a new Client.
$client = new Balsama\Client();

// Find all Reports that contain the word "fireworks" or a common alternative spelling "fire works" in the description.
$client->filterDescription(['fireworks', 'fire works']);

// Group the results by day
$client->groupResultsByDay();

// Fetch the reports and store them in `$reports`.
$client->fetchReports();
$reports = $client->getReports();

// Format the number of reports per day into a simple `date` and `count` csv suitible for graphing.
\Balsama\Helpers::convertDateValArrayToCsv($reports, 'fireworks.csv');
```

## Documentation

### class `Client`
#### Public methods

Name | Description | Parameters | Returns
-----|-------------|------------|--------
`fetchReports` | Fetches all reports from the API. | | `$this`
`getReports` | Gets al of the fetched reports. | | `array`
`getReportsFields` | Gets specific fields from the fetched Reports | | `array`
`setLimit` | Limit the number of records returned per request. Note that this does not limit the total number of records returned. It just splits them up over more pages/requests. | `@param int $limit` | $this
`limitRequests` | Limits the number of requests/pages, even if there are more results to be fetched. | `@param int $numberOfRequests` | `$this`
`setOffset` | Offset the start of the records returned by a given number. | `@param int $offset` | `$this`
`dateFilterAfterDate` | Filters reports that come before the provided "after" date. | `@param string $afterDate`: Any readable PHP date format. | `$this`
`dateFilterBeforeDate` | Filters reports that come after the provided "before" date. | `@param string $beforeDate`: Any readable PHP date format. | `$this`
`filterCategory` | Filters reports to only include reports in the given category. | `@param string $category` | `$this`
`filterNeighborhood` | Filters reports to only include reports from the given neighborhood. | `@param string $neighborhood` | `$this`
`filterDescription` | An array of strings of which at least one must be present in the Description field. | `@param string[] $description` | `$this`
`filterStatusOpen` | Filter on whether the report is open or not. True for open, False for closed. Do not use this method to return all. | `$this`
`filterStatusNotes` | An array of strings which much be present in the Status Notes field. Note: The Status Notes field is populated by the city when a report is closed. It is generally empty for open reports. As such, using this will only return closed reports. | `@param string[] $terms | `$this`
`groupResultsByDay` | Groups the results into arrays keyed by day on which they were opened. |  | `$this`
`groupResultsByWeek` | Groups the results into arrays keyed by Month in which they were opened. |  | `$this`
`groupResultsByMonth` | Groups the results into arrays keyed by Year in which they were opened. |  | `$this`

### class `Helpers`
#### Methods (all methods in the Helpers class are static)

Name | Description | Parameters | Returns
-----|-------------|------------|--------
`convertDateValArrayToCsv` | For a typical array of `[$date => $value]`'s, converts it into a CSV of the given name. | `@param array $array` | array
`includeArrayKeysInArray` | Shifts each top level key of an array of arrays into the row's contained array while preserving tol-level keys. | `@param array[] | string[] $array` | `array[]`
`csv` | Writes an array of arrays to a CSV file. | `@param string[] $headers`, `@param array[] $data`, `@param string $filename`, `@param string $path` | 
`fillMissingDateArrayKeys` | Given an array keyed by dates, returns an array with any missing date keys filled. | `@param array $array` | array
`arrayValuesToCounts` | Given an array of arrays, returns the count of each second level array while preserving top-level array keys. | `@param array $array | array
`fillLowerLevelDates` | Given an array of arrays keyed by dates, fills each of the arrays with any missing date keys and values based on the earliest start and latest end of all the arrays. | @param array[] $array | array[]
`flattenMultitermDateCount` | Given an array of arrays keyed by date (with identical numbers of items in each array and the same start and enddate), flattens the array into `[date, val1, val2, ...]`. | @param array[] $arrays | array[]

## About
[Boston 311 Archive](https://311.report) is a volunteer project to create a searchable archive of Boston 311 reports. See [Boston 311 Archive: About](https://311.report/about) for more information.

Boston 311 Archive also provides an easily discoverable read-only API that conforms to the JSON:API standard. You can browse the API directly at https://311.report/jsonapi.
