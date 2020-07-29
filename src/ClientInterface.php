<?php

namespace Balsama;

/**
 * Defines an interface for fetching 311 reports.
 */
interface ClientInterface
{

    /**
     * Fetches the reports from the API.
     *
     * @return $this
     */
    public function fetchReports();

    /**
     * Gets all of the fetched reports.
     *
     * @return array
     */
    public function getReports();

    /**
     * Gets specific fields from the fetched Reports.
     *
     * @param  string[] $fields
     *   An array of field names to get.
     * @return array
     */
    public function getReportsFields(array $fields);

    /**
     * Limit the number of records returned per request. Note that this does not limit the total number of records
     * returned. It just splits them up over more pages/requests.
     *
     * @param  int $limit
     * @return $this
     */
    public function setLimit(int $limit);

    /**
     * Limits the number of requests/pages, even if there are more results to be fetched.
     *
     * @param  $numberOfRequests
     * @return $this
     */
    public function limitRequests($numberOfRequests);

    /**
     * Offset the start of the records returned by a given number.
     *
     * @param  int $offset
     * @return $this
     */
    public function setOffset(int $offset);

    /**
     * Filters reports that come before the provided "after" date.
     *
     * @param  string $afterDate
     *   Any readable PHP date format.
     * @return $this;
     */
    public function dateFilterAfterDate(string $afterDate);

    /**
     * Filters reports that come after the provided "before" date.
     *
     * @param  string $beforeDate
     *   Any readable PHP date format.
     * @return $this;
     */
    public function dateFilterBeforeDate(string $beforeDate);

    /**
     * Filters reports to only include reports in the given category.
     *
     * @param  $category
     * @return $this
     */
    public function filterCategory($category);

    /**
     * Filters reports to only include reports from the given neighborhood.
     *
     * @param  string $neighborhood
     * @return $this
     */
    public function filterNeighborhood(string $neighborhood);

    /**
     * An array of strings of which at least one must be present in the Description field.
     *
     * @param  string[] $terms
     * @return $this
     */
    public function filterDescription(array $terms);

    /**
     * An array of strings which much be present in the Status Notes field. Note: The Status Notes field is populated by
     * the city when a report is closed. It is generally empty for open reports. As such, using this will only return
     * closed reports.
     *
     * @param  string[] $terms
     * @return $this
     */
    public function filterStatusNotes(array $terms);

    /**
     * Filter on whether the report is open or not. True for open, False for closed. Do not use this method to return
     * all.
     *
     * @param  bool $status
     * @return $this
     */
    public function filterStatusOpen($status);

    /**
     * Groups the results into arrays keyed by day on which they were opened.
     *
     * @return $this
     */
    public function groupResultsByDay();

    /**
     * Groups the resuluts into arrays keyed by month in which they were opened.
     *
     * @return $this
     */
    public function groupResultsByMonth();

    /**
     * Groups results into arays keyed by year in which they were opened.
     *
     * @return $this
     */
    public function groupResultsByYear();
}
