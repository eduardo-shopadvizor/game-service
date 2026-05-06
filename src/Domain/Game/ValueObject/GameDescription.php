<?php

declare(strict_types=1);

namespace Saz\Game\Domain\Game\ValueObject;

use Saz\CatalogSharedContext\Domain\Base\ValueObject\StringValueObject;

/**
 * Value Object para la descripción de un Game.
 * No impone restricciones adicionales más allá de las de StringValueObject.
 *
 * Es opcional en la entidad Game (se almacena como ?GameDescription).
 */
class GameDescription extends StringValueObject
{
}
