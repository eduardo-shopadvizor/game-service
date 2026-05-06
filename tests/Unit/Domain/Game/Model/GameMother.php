<?php

declare(strict_types=1);

namespace Saz\Game\Tests\Unit\Domain\Game\Model;

use DateTimeInterface;
use Saz\Game\Domain\Game\Enum\GameGenreEnum;
use Saz\Game\Domain\Game\Model\Game;
use Saz\Game\Tests\Unit\Domain\Common\Enum\DateTimeDefaultValueEnum;
use Saz\Game\Tests\Unit\Domain\Common\Enum\UuidDefaultValueEnum;

class GameMother
{
    public static function create(
        UuidDefaultValueEnum|string $id = UuidDefaultValueEnum::RANDOM,
        string $name = 'Half-Life',
        ?string $description = 'A classic first-person shooter.',
        string $genre = GameGenreEnum::ACTION->value,
        DateTimeDefaultValueEnum|DateTimeInterface $createdAt = DateTimeDefaultValueEnum::NOW,
        DateTimeDefaultValueEnum|DateTimeInterface $updatedAt = DateTimeDefaultValueEnum::NOW,
    ): Game {
        return Game::createFromPrimitives(
            id: UuidDefaultValueEnum::parse($id),
            name: $name,
            description: $description,
            genre: $genre,
            createdAt: DateTimeDefaultValueEnum::parse($createdAt),
            updatedAt: DateTimeDefaultValueEnum::parse($updatedAt),
        );
    }
}
