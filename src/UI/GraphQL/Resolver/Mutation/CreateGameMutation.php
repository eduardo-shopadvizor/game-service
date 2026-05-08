<?php

declare(strict_types=1);

namespace Saz\Game\UI\GraphQL\Resolver\Mutation;

use Overblog\GraphQLBundle\Definition\Argument as ArgumentInterface;
use Ramsey\Uuid\Uuid;
use Saz\CatalogSharedContext\UI\GraphQL\Resolver\Mutation\BaseMutation;
use Saz\Game\Application\Game\Create\CreateGameCommand;
use Saz\Game\Application\Game\Find\GetGameByIdQuery;

final class CreateGameMutation extends BaseMutation
{
    public function __invoke(ArgumentInterface $argument): mixed
    {
        $id = Uuid::uuid4()->toString();

        $this->command(new CreateGameCommand(...[
            'id' => $id,
            ...$argument->offsetGet('input'),
        ]));

        return $this->query(new GetGameByIdQuery($id));
    }
}
