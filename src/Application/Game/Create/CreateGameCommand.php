<?php

declare(strict_types=1);

namespace Saz\Game\Application\Game\Create;

final class CreateGameCommand
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $description,
        public readonly string $genre,
    ) {
    }
}
