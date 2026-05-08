#!/usr/bin/env bash
# Scaffolds GraphQL resolver, schema entry, and ResolverMap wiring for
# a new use case. Run from the project root.
#
# Usage: ./generate-crud.sh <Entity> <operation>
#   operation: mutation (create|update|delete) or query (get|list)
#
# Example:
#   ./generate-crud.sh Game mutation create
#   Creates resolver, schema type/input, and updates resolver map

set -euo pipefail

if [[ $# -lt 2 ]]; then
    echo "Usage: $0 <Entity> <type> [operation]"
    echo "  Entity:    Game, Player, Category, etc."
    echo "  Type:      mutation | query"
    echo "  Operation: for mutation: create | update | delete"
    echo "             for query:    get | list"
    exit 1
fi

ENTITY="$1"
TYPE="$2"
OP="${3:-}"
ENTITY_LC=$(echo "$ENTITY" | tr '[:upper:]' '[:lower:]')

case "$TYPE" in
    mutation)
        if [[ -z "$OP" ]]; then
            echo "Error: mutation requires an operation: create, update, or delete"
            exit 1
        fi
        case "$OP" in
            create)
                echo "Creating Create${ENTITY}Mutation..."
                cat > "src/UI/GraphQL/Resolver/Mutation/Create${ENTITY}Mutation.php" <<PHP
<?php

declare(strict_types=1);

namespace Saz\\Game\\UI\\GraphQL\\Resolver\\Mutation;

use Overblog\\GraphQLBundle\\Definition\\Argument as ArgumentInterface;
use Ramsey\\Uuid\\Uuid;
use Saz\\CatalogSharedContext\\UI\\GraphQL\\Resolver\\BaseMutation;
use Saz\\Game\\Application\\${ENTITY}\\Create\\Create${ENTITY}Command;
use Saz\\Game\\Application\\${ENTITY}\\Find\\Find${ENTITY}Query;

final class Create${ENTITY}Mutation extends BaseMutation
{
    public function __invoke(ArgumentInterface \$argument): mixed
    {
        \$this->denyAccessUnlessGrantedForAdmin();

        \$id = Uuid::uuid4()->toString();

        \$this->command(new Create${ENTITY}Command(
            id: \$id,
            ...\$argument->offsetGet('input'),
        ));

        return \$this->query(new Find${ENTITY}Query(\$id));
    }
}
PHP
                ;;

            update)
                echo "Creating Update${ENTITY}Mutation..."
                cat > "src/UI/GraphQL/Resolver/Mutation/Update${ENTITY}Mutation.php" <<PHP
<?php

declare(strict_types=1);

namespace Saz\\Game\\UI\\GraphQL\\Resolver\\Mutation;

use Overblog\\GraphQLBundle\\Definition\\Argument as ArgumentInterface;
use Saz\\CatalogSharedContext\\UI\\GraphQL\\Resolver\\BaseMutation;
use Saz\\Game\\Application\\${ENTITY}\\Update\\Update${ENTITY}Command;
use Saz\\Game\\Application\\${ENTITY}\\Find\\Find${ENTITY}Query;

final class Update${ENTITY}Mutation extends BaseMutation
{
    public function __invoke(ArgumentInterface \$argument): mixed
    {
        \$id = \$argument->offsetGet('id');

        \$this->command(new Update${ENTITY}Command(
            id: \$id,
            ...\$argument->offsetGet('input'),
        ));

        return \$this->query(new Find${ENTITY}Query(\$id));
    }
}
PHP
                ;;

            delete)
                echo "Creating Delete${ENTITY}Mutation..."
                cat > "src/UI/GraphQL/Resolver/Mutation/Delete${ENTITY}Mutation.php" <<PHP
<?php

declare(strict_types=1);

namespace Saz\\Game\\UI\\GraphQL\\Resolver\\Mutation;

use Overblog\\GraphQLBundle\\Definition\\Argument as ArgumentInterface;
use Saz\\CatalogSharedContext\\UI\\GraphQL\\Resolver\\BaseMutation;
use Saz\\Game\\Application\\${ENTITY}\\Delete\\Delete${ENTITY}Command;

final class Delete${ENTITY}Mutation extends BaseMutation
{
    public function __invoke(ArgumentInterface \$argument): bool
    {
        \$this->denyAccessUnlessGrantedForAdmin();
        \$this->command(new Delete${ENTITY}Command(\$argument->offsetGet('id')));
        return true;
    }
}
PHP
                ;;
        esac
        ;;

    query)
        case "$OP" in
            get)
                echo "Creating ${ENTITY}Query..."
                cat > "src/UI/GraphQL/Resolver/Query/${ENTITY}Query.php" <<PHP
<?php

declare(strict_types=1);

namespace Saz\\Game\\UI\\GraphQL\\Resolver\\Query;

use Overblog\\GraphQLBundle\\Definition\\Argument as ArgumentInterface;
use Saz\\CatalogSharedContext\\UI\\GraphQL\\Resolver\\BaseQuery;
use Saz\\Game\\Application\\${ENTITY}\\Find\\Find${ENTITY}Query;

final class ${ENTITY}Query extends BaseQuery
{
    public function __invoke(ArgumentInterface \$argument): mixed
    {
        return \$this->query(new Find${ENTITY}Query(\$argument->offsetGet('id')));
    }
}
PHP
                ;;

            list)
                echo "Creating ${ENTITY}sQuery..."
                cat > "src/UI/GraphQL/Resolver/Query/${ENTITY}sQuery.php" <<PHP
<?php

declare(strict_types=1);

namespace Saz\\Game\\UI\\GraphQL\\Resolver\\Query;

use Overblog\\GraphQLBundle\\Definition\\Argument as ArgumentInterface;
use Saz\\CatalogSharedContext\\UI\\GraphQL\\Resolver\\BaseQuery;
use Saz\\Game\\Application\\${ENTITY}\\List\\List${ENTITY}sQuery;

final class ${ENTITY}sQuery extends BaseQuery
{
    public function __invoke(ArgumentInterface \$argument): mixed
    {
        return \$this->query(new List${ENTITY}sQuery(
            pagination: \$argument->offsetGet('pagination'),
            filter:     \$argument->offsetGet('filter'),
        ));
    }
}
PHP
                ;;
        esac
        ;;
esac

echo "Done. Don't forget to register the resolver in GameResolverMap."
