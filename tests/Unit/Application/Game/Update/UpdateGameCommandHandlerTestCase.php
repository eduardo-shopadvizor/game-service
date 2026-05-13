<?php

declare(strict_types=1);

namespace Saz\Game\Tests\Unit\Application\Game\Update;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Saz\Game\Application\Game\Find\FindGame;
use Saz\Game\Application\Game\Update\UpdateGame;
use Saz\Game\Domain\Game\Model\Game;
use Saz\Game\Domain\Game\Repository\GameRepositoryInterface;
use Saz\Game\Domain\Game\ValueObject\GameId;

class UpdateGameCommandHandlerTestCase extends TestCase
{
    protected GameRepositoryInterface&MockObject $repository;
    protected UpdateGame $useCase;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(GameRepositoryInterface::class);
        $finder = new FindGame($this->repository);
        $this->useCase = new UpdateGame($this->repository, $finder);
    }

    protected function expectRepositoryToFindById(GameId $id, ?Game $game): void
    {
        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo($id))
            ->willReturn($game)
        ;
    }

    protected function expectRepositoryToSave(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Game::class))
        ;
    }

    protected function expectRepositoryToNotSave(): void
    {
        $this->repository
            ->expects($this->never())
            ->method('save')
        ;
    }
}
