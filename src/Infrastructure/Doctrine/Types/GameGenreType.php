<?php

declare(strict_types=1);

namespace Saz\Game\Infrastructure\Doctrine\Types;

use Saz\CatalogSharedContext\Infrastructure\Doctrine\Types\StringEnumType;
use Saz\Game\Domain\Game\Enum\GameGenreEnum;

final class GameGenreType extends StringEnumType
{
    public function getEntityClass(): string
    {
        return GameGenreEnum::class;
    }
}
