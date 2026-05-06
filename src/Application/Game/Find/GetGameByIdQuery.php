<?php

declare(strict_types=1);

namespace Saz\Game\Application\Game\Find;

/**
 * Query (DTO de lectura) para obtener un Game por su ID.
 *
 * En CQRS, las Queries solo transportan datos hacia los handlers;
 * no tienen lógica de negocio y sus propiedades son readonly.
 *
 * El bus de Symfony Messenger enruta este objeto al handler correspondiente
 * basándose en el tipo: GetGameByIdQueryHandler.
 */
class GetGameByIdQuery
{
    public function __construct(public readonly string $id)
    {
    }
}
