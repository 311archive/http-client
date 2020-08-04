<?php

namespace Balsama;

use http\Exception\InvalidArgumentException;

class Client implements ClientInterface
{
    private array $filters = [];
    private array $limit = [];
    private array $offset = [];
    private array $includes = [];
    private int $numberOfRequests = 0;

    private array $reports = [];

    private string $groupBy = '';

    /**
     * @inheritDoc
     */
    public function getReports()
    {
        return $this->reports;
    }

    /**
     * @inheritDoc
     */
    public function getReportsFields(array $fields)
    {
        $reportsFields = [];

        if ($this->groupBy) {
            foreach ($this->reports as $dateGroup => $reports) {
                foreach ($reports as $report) {
                    foreach ($fields as $field) {
                        if (!isset($report['attributes'][$field])) {
                            continue;
                        }
                        if (count($fields) === 1) {
                            $reportsFields
                            [$dateGroup]
                            [$report['attributes']['field_service_request_id']] = $report['attributes'][$field];
                        } else {
                            $reportsFields
                            [$dateGroup]
                            [$report['attributes']['field_service_request_id']]
                            [$field] = $report['attributes'][$field];
                        }
                    }
                }
            }
        } else {
            foreach ($this->reports as $report) {
                foreach ($fields as $field) {
                    if (!isset($report['attributes'][$field])) {
                        continue;
                    }

                    if (count($fields) === 1) {
                        $reportsFields
                        [$report['attributes']['field_service_request_id']] = $report['attributes'][$field];
                    } else {
                        $reportsFields
                        [$report['attributes']
                        ['field_service_request_id']][$field] = $report['attributes'][$field];
                    }
                }
            }
        }

        return $reportsFields;
    }

    /**
     * @inheritDoc
     */
    public function fetchReports()
    {
        $fetch = new Fetch(
            array_merge_recursive(
                $this->filters,
                $this->limit,
                $this->offset,
                ['include' => implode(',', $this->includes)]
            ),
            $this->numberOfRequests
        );
        $this->reports = $fetch->fetchAll();

        if ($this->groupBy) {
            $groupedReports = [];
            foreach ($this->reports as $report) {
                $groupedReports[
                date($this->mapGroupByToDatFormat(), strtotime($report['attributes']['field_requested_timestamp']))
                ][] = $report;
            }
            ksort($groupedReports);
            $this->reports = $groupedReports;
        }
    }

    /**
     * @inheritDoc
     */
    public function dateFilterAfterDate(string $afterDate)
    {
        $this->filters['filter']['dateFilterAfter'] = [
            'condition' => [
                'path' => 'field_requested_timestamp',
                'operator' => '>=',
                'value' => $this->normalizeDateFormat($afterDate),
            ]

        ];
    }

    /**
     * @inheritDoc
     */
    public function dateFilterBeforeDate(string $beforeDate)
    {
        $this->filters['filter']['dateFilterBefore'] = [
            'condition' => [
                'path' => 'field_requested_timestamp',
                'operator' => '<=',
                'value' => $this->normalizeDateFormat($beforeDate),
            ]

        ];
    }

    /**
     * @inheritDoc
     */
    public function filterCategory($category)
    {
        $this->includes[] = 'field_service_name';
        $this->filters['filter']['fieldServiceName'] = [
            'path' => 'field_service_name.name',
            'operator' => '=',
            'value' => $category,
        ];
    }

    /**
     * @inheritDoc
     */
    public function filterNeighborhood(string $neighborhood)
    {
        $this->includes[] = 'field_neighborhood';
        $this->filters['filter']['fieldNeighborhood'] = [
            'path' => 'field_neighborhood.name',
            'operator' => '=',
            'value' => $neighborhood,
        ];
    }

    /**
     * @inheritDoc
     */
    public function filterDescription(array $terms)
    {
        if (count($terms) === 1) {
            // We can do a more efficient query if there is only one term
            $this->filters['filter']['descriptionContains'] = [
                'condition' => [
                    'path' => 'field_description',
                    'operator' => 'CONTAINS',
                    'value' => reset($terms),
                ]
            ];
        } else {
            $this->filters['filter']['descriptionGroup']['group']['conjunction'] = 'OR';

            $n = 1;
            foreach ($terms as $term) {
                $this->filters['filter']['descriptionContains' . $n] = [
                    'condition' => [
                        'path' => 'field_description',
                        'operator' => 'CONTAINS',
                        'value' => $term,
                        'memberOf' => 'descriptionGroup',
                    ]
                ];
                $n++;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function filterStatusNotes(array $terms)
    {
        // TODO: Implement filterStatusNotes() method.
    }

    /**
     * @inheritDoc
     */
    public function filterStatusOpen($status)
    {
        // TODO: Implement filterStatusOpen() method.
    }

    /**
     * Parses a date string into a timestamp.
     *
     * @param  $date
     * @return false|int
     */
    private function normalizeDateFormat($date)
    {
        if (preg_match("/^\d+$/", $date)) {
            if (strlen($date) == 4) {
                throw new \InvalidArgumentException(
                    'You cannot pass just a year as a date string. You at least need a month too.'
                );
            }
        }
        $normalizedDate = strtotime($date);
        if ($normalizedDate === false) {
            throw new \InvalidArgumentException('Could not parse ' . $date . ' into date');
        }
        return $normalizedDate;
    }

    /**
     * @inheritDoc
     */
    public function setLimit(int $limit)
    {
        $this->limit = [
            'page' => [
                'limit' => $limit,
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function setOffset(int $offset)
    {
        $this->offset = [
            'page' => [
                'offset' => $offset,
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function limitRequests($numberOfRequests)
    {
        $this->numberOfRequests = $numberOfRequests;
    }

    public function groupResultsByDay()
    {
        $this->groupBy = 'day';
    }

    public function groupResultsByMonth()
    {
        $this->groupBy = 'month';
    }

    public function groupResultsByYear()
    {
        // TODO: Implement groupResultsByYear() method.
    }

    private function mapGroupByToDatFormat()
    {
        switch ($this->groupBy) {
            case 'day':
                $format = 'Y-m-d';
                break;
            case 'month':
                $format = 'Y-m';
                break;
            case 'year':
                $format = 'Y';
                break;
            default:
                throw new \InvalidArgumentException('Invalid group by: ' . $this->groupBy);
        }
        return $format;
    }
}
