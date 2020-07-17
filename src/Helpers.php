<?php

namespace Balsama;

class Helpers
{

    /**
     * For a typical array of `[$date => $value]`'s, converts it into a CSV of the given name.
     *
     * @param array $array
     * @param string $filename
     */
    public static function convertDateValArrayToCsv(array $array, string $filename)
    {
        $array = self::fillMissingDateArrayKeys($array);
        $array = self::arrayValuesToCounts($array);
        $array = self::includeArrayKeysInArray($array);

        self::csv(['date', 'value'], $array, $filename);
    }

    /**
     * Shifts each top level key of an array of arrays into the row's contained array while preserving top-level keys.
     * @example
     *   Given
     *     [
     *       'a' => 'foo',
     *       'b' => 'bar',
     *     ]
     *   Returns
     *     [
     *       'a' => ['a', 'foo'],
     *       'b' => ['b', 'bar'],
     *     ]
     *
     * @param array[] | string[] $array
     * @return array[]
     */
    public static function includeArrayKeysInArray(array $array)
    {
        $newArray = [];
        foreach ($array as $key => $row) {
            if (is_array($row)) {
                array_unshift($row, $key);
                $newArray[$key] = $row;
            } elseif (is_string($row) || is_int($row)) {
                $newArray[$key] = [$key, $row];
            } else {
                throw new \InvalidArgumentException('Expected each row in the array to be an array, string, or int.');
            }
        }

        return $newArray;
    }

    /**
     * Writes an array of arrays to a CSV file.
     *
     * @param string[] $headers
     *   The names of the table columns.
     * @param array[] $data
     *   Data to write. Each top-level array should contain an array the same length as the $header array.
     * @param string $filename
     * @param string $path
     */
    public static function csv(array $headers, array $data, string $filename, $path = 'data/')
    {
        if (count($headers) !== count(reset($data))) {
            throw new \InvalidArgumentException(
                'The length of the `$header` array must equal the length of each of the arrays in `$data`'
            );
        }

        $fp = fopen($path . $filename, 'w');
        fputcsv($fp, $headers);
        foreach ($data as $datum) {
            fputcsv($fp, $datum);
        }
        fclose($fp);
    }

    /**
     * Given an array keyed by dates, returns an array with any missing date keys filled.
     *
     * @param array $array
     * @return array $array
     */
    public static function fillMissingDateArrayKeys(array $array)
    {
        $formatLength = strlen(array_key_first($array));

        switch ($formatLength) {
            case 4:
                $format = 'Y';
                $step = 'year';
                break;
            case 7:
                $format = 'Y-m';
                $step = 'month';
                break;
            case 10:
                $format = 'Y-m-d';
                $step = 'day';
                break;
            default:
                throw new \InvalidArgumentException(
                    'The keys must be dates in one of the following formats: `Y`, `Y-m`, or `Y-m-d`'
                );
        }

        $start = strtotime(array_key_first($array));
        $current = $start;
        $end = strtotime(array_key_last($array));
        $newArray = [];
        $defaultValue = (is_array(reset($array))) ? [] : 0;
        while ($current < $end) {
            $newArray[date($format, $current)] = $defaultValue;
            $current = strtotime("+1 $step", $current);
        }

        return array_merge($newArray, $array);
    }

    /**
     * Given an array of arrays, returns the count of each second level array while preserving top-level array keys.
     *
     * @param array[] $array
     * @return array[]
     */
    public static function arrayValuesToCounts($array)
    {
        $newArray = [];
        foreach ($array as $key => $row) {
            $newArray[$key] = count($row);
        }

        return $newArray;
    }

    public static function fillLowerLevelDates($arrays)
    {
        // 1. find earliest
        foreach ($arrays as $array) {
            $potentialFirsts[] = array_key_first($array);
            $potentialLasts[] = array_key_last($array);
        }
        sort($potentialFirsts);
        sort($potentialLasts);
        $start = reset($potentialFirsts);
        $end = end($potentialLasts);
        $foo = 21;

        // 3. call existing fil dates function on each
        foreach ($arrays as $array) {
            // Fill them in!
        }
    }

}
