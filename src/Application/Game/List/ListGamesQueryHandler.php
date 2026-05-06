<?php

declare(strict_types=1);

namespace Saz\Game\Application\Game\List;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ListGamesQueryHandler
{
    public function __construct(private readonly ListGames $useCase)
    {
    }

    /** @return \Saz\Game\Domain\Game\Model\Game[] */
    public function __invoke(ListGamesQuery $query): array
    {
        return $this->useCase->list($query->genre);
    }
}
