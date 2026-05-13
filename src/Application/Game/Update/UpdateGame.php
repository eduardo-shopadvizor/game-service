<?php

declare(strict_types=1);

namespace Saz\Game\Application\Game\Update;

use Saz\Game\Application\Game\Find\FindGame;
use Saz\Game\Domain\Game\Enum\GameGenreEnum;
use Saz\Game\Domain\Game\Model\Game;
use Saz\Game\Domain\Game\Repository\GameRepositoryInterface;
use Saz\Game\Domain\Game\ValueObject\GameDescription;
use Saz\Game\Domain\Game\ValueObject\GameId;
use Saz\Game\Domain\Game\ValueObject\GameName;

final class UpdateGame
{
    public function __construct(
        private readonly GameRepositoryInterface $repository,
        private readonly FindGame $finder,
    ) {
    }

    public function update(
        GameId $id,
        GameName $name,
        ?GameDescription $description,
        GameGenreEnum $genre,
    ): Game {
        $game = $this->finder->get($id);

        $game->update(
            name: $name,
            description: $description,
            genre: $genre,
        );

        $this->repository->save($game);

        return $game;
    }
}
