<?php

namespace Tests\Unit;

use App\Bet;
use App\Outlet;
use App\System\Data\Timeslot;
use App\Transaction;
use App\System\Services\TransactionService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class TransactionServiceTest extends TestCase
{
    private $transactionService;
    private $transactionServiceAdmin;

    public function setUp()
    {
        parent::setUp();
        $outlet = Outlet::find(1)->first();
        $this->transactionService = new TransactionService($outlet);
        $this->transactionServiceAdmin = new TransactionService();
    }

    public function testGetTransactions()
    {
        $transactions = $this->transactionService->getTransactions(0, 50);
        $this->assertEquals(3, count($transactions));

        $transactionsAdmin = $this->transactionServiceAdmin->getTransactions(0,50);
        $this->assertEquals(3, count($transactionsAdmin));

        // Test betString
        $transaction = $transactions[2];
        $this->assertNotNull($transaction);
        $this->assertEquals('123, 123, 121, 111, 1:40', $transaction->betsString());
    }

    public function testSkipTakeTransactions()
    {
        $first = $this->transactionService->getTransactions(0,2);
        $first = $first[0];
        $this->assertNotNull($first);

        $second = $this->transactionService->getTransactions(2,1);
        $second = $second[0];
        $this->assertNotNull($second);

        $id = (int) $first->getTransaction()->id;
        $this->assertEquals($id -= 2, (int) $second->getTransaction()->id);

        return $second;
    }

    /**
     * @depends testSkipTakeTransactions
     */
    public function testGetTransactionDataAsArray($transaction)
    {
        $transactionArray = $this->transactionService->getTransactionDataAsArray($transaction);
        $this->assertEquals(5, count($transactionArray));
    }

    public function testGetTotalAmountToday()
    {
        $now = Carbon::createFromDate(2017, 10, 19, env('APP_TIMEZONE'));
        $total = $this->transactionService->getTotalAmountByDate($now);
        $this->assertEquals(15, $total);
    }

    /*public function testDrawDateTimeslotHasPassed()
    {
        $date = date('Y-m-d');
        $key = Timeslot::TIMESLOT_EVENING;

        $result = $this->transactionService->drawDateTimeslotHasPassed($date, $key);
        if (date('Y-m-d H:i:s') > date('Y-m-d H:i:s',strtotime($date . ' ' . Timeslot::getTimeByKey($key)))) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }*/
}
