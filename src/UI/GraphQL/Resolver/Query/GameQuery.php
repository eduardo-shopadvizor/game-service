<?php

declare(strict_types=1);

namespace Saz\Game\UI\GraphQL\Resolver\Query;

use Overblog\GraphQLBundle\Definition\Argument as ArgumentInterface;
use Saz\CatalogSharedContext\UI\GraphQL\Resolver\Query\BaseQuery;
use Saz\Game\Application\Game\Find\GetGameByIdQuery;

final class GameQuery extends BaseQuery
{
    public function __invoke(ArgumentInterface $argument): mixed
    {
        return $this->query(new GetGameByIdQuery($argument->offsetGet('id')));
    }
}
