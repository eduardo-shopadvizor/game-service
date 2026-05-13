<?php

declare(strict_types=1);

namespace Saz\Game\Tests\Unit\Application\Game\Update;

use Saz\Game\Application\Game\Update\UpdateGameHandler;
use Saz\Game\Domain\Game\Exception\GameNotFoundException;
use Saz\Game\Domain\Game\ValueObject\GameId;
use Saz\Game\Tests\Unit\Domain\Game\Model\GameMother;

class UpdateGameCommandHandlerTest extends UpdateGameCommandHandlerTestCase
{
    private UpdateGameHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = new UpdateGameHandler(
            useCase: $this->useCase,
        );
    }

    public function testGameIsUpdated(): void
    {
        $game = GameMother::create();
        $command = UpdateGameCommandMother::create(id: $game->id()->value());

        $this->expectRepositoryToFindById($game->id(), $game);
        $this->expectRepositoryToSave();

        $this->handler->__invoke($command);
    }

    public function testGameIsUpdatedWithNullDescription(): void
    {
        $game = GameMother::create();
        $command = UpdateGameCommandMother::withoutDescription(id: $game->id()->value());

        $this->expectRepositoryToFindById($game->id(), $game);
        $this->expectRepositoryToSave();

        $this->handler->__invoke($command);
    }

    public function testExceptionIsThrownWhenGameNotFound(): void
    {
        $command = UpdateGameCommandMother::create();

        $this->expectException(GameNotFoundException::class);
        $this->expectRepositoryToFindById(new GameId($command->id), null);
        $this->expectRepositoryToNotSave();

        $this->handler->__invoke($command);
    }
}
