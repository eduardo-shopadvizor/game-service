<?php

declare(strict_types=1);

namespace Saz\Game\Domain\Game\ValueObject;

use Saz\CatalogSharedContext\Domain\Base\ValueObject\EntityId;

/**
 * Value Object que representa el identificador único de un Game.
 * Extiende EntityId (UUID v4) del contexto compartido.
 *
 * Al ser un Value Object:
 * - Es inmutable (readonly internamente)
 * - Su igualdad se basa en el valor, no en la referencia
 * - Se puede convertir a string con (string) $gameId
 */
class GameId extends EntityId
{
}
