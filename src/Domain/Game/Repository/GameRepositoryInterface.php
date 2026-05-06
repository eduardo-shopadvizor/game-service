<?php

declare(strict_types=1);

namespace Saz\Game\Domain\Game\Repository;

use Saz\CatalogSharedContext\Domain\Criteria\Criteria;
use Saz\Game\Domain\Game\Model\Game;
use Saz\Game\Domain\Game\ValueObject\GameId;
use Saz\Game\Domain\Game\ValueObject\GameName;

/**
 * Contrato del repositorio de Game definido en el dominio.
 * La implementación concreta vive en la capa Infrastructure.
 *
 * Este diseño garantiza la Regla de Dependencia del DDD:
 * el dominio no depende de infraestructura, sino al revés.
 *
 * Patrón equivalente a BrandRepositoryInterface en brand-service.
 */
interface GameRepositoryInterface
{
    /** Busca un Game por su ID. Devuelve null si no existe. */
    public function find(GameId $id): ?Game;

    /** Busca un Game por nombre exacto. Devuelve null si no existe. */
    public function findOneByName(GameName $name): ?Game;

    /**
     * Busca Games que cumplan los criterios dados.
     *
     * @return Game[]
     */
    public function matching(Criteria $criteria): array;

    public function count(Criteria $criteria): int;

    public function save(Game $game): void;

    public function delete(Game $game): void;
}
