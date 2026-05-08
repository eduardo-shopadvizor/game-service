#!/usr/bin/env bash
# Scaffolds the three test files for a new use case.
#
# Usage: ./generate-test.sh <Entity> <Action>
#   Entity: Game, Player, Category, etc.
#   Action: Create, Update, Delete, Find, List, etc.
#
# Example:
#   ./generate-test.sh Game Create
#   Creates:
#     tests/Unit/Application/Game/Create/
#     ├── CreateGameCommandHandlerTestCase.php
#     ├── CreateGameCommandMother.php
#     └── CreateGameCommandHandlerTest.php

set -euo pipefail

if [[ $# -ne 2 ]]; then
    echo "Usage: $0 <Entity> <Action>"
    echo "  Entity: e.g., Game, Player, Category"
    echo "  Action: e.g., Create, Update, Delete, Find"
    exit 1
fi

ENTITY="$1"
ACTION="$2"
ENTITY_LC=$(echo "$ENTITY" | tr '[:upper:]' '[:lower:]')
NS="Saz\\\\Game\\\\Tests\\\\Unit\\\\Application\\\\${ENTITY}\\\\${ACTION}"
APP_NS="Saz\\\\Game\\\\Application\\\\${ENTITY}\\\\${ACTION}"
DIR="tests/Unit/Application/${ENTITY}/${ACTION}"

mkdir -p "$DIR"

# --- TestCase ---
cat > "${DIR}/${ACTION}${ENTITY}CommandHandlerTestCase.php" <<TESTCASE
<?php

declare(strict_types=1);

namespace ${NS};

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ${APP_NS}\${ACTION}${ENTITY}UseCase;
use Saz\\Game\\Domain\\${ENTITY}\\Model\\${ENTITY};
use Saz\\Game\\Domain\\${ENTITY}\\Repository\\${ENTITY}RepositoryInterface;

class ${ACTION}${ENTITY}CommandHandlerTestCase extends TestCase
{
    protected ${ENTITY}RepositoryInterface&MockObject \$repository;
    protected ${ACTION}${ENTITY}UseCase \$useCase;

    protected function setUp(): void
    {
        \$this->repository = \$this->createMock(${ENTITY}RepositoryInterface::class);
        \$this->useCase = new ${ACTION}${ENTITY}UseCase(
            repository: \$this->repository,
        );
    }

    protected function expectRepositoryToSave(${ENTITY} \$${ENTITY_LC}): void
    {
        \$this->repository
            ->expects(\$this->once())
            ->method('save')
            ->with(\$${ENTITY_LC})
        ;
    }
}
TESTCASE

# --- Mother ---
cat > "${DIR}/${ACTION}${ENTITY}CommandMother.php" <<MOTHER
<?php

declare(strict_types=1);

namespace ${NS};

use ${APP_NS}\${ACTION}${ENTITY}Command;

class ${ACTION}${ENTITY}CommandMother
{
    public static function create(
        ?string \$id = null,
    ): ${ACTION}${ENTITY}Command {
        return new ${ACTION}${ENTITY}Command(
            id: \$id ?? '550e8400-e29b-41d4-a716-446655440000',
        );
    }
}
MOTHER

# --- Test ---
cat > "${DIR}/${ACTION}${ENTITY}CommandHandlerTest.php" <<TEST
<?php

declare(strict_types=1);

namespace ${NS};

use Saz\\Game\\Tests\\Unit\\Domain\\${ENTITY}\\Model\\${ENTITY}Mother;

class ${ACTION}${ENTITY}CommandHandlerTest extends ${ACTION}${ENTITY}CommandHandlerTestCase
{
    private ${ACTION}${ENTITY}CommandHandler \$handler;

    protected function setUp(): void
    {
        parent::setUp();
        \$this->handler = new ${ACTION}${ENTITY}CommandHandler(
            useCase: \$this->useCase,
        );
    }

    public function test${ENTITY}Is${ACTION}dSuccessfully(): void
    {
        \$${ENTITY_LC} = ${ENTITY}Mother::create();
        \$command = ${ACTION}${ENTITY}CommandMother::create(
            id: \$${ENTITY_LC}->id()->value(),
        );

        \$this->expectRepositoryToSave(\$${ENTITY_LC});

        \$this->handler->__invoke(\$command);
    }
}
TEST

echo "Created test files in $DIR/"
ls -la "$DIR/"
