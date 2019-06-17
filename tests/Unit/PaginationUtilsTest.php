<?php

namespace Tests\Unit;

use App\System\Utils\PaginationUtils;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaginationUtilsTest extends TestCase
{
    public function testCalculateNumberOfPages()
    {
        $this->assertEquals(20, PaginationUtils::calculateNumberOfPages(1000,50));
        $this->assertEquals(21, PaginationUtils::calculateNumberOfPages(1001,50));
        $this->assertEquals(1, PaginationUtils::calculateNumberOfPages(21,50));
        $this->assertExceptionCalculateNumberOfPages('test', 'test');
        $this->assertExceptionCalculateNumberOfPages(1001, 'test');
        $this->assertExceptionCalculateNumberOfPages('test', 1000);
    }

    public function testGetOffsetLimitByPageNumber()
    {
        $this->assertEquals([
            'offset' => 1000,
            'limit' => 1050,
        ], PaginationUtils::getOffsetLimitByPageNumber(1001,50, 21));

        $this->assertExceptionGetOffsetLimitByPageNumber(1001,50, 22);
        $this->assertExceptionGetOffsetLimitByPageNumber(1001,50, 0);
        $this->assertExceptionGetOffsetLimitByPageNumber(1001,50, 'a');
    }

    private function assertExceptionCalculateNumberOfPages($totalCount, $resultsPerPage)
    {
        $hasException = false;
        try {
            PaginationUtils::calculateNumberOfPages($totalCount, $resultsPerPage);
        } catch (\Exception $e) {
            $hasException = true;
        }
        $this->assertTrue($hasException);
    }

    private function assertExceptionGetOffsetLimitByPageNumber($totalCount, $resultsPerPage, $page)
    {
        $hasException = false;
        try {
            PaginationUtils::getOffsetLimitByPageNumber($totalCount, $resultsPerPage, $page);
        } catch (\Exception $e) {
            $hasException = true;
        }
        $this->assertTrue($hasException);
    }
}
