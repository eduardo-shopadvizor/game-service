<?php

declare(strict_types=1);

namespace Saz\Game\Tests\Unit\Application\Game\Create;

use Saz\Game\Application\Game\Create\CreateGameCommand;
use Saz\Game\Domain\Game\Enum\GameGenreEnum;
use Saz\Game\Tests\Unit\Domain\Common\Enum\UuidDefaultValueEnum;

class CreateGameCommandMother
{
    public static function create(
        ?string $id = null,
        string $name = 'Half-Life',
        ?string $description = 'A classic first-person shooter.',
        string $genre = GameGenreEnum::ACTION->value,
    ): CreateGameCommand {
        return new CreateGameCommand(
            id: $id ?? UuidDefaultValueEnum::parse(UuidDefaultValueEnum::RANDOM),
            name: $name,
            description: $description,
            genre: $genre,
        );
    }

    public static function withoutDescription(): CreateGameCommand
    {
        return self::create(description: null);
    }
}
