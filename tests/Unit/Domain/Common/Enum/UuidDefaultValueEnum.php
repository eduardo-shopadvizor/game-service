<?php

declare(strict_types=1);

namespace Saz\Game\Tests\Unit\Domain\Common\Enum;

use Ramsey\Uuid\Uuid;

enum UuidDefaultValueEnum
{
    case RANDOM;

    public static function parse(string|self $value): string
    {
        return $value instanceof self ? $value->default() : $value;
    }

    public function default(): string
    {
        return match ($this) {
            self::RANDOM => Uuid::uuid4()->toString(),
        };
    }
}
