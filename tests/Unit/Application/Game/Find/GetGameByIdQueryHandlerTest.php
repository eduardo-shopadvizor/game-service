<?php

declare(strict_types=1);

namespace Saz\Game\Tests\Unit\Application\Game\Find;

use Saz\Game\Application\Game\Find\GetGameByIdQueryHandler;
use Saz\Game\Domain\Game\Exception\GameNotFoundException;
use Saz\Game\Domain\Game\ValueObject\GameId;
use Saz\Game\Tests\Unit\Domain\Game\Model\GameMother;

class GetGameByIdQueryHandlerTest extends GetGameByIdQueryHandlerTestCase
{
    private GetGameByIdQueryHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = new GetGameByIdQueryHandler($this->useCase);
    }

    public function testGameIsGetById(): void
    {
        $query = GetGameByIdQueryMother::create();
        $game = GameMother::create(id: $query->id);

        $this->expectRepositoryToFindById($game->id(), $game);
        $result = $this->handler->__invoke($query);

        $this->assertSame($result, $game);
    }

    public function testExceptionIsThrownIfGameIsNotFound(): void
    {
        $query = GetGameByIdQueryMother::create();
        $gameId = new GameId($query->id);

        $this->expectException(GameNotFoundException::class);
        $this->expectRepositoryToFindById($gameId, null);
        $this->handler->__invoke($query);
    }
}
