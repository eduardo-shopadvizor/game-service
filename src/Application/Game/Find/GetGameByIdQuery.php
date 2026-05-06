<?php

declare(strict_types=1);

namespace Saz\Game\Application\Game\Find;

final class GetGameByIdQuery
{
    public function __construct(public readonly string $id)
    {
    }
}
