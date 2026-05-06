<?php

declare(strict_types=1);

namespace Saz\Game\Infrastructure\Doctrine\Types;

use Saz\Game\Domain\Game\ValueObject\GameId;
use Saz\Game\Infrastructure\Doctrine\Types\Base\IdType;

final class GameIdType extends IdType
{
    public function getEntityClass(): string
    {
        return GameId::class;
    }

    public function getName(): string
    {
        return 'game_id';
    }
}
