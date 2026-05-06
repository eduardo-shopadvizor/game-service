<?php

declare(strict_types=1);

namespace Saz\Game\Tests\Unit\Domain\Common\Enum;

use DateTimeInterface;
use Saz\Game\Tests\Unit\Domain\Common\Model\DateTimeMother;

enum DateTimeDefaultValueEnum
{
    case NOW;

    public static function parse(DateTimeInterface|self $value): DateTimeInterface
    {
        return $value instanceof self ? $value->default() : $value;
    }

    public function default(): DateTimeInterface
    {
        return match ($this) {
            self::NOW => DateTimeMother::create(),
        };
    }
}
