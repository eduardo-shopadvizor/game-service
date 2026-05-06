<?php

declare(strict_types=1);

namespace Saz\Game\Infrastructure\Repository;

use Saz\CatalogSharedContext\Domain\Criteria\Criteria;
use Saz\CatalogSharedContext\Infrastructure\Repository\DoctrineRepository;
use Saz\Game\Domain\Game\Model\Game;
use Saz\Game\Domain\Game\Repository\GameRepositoryInterface;
use Saz\Game\Domain\Game\ValueObject\GameId;
use Saz\Game\Domain\Game\ValueObject\GameName;

/**
 * @extends DoctrineRepository<Game>
 */
final class DoctrineGameRepository extends DoctrineRepository implements GameRepositoryInterface
{
    protected function getEntityClass(): string
    {
        return Game::class;
    }

    public function find(GameId $id): ?Game
    {
        return $this->getRepository()->find($id);
    }

    public function findOneByName(GameName $name): ?Game
    {
        $queryBuilder = $this->createQueryBuilder();
        $expr = $queryBuilder->expr();

        return $queryBuilder
            ->where($expr->like(self::ALIAS . '.name', ':name'))
            ->setParameter('name', (string) $name)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function matching(Criteria $criteria): array
    {
        return $this->executeQuery($criteria);
    }

    public function count(Criteria $criteria): int
    {
        return $this->executeCount($criteria);
    }

    public function save(Game $game): void
    {
        $this->persist($game);
    }

    public function delete(Game $game): void
    {
        $this->remove($game);
    }
}
