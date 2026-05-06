<?php

declare(strict_types=1);

namespace Saz\Game\Domain\Common;

/**
 * Marcador para distinguir "campo no enviado" de "campo enviado como null".
 * Se usa en los métodos update() de las entidades para soportar actualizaciones parciales.
 *
 * Ejemplo de uso:
 *   public function update(GameName|EmptyValue $name = new EmptyValue()): void
 *   {
 *       if (!$name instanceof EmptyValue) {
 *           $this->name = $name;  // solo actualiza si se envió un valor
 *       }
 *   }
 */
class EmptyValue
{
}
