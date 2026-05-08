<?php

declare(strict_types=1);

namespace Saz\Game\Tests\Unit\Application\Game\Create;

use Saz\Game\Application\Game\Create\CreateGameHandler;
use Saz\Game\Domain\Game\Exception\GameAlreadyExistsException;
use Saz\Game\Domain\Game\ValueObject\GameName;
use Saz\Game\Tests\Unit\Domain\Game\Model\GameMother;

class CreateGameCommandHandlerTest extends CreateGameCommandHandlerTestCase
{
    private CreateGameHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = new CreateGameHandler(
            useCase: $this->useCase,
            bus: $this->bus,
        );
    }

    public function testGameIsCreated(): void
    {
        $command = CreateGameCommandMother::create();

        $this->expectRepositoryToFindOneByName(new GameName($command->name), null);
        $this->expectRepositoryToSave();
        $this->expectBusToDispatchEvent();

        $this->handler->__invoke($command);
    }

    public function testGameIsCreatedWithoutDescription(): void
    {
        $command = CreateGameCommandMother::withoutDescription();

        $this->expectRepositoryToFindOneByName(new GameName($command->name), null);
        $this->expectRepositoryToSave();
        $this->expectBusToDispatchEvent();

        $this->handler->__invoke($command);
    }

    public function testExceptionIsThrownWhenGameAlreadyExists(): void
    {
        $existing = GameMother::create(name: 'Half-Life');
        $command = CreateGameCommandMother::create(name: 'Half-Life');

        $this->expectException(GameAlreadyExistsException::class);
        $this->expectRepositoryToFindOneByName(new GameName($command->name), $existing);
        $this->expectRepositoryToNotSave();
        $this->expectBusToNotDispatchEvent();

        $this->handler->__invoke($command);
    }
}
