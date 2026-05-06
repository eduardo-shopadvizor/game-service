<?php

declare(strict_types=1);

namespace Saz\Game\Application\Game\Find;

use Saz\Game\Domain\Game\Model\Game;
use Saz\Game\Domain\Game\ValueObject\GameId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetGameByIdQueryHandler
{
    public function __construct(private readonly FindGame $useCase)
    {
    }

    public function __invoke(GetGameByIdQuery $query): Game
    {
        return $this->useCase->get(new GameId($query->id));
    }
}
