<?php

declare(strict_types=1);

namespace Saz\Game\Tests\Unit\Application\Game\Find;

use Ramsey\Uuid\Uuid;
use Saz\Game\Application\Game\Find\GetGameByIdQuery;

class GetGameByIdQueryMother
{
    public static function create(?string $id = null): GetGameByIdQuery
    {
        return new GetGameByIdQuery($id ?? Uuid::uuid4()->toString());
    }
}
