<?php

declare(strict_types=1);

namespace Saz\Game\Infrastructure\Doctrine\Types\Base;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Saz\CatalogSharedContext\Infrastructure\Doctrine\Types\IdType as IdTypeSharedContext;

abstract class IdType extends IdTypeSharedContext
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getBinaryTypeDeclarationSQL([
            'length' => 16,
            'fixed' => true,
        ]);
    }
}
