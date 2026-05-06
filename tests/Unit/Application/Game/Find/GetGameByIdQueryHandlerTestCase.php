<?php

declare(strict_types=1);

namespace Saz\Game\Tests\Unit\Application\Game\Find;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Saz\Game\Application\Game\Find\FindGame;
use Saz\Game\Domain\Game\Model\Game;
use Saz\Game\Domain\Game\Repository\GameRepositoryInterface;
use Saz\Game\Domain\Game\ValueObject\GameId;

class GetGameByIdQueryHandlerTestCase extends TestCase
{
    protected GameRepositoryInterface&MockObject $repository;

    protected FindGame $useCase;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(GameRepositoryInterface::class);
        $this->useCase = new FindGame($this->repository);
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
}
