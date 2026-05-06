<?php

declare(strict_types=1);

namespace Saz\Game\Infrastructure\Doctrine\Types;

use Saz\CatalogSharedContext\Infrastructure\Doctrine\Types\ValueObjectAsStringType;
use Saz\Game\Domain\Game\ValueObject\GameDescription;

final class GameDescriptionType extends ValueObjectAsStringType
{
    public function getEntityClass(): string
    {
        return GameDescription::class;
    }
}
