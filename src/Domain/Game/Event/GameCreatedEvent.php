<?php

declare(strict_types=1);

namespace Saz\Game\Domain\Game\Event;

final class GameCreatedEvent
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $description,
        public readonly string $genre,
        public readonly \DateTimeInterface $createdAt,
    ) {
    }
}
