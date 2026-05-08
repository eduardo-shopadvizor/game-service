<?php

declare(strict_types=1);

namespace Saz\Game\Application\Game\Create;

use Saz\Game\Domain\Game\Event\GameCreatedEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class OnGameCreated
{
    public function __invoke(GameCreatedEvent $event): void
    {
        // Publishes to GAME_CREATED_TOPIC when async transport is configured
    }
}
