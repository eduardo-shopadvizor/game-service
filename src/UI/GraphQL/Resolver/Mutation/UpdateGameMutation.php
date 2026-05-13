<?php

declare(strict_types=1);

namespace Saz\Game\UI\GraphQL\Resolver\Mutation;

use Overblog\GraphQLBundle\Definition\Argument as ArgumentInterface;
use Saz\CatalogSharedContext\UI\GraphQL\Resolver\Mutation\BaseMutation;
use Saz\Game\Application\Game\Find\GetGameByIdQuery;
use Saz\Game\Application\Game\Update\UpdateGameCommand;

final class UpdateGameMutation extends BaseMutation
{
    public function __invoke(ArgumentInterface $argument): mixed
    {
        $id = $argument->offsetGet('id');

        $this->command(new UpdateGameCommand(...[
            'id' => $id,
            ...$argument->offsetGet('input'),
        ]));

        return $this->query(new GetGameByIdQuery($id));
    }
}
