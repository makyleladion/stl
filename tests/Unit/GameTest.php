<?php

namespace Tests\Unit;

use App\System\Game;
use App\System\Games\Pares\ParesGame;
use App\System\Games\Swertres\SwertresGame;
use App\System\Games\Swertres\SwertresService;
use Tests\TestCase;
use App\System\Games\GamesFactory;

class GameTest extends TestCase
{
    private $game;
    private $service;

    private $paresGame;

    public function setUp()
    {
        parent::setUp();
        $this->game = GamesFactory::getGame(SwertresGame::name());
        $this->paresGame = GamesFactory::getGame(ParesGame::name());
        $this->service = new SwertresService();
    }

    public function testSwertresGameProperties()
    {
        $this->assertEquals('swertres', SwertresGame::name());
        $this->assertEquals('Swertres', $this->game->label());

        $this->assertTrue(in_array('straight', $this->game->betTypes()));
        $this->assertTrue(in_array('rambled', $this->game->betTypes()));
    }

    public function testSwertresValidBet()
    {
        $this->assertTrue($this->game->isValidBet('123'));
        $this->assertFalse($this->game->isValidBet('1a3'));
        $this->assertFalse($this->game->isValidBet('12'));
        $this->assertFalse($this->game->isValidBet('1234'));
    }

    public function testSwertresPermutation()
    {
        $this->assertEquals(6, count($this->service->permutation('123')));
        $this->assertEquals(3, count($this->service->permutation('122')));
        $this->assertEquals(1, count($this->service->permutation('111')));
    }

    public function testPrice()
    {
        $this->assertEquals(450, $this->game->price('123', 1, SwertresGame::TYPE_STRAIGHT));
        $this->assertEquals(75, $this->game->price('123', 1, SwertresGame::TYPE_RAMBLED));
        $this->assertEquals(150, $this->game->price('122', 1, SwertresGame::TYPE_RAMBLED));
        $this->assertEquals(450, $this->game->price('111', 1, SwertresGame::TYPE_RAMBLED));

        $this->priceException($this->game, '123', 1, 'test_mistake');
        $this->priceException($this->game, 'abc', 1, SwertresGame::TYPE_STRAIGHT);
        $this->priceException($this->game, '123', 'abc', SwertresGame::TYPE_STRAIGHT);
    }

    public function testParesGameProperties()
    {
        $this->assertEquals('pares', ParesGame::name());
        $this->assertEquals('Pares', $this->paresGame->label());

        $this->assertTrue(in_array('none', $this->paresGame->betTypes()));
        $this->assertEquals(1, count($this->paresGame->betTypes()));
    }

    public function testParesValidBet()
    {
        $this->assertTrue($this->paresGame->isValidBet('40:40'));
        $this->assertTrue($this->paresGame->isValidBet('1:40'));
        $this->assertTrue($this->paresGame->isValidBet('40:1'));
        $this->assertFalse($this->paresGame->isValidBet('41:0'));
        $this->assertFalse($this->paresGame->isValidBet('40:x'));
        $this->assertFalse($this->paresGame->isValidBet('11'));
    }

    public function testParesPrice()
    {
        $this->assertEquals(800, $this->paresGame->price('9:10', 1, ParesGame::TYPE_NONE));
        $this->assertEquals(2400, $this->paresGame->price('9:10', 3, ParesGame::TYPE_NONE));

        $this->priceException($this->paresGame, '39:2', 1, 'test_mistake');
        $this->priceException($this->paresGame, 'abc', 1, ParesGame::TYPE_NONE);
        $this->priceException($this->paresGame, '10:20', 'abc', ParesGame::TYPE_NONE);
    }

    public function testIsWin()
    {
        $game = GamesFactory::getGame('swertres');
        $winningNumber = '638';
        $toCompareBetNumber = '863';

        $this->assertTrue($game->isWin($winningNumber, $toCompareBetNumber, $game::TYPE_RAMBLED));
    }

    private function priceException(Game $game, $bet, $amount, $type)
    {
        $hasException = false;
        try {
            $game->price($bet, $amount, $type);
        } catch (\Exception $e) {
            $hasException = true;
        }
        $this->assertTrue($hasException);
    }
}
