<?php

declare(strict_types=1);

namespace Saz\Game\Tests\Unit\Application\Game\List;

use Saz\Game\Application\Game\List\ListGamesQueryHandler;
use Saz\Game\Domain\Game\Enum\GameGenreEnum;
use Saz\Game\Tests\Unit\Domain\Game\Model\GameMother;

class ListGamesQueryHandlerTest extends ListGamesQueryHandlerTestCase
{
    private ListGamesQueryHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = new ListGamesQueryHandler($this->useCase);
    }

    public function testAllGamesAreReturned(): void
    {
        $games = [
            GameMother::create(genre: GameGenreEnum::ACTION->value),
            GameMother::create(genre: GameGenreEnum::RPG->value),
            GameMother::create(genre: GameGenreEnum::STRATEGY->value),
        ];
        $query = ListGamesQueryMother::create();

        $this->expectRepositoryToReturnGames($games);
        $result = $this->handler->__invoke($query);

        $this->assertSame($games, $result);
    }

    public function testGamesAreFilteredByGenre(): void
    {
        $games = [
            GameMother::create(genre: GameGenreEnum::RPG->value),
            GameMother::create(genre: GameGenreEnum::RPG->value),
        ];
        $query = ListGamesQueryMother::withGenre(GameGenreEnum::RPG->value);

        $this->expectRepositoryToReturnGames($games);
        $result = $this->handler->__invoke($query);

        $this->assertSame($games, $result);
    }

    public function testEmptyListIsReturnedWhenNoGamesExist(): void
    {
        $query = ListGamesQueryMother::create();

        $this->expectRepositoryToReturnGames([]);
        $result = $this->handler->__invoke($query);

        $this->assertSame([], $result);
    }
}
