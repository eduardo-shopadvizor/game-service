<?php

declare(strict_types=1);

namespace Saz\Game\Tests\Unit\Application\Game\List;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Saz\Game\Application\Game\List\ListGames;
use Saz\Game\Domain\Game\Model\Game;
use Saz\Game\Domain\Game\Repository\GameRepositoryInterface;

class ListGamesQueryHandlerTestCase extends TestCase
{
    protected GameRepositoryInterface&MockObject $repository;

    protected ListGames $useCase;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(GameRepositoryInterface::class);
        $this->useCase = new ListGames($this->repository);
    }

    /** @param Game[] $games */
    protected function expectRepositoryToReturnGames(array $games): void
    {
        $this->repository
            ->expects($this->once())
            ->method('matching')
            ->willReturn($games)
        ;
    }

    protected function expectRepositoryToNotBeCalled(): void
    {
        $this->repository
            ->expects($this->never())
            ->method('matching')
        ;
    }
}
