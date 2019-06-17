<?php

namespace App\System\Utils;


class PaginationUtils
{
    /**
     * Calculate number of pages.
     *
     * @param $totalCount
     * @param $resultsPerPage
     * @return float
     * @throws \Exception
     */
    public static function calculateNumberOfPages($totalCount, $resultsPerPage)
    {
        if (!is_numeric($totalCount)) {
            throw new \Exception('TotalCount must be an number.');
        } else if (!is_numeric($resultsPerPage)) {
            throw new \Exception('resultsPerPage must be an number.');
        } else if ($totalCount < $resultsPerPage) {
            $totalCount = $resultsPerPage;
        }

        if ($resultsPerPage == 0) {
            return 1;
        }

        return ceil($totalCount /  $resultsPerPage);
    }

    /**
     * Calculates offset and limit by page number.
     *
     * @param $totalCount
     * @param $resultsPerPage
     * @param $page
     * @return array
     * @throws \Exception
     */
    public static function getOffsetLimitByPageNumber($totalCount, $resultsPerPage, $page)
    {
        if ($page > 0 && $page <= self::calculateNumberOfPages($totalCount, $resultsPerPage)) {
            $limit = $resultsPerPage * $page;
            $offset = $limit - $resultsPerPage;
            return [
                'offset' => $offset,
                'limit' => $limit,
            ];
        }
        throw new \Exception('Please enter a valid page number.');
    }

    /**
     * Previous page number.
     *
     * Get the page number to be used as the previous page.
     *
     * @param $currentPage
     * @param $totalCount
     * @param $resultsPerPage
     * @return int|string
     * @throws \Exception
     */
    public static function getPreviousPageNumber($currentPage, $totalCount, $resultsPerPage)
    {
        $totalPages = self::calculateNumberOfPages($totalCount, $resultsPerPage);
        if (!is_numeric($currentPage)) {
            throw new \Exception('currentPage must be an number.');
        }

        if ($currentPage > 1 && $currentPage <= $totalPages) {
            return --$currentPage;
        }

        return (int) $currentPage;
    }

    /**
     * Next page number.
     *
     * Get the page number to be used as the next page.
     *
     * @param $currentPage
     * @param $totalCount
     * @param $resultsPerPage
     * @return int|string
     * @throws \Exception
     */
    public static function getNextPageNumber($currentPage, $totalCount, $resultsPerPage)
    {
        $totalPages = self::calculateNumberOfPages($totalCount, $resultsPerPage);
        if (!is_numeric($currentPage)) {
            throw new \Exception('currentPage must be an number.');
        }

        if ($currentPage >= 1 && $currentPage < $totalPages) {
            return ++$currentPage;
        }

        return (int) $currentPage;;
    }
    
    /**
     * Get global records per page number.
     * 
     * @return number
     */
    public static function globalRecordsPerPage()
    {
        return 100;
    }
}
