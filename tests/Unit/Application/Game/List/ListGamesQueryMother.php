<?php

declare(strict_types=1);

namespace Saz\Game\Tests\Unit\Application\Game\List;

use Saz\Game\Application\Game\List\ListGamesQuery;

class ListGamesQueryMother
{
    public static function create(?string $genre = null): ListGamesQuery
    {
        return new ListGamesQuery(genre: $genre);
    }

    public static function withGenre(string $genre): ListGamesQuery
    {
        return new ListGamesQuery(genre: $genre);
    }
}
