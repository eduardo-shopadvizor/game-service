<?php

declare(strict_types=1);

namespace Saz\Game\Application\Game\Update;

use Saz\Game\Domain\Game\Enum\GameGenreEnum;
use Saz\Game\Domain\Game\ValueObject\GameDescription;
use Saz\Game\Domain\Game\ValueObject\GameId;
use Saz\Game\Domain\Game\ValueObject\GameName;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class UpdateGameHandler
{
    public function __construct(
        private readonly UpdateGame $useCase,
    ) {
    }

    public function __invoke(UpdateGameCommand $command): void
    {
        $this->useCase->update(
            id: new GameId($command->id),
            name: new GameName($command->name),
            description: null !== $command->description ? new GameDescription($command->description) : null,
            genre: GameGenreEnum::from($command->genre),
        );
    }
}
