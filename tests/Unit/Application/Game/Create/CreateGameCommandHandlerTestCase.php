<?php

declare(strict_types=1);

namespace Saz\Game\Tests\Unit\Application\Game\Create;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Saz\CatalogSharedContext\Application\Service\BusInterface;
use Saz\Game\Application\Game\Create\CreateGame;
use Saz\Game\Domain\Game\Model\Game;
use Saz\Game\Domain\Game\Repository\GameRepositoryInterface;
use Saz\Game\Domain\Game\ValueObject\GameName;

class CreateGameCommandHandlerTestCase extends TestCase
{
    protected GameRepositoryInterface&MockObject $repository;
    protected BusInterface&MockObject $bus;
    protected CreateGame $useCase;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(GameRepositoryInterface::class);
        $this->bus = $this->createMock(BusInterface::class);
        $this->useCase = new CreateGame($this->repository);
    }

    protected function expectRepositoryToFindOneByName(GameName $name, ?Game $game): void
    {
        $this->repository
            ->expects($this->once())
            ->method('findOneByName')
            ->with($this->equalTo($name))
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

    protected function expectBusToDispatchEvent(): void
    {
        $this->bus
            ->expects($this->once())
            ->method('command')
        ;
    }

    protected function expectBusToNotDispatchEvent(): void
    {
        $this->bus
            ->expects($this->never())
            ->method('command')
        ;
    }
}
