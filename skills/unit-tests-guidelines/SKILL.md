---
name: unit-tests-guidelines
description: >
  Guide for creating unit tests for Application-layer use cases following
  the project's hexagonal architecture conventions. Use this whenever you
  need to write tests for a new CommandHandler or QueryHandler, or when
  reviewing existing tests for consistency. Also applies when creating
  Object Mothers, TestCase base classes, or adding test coverage to an
  existing use case — even if the handler itself is already written.
---

# Unit Test Guidelines for Use Cases

## Why this pattern

Every use case test follows a three-class pattern: **TestCase → Mother → Test**.
This separation exists because:

- **TestCase** (base class) sets up mocks and provides helper expectations.
  You write the mock wiring once and reuse it across all test methods.
- **Mother** encapsulates valid Command/Query construction. When the DTO
  changes, you update the Mother in one place instead of every test.
- **Test** contains the actual test methods. It stays clean — just arrange,
  act, and assert.

## File structure

For a use case like `CreateGame`, the test files mirror the source layout:

```
tests/Unit/Application/Game/Create/
├── CreateGameCommandHandlerTestCase.php    # Base class with mocks + helpers
├── CreateGameCommandMother.php             # Object Mother for Command DTO
└── CreateGameCommandHandlerTest.php        # Actual test methods
```

## Naming

| File | Format | Example |
|------|--------|---------|
| TestCase | `{Action}{Entity}CommandHandlerTestCase` | `CreateGameCommandHandlerTestCase` |
| Mother | `{Action}{Entity}CommandMother` | `CreateGameCommandMother` |
| Test | `{Action}{Entity}CommandHandlerTest` | `CreateGameCommandHandlerTest` |

| Element | Format | Example |
|---------|--------|---------|
| Test method | `test{Description}()` | `testGameIsCreatedSuccessfully()` |
| Helper method | `expect{Target}{Action}()` | `expectRepositoryToSave()` |

## Components

### 1. TestCase — base class with mocks

Sets up all repository and service mocks, plus the UseCase under test.
Child classes extend this to get pre-configured dependencies.

```php
namespace Saz\Game\Tests\Unit\Application\Game\Create;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Saz\Game\Application\Game\Create\CreateGameUseCase;
use Saz\Game\Domain\Game\Model\Game;
use Saz\Game\Domain\Game\Repository\GameRepositoryInterface;

class CreateGameCommandHandlerTestCase extends TestCase
{
    protected GameRepositoryInterface&MockObject $repository;
    protected CreateGameUseCase $useCase;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(GameRepositoryInterface::class);
        $this->useCase = new CreateGameUseCase(
            repository: $this->repository,
        );
    }

    protected function expectRepositoryToSave(Game $game): void
    {
        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($game)
        ;
    }
}
```

### 2. Mother — Command DTO factory

```php
namespace Saz\Game\Tests\Unit\Application\Game\Create;

use Saz\Game\Application\Game\Create\CreateGameCommand;

class CreateGameCommandMother
{
    public static function create(
        ?string $id = null,
        ?string $name = null,
    ): CreateGameCommand {
        return new CreateGameCommand(
            id: $id ?? '550e8400-e29b-41d4-a716-446655440000',
            name: $name ?? 'Test Game',
        );
    }
}
```

### 3. Test — actual test methods

```php
namespace Saz\Game\Tests\Unit\Application\Game\Create;

use Saz\Game\Tests\Unit\Domain\Game\Model\GameMother;

class CreateGameCommandHandlerTest extends CreateGameCommandHandlerTestCase
{
    private CreateGameCommandHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = new CreateGameCommandHandler(
            useCase: $this->useCase,
        );
    }

    public function testGameIsCreatedSuccessfully(): void
    {
        $game = GameMother::create();
        $command = CreateGameCommandMother::create(
            id: $game->id()->value(),
            name: $game->name()->value(),
        );

        $this->expectRepositoryToSave($game);

        $this->handler->__invoke($command);
    }
}
```

## Mock patterns

| Scenario | Pattern |
|----------|---------|
| Repository returns entity | `->method('find')->with($id)->willReturn($entity)` |
| Repository returns null | `->method('find')->with($id)->willReturn(null)` |
| Method must not be called | `->expects($this->never())->method('delete')` |
| Method must throw | `->method('save')->willThrowException(new \Exception())` |
| Void method (no return) | `->expects($this->once())->method('save')` |

## Using Domain Mothers

Reuse existing Domain Mothers rather than creating entities inline:

```php
use Saz\Game\Tests\Unit\Domain\Game\Model\GameMother;

$game = GameMother::create();                           // defaults
$game = GameMother::create(name: new GameName('Custom')); // overrides
```

## Common mistakes

- **Wrong namespace**: Must match `Saz\Game\Tests\Unit\Application\{Entity}\{Action}`
- **Missing `parent::setUp()`**: The child Test class must call `parent::setUp()` first
- **Skipping Mother**: Don't construct Command DTOs inline in tests — use the Mother
- **Inconsistent naming**: Methods should be `expect{Target}{Action}`, not `expect{Action}{Target}`
