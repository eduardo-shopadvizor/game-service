<?php

declare(strict_types=1);

namespace Saz\Game\Tests\Unit\Application\Game\Update;

use Saz\Game\Application\Game\Update\UpdateGameCommand;
use Saz\Game\Domain\Game\Enum\GameGenreEnum;
use Saz\Game\Tests\Unit\Domain\Common\Enum\UuidDefaultValueEnum;

class UpdateGameCommandMother
{
    public static function create(
        ?string $id = null,
        string $name = 'Half-Life 2',
        ?string $description = 'An updated description.',
        string $genre = GameGenreEnum::ACTION->value,
    ): UpdateGameCommand {
        return new UpdateGameCommand(
            id: $id ?? UuidDefaultValueEnum::parse(UuidDefaultValueEnum::RANDOM),
            name: $name,
            description: $description,
            genre: $genre,
        );
    }

    public static function withoutDescription(?string $id = null): UpdateGameCommand
    {
        return self::create(id: $id, description: null);
    }
}
