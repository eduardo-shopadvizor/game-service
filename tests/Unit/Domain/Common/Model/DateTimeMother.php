<?php

declare(strict_types=1);

namespace Saz\Game\Tests\Unit\Domain\Common\Model;

class DateTimeMother
{
    public static function create(?string $datetime = null): \DateTimeInterface
    {
        return new \DateTimeImmutable($datetime ?? (new \DateTimeImmutable())->format(\DATE_RFC3339));
    }
}
