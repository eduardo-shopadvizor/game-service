<?php

declare(strict_types=1);

namespace Saz\Game\Application\Game\List;

final class ListGamesQuery
{
    public function __construct(public readonly ?string $genre = null)
    {
    }
}
