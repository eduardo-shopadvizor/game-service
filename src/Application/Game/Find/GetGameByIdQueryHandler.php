<?php

declare(strict_types=1);

namespace Saz\Game\Application\Game\Find;

use Saz\Game\Domain\Game\Model\Game;
use Saz\Game\Domain\Game\ValueObject\GameId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Handler del query GetGameByIdQuery.
 *
 * Responsabilidades del handler:
 * 1. Traducir el DTO de la query a tipos del dominio (string → GameId)
 * 2. Delegar la lógica de negocio al Use Case (FindGame)
 * 3. Devolver el resultado sin transformación adicional
 *
 * El atributo #[AsMessageHandler] registra automáticamente esta clase
 * en el bus de Symfony Messenger como manejador de GetGameByIdQuery.
 */
#[AsMessageHandler]
class GetGameByIdQueryHandler
{
    public function __construct(private readonly FindGame $useCase)
    {
    }

    public function __invoke(GetGameByIdQuery $query): Game
    {
        return $this->useCase->get(new GameId($query->id));
    }
}
