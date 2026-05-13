<?php

declare(strict_types=1);

namespace Saz\Game\Application\Game\Update;

final class UpdateGameCommand
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $description,
        public readonly string $genre,
    ) {
    }
}
