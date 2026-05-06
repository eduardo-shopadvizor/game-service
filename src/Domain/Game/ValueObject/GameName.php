<?php

declare(strict_types=1);

namespace Saz\Game\Domain\Game\ValueObject;

use Saz\CatalogSharedContext\Domain\Base\ValueObject\StringValueObject;
use Saz\Game\Domain\Game\Exception\InvalidGameNameFormatException;

/**
 * Value Object para el nombre de un Game.
 * Extiende StringValueObject e impone las reglas de negocio en valid().
 *
 * El método valid() se llama automáticamente en el constructor de StringValueObject,
 * garantizando que nunca exista un GameName con un valor inválido.
 */
class GameName extends StringValueObject
{
    private const MIN_LENGTH = 2;

    public function valid(): void
    {
        if (\strlen($this->value()) < self::MIN_LENGTH) {
            throw new InvalidGameNameFormatException(\sprintf(
                'Provided name `%s` must have at least %d characters.',
                $this->value(),
                self::MIN_LENGTH
            ));
        }
    }
}
