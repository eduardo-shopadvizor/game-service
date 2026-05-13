<?php

declare(strict_types=1);

namespace Saz\Game\UI\GraphQL\ResolverMap;

use Saz\CatalogSharedContext\UI\GraphQL\TypeResolver\DateTimeTypeResolver;
use Saz\Game\UI\GraphQL\Resolver\Mutation\CreateGameMutation;
use Saz\Game\UI\GraphQL\Resolver\Mutation\UpdateGameMutation;
use Saz\Game\UI\GraphQL\Resolver\Query\GameQuery;
use Saz\Helix\GraphQLBundle\ResolverMap\BaseResolverMap;

final class GameResolverMap extends BaseResolverMap
{
    public function __construct(
        private readonly GameQuery $game,
        private readonly CreateGameMutation $createGame,
        private readonly UpdateGameMutation $updateGame,
    ) {
    }

    /**
     * @return array<string, callable>
     */
    protected function map(): array
    {
        $this->addType('DateTime', DateTimeTypeResolver::class);

        $this->addQuery('game', $this->game);

        $this->addMutation('createGame', $this->createGame);
        $this->addMutation('updateGame', $this->updateGame);

        return $this->getMap();
    }
}
