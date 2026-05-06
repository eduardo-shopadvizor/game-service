<?php

declare(strict_types=1);

namespace Saz\Game\Application\Game\Find;

use Saz\Game\Domain\Game\Exception\GameNotFoundException;
use Saz\Game\Domain\Game\Model\Game;
use Saz\Game\Domain\Game\Repository\GameRepositoryInterface;
use Saz\Game\Domain\Game\ValueObject\GameId;

final class FindGame
{
    public function __construct(private readonly GameRepositoryInterface $repository)
    {
    }

    public function find(GameId $id): ?Game
    {
        return $this->repository->find($id);
    }

    public function get(GameId $id): Game
    {
        $result = $this->find($id);

        if (null === $result) {
            throw new GameNotFoundException(\sprintf('Game with id `%s` was not found', $id));
        }

        return $result;
    }
}
