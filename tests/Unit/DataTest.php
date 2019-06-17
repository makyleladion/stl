<?php

namespace Tests\Unit;

use App\Outlet;
use App\User as UserModel;
use App\System\Data\Ticket;
use App\System\Data\Transaction;
use App\System\Data\User;
use App\System\Games\Swertres\SwertresGame;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DataTest extends TestCase
{
    private $outlet;
    private $transaction;
    private $ticket;

    public function setUp()
    {
        parent::setUp();
        $this->outlet = Outlet::find(1)->first();
        $this->transaction = $this->outlet->transactions()->first();
        $this->ticket = $this->transaction->tickets()->first();
    }

    public function testDataTransactionProperties()
    {
        $transaction = new Transaction(
            $this->outlet,
            $this->transaction
        );

        $this->assertEquals(5, $transaction->numberOfBets());
        $this->assertEquals(15, $transaction->amount());
        $this->assertEquals('Joshua Paylaga', $transaction->teller());
        $this->assertEquals(1, count($transaction->tickets()));
        $this->assertEquals('1234-5678-9012-3456', $transaction->transactionNumber());
        $this->assertEquals('Test Outlet', $transaction->outletName());

        return $transaction;
    }

    /**
     * @depends testDataTransactionProperties
     */
    public function testDataTicketProperties(Transaction $transaction)
    {
        $ticket = $transaction->tickets()[0];

        $this->assertEquals('2017-10-19 11:00:00', $ticket->drawDateTime());
        $this->assertEquals('1234-5678-9012-3456', $ticket->ticketNumber());
        $this->assertEquals(15, $ticket->amount());
        $this->assertEquals(5, count($ticket->bets()));
        $this->assertTrue($ticket->isDrawTimePassed());

        return $ticket;
    }

    /**
     * @depends testDataTicketProperties
     */
    public function testDataBetProperties(Ticket $ticket)
    {
        $bet = $ticket->bets()[0];

        $this->assertEquals('123', $bet->betNumber());
        $this->assertEquals(SwertresGame::TYPE_STRAIGHT, $bet->betType());
        $this->assertEquals(1, $bet->amount());
        $this->assertEquals(450, $bet->price());
    }

    public function testDataUserProperties()
    {
        $userModel = UserModel::find(1)->first();
        $this->assertTrue($userModel instanceof UserModel);

        $user = new User($userModel);
        $this->assertNotNull($user);

        $this->assertEquals('Joshua Paylaga', $user->name());
        $this->assertEquals('test@stl.ph', $user->email());
        $this->assertTrue(Hash::check('testpass', $user->password()));
        $this->assertEquals('owner', $user->role());
    }
}
