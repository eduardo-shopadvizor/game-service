<?php

declare(strict_types=1);

namespace Saz\Game\Application\Game\Create;

use Saz\Game\Domain\Game\Enum\GameGenreEnum;
use Saz\Game\Domain\Game\Exception\GameAlreadyExistsException;
use Saz\Game\Domain\Game\Model\Game;
use Saz\Game\Domain\Game\Repository\GameRepositoryInterface;
use Saz\Game\Domain\Game\ValueObject\GameDescription;
use Saz\Game\Domain\Game\ValueObject\GameId;
use Saz\Game\Domain\Game\ValueObject\GameName;

final class CreateGame
{
    public function __construct(private readonly GameRepositoryInterface $repository)
    {
    }

    public function create(
        GameId $id,
        GameName $name,
        ?GameDescription $description,
        GameGenreEnum $genre,
    ): Game {
        if (null !== $this->repository->findOneByName($name)) {
            throw new GameAlreadyExistsException(
                \sprintf('Game with name `%s` already exists', $name)
            );
        }

        $now = new \DateTimeImmutable();

        $game = new Game($id, $name, $description, $genre, $now, $now);

        $this->repository->save($game);

        return $game;
    }
}
