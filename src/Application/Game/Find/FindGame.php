<?php

declare(strict_types=1);

namespace Saz\Game\Application\Game\Find;

use Saz\Game\Domain\Game\Exception\GameNotFoundException;
use Saz\Game\Domain\Game\Model\Game;
use Saz\Game\Domain\Game\Repository\GameRepositoryInterface;
use Saz\Game\Domain\Game\ValueObject\GameId;

/**
 * Caso de uso para buscar Games.
 * Patrón equivalente a FindBrand en brand-service.
 *
 * Distinción entre find() y get():
 * - find(): devuelve null si no existe → el llamador decide qué hacer
 * - get():  lanza GameNotFoundException si no existe → para contextos donde la ausencia es un error
 *
 * El Use Case solo conoce la interfaz del repositorio (GameRepositoryInterface),
 * nunca la implementación concreta. Esto es la Inversión de Dependencias en acción.
 */
class FindGame
{
    public function __construct(private readonly GameRepositoryInterface $repository)
    {
    }

    /** Devuelve null si el Game no existe. */
    public function find(GameId $id): ?Game
    {
        return $this->repository->find($id);
    }

    /** Lanza GameNotFoundException si el Game no existe. */
    public function get(GameId $id): Game
    {
        $result = $this->find($id);

        if (null === $result) {
            throw new GameNotFoundException(\sprintf('Game with id `%s` was not found', $id));
        }

        return $result;
    }
}
