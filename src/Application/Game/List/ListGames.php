<?php

declare(strict_types=1);

namespace Saz\Game\Application\Game\List;

use Saz\CatalogSharedContext\Domain\Criteria\Criteria;
use Saz\Game\Domain\Game\Model\Game;
use Saz\Game\Domain\Game\Repository\GameRepositoryInterface;

final class ListGames
{
    public function __construct(private readonly GameRepositoryInterface $repository)
    {
    }

    /** @return Game[] */
    public function list(?string $genre = null): array
    {
        $filters = [];

        if (null !== $genre) {
            $filters[] = ['field' => 'genre', 'operator' => '=', 'value' => $genre];
        }

        $criteria = Criteria::fromValues(
            filters: $filters,
            orderings: [['orderBy' => 'name', 'order' => 'ASC']],
        );

        return $this->repository->matching($criteria);
    }
}
