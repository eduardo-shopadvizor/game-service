<?php

declare(strict_types=1);

namespace Saz\Game\Domain\Game\Exception;

/**
 * Excepción de dominio lanzada cuando no se encuentra un Game por su ID.
 * Extiende RuntimeException para indicar un error recuperable en tiempo de ejecución.
 *
 * Se lanza en: Application\Game\Find\FindGame::get()
 */
class GameNotFoundException extends \RuntimeException
{
}
