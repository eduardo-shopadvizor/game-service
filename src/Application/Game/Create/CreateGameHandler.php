<?php

declare(strict_types=1);

namespace Saz\Game\Application\Game\Create;

use Saz\CatalogSharedContext\Application\Service\BusInterface;
use Saz\Game\Domain\Game\Enum\GameGenreEnum;
use Saz\Game\Domain\Game\Event\GameCreatedEvent;
use Saz\Game\Domain\Game\ValueObject\GameDescription;
use Saz\Game\Domain\Game\ValueObject\GameId;
use Saz\Game\Domain\Game\ValueObject\GameName;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateGameHandler
{
    public function __construct(
        private readonly CreateGame $useCase,
        private readonly BusInterface $bus,
    ) {
    }

    public function __invoke(CreateGameCommand $command): void
    {
        $game = $this->useCase->create(
            id: new GameId($command->id),
            name: new GameName($command->name),
            description: null !== $command->description ? new GameDescription($command->description) : null,
            genre: GameGenreEnum::from($command->genre),
        );

        $this->bus->command(new GameCreatedEvent(
            id: (string) $game->id(),
            name: (string) $game->name(),
            description: $game->description()?->value(),
            genre: $game->genre()->value,
            createdAt: $game->createdAt(),
        ));
    }
}
