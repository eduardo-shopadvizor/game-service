<?php

declare(strict_types=1);

namespace Saz\Game\Domain\Game\Exception;

/**
 * Excepción lanzada por GameName cuando el nombre no cumple las reglas de negocio.
 * Se define en el dominio para que la validación sea responsabilidad del Value Object.
 */
class InvalidGameNameFormatException extends \RuntimeException
{
}
